<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\SubCategoryRepositoryInterface;
use App\Models\SubCategories;
use App\Models\Discount;
use App\Models\DiscountTarget;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function getSubCategoriesData()
    {
        return SubCategories::where('status', 'active')->get();
    }

    public function getSubCategoryBySlug($slug)
    {
        return SubCategories::where('slug', $slug)->first();
    }

    public function getProductsBySubCategorySlug(string $slug, int $perPage = 12)
    {
        $subCategory = SubCategories::where('slug', $slug)->where('status', 'active')->first();
        if (!$subCategory) {
            return null;
        }
        return $subCategory->products()->where('status', 'active')->paginate($perPage);
    }

    public function getVariantsBySubCategorySlug(string $slug, int $perPage = 12)
    {
        $subCategory = SubCategories::where('slug', $slug)->where('status', 'active')->first();
        if (!$subCategory) {
            return null;
        }
        return $subCategory->variants()
            ->where('product_variants.status', 'active')
            ->whereHas('product', function($query) {
                $query->where('status', 'active');
            })
            ->paginate($perPage);
    }

    public function checkDiscountonSubCategory(int $subCategoryId): array
    {
        $discounts = Discount::where('status', 'active')
            ->whereHas('targets', function ($query) use ($subCategoryId) {
                $query->where('target_type', 'subcategory')->where('target_id', $subCategoryId);
            })
            ->get();
        return $discounts->toArray();
    }

    public function getDiscountedProductsBySubCategorySlug(string $slug, int $perPage = 12)
    {
        $subCategory = SubCategories::where('slug', $slug)->where('status', 'active')->first();
        if (!$subCategory) {
            return null;
        }
        $productIds = DiscountTarget::where('target_type', 'product')
            ->pluck('target_id')->toArray();
        return $subCategory->products()
            ->whereIn('id', $productIds)
            ->where('status', 'active')
            ->paginate($perPage);
    }
}
