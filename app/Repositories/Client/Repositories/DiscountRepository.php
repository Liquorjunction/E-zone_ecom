<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\DiscountRepositoryInterface;
use App\Models\Discount;
use App\Models\DiscountTarget;
use App\Models\Product;
use Illuminate\Support\Carbon;
use App\Models\Categories;
use App\Models\Brand;

class DiscountRepository implements DiscountRepositoryInterface
{
    public function getActiveDiscounts(array $filters = [])
    {
        $query = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->get();
    }

    public function getDiscountDetails(int $id)
    {
        return Discount::with(['targets', 'coupons', 'usages'])->find($id);
    }

    public function getDiscountTargets(int $discountId)
    {
        return DiscountTarget::where('discount_id', $discountId)->get();
    }

    public function getApplicableDiscountsForProduct(int $productId, array $context = [])
    {
        // Logic to determine applicable discounts for a product
        $discounts = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->whereHas('targets', function ($query) use ($productId) {
                $query->where(function ($q) use ($productId) {
                    $q->where('target_id', $productId)
                      ->where(function ($t) {
                          $t->where('target_type', Product::class)
                            ->orWhere('target_type', 'product');
                      });
                });
            })->get();

        return $discounts;
    }

    public function getApplicableDiscountsForCategory(int $categoryId, array $context = [])
    {
        $discounts = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->whereHas('targets', function ($query) use ($categoryId) {
                $query->where(function ($q) use ($categoryId) {
                    $q->where('target_id', $categoryId)
                      ->where(function ($t) {
                          $t->where('target_type', Categories::class)
                            ->orWhere('target_type', 'category');
                      });
                });
            })->get();

        return $discounts;
    }

    public function getApplicableDiscountsForSubcategory(int $subcategoryId, array $context = [])
    {
        $discounts = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->whereHas('targets', function ($query) use ($subcategoryId) {
                $query->where(function ($q) use ($subcategoryId) {
                    $q->where('target_id', $subcategoryId)
                      ->where(function ($t) {
                          $t->where('target_type', 'subcategory')
                            ->orWhere('target_type', Categories::class);
                      });
                });
            })->get();

        return $discounts;
    }

    public function getApplicableDiscountsForBrand(int $brandId, array $context = [])
    {
        $discounts = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->whereHas('targets', function ($query) use ($brandId) {
                $query->where(function ($q) use ($brandId) {
                    $q->where('target_id', $brandId)
                      ->where(function ($t) {
                          $t->where('target_type', Brand::class)
                            ->orWhere('target_type', 'brand');
                      });
                });
            })->get();

        return $discounts;
    }

    public function calculateDiscount(int $discountId, float $originalPrice, array $context = [])
    {
        $discount = Discount::find($discountId);

        if (!$discount || !$discount->isValid()) {
            return [
                'original_price' => $originalPrice,
                'discount_amount' => 0,
                'final_price' => $originalPrice,
            ];
        }

        $discountAmount = 0;

        if ($discount->type === 'flat') {
            $discountAmount = (float) $discount->value;
        } elseif ($discount->type === 'percentage') {
            $discountAmount = ($originalPrice * ((float) $discount->value / 100));
            if (!is_null($discount->max_discount) && $discount->max_discount > 0) {
                $discountAmount = min($discountAmount, (float) $discount->max_discount);
            }
        }

        $discountAmount = min($discountAmount, $originalPrice);
        $finalPrice = round($originalPrice - $discountAmount, 2);

        return [
            'original_price' => $originalPrice,
            'discount_amount' => round($discountAmount, 2),
            'final_price' => $finalPrice,
            'discount_id' => $discountId,
        ];
    }
}
