<?php

namespace App\Repositories\Client\Interfaces;

interface DiscountRepositoryInterface
{
    public function getActiveDiscounts(array $filters = []);
    public function getDiscountDetails(int $id);
    public function getDiscountTargets(int $discountId);
    public function getApplicableDiscountsForProduct(int $variantId, array $context = []);
    public function getApplicableDiscountsForCategory(int $categoryId, array $context = []);
    public function getApplicableDiscountsForSubcategory(int $subcategoryId, array $context = []);
    public function getApplicableDiscountsForBrand(int $brandId, array $context = []);
    public function calculateDiscount(int $discountId, float $originalPrice, array $context = []);
}
