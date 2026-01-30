<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\BrandRepositoryInterface;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Discount;
use App\Models\DiscountTarget;

class BrandRepository implements BrandRepositoryInterface
{
    public function getActiveBrandsData()
    {
        return Brand::where('status', 'active')->get();
    }

    public function getBrandBySlug(string $slug)
    {
        return Brand::where('slug', $slug)->where('status', 'active')->first();
    }

    public function getProductsByBrandSlug(string $slug, int $perPage = 12)
    {
        $brand = $this->getBrandBySlug($slug);
        if ($brand) {
            return $brand->products()
                ->with(['images', 'category', 'productVariants.images', 'productVariants'])
                ->where('status', 'active')
                ->paginate($perPage);
        }
        return null;
    }

    public function checkDiscountonbrand(int $brandId): array
    {
        $discounts = Discount::where('status', 'active')
            ->whereHas('targets', function ($query) use ($brandId) {
                $query->where('target_type', DiscountTarget::targetType('brand'))
                      ->where('target_id', $brandId);
            })
            ->get();

        $totalDiscountPercentage = 0;
        foreach ($discounts as $discount) {
            $totalDiscountPercentage += $discount->percentage;
        }

        return [
            'has_discount' => $totalDiscountPercentage > 0,
            'total_discount_percentage' => $totalDiscountPercentage,
        ];
    }

    public function getDiscountedProductsByBrandSlug(string $slug, int $perPage = 12)
    {
        $brand = $this->getBrandBySlug($slug);
        if ($brand) {
            $productsWithDiscounts = $brand->products()
                ->where('status', 'active')
                ->whereHas('discounts', function ($query) {
                    $query->where('status', 'active');
                })
                ->paginate($perPage);

            return $productsWithDiscounts;
        }
        return null;
    }
}
