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

    protected function makeCacheKey(int $productId, float $price): string
    {
        return "best_discount_product_{$productId}_" . md5((string) $price);
    }

    protected function rememberCacheKey(int $productId, string $cacheKey): void
    {
        $listKey = "best_discount_product_{$productId}_keys";
        $keys = Cache::get($listKey, []);

        if (!in_array($cacheKey, $keys, true)) {
            $keys[] = $cacheKey;
            Cache::put($listKey, $keys, now()->addHours(12));
        }
    }

    public function getPriceforProductVariant(ProductVariant $productVariant): float
    {
        return $productVariant->price;
    }

    public function calculateProductDiscount(Product $product, ?float $priceOverride = null): array
    {
        $originalPrice = $priceOverride ?? $product->price;

        // Fallback: If price is 0 (container product), use minimum variant price
        if ($originalPrice <= 0) {
            $originalPrice = $product->productVariants()->min('price') ?? 0;
        }

        $bestDiscount = $this->findBestDiscountForProduct($product, $originalPrice);

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

    public function findBestDiscountAmongMultipleDiscountIds(array $discountIds, float $price): ?Discount
    {
        if (empty($discountIds)) {
            return null;
        }

        // Get active discounts matching the provided IDs
        $discounts = $this->getActiveDiscounts()->whereIn('id', $discountIds);

        if ($discounts->isEmpty()) {
            return null;
        }

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

    /**
     * Find best discount for a product considering all applicable targets
     *
     * Checks for discounts targeting:
     * - Product variants (target_type='product' contains variant IDs)
     * - Brand (target_type='brand')
     * - Category (target_type='category')
     * - Subcategory (target_type='subcategory')
     *
     * @param Product $product The product to find discount for
     * @param float|null $price Optional price override (e.g., for specific variant)
     * @return Discount|null The best discount or null
     */
    public function findBestDiscountForProduct(Product $product, ?float $price = null): ?Discount
    {
        $comparisonPrice = $price ?? $product->price;
        $cacheKey = $this->makeCacheKey($product->id, $comparisonPrice);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($product, $comparisonPrice, $cacheKey) {
            // Get all variant IDs for this product
            $variantIds = $product->productVariants()->pluck('id')->toArray();
            Log::debug("Variant IDs for product {$product->id}: " . implode(', ', $variantIds));

            // Get all active discounts with their targets
            $allDiscounts = $this->getActiveDiscounts();
            Log::debug("Total active discounts retrieved: " . $allDiscounts->count());

            // Filter applicable discounts based on targets
            $applicableDiscounts = $allDiscounts->filter(function ($discount) use ($product, $variantIds) {
                return $this->isDiscountApplicableToProduct($discount, $product, $variantIds);
                Log::debug("Discount {$discount->id} is applicable to product {$product->id}");
            });

            if ($applicableDiscounts->isEmpty()) {
                return null;
            }

            // Find the discount that gives maximum savings
            $bestDiscount = $this->selectBestDiscount($applicableDiscounts, $comparisonPrice);

            if ($bestDiscount) {
                $this->rememberCacheKey($product->id, $cacheKey);
            }

            return $bestDiscount;
        });
    }

    /**
     * Check if a discount is applicable to a product
     *
     * @param Discount $discount
     * @param Product $product
     * @param array $variantIds
     * @return bool
     */
    protected function isDiscountApplicableToProduct(Discount $discount, Product $product, array $variantIds): bool
    {
        foreach ($discount->targets as $target) {
            // target_type='product' actually stores ProductVariant IDs
            if ($target->target_type === 'product' && in_array($target->target_id, $variantIds, true)) {
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

    /**
     * Select the best discount from a collection based on maximum reduction
     *
     * @param Collection $discounts
     * @param float $price
     * @return Discount|null
     */
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

    /**
     * Calculate the reduction amount for a discount on a given price
     *
     * @param Discount $discount
     * @param float $price
     * @return float The reduction amount
     */
    public function calculateVariantDiscount(ProductVariant $variant): array
    {
        // Ensure we get the associated price with the product variant
        $price = $variant->price; // Get the price from the variant
        $product = $variant->product; // Get the associated product

        // Check if the product has a discount
        $discount = $this->getDiscount($product);

        if ($discount) {
            // Apply the discount to the price
            $discountedPrice = $price - ($price * ($discount->percentage / 100));
            return ['original_price' => $price, 'discounted_price' => $discountedPrice];
        }

        return ['original_price' => $price, 'discounted_price' => $price];
    }

    /**
     * Get all active discounts with their targets eagerly loaded
     *
     * @return Collection
     */
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

    /**
     * Get a single applicable discount for a product (legacy method)
     * Note: Use findBestDiscountForProduct for "Best Discount Wins" logic
     *
     * @param Product $product
     * @return Discount|null
     */
    public function getDiscount(Product $product): ?Discount
    {
        // Get product variants
        $variantIds = $product->productVariants()->pluck('id')->toArray();

        // Query for active discounts that target this product's variants, brand, category, or subcategory
        $discount = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->whereHas('targets', function ($q) use ($product, $variantIds) {
                $q->where(function ($query) use ($variantIds) {
                    // target_type='product' contains ProductVariant IDs
                    if (!empty($variantIds)) {
                        $query->where('target_type', 'product')
                            ->whereIn('target_id', $variantIds);
                    }
                })
                    ->orWhere(function ($query) use ($product) {
                        // target_type='brand' contains Brand IDs
                        if ($product->brand_id) {
                            $query->where('target_type', 'brand')
                                ->where('target_id', $product->brand_id);
                        }
                    })
                    ->orWhere(function ($query) use ($product) {
                        // target_type='category' contains Category IDs
                        if ($product->category_id) {
                            $query->where('target_type', 'category')
                                ->where('target_id', $product->category_id);
                        }
                    })
                    ->orWhere(function ($query) use ($product) {
                        // target_type='subcategory' contains SubCategory IDs
                        if ($product->sub_category_id) {
                            $query->where('target_type', 'subcategory')
                                ->where('target_id', $product->sub_category_id);
                        }
                    });
            })
            ->orderByDesc('value')
            ->first();

        return $discount;
    }

    /**
     * Get all active discounts (alias for compatibility)
     *
     * @return Collection
     */
    public function getAllDiscounts(): Collection
    {
        return $this->getActiveDiscounts();
    }

    /**
     * Clear discount cache for a specific product
     *
     * @param int $productId
     * @return void
     */
    public function clearProductDiscountCache(int $productId): void
    {
        $listKey = "best_discount_product_{$productId}_keys";
        $keys = Cache::get($listKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($listKey);
        Cache::forget("best_discount_product_{$productId}");
    }

    /**
     * Clear all discount caches
     *
     * @return void
     */
    public function clearAllDiscountCaches(): void
    {
        Cache::flush();
        Log::info('All discount caches cleared');
    }

    /**
     * Calculate cart totals with discounts applied
     *
     * @param Collection $cartItems Collection of cart items with product/variant relationships
     * @return array Cart totals with discount details
     */
    public function calculateCartTotals($cartItems): array
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $itemsWithDiscounts = [];

        foreach ($cartItems as $item) {
            $product = $item->product;
            $variant = $item->variant;

            // Get price (variant price takes precedence)
            $itemPrice = $variant ? $variant->price : $product->price;
            $quantity = $item->quantity;

            // Calculate discount
            $discountData = $variant
                ? $this->calculateVariantDiscount($variant)
                : $this->calculateProductDiscount($product);

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

    /**
     * Get simplified discount information for a product
     *
     * @param Product $product
     * @return array
     */
    public function getDiscountInfoForProduct(Product $product): array
    {
        $discount = $this->findBestDiscountForProduct($product, $product->price);

        if (!$discount) {
            return [
                'has_discount' => false,
                'discount_name' => null,
                'discount_id' => null,
            ];
        }

        return [
            'has_discount' => true,
            'discount_name' => $discount->getTranslation('name', app()->getLocale()),
            'discount_id' => $discount->id,
        ];
    }

    protected function calculateReduction(Discount $discount, float $price): float
    {
        $value = (float) $discount->value;
        $reduction = 0;

        // Debug logging
        Log::info('Calculating reduction', [
            'discount_id' => $discount->id,
            'discount_type' => $discount->type,
            'discount_value' => $discount->value,
            'price' => $price,
        ]);

        // Calculate the base reduction based on discount type
        if ($discount->type === 'flat') {
            // Flat discount - direct amount off
            $reduction = $value;
        } else {
            // Percentage discount - calculate percentage of price
            $reduction = ($value / 100) * $price;
        }

        // Apply max_discount cap if set (upper limit on discount)
        $maxDiscount = (float) ($discount->max_discount ?? 0);
        if ($maxDiscount > 0) {
            $reduction = min($reduction, $maxDiscount);
        }

        // Apply min_discount cap if set (ensure minimum discount is applied)
        $minDiscount = (float) ($discount->min_discount ?? 0);
        if ($minDiscount > 0) {
            $reduction = max($reduction, $minDiscount);
        }

        return $reduction;
    }
}
