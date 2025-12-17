<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Product\ReadProductRequest;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface as ProductRepo;

class ProductController extends Controller
{

    public function index(ReadProductRequest $request, ProductRepo $productRepo)
    {
        // 1. Get Products (Filtered & Paginated)
        $products = $productRepo->getFilteredProducts($request, 12);

        // 2. Get Sidebar Data (Optimized to only show relevant filters)
        $categories = $productRepo->getActiveCategories();
        $brands = $productRepo->getActiveBrands();

        // Note: $subCategories isn't strictly needed if loaded inside categories,
        // but if your view expects a flat list, you can fetch it similarly.
        $subCategories = collect();

        return view('client.pages.products', compact('products', 'categories', 'subCategories', 'brands'));
    }

    /**
     * Display Single Product Detail.
     */
    public function productDetail($slug, ProductRepo $productRepo)
    {
        $product = $productRepo->getProductBySlug($slug);
        $relatedProducts = $productRepo->getRelatedProducts($product);

        return view('client.pages.product-detail', compact('product', 'relatedProducts'));
    }
}
