<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface as ProductRepo;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request, ProductRepo $productRepository)
    {
        // Get sellable variants (flattened view for listing)
        $products = $productRepository->getAllActiveVariants($request);
        return view('client.pages.products', compact('products'));
    }

    public function show($slug, ProductRepo $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);
        $relatedProducts = $productRepository->getRelatedProducts($product);

        return view('client.pages.product-detail', compact('product', 'relatedProducts'));
    }

    public function productDetail($slug, ProductRepo $productRepository)
    {
        return $this->show($slug, $productRepository);
    }
}
