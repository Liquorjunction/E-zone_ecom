<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * DiscountService
 *
 * Implements "Best Discount Wins" strategy
 *
 * IMPORTANT: In discount_targets table, target_type enum values:
 * - 'brand' => stores Brand ID
 * - 'category' => stores Category ID
 * - 'subcategory' => stores SubCategory ID
 * - 'product' => stores ProductVariant ID (NOT Product container ID!)
 *
 * Returns the discount that gives maximum savings to the customer
 */
class DiscountService
{
    // --- Discount Retrieval -----------------------------------------------------------------
    public function getActiveDiscounts(): Collection
    {
        $now = now();

        return Discount::where('status', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->with(['targets'])
            ->get();
    }

    protected function isDiscountApplicableToProduct(Discount $discount, Product $product, int $variantId): bool
    {
        foreach ($discount->targets as $target) {
            // target_type='product' actually stores ProductVariant IDs
            if ($target->target_type === 'product' && $target->target_id === $variantId) {
                return true;
            }

            // target_type='brand' stores Brand ID
            if ($target->target_type === 'brand' && $product->brand_id && $target->target_id === $product->brand_id) {
                return true;
            }

            // target_type='category' stores Category ID
            if ($target->target_type === 'category' && $product->category_id && $target->target_id === $product->category_id) {
                return true;
            }

            // target_type='subcategory' stores SubCategory ID
            if ($target->target_type === 'subcategory' && $product->sub_category_id && $target->target_id === $product->sub_category_id) {
                return true;
            }
        }

        return false;
    }

    protected function selectBestDiscount(Collection $discounts, float $price): ?Discount
    {
        $bestDiscount = null;
        $maxReduction = 0.0;

        foreach ($discounts as $discount) {
            $reduction = $this->calculateReduction($discount, $price);

            if ($reduction > $maxReduction) {
                $maxReduction = $reduction;
                $bestDiscount = $discount;
            }
        }

        return $bestDiscount;
    }

    // Helpers -----------------------------------------------------------------
    public function calculateProductDiscount(Product $product, int $variantId, ?float $priceOverride = null): array
    {
        // Include soft-deleted variants so carts keep their historical price
        $variant = $product->productVariants()->withTrashed()->find($variantId);
        Log::debug("Variant IDs for product {$product->id}: " . $product->productVariants()->pluck('id')->join(', '));

        if (!$variant) {
            return [
                'has_discount' => false,
                'original_price' => 0,
                'final_price' => 0,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'discount_name' => null,
                'discount_id' => null,
            ];
        }

        $originalPrice = $priceOverride ?? $variant->price;

        $bestDiscount = $this->findBestDiscountForProduct($product, $variantId, $originalPrice);

        if (!$bestDiscount) {
            return [
                'has_discount' => false,
                'original_price' => $originalPrice,
                'final_price' => $originalPrice,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'discount_name' => null,
                'discount_id' => null,
            ];
        }

        $reduction = $this->calculateReduction($bestDiscount, $originalPrice);
        $finalPrice = max($originalPrice - $reduction, 0);

        return [
            'has_discount' => true,
            'original_price' => $originalPrice,
            'final_price' => round($finalPrice, 2),
            'discount_amount' => round($reduction, 2),
            'discount_percentage' => $bestDiscount->type === 'percentage' ? $bestDiscount->value : 0,
            'discount_name' => $bestDiscount->getTranslation('name', app()->getLocale()),
            'discount_id' => $bestDiscount->id,
        ];
    }

    public function findBestDiscountForProduct(Product $product, int $variantId, ?float $price = null): ?Discount
    {
        // Include soft-deleted variants so we can still compute pricing for cart lines
        $variant = $product->productVariants()->withTrashed()->find($variantId);

        if (!$variant) {
            return null;
        }

        $comparisonPrice = $price ?? $variant->price;
        $cacheKey = $this->makeCacheKey($variantId, $comparisonPrice);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($product, $variantId, $comparisonPrice, $cacheKey) {
            Log::debug("Looking for discounts for variant {$variantId}");

            // Get all active discounts with their targets
            $allDiscounts = $this->getActiveDiscounts();
            Log::debug("Total active discounts retrieved: " . $allDiscounts->count());

            // Filter applicable discounts based on targets
            $applicableDiscounts = $allDiscounts->filter(function ($discount) use ($product, $variantId) {
                return $this->isDiscountApplicableToProduct($discount, $product, $variantId);
            });

            if ($applicableDiscounts->isEmpty()) {
                return null;
            }

            // Find the discount that gives maximum savings
            $bestDiscount = $this->selectBestDiscount($applicableDiscounts, $comparisonPrice);

            if ($bestDiscount) {
                $this->rememberCacheKey($variantId, $cacheKey);
            }

            return $bestDiscount;
        });
    }

