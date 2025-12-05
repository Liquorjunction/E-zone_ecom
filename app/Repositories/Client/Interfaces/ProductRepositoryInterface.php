<?php

namespace App\Repositories\Client\Interfaces;

interface ProductRepositoryInterface
{
    public function getActiveProductsData();
    public function getBrandsData();
    public function getCategoriesData();
    public function getSubCategoriesData();
    public function getProductImagesData();
    public function getProductVariantsData();
}
