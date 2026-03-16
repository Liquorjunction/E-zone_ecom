<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Brand;
use App\Models\Categories;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SubCategories;
use App\Repositories\Client\Interfaces\SearchRepositoryInterface;

class SearchRepository implements SearchRepositoryInterface
{
    public function search($query)
    {
        $lowerQuery = '%' . strtolower($query) . '%';

        $variants = ProductVariant::query()
            ->with(['product', 'images'])
            ->where('status', 'active')
            ->whereHas('product', function ($q) {
                $q->where('status', 'active');
            })
            ->where(function ($q) use ($query, $lowerQuery) {
                $q->whereRaw('LOWER(variant_name) LIKE ?', [$lowerQuery])
                  ->orWhere('sku', 'LIKE', "%{$query}%")
                  ->orWhereHas('product', function ($pq) use ($query, $lowerQuery) {
                      $pq->whereRaw('LOWER(name) LIKE ?', [$lowerQuery])
                         ->orWhereRaw('LOWER(description) LIKE ?', [$lowerQuery])
                         ->orWhere('sku', 'LIKE', "%{$query}%");
                  });
            })
            ->orderBy('is_bestseller', 'desc')
            ->get();

        $categories = Categories::query()
            ->where('status', 'active')
            ->whereRaw('LOWER(`category-name`) LIKE ?', [$lowerQuery])
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get()
            ->filter(function ($category) {
                return $category->products_count > 0;
            });

        $subCategories = SubCategories::query()
            ->where('status', 'active')
            ->whereRaw('LOWER(`sub-category-name`) LIKE ?', [$lowerQuery])
            ->with('category')
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get()
            ->filter(function ($subCategory) {
                return $subCategory->products_count > 0;
            });

        $brands = Brand::query()
            ->where('status', 'active')
            ->whereRaw('LOWER(name) LIKE ?', [$lowerQuery])
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get()
            ->filter(function ($brand) {
                return $brand->products_count > 0;
            });

        return [
            'products'       => $variants,
            'categories'     => $categories->values(),
            'sub_categories' => $subCategories->values(),
            'brands'         => $brands->values(),
        ];
    }

    public function searchUserOrders($query, $userId)
    {
        $lowerQuery = '%' . strtolower($query) . '%';

        return Order::query()
            ->where('user_id', $userId)
            ->where(function ($q) use ($query, $lowerQuery) {
                $q->where('order_number', 'LIKE', "%{$query}%")
                    ->orWhereHas('items.product', function ($productQuery) use ($lowerQuery) {
                        $productQuery->whereRaw('LOWER(name) LIKE ?', [$lowerQuery]);
                    });
            })
            ->with(['items.product', 'items.variant'])
            ->limit(5)
            ->get();
    }
}
