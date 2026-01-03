<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DiscountService
 *
 * Implements "Best Discount Wins" strategy
 * - Fetches all applicable discounts (brand, category, subcategory, product)
 * - Returns the discount that gives maximum savings to the customer
 */
class DiscountService
{

    public function getPriceforProductVariant(ProductVariant $productVariant): float
    {
        // Get the price associated with the variant
        return $productVariant->price;
    }

    /**
     * Calculate the best discount for a product
     *
     * @param Product $product
     * @param float|null $priceOverride Optional price override (for variants)
     * @return array Discount calculation result
     */
    public function calculateProductDiscount(Product $product, ?float $priceOverride = null): array
    {
        $originalPrice = $priceOverride ?? $product->price;
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

    /**
     * Calculate discount for a product variant
     *
     * @param ProductVariant $variant
     * @return array Discount calculation result
     */
    public function calculateVariantDiscount(ProductVariant $variant): array
    {
        $product = $variant->product;
        return $this->calculateProductDiscount($product, $variant->price);
    }

    /**
     * Find the best discount for a product using "Best Discount Wins" logic
     *
     * @param Product $product
     * @param float|null $price The price to use for discount comparison
     * @return Discount|null
     */
    public function findBestDiscountForProduct(Product $product, ?float $price = null)
    {
        // Use provided price or fallback to product price
        $comparisonPrice = $price ?? $product->price;

        // Cache key includes price to handle different variant prices
        $cacheKey = "best_discount_product_{$product->id}_" . md5((string)$comparisonPrice);

        // Logging for debugging
        Log::info('Finding best discount for product', [
            'product_id' => $product->id,
            'comparison_price' => $comparisonPrice
        ]);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($product, $comparisonPrice) {
            $now = now();

            // Get product variants
            $variantIds = $product->productVariants()->pluck('id')->toArray();

            // Fetch all active discounts that target this product or its parents
            $query = Discount::where('status', true)
                ->where(function($q) use ($now) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                })
                ->whereHas('targets', function ($q) use ($product, $variantIds) {
                    $q->where(function ($query) use ($product) {
                        // 1. Direct Product Target (highest priority conceptually, but we compare actual savings)
                        $query->where('target_type', 'product')
                              ->where('target_id', $product->id);
                    })
                    ->orWhere(function ($query) use ($variantIds) {
                        // 2. Variant Target
                        if (!empty($variantIds)) {
                            $query->where('target_type', 'variant')
                                  ->whereIn('target_id', $variantIds);
                        }
                    })
                    ->orWhere(function ($query) use ($product) {
                        // 3. Brand Target
                        if ($product->brand_id) {
                            $query->where('target_type', 'brand')
                                  ->where('target_id', $product->brand_id);
                        }
                    })
                    ->orWhere(function ($query) use ($product) {
                        // 4. Category Target
                        if ($product->category_id) {
                            $query->where('target_type', 'category')
                                  ->where('target_id', $product->category_id);
                        }
                    })
                    ->orWhere(function ($query) use ($product) {
                        // 5. SubCategory Target
                        if ($product->sub_category_id) {
                            $query->where('target_type', 'subcategory')
                                  ->where('target_id', $product->sub_category_id);
                        }
                    });
                })

                // Log the generated SQL for debugging
                ->tap(function ($q) {
                    Log::info('Discount Query SQL', ['sql' => $q->toSql(), 'bindings' => $q->getBindings()]);
                })
                ->get();

                // Logging for debugging
                Log::info('Finding best discount for product', [
                    'product_id' => $product->id,
                    'applicable_discounts' => $query->pluck('id')->toArray(),
                    'applicable_discounts_count' => $query->count(),
                    'applicable_discounts_type' => $query->pluck('type')->toArray(),
                    'applicable_discounts_name' => $query->pluck('name')->toArray(),
                    'applicable_discounts_value' => $query->pluck('value')->toArray(),
                    'applicable_discounts_starts_at' => $query->pluck('starts_at')->toArray(),
                    'applicable_discounts_ends_at' => $query->pluck('ends_at')->toArray(),
                    'applicable_discounts_targets' => $query->pluck('targets')->toArray(),
                    'applicable_discounts_targets_count' => $query->pluck('targets')->count(),
                ]);

                // Log the Query Results
                foreach ($query as $discount) {
                    Log::info('Applicable discount', [
                        'discount_id' => $discount->id,
                        'discount_name' => $discount->getTranslation('name', app()->getLocale()),
                        'discount_type' => $discount->type,
                        'discount_value' => $discount->value,
                    ]);
                }

            if ($query->isEmpty()) {
                return null;
            }

            // "Best Discount Wins" - Compare all and return the one with maximum savings
            $bestDiscount = null;
            $maxReductionAmount = 0;

            foreach ($query as $discount) {
                $reduction = $this->calculateReduction($discount, $comparisonPrice);

                // Logging for debugging
                Log::info('Applicable discount - reduction', [
                    'discount_id' => $discount->id,
                    'discount_name' => $discount->getTranslation('name', app()->getLocale()),
                    'discount_type' => $discount->type,
                    'discount_value' => $discount->value,
                    'discount_reduction' => $reduction,
                ]);

                if ($reduction > $maxReductionAmount) {
                    $maxReductionAmount = $reduction;
                    $bestDiscount = $discount;
                }
            }

            return $bestDiscount;
        });
    }

    /**
     * Calculate the reduction amount for a discount
     *
     * @param Discount $discount
     * @param float $price
     * @return float
     */
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

    /**
     * Get all active discounts for display purposes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveDiscounts()
    {
        $now = now();

        return Discount::where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->with(['targets.target'])
            ->get();
    }

    /**
     * Get the best discount for a product (wrapper for findBestDiscountForProduct)
     * This method exists for backward compatibility
     *
     * @param Product $product
     * @return Discount|null
     */
    public function getDiscount(Product $product)
    {
        $now = now();

        // Get product variants
        $variantIds = $product->productVariants()->pluck('id')->toArray();

        // Debug logging
        Log::info('DiscountService.GetDiscount Start');

        // Check if This product has any active discounts
        $query = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->whereHas('targets', function ($q) use ($product, $variantIds) {
                $q->where(function ($query) use ($product, $variantIds) {
                    $query->where('target_type', 'product')
                          ->where('target_id', $product->id);
                })
                ->orWhere(function ($query) use ($variantIds) {
                    if (!empty($variantIds)) {
                        $query->where('target_type', 'variant')
                              ->whereIn('target_id', $variantIds);
                    }
                })
                ->orWhere(function ($query) use ($product) {
                    if ($product->brand_id) {
                        $query->where('target_type', 'brand')
                              ->where('target_id', $product->brand_id);
                    }
                })
                ->orWhere(function ($query) use ($product) {
                    if ($product->category_id) {
                        $query->where('target_type', 'category')
                              ->where('target_id', $product->category_id);
                    }
                })
                ->orWhere(function ($query) use ($product) {
                    if ($product->sub_category_id) {
                        $query->where('target_type', 'subcategory')
                              ->where('target_id', $product->sub_category_id);
                    }
                });
            });

        $discount = $query->orderByDesc('value')
            ->first();


        Log::info('Discount fetched', [
            'discount' => $discount ? $discount->toArray() : null,
        ]);

        return $discount;
    }

    /**
     * Get all active discounts with proper date validation
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDiscounts()
    {
        $now = now();

        return Discount::where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->with(['targets.target'])
            ->get();
    }

    /**
     * Clear discount cache for a product
     *
     * @param int $productId
     * @return void
     */
    public function clearProductDiscountCache(int $productId): void
    {
        Cache::forget("best_discount_product_{$productId}");
    }

    /**
     * Clear all discount caches
     *
     * @return void
     */
    public function clearAllDiscountCaches(): void
    {
        // You might want to implement a more sophisticated cache clearing strategy
        // For now, this is a placeholder
        Cache::flush();
        Log::info('All discount caches cleared');
    }

    /**
     * Calculate cart total with discounts applied
     *
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return array
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
}
