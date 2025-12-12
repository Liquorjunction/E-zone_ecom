<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Product\ReadProductRequest;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(ReadProductRequest $request, ProductRepo $productRepo)
    {
        $categories = $productRepo->getCategoriesData();
        $subCategories = $productRepo->getSubCategoriesData();
        $brands = $productRepo->getBrandsData();
        $products = $productRepo->getActiveProductsData();
        $productImages = $productRepo->getProductImagesData();
        $productVariants = $productRepo->getProductVariantsData();
        return view('client.pages.products', compact('categories', 'subCategories', 'brands', 'products', 'productImages', 'productVariants'));
    }

    public function productDetail($slug, ProductRepo $productRepo)
    {
        $product = Product::with(['category', 'subCategory', 'brand', 'images', 'productVariants'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Get related products (same category, excluding current product)
        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view('client.pages.product-detail', compact('product', 'relatedProducts'));
    }
}
