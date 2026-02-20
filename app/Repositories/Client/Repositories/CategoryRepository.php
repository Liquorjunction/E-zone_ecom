<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\CategoryRepositoryInterface;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Discount;
use App\Models\DiscountTarget;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getCategoriesData()
    {
        return Categories::where('status', 'active')->get();
    }

    public function getCategoryBySlug(string $slug)
    {
        return Categories::where('slug', $slug)->where('status', 'active')->first();
    }

    public function getProductsByCategorySlug(string $slug, int $perPage = 12)
    {
        $category = Categories::where('slug', $slug)->where('status', 'active')->first();
        if (!$category) {
            return null;
        }
        return $category->products()->where('status', 'active')->paginate($perPage);
    }

    public function checkDiscountonCategory(int $categoryId): array
    {
        $discounts = Discount::where('status', 'active')
            ->whereHas('targets', function($query) use ($categoryId) {
                $query->where('target_type', 'category')->where('target_id', $categoryId);
            })
            ->get();
        return $discounts->toArray();
    }

    public function getDiscountedProductsByCategorySlug(string $slug, int $perPage = 12)
    {
        $category = Categories::where('slug', $slug)->where('status', 'active')->first();
        if (!$category) {
            return null;
        }
        $productIds = DiscountTarget::where('target_type', 'product')
            ->pluck('target_id')->toArray();
        return $category->products()
            ->whereIn('id', $productIds)
            ->where('status', 'active')
            ->paginate($perPage);
    }
}
