<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class DiscountService
{
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
                $variant = $product->productVariants->first();
            }
        }

        if (!$product || !$variant) {
            return false;
        }

        foreach ($discount->targets as $target) {
            if ($target->target_type === 'product' && $target->target_id == $variant->id) {
                return true;
            }

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

    public function calculateProductDiscount($product, $variantId = null): array
    {
        if ($product instanceof ProductVariant) {
            $variant = $product;
            $variantId = $variant->id;
            if (!$variant->relationLoaded('product')) {
                $variant->load('product');
            }
            $parentProduct = $variant->product;
            $price = $variant->price;
        } else {
            $parentProduct = $product;
            $variant = null;

            if ($variantId) {
                $variant = $parentProduct->productVariants->find($variantId);
            }

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
            $price = $variant->price;
        }

        $discount = $this->findBestDiscountForProduct($parentProduct, $variantId, $price);

        if ($discount) {
            $reduction = $this->calculateReduction($discount, $price);
            $finalPrice = max($price - $reduction, 0);

            return [
                'has_discount' => true,
                'original_price' => $price,
                'final_price' => round($finalPrice, 2),
                'discount_amount' => round($reduction, 2),
                'discount_percentage' => $discount->type === 'percentage' ? $discount->value : 0,
                'discount_name' => $discount->name ?? 'Discount',
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
            $variant = $item->variant ?? ProductVariant::find($item->variant_id);

            $firstVariant = null;
            if (!$variant) {
                $firstVariant = $product->productVariants()->first();
                $variant = $firstVariant;
            }
            $itemPrice = $variant?->price ?? ($item->unit_price ?? $product->price ?? 0);
            $quantity = $item->quantity;

            if ($variant) {
                $discountData = $this->calculateVariantDiscount($variant);
            } else {
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
        $price = $variant->price;
        $product = $variant->product;

        $discount = $this->findBestDiscountForProduct($product, $variant->id, $price);

        if ($discount) {
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

    public function clearProductDiscountCache(int $productId): void
    {
        $product = Product::with('productVariants')->find($productId);

        if (!$product) {
            Log::warning("Product with ID {$productId} not found for cache clearing.");
            return;
        }

        foreach ($product->productVariants as $variant) {
            $listKey = "best_discount_product_{$variant->id}_keys";
            $keys = Cache::get($listKey, []);

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            Cache::forget($listKey);
        }

        Log::info("Cleared discount cache for product ID {$productId}");
    }
}
