<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\DiscountRepositoryInterface;
use App\Models\Discount;
use App\Models\DiscountTarget;
use App\Models\Product;
use Illuminate\Support\Carbon;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\ProductVariant;

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

    public function getApplicableDiscountsForProduct(int $variantId, array $context = [])
    {
        // 1. Resolve the Variant and its Parent Product (Container)
        $variant = ProductVariant::with('product')->find($variantId);

        if (!$variant || !$variant->product) {
            return collect([]);
        }

        $product = $variant->product;

        // 2. Query discounts that match either the Variant ID specifically OR the Parent Product's attributes
        $discounts = Discount::where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->whereHas('targets', function ($q) use ($variant, $product) {
                $q->where(function ($query) use ($variant) {
                    // Check for direct Variant discount
                    // 'product' target type stores the Variant ID
                    $query->where('target_type', 'product')
                        ->where('target_id', $variant->id);
                })
                    ->orWhere(function ($query) use ($product) {
                        // Check for Category/Subcategory/Brand discounts using the Parent Product
                        $query->where(function ($t) use ($product) {
                            $t->where('target_type', 'category')
                                ->where('target_id', $product->category_id);
                        })
                            ->orWhere(function ($t) use ($product) {
                                $t->where('target_type', 'subcategory')
                                    ->where('target_id', $product->sub_category_id);
                            })
                            ->orWhere(function ($t) use ($product) {
                                $t->where('target_type', 'brand')
                                    ->where('target_id', $product->brand_id);
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
