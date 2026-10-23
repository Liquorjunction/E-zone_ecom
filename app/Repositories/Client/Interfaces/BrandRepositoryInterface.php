<?php

namespace App\Repositories\Client\Interfaces;

interface BrandRepositoryInterface
{
    public function getActiveBrandsData();
    public function getBrandBySlug(string $slug);
    public function getProductsByBrandSlug(string $slug, int $perPage = 12, $categoryId = null);
    public function getVariantsByBrandSlug(string $slug, int $perPage = 12, $categoryId = null);
    public function checkDiscountonbrand(int $brandId): array;
    public function getDiscountedProductsByBrandSlug(string $slug, int $perPage = 12);
}
