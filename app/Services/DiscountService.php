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

    protected function isDiscountApplicableToProduct(Discount $discount, $productOrVariant, $variantId = null): bool
    {
        // Resolve Variant and Product
        $variant = null;
        $product = null;

        if ($productOrVariant instanceof ProductVariant) {
            $variant = $productOrVariant;
            $product = $variant->product;
        } elseif ($productOrVariant instanceof Product) {
            $product = $productOrVariant;
            if ($variantId) {
                $variant = $product->productVariants->find($variantId);
            } else {
                // If no variant specified, try to find first or default
                $variant = $product->productVariants->first();
            }
        }

        if (!$product || !$variant) {
            return false;
        }

        // Logic: specific variant match OR parent category/brand match
        foreach ($discount->targets as $target) {
            // target_type='product' actually stores ProductVariant IDs (Sellable Item)
            if ($target->target_type === 'product' && $target->target_id == $variant->id) {
                return true;
            }

            // Check Parent Product attributes (Container)
            if ($target->target_type === 'category' && $target->target_id == $product->category_id) {
                return true;
            }

            if ($target->target_type === 'subcategory' && $target->target_id == $product->sub_category_id) {
                return true;
            }

            if ($target->target_type === 'brand' && $target->target_id == $product->brand_id) {
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
    public function calculateProductDiscount($product, $variantId = null): array
    {
        // Normalize input: If passed a ProductVariant, extract ID and Parent
        if ($product instanceof ProductVariant) {
            $variant = $product;
            $variantId = $variant->id;
            // Ensure product relation is loaded
            if (!$variant->relationLoaded('product')) {
                $variant->load('product');
            }
            $parentProduct = $variant->product;
            $price = $variant->price;
        } else {
            // It is a Product model, we need the variant
            $parentProduct = $product;
            $variant = null;

            if ($variantId) {
                $variant = $parentProduct->productVariants->find($variantId);
            }

            if (!$variant) {
                // Fallback or error handling if variant not found
                // For safety, return no discount if we can't identify the sellable item
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
            $price = $variant->price;
        }

        // Check if the product/variant has a discount
        // We pass the object effectively to findBestDiscountForProduct
        $discount = $this->findBestDiscountForProduct($parentProduct, $variantId, $price);

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
                'discount_name' => $discount->name ?? 'Discount', // Fallback if translation fails
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

    public function findBestDiscountForProduct($product, $variantId = null, $price = null): ?Discount
    {
        $discounts = $this->getActiveDiscounts();
        $bestDiscount = null;
        $maxSavings = 0;

        // Resolve price if not provided
        if (is_null($price)) {
            if ($product instanceof ProductVariant) {
                $price = $product->price;
            } elseif ($product instanceof Product && $variantId) {
                $variant = $product->productVariants->find($variantId);
                $price = $variant ? $variant->price : 0;
            }
        }

        foreach ($discounts as $discount) {
            if ($this->isDiscountApplicableToProduct($discount, $product, $variantId)) {
                $savings = $this->calculateReduction($discount, $price);

                if ($savings > $maxSavings) {
                    $maxSavings = $savings;
                    $bestDiscount = $discount;
                }
            }
        }

        return $bestDiscount;
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

    public function calculateReduction(Discount $discount, float $price): float
    {
        if ($discount->type === 'flat') {
            return min($discount->value, $price);
        } elseif ($discount->type === 'percentage') {
            $reduction = $price * ($discount->value / 100);
            if ($discount->max_discount > 0) {
                $reduction = min($reduction, $discount->max_discount);
            }
            return $reduction;
        }
        return 0;
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
            // Get the Prices associated with the Product Variant
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
