<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Brand;

class ProductRepository implements ProductRepositoryInterface
{
    public function getActiveProductsData()
    {
        return Product::with('category')->where('status', 'active')->paginate(12);
    }

    public function getCategoriesData()
    {
        return Categories::with('products')->get();
    }

    public function getSubCategoriesData()
    {
        return SubCategories::with('products')->get();
    }

    public function getProductImagesData()
    {
        return ProductImage::all();
    }

    public function getProductVariantsData()
    {
        return ProductVariant::all();
    }

    public function getBrandsData()
    {
        // Fetch brands with their products relationship loaded
        return Brand::with('products')->get();
    }
}
