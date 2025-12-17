<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductRepository implements ProductRepositoryInterface
{
    public function getFilteredProducts(Request $request, int $perPage = 12)
    {
        $query = Product::query()
            ->with(['category', 'brand', 'images', 'productVariants.inventory']) // Load relations efficiently
            ->where('status', 'active');

        // 1. Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // 2. Category Filter (Slug)
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 3. SubCategory Filter (Slug)
        if ($request->filled('subcategory')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('slug', $request->subcategory);
            });
        }

        // 4. Brand Filter (Slug)
        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        // 5. Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc': $query->orderBy('price', 'asc'); break;
                case 'price_desc': $query->orderBy('price', 'desc'); break;
                case 'name_asc': $query->orderBy('name', 'asc'); break;
                case 'newest': default: $query->latest(); break;
            }
        } else {
            $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getProductBySlug(string $slug)
    {
        return Product::with([
                'category',
                'subCategory',
                'brand',
                'images',
                // Important: Load active variants AND their specific inventory
                'productVariants' => function($q) {
                    $q->where('status', 'active')->with('inventory');
                }
            ])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
    }

    public function getRelatedProducts($product, int $limit = 4)
    {
        return Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function getActiveBrands()
    {
        // Only return brands that actually have active products
        return Brand::whereHas('products', fn($q) => $q->where('status', 'active'))->get();
    }

    public function getActiveCategories()
    {
        // Fetch categories with subcategories that have active products
        return Categories::with(['subCategories' => function($q) {
            $q->whereHas('products', fn($p) => $p->where('status', 'active'));
        }])
        ->whereHas('products', fn($p) => $p->where('status', 'active'))
        ->get();
    }
}
