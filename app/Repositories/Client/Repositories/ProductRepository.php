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
                $q->whereIn('slug', $categories);
            });
        }
        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : [$request->brand];
            $query->whereHas('brand', function (Builder $q) use ($brands) {
                $q->whereIn('slug', $brands);
            });
        }

        if ($request->filled('subcategory')) {
            $subcategories = is_array($request->subcategory) ? $request->subcategory : [$request->subcategory];
            $query->whereIn('sub_category_id', $subcategories);
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
            ->whereHas('product', function (Builder $q) {
                $q->where('status', 'active');
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
            $query->whereHas('product', function (Builder $q) use ($subcategories) {
                $q->whereIn('sub_category_id', $subcategories);
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
            ->whereHas('product', function (Builder $q) {
                $q->where('status', 'active');
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
            $query->whereHas('product', function (Builder $q) use ($subcategories) {
                $q->whereIn('sub_category_id', $subcategories);
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
            ->with(['images', 'reviews' => function($q) {
                $q->where('is_approved', 'approved');
            }])
            ->take($limit)
            ->get();
    }

    public function searchProducts(string $keyword, int $limit = 10): Collection
    {
        return Product::where('status', 'active')
            ->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
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
                $q->where('status', 'active');
            })
            ->latest()
            ->take($limit)
            ->get();
    }
}
