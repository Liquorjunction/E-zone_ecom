<?php

namespace App\Services;

use App\Repositories\Client\Interfaces\SearchRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SearchService
{
    protected $searchRepo;

    public function __construct(SearchRepositoryInterface $searchRepo)
    {
        $this->searchRepo = $searchRepo;
    }

    public function performUniversalSearch(?string $query): array
    {
        if (!$query || mb_strlen($query) < 2) {
            return [
                'has_results' => false,
                'total_found' => 0,
                'data'        => []
            ];
        }

        $results = $this->searchRepo->search($query);

        $results['products']->map(function ($variant) {
            $product = $variant->product;

            $variant->url = route('products.detail', $product->slug) . '?variant=' . $variant->slug;

            $imagePath = $variant->images->first()->image_path ?? $product->cover_image;
            $variant->image_url = $imagePath
                ? asset('storage/' . ltrim($imagePath, '/'))
                : 'https://via.placeholder.com/100x100';

            $variantName = $variant->variant_name;
                $productName = $product->name;

            $variant->display_name = $variantName
                ? $productName . ' - ' . $variantName
                : $productName;

            return $variant;
        });

        $results['categories']->map(function ($category) {
            $category->url = route('category.products', $category->slug);
            $category->display_name = $category->{'category-name'};
            return $category;
        });

        $results['sub_categories']->map(function ($subCategory) {
            $subCategory->url = route('subcategory.products', $subCategory->slug);
            $subCategory->display_name = $subCategory->{'sub-category-name'};
            return $subCategory;
        });

        $results['brands']->map(function ($brand) {
            $brand->url = route('brand.products', $brand->slug);
            $brand->display_name = $brand->name;
            return $brand;
        });

        $totalFound = $results['products']->count() +
                      $results['categories']->count() +
                      $results['sub_categories']->count() +
                      $results['brands']->count();

        return [
            'has_results' => $totalFound > 0,
            'total_found' => $totalFound,
            'data'        => $results
        ];
    }
}
