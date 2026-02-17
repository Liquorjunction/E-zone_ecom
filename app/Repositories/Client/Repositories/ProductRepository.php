<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllActiveProducts(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('status', 'active')
            ->whereHas('brand', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->where(function (Builder $q) {
                $q->whereHas('subCategory', function (Builder $scq) {
                    $scq->where('status', 'active');
                })->orWhereNull('sub_category_id');
            })
            ->with(['category', 'brand', 'images', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }, 'productVariants' => function($q) {
                $q->where('status', 'active')->with(['reviews' => function($query) {
                    $query->where('is_approved', 'approved');
                }]);
            }]);

        if ($request->filled('category')) {
            $categories = is_array($request->category) ? $request->category : [$request->category];
            $query->whereHas('category', function (Builder $q) use ($categories) {
                $q->whereIn('slug', $categories)->where('status', 'active');
            });
        }
        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : [$request->brand];
            $query->whereHas('brand', function (Builder $q) use ($brands) {
                $q->whereIn('slug', $brands)->where('status', 'active');
            });
        }

        if ($request->filled('subcategory')) {
            $subcategories = is_array($request->subcategory) ? $request->subcategory : [$request->subcategory];
            $query->whereHas('subCategory', function (Builder $q) use ($subcategories) {
                $q->whereIn('slug', $subcategories)->where('status', 'active');
            });
        }

        if ($request->filled('bestseller') && $request->bestseller) {
            $query->where('is_bestseller', true);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        switch ($request->get('sort')) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'bestseller': $query->where('is_bestseller', true)->latest(); break;
            case 'newest': default: $query->latest(); break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getDealVariants(Request $request, int $perPage = 12): LengthAwarePaginator {
        $query = ProductVariant::query()
            ->where('status', 'active')
            ->with(['images', 'inventory', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }, 'product' => function($q) {
                $q->where('status', 'active')->with(['category', 'brand', 'images']);
            }])
            ->withAvg(['reviews as avg_rating' => function($q) {
                $q->where('is_approved', 'approved');
            }], 'rating')
            ->whereHas('product', function (Builder $q) {
                $q->where('status', 'active')
                  ->whereHas('brand', function (Builder $brandquery) {
                      $brandquery->where('status', 'active');
                  })
                  ->whereHas('category', function (Builder $categoryquery) {
                      $categoryquery->where('status', 'active');
                  })
                  ->where(function (Builder $subCategoryquery) {
                      $subCategoryquery->whereHas('subCategory', function (Builder $scq) {
                          $scq->where('status', 'active');
                      })->orWhereNull('sub_category_id');
                  });
            });

        if ($request->filled('search')) {
            $keyword = $request->get('search');
            $query->where(function (Builder $q) use ($keyword) {
                $like = '%' . $keyword . '%';
                $q->where('variant_name', 'LIKE', $like)
                  ->orWhere('sku', 'LIKE', $like)
                  ->orWhereHas('product', function (Builder $productQuery) use ($like) {
                      $productQuery->where('name', 'LIKE', $like)
                                   ->orWhere('description', 'LIKE', $like);
                  });
            });
        }

        if ($request->filled('category')) {
            $categories = is_array($request->category) ? $request->category : [$request->category];
            $query->whereHas('product.category', function (Builder $q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : [$request->brand];
            $query->whereHas('product.brand', function (Builder $q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        if ($request->filled('subcategory')) {
            $subcategories = is_array($request->subcategory) ? $request->subcategory : [$request->subcategory];
            $query->whereHas('product.subCategory', function (Builder $q) use ($subcategories) {
                $q->whereIn('slug', $subcategories)->where('status', 'active');
            });
        }

        if ($request->filled('bestseller') && $request->bestseller) {
            $query->where('is_bestseller', true);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('rating')) {
            $ratings = is_array($request->rating) ? $request->rating : [$request->rating];
            $minRating = min($ratings);
            $query->where('avg_rating', '>=', $minRating);
        }

        if ($request->filled('availability')) {
            $availability = is_array($request->availability) ? $request->availability : [$request->availability];
            if (in_array('in_stock', $availability) && !in_array('out_of_stock', $availability)) {
                $query->whereHas('inventory', function (Builder $q) {
                    $q->where('quantity', '>', 0);
                });
            }
            if (in_array('out_of_stock', $availability) && !in_array('in_stock', $availability)) {
                $query->whereDoesntHave('inventory', function (Builder $q) {
                    $q->where('quantity', '>', 0);
                });
            }
        }

        if ($request->filled('discount')) {
            $discounts = is_array($request->discount) ? array_map('intval', $request->discount) : [intval($request->discount)];
            $minDiscount = min($discounts);
            $query->whereHas('discountTargets', function (Builder $q) use ($minDiscount) {
                $q->whereHas('discount', function (Builder $discountQuery) use ($minDiscount) {
                    $discountQuery->where('status', true)
                                  ->where('value', '>=', $minDiscount)
                                  ->where('starts_at', '<=', now())
                                  ->where('ends_at', '>=', now());
                });
            })->orWhereHas('product.discountTargets', function (Builder $q) use ($minDiscount) {
                $q->whereHas('discount', function (Builder $discountQuery) use ($minDiscount) {
                    $discountQuery->where('status', true)
                                  ->where('value', '>=', $minDiscount)
                                  ->where('starts_at', '<=', now())
                                  ->where('ends_at', '>=', now());
                });
            });
        }

        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'bestseller':
                $query->where('is_bestseller', true)->latest();
                break;
            case 'featured':
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->each(function (ProductVariant $variant) {
            if (!$variant->relationLoaded('product') || !$variant->product) {
                $variant->load('product.category', 'product.brand', 'product.images');
            }
        });

        return $paginator;
    }

    public function getAllActiveVariants(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $query = ProductVariant::query()
            ->where('status', 'active')
            ->with(['images', 'inventory', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }, 'product' => function($q) {
                $q->where('status', 'active')->with(['category', 'brand', 'images']);
            }])
            ->withAvg(['reviews as avg_rating' => function($q) {
                $q->where('is_approved', 'approved');
            }], 'rating')
            ->whereHas('product', function (Builder $q) {
                $q->where('status', 'active')
                  ->whereHas('brand', function (Builder $brandquery) {
                      $brandquery->where('status', 'active');
                  })
                  ->whereHas('category', function (Builder $categoryquery) {
                      $categoryquery->where('status', 'active');
                  })
                  ->where(function (Builder $subCategoryquery) {
                      $subCategoryquery->whereHas('subCategory', function (Builder $scq) {
                          $scq->where('status', 'active');
                      })->orWhereNull('sub_category_id');
                  });
            });

        if ($request->filled('search')) {
            $keyword = $request->get('search');
            $query->where(function (Builder $q) use ($keyword) {
                $like = '%' . $keyword . '%';
                $q->where('variant_name', 'LIKE', $like)
                  ->orWhere('sku', 'LIKE', $like)
                  ->orWhereHas('product', function (Builder $productQuery) use ($like) {
                      $productQuery->where('name', 'LIKE', $like)
                                   ->orWhere('description', 'LIKE', $like);
                  });
            });
        }

        if ($request->filled('category')) {
            $categories = is_array($request->category) ? $request->category : [$request->category];
            $query->whereHas('product.category', function (Builder $q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : [$request->brand];
            $query->whereHas('product.brand', function (Builder $q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        if ($request->filled('subcategory')) {
            $subcategories = is_array($request->subcategory) ? $request->subcategory : [$request->subcategory];
            $query->whereHas('product.subCategory', function (Builder $q) use ($subcategories) {
                $q->whereIn('slug', $subcategories)->where('status', 'active');
            });
        }

        if ($request->filled('bestseller') && $request->bestseller) {
            $query->where('is_bestseller', true);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('rating')) {
            $ratings = is_array($request->rating) ? $request->rating : [$request->rating];
            $minRating = min($ratings);
            $query->where('avg_rating', '>=', $minRating);
        }

        if ($request->filled('availability')) {
            $availability = is_array($request->availability) ? $request->availability : [$request->availability];
            if (in_array('in_stock', $availability) && !in_array('out_of_stock', $availability)) {
                $query->whereHas('inventory', function (Builder $q) {
                    $q->where('quantity', '>', 0);
                });
            }
            if (in_array('out_of_stock', $availability) && !in_array('in_stock', $availability)) {
                $query->whereDoesntHave('inventory', function (Builder $q) {
                    $q->where('quantity', '>', 0);
                });
            }
        }

        if ($request->filled('discount')) {
            $discounts = is_array($request->discount) ? array_map('intval', $request->discount) : [intval($request->discount)];
            $minDiscount = min($discounts);
            $query->whereHas('discountTargets', function (Builder $q) use ($minDiscount) {
                $q->whereHas('discount', function (Builder $discountQuery) use ($minDiscount) {
                    $discountQuery->where('status', true)
                                  ->where('value', '>=', $minDiscount)
                                  ->where('starts_at', '<=', now())
                                  ->where('ends_at', '>=', now());
                });
            })->orWhereHas('product.discountTargets', function (Builder $q) use ($minDiscount) {
                $q->whereHas('discount', function (Builder $discountQuery) use ($minDiscount) {
                    $discountQuery->where('status', true)
                                  ->where('value', '>=', $minDiscount)
                                  ->where('starts_at', '<=', now())
                                  ->where('ends_at', '>=', now());
                });
            });
        }

        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'bestseller':
                $query->where('is_bestseller', true)->latest();
                break;
            case 'featured':
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->each(function (ProductVariant $variant) {
            if (!$variant->relationLoaded('product') || !$variant->product) {
                $variant->load('product.category', 'product.brand', 'product.images');
            }
        });

        return $paginator;
    }

    public function findActiveBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)
            ->where('status', 'active')
            ->whereHas('brand', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->where(function (Builder $q) {
                $q->whereHas('subCategory', function (Builder $scq) {
                    $scq->where('status', 'active');
                })->orWhereNull('sub_category_id');
            })
            ->with([
                'category',
                'brand',
                'images',
                'productVariants' => function($q) {
                    $q->where('status', 'active')
                      ->with(['images', 'inventory', 'reviews' => function($query) {
                          $query->where('is_approved', 'approved');
                      }]);
                }
            ])
            ->firstOrFail();
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return Product::where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereHas('brand', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->where(function (Builder $q) {
                $q->whereHas('subCategory', function (Builder $scq) {
                    $scq->where('status', 'active');
                })->orWhereNull('sub_category_id');
            })
            ->with(['images', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }])
            ->take($limit)
            ->get();
    }

    public function searchProducts(string $keyword, int $limit = 10): Collection
    {
        return Product::where('status', 'active')
            ->whereHas('brand', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->where(function (Builder $q) {
                $q->whereHas('subCategory', function (Builder $scq) {
                    $scq->where('status', 'active');
                })->orWhereNull('sub_category_id');
            })
            ->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->with(['images', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }])
            ->take($limit)
            ->get();
    }

    public function getDataforproductlisting(int $productId): Collection
    {
        $product = Product::where('id', $productId)
            ->where('status', 'active')
            ->whereHas('brand', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->where(function (Builder $q) {
                $q->whereHas('subCategory', function (Builder $scq) {
                    $scq->where('status', 'active');
                })->orWhereNull('sub_category_id');
            })
            ->with(['category', 'brand', 'images'])
            ->firstOrFail();

        $productVariants = ProductVariant::where('product_id', $productId)
            ->where('status', 'active')
            ->with(['images', 'inventory', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }])
            ->get();

        $productVariants->each(function (ProductVariant $variant) use ($product) {
            $variant->product = $product;
        });

        return $productVariants;
    }

    public function getBestsellerProducts(int $limit = 12): Collection
    {
        return Product::where('status', 'active')
            ->where('is_bestseller', true)
            ->whereHas('brand', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function (Builder $q) {
                $q->where('status', 'active');
            })
            ->where(function (Builder $q) {
                $q->whereHas('subCategory', function (Builder $scq) {
                    $scq->where('status', 'active');
                })->orWhereNull('sub_category_id');
            })
            ->with(['category', 'brand', 'images', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }, 'productVariants' => function($q) {
                $q->where('status', 'active')->with(['reviews' => function($query) {
                    $query->where('is_approved', 'approved');
                }]);
            }])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getAvailabilityCounts(): array
    {
        // Count in-stock variants
        $inStockCount = ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', function(Builder $q) {
                $q->where('status', 'active')
                  ->whereHas('brand', fn($bq) => $bq->where('status', 'active'))
                  ->whereHas('category', fn($cq) => $cq->where('status', 'active'))
                  ->where(fn($sq) => $sq->whereHas('subCategory', fn($scq) => $scq->where('status', 'active'))->orWhereNull('sub_category_id'));
            })
            ->whereHas('inventory', fn($q) => $q->where('quantity', '>', 0))
            ->count();

        // Count out-of-stock variants
        $outOfStockCount = ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', function(Builder $q) {
                $q->where('status', 'active')
                  ->whereHas('brand', fn($bq) => $bq->where('status', 'active'))
                  ->whereHas('category', fn($cq) => $cq->where('status', 'active'))
                  ->where(fn($sq) => $sq->whereHas('subCategory', fn($scq) => $scq->where('status', 'active'))->orWhereNull('sub_category_id'));
            })
            ->whereDoesntHave('inventory', fn($q) => $q->where('quantity', '>', 0))
            ->count();

        return [
            'in_stock' => $inStockCount,
            'out_of_stock' => $outOfStockCount,
        ];
    }

    public function getBestsellerVariants(int $limit = 12): Collection
    {
        return ProductVariant::where('status', 'active')
            ->where('is_bestseller', true)
            ->with(['images', 'inventory', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }, 'product' => function($q) {
                $q->where('status', 'active')->with(['category', 'brand', 'images']);
            }])
            ->whereHas('product', function (Builder $q) {
                $q->where('status', 'active')
                  ->whereHas('brand', function (Builder $brandquery) {
                      $brandquery->where('status', 'active');
                  })
                  ->whereHas('category', function (Builder $categoryquery) {
                      $categoryquery->where('status', 'active');
                  })
                  ->where(function (Builder $subCategoryquery) {
                      $subCategoryquery->whereHas('subCategory', function (Builder $scq) {
                          $scq->where('status', 'active');
                      })->orWhereNull('sub_category_id');
                  });
            })
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getMinPrice(): float
    {
        $minPrice = ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', function (Builder $q) {
                $q->where('status', 'active')
                  ->whereHas('brand', function (Builder $brandQuery) {
                      $brandQuery->where('status', 'active');
                  })
                  ->whereHas('category', function (Builder $categoryQuery) {
                      $categoryQuery->where('status', 'active');
                  });
            })
            ->min('price');

        return $minPrice ? round($minPrice, 2) : 0.00;
    }

    public function getMaxPrice(): float
    {
        $maxPrice = ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', function (Builder $q) {
                $q->where('status', 'active')
                  ->whereHas('brand', function (Builder $brandQuery) {
                      $brandQuery->where('status', 'active');
                  })
                  ->whereHas('category', function (Builder $categoryQuery) {
                      $categoryQuery->where('status', 'active');
                  });
            })
            ->max('price');

        return $maxPrice ? round($maxPrice, 2) : 5000.00;
    }
}
