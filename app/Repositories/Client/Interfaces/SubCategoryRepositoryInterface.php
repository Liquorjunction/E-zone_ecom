<?php

namespace App\Repositories\Client\Interfaces;

interface SubCategoryRepositoryInterface
{
    public function getSubCategoriesData();
    public function getSubCategoryBySlug(string $slug);
    public function getProductsBySubCategorySlug(string $slug, int $perPage = 12);
    public function getVariantsBySubCategorySlug(string $slug, int $perPage = 12);
    public function checkDiscountonSubCategory(int $subCategoryId): array;
    public function getDiscountedProductsBySubCategorySlug(string $slug, int $perPage = 12);
}