    protected function makeCacheKey(int $variantId, float $price): string
    {
        return "best_discount_product_{$variantId}_" . md5((string) $price);
    }

    protected function rememberCacheKey(int $variantId, string $cacheKey): void
    {
        // Retrieve and update the list of cache keys for this product
        $listKey = "best_discount_product_{$variantId}_keys";
        $keys = Cache::get($listKey, []);

        if (!in_array($cacheKey, $keys, true)) {
            $keys[] = $cacheKey;
            Cache::put($listKey, $keys, now()->addHours(12));
        }
    }

    protected function calculateReduction(Discount $discount, float $price): float
    {
        $value = (float) $discount->value;
        $reduction = 0;

        Log::info('Calculating reduction', [
            'discount_id' => $discount->id,
            'discount_type' => $discount->type,
            'discount_value' => $discount->value,
            'price' => $price,
        ]);

        if ($discount->type === 'flat') {
            $reduction = $value;
            Log::info('Flat discount applied', ['reduction' => $reduction]);
        } else {
            $reduction = ($value / 100) * $price;
            Log::info('Percentage discount applied', ['reduction' => $reduction]);
        }

        $maxDiscount = (float) ($discount->max_discount ?? 0);
        if ($maxDiscount > 0) {
            $reduction = min($reduction, $maxDiscount);
            Log::info('Max discount applied', ['reduction' => $reduction]);
        }

        $minDiscount = (float) ($discount->min_discount ?? 0);
        if ($minDiscount > 0) {
            $reduction = max($reduction, $minDiscount);
            Log::info('Min discount applied', ['reduction' => $reduction]);
        }

        return $reduction;
    }

    public function calculateCartTotals($cartItems): array
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $itemsWithDiscounts = [];

        foreach ($cartItems as $item) {
            $product = $item->product;
            // Make sure we have the variant instance even if the relation isn't eager-loaded
            $variant = $item->variant ?? ProductVariant::find($item->variant_id);

            // Get price (variant price takes precedence)
                // If variant is missing, try first variant (include soft-deleted) before falling back to product price
                $firstVariant = null;
                if (!$variant) {
                    $firstVariant = $product->productVariants()->first();
                    $variant = $firstVariant;
                }
                $itemPrice = $variant?->price ?? ($item->unit_price ?? $product->price ?? 0);
            $quantity = $item->quantity;

            // Calculate discount
            if ($variant) {
                // Pass the resolved variant for accurate pricing
                $discountData = $this->calculateVariantDiscount($variant);
            } else {
                // Get the first variant ID for products without a specific variant
                    $variantId = $variant?->id ?? ($firstVariant?->id ?? 0);
                    $discountData = $this->calculateProductDiscount($product, $variantId, $itemPrice);
            }

            $lineTotal = $itemPrice * $quantity;
            $lineDiscount = $discountData['discount_amount'] * $quantity;
            $lineFinal = $discountData['final_price'] * $quantity;

            $subtotal += $lineTotal;
            $totalDiscount += $lineDiscount;

            $itemsWithDiscounts[] = [
                'cart_item_id' => $item->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
                'unit_price' => $itemPrice,
                'line_total' => round($lineTotal, 2),
                'line_discount' => round($lineDiscount, 2),
                'line_final' => round($lineFinal, 2),
                'discount_info' => $discountData,
            ];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'total_discount' => round($totalDiscount, 2),
            'total' => round($subtotal - $totalDiscount, 2),
            'items' => $itemsWithDiscounts,
        ];
    }

    public function calculateVariantDiscount(ProductVariant $variant): array
    {
        // Ensure we get the associated price with the product variant
        $price = $variant->price; // Get the price from the variant
        $product = $variant->product; // Get the associated product

        // Check if the product has a discount
        $discount = $this->findBestDiscountForProduct($product, $variant->id, $price);

        if ($discount) {
            // Apply the discount to the price
            $reduction = $this->calculateReduction($discount, $price);
            $finalPrice = max($price - $reduction, 0);

            return [
                'has_discount' => true,
                'original_price' => $price,
                'final_price' => round($finalPrice, 2),
                'discount_amount' => round($reduction, 2),
                'discount_percentage' => $discount->type === 'percentage' ? $discount->value : 0,
                'discount_name' => $discount->getTranslation('name', app()->getLocale()),
                'discount_id' => $discount->id,
            ];
        }

        return [
            'has_discount' => false,
            'original_price' => $price,
            'final_price' => $price,
            'discount_amount' => 0,
            'discount_percentage' => 0,
            'discount_name' => null,
            'discount_id' => null,
        ];
    }

    public function clearAllDiscountCaches(): void
    {
        Cache::flush();
        Log::info('All discount caches cleared');
    }
}
