<?php

namespace App\Repositories\Client\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getAllActiveProducts(Request $request, int $perPage = 12): LengthAwarePaginator;
    public function getDealVariants(Request $request, int $perPage = 12): LengthAwarePaginator;
    public function getAllActiveVariants(Request $request, int $perPage = 12): LengthAwarePaginator;
    public function findActiveBySlug(string $slug): ?Product;
    public function getRelatedProducts(Product $product, int $limit = 4): Collection;
    public function searchProducts(string $keyword, int $limit = 10): Collection;
    public function getDataforproductlisting(int $productId): Collection;
    public function getBestsellerProducts(int $limit = 12): Collection;
    public function getBestsellerVariants(int $limit = 12): Collection;
    public function getAvailabilityCounts(): array;
    public function getMaxPrice(): float;
}
