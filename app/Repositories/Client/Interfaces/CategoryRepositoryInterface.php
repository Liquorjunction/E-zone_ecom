<?php

namespace App\Repositories\Client\Interfaces;

interface CategoryRepositoryInterface
{
    public function getCategoriesData();
    public function getCategoryBySlug(string $slug);
    public function getProductsByCategorySlug(string $slug, int $perPage = 12);
    public function checkDiscountonCategory(int $categoryId): array;
    public function getDiscountedProductsByCategorySlug(string $slug, int $perPage = 12);
}
