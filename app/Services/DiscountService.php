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
        $bestDiscount = $this->findBestDiscountForProduct($product);

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
     * @return Discount|null
     */
    public function findBestDiscountForProduct(Product $product): ?Discount
    {
        // Cache key for this product's discount
        $cacheKey = "best_discount_product_{$product->id}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($product) {
            $now = now();

            // Get product variants
            $variantIds = $product->productVariants()->pluck('id')->toArray();

            // Fetch all active discounts that target this product or its parents
            $applicableDiscounts = Discount::where('status', true)
                ->where(function($q) use ($now) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
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
                ->get();

            if ($applicableDiscounts->isEmpty()) {
                return null;
            }

            // "Best Discount Wins" - Compare all and return the one with maximum savings
            $bestDiscount = null;
            $maxReductionAmount = 0;

            foreach ($applicableDiscounts as $discount) {
                $reduction = $this->calculateReduction($discount, $product->price);

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
        if ($discount->type === 'flat') {
            return min($discount->value, $price); // Can't discount more than price
        }

        // Percentage discount
        $calculatedReduction = ($price * $discount->value) / 100;

        // Apply max_discount cap if set
        if ($discount->max_discount > 0) {
            return min($calculatedReduction, $discount->max_discount);
        }

        return $calculatedReduction;
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
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->with(['targets.target'])
            ->get();
    }

    public function getDiscount(Product $product)
    {
        $now = now();

        // Get product variants
        $variantIds = $product->productVariants()->pluck('id')->toArray();

        // Debug: Check what target_types exist in database
        $allTargets = DB::table('discount_targets')->get();
        Log::info('All discount targets in DB', [
            'targets' => $allTargets->toArray()
        ]);

        // Debug logging
        Log::info('getDiscount called', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant_ids' => $variantIds,
            'brand_id' => $product->brand_id,
            'category_id' => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
            'current_time' => $now,
        ]);

        // Check if This product has any active discounts
        $query = Discount::where('status', true)
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

        Log::info('Discount query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        $discount = $query->orderByDesc('value')
            ->first();


        Log::info('getDiscount result', [
            'product_id' => $product->id,
            'variant_ids' => $variantIds,
            'discount_found' => $discount ? true : false,
            'discount_id' => $discount?->id,
            'discount_name' => $discount?->name,
        ]);

        return $discount;
    }

    public function getAllDiscounts()
    {
        $now = now();

        $discounted_products = Discount::where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->with(['targets.target'])
            ->get();
            dd($discounted_products);

        return $discounted_products;
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
        $discount = $this->findBestDiscountForProduct($product);

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
