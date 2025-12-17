<?php

namespace App\Repositories\Client\Interfaces;

use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function getFilteredProducts(Request $request, int $perPage = 12);
    public function getProductBySlug(string $slug);
    public function getActiveBrands();
    public function getActiveCategories();
    public function getRelatedProducts($product, int $limit = 4);
}
