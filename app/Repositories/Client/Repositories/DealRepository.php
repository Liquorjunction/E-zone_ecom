<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Deal;
use App\Models\ProductVariant;
use App\Repositories\Client\Interfaces\DealRepositoryInterface;

class DealRepository implements DealRepositoryInterface
{
    public function getDealVariants($request)
    {
        $now = now();

        $variants = ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', function ($q) {
                $q->where('status', 'active');
            })
            ->where(function ($query) use ($now) {
                $query
                    // Variant-level discount
                    ->orWhereHas('discountTargets.discount', function ($q) use ($now) {
                        $q->where('status', true)
                            ->where(function ($q) use ($now) {
                                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                            })
                            ->where(function ($q) use ($now) {
                                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                            });
                    })
                    // Product-level discount
                    ->orWhereHas('product.discountTargets.discount', function ($q) use ($now) {
                        $q->where('status', true)
                            ->where(function ($q) use ($now) {
                                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                            })
                            ->where(function ($q) use ($now) {
                                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                            });
                    })
                    // Category-level discount
                    ->orWhereHas('product.category.discountTargets.discount', function ($q) use ($now) {
                        $q->where('status', true)
                            ->where(function ($q) use ($now) {
                                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                            })
                            ->where(function ($q) use ($now) {
                                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                            });
                    })
                    // Brand-level discount
                    ->orWhereHas('product.brand.discountTargets.discount', function ($q) use ($now) {
                        $q->where('status', true)
                            ->where(function ($q) use ($now) {
                                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                            })
                            ->where(function ($q) use ($now) {
                                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                            });
                    })
                    // Subcategory-level discount
                    ->orWhereHas('product.subCategory.discountTargets.discount', function ($q) use ($now) {
                        $q->where('status', true)
                            ->where(function ($q) use ($now) {
                                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                            })
                            ->where(function ($q) use ($now) {
                                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                            });
                    });
            })
            ->with([
                'product' => function ($q) {
                    $q->where('status', 'active')
                      ->with([
                          'category',
                          'brand',
                          'images',
                          'productVariants',
                          'reviews',
                          'cartItems',
                          'subCategory',
                      ]);
                },
                'images' => function ($q) { $q->orderBy('sort_order'); },
                'inventory',
                'reviews' => function ($q) {
                    $q->where('is_approved', 'approved');
                }
            ])
            ->paginate($request->per_page ?? 12);

        return $variants;
    }
}
