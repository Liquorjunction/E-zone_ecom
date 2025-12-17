<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request, ProductRepositoryInterface $productRepository)
    {
        // Get sellable variants (flattened view for listing)
        $products = $productRepository->getAllActiveVariants($request);
        return view('client.pages.products', compact('products'));
    }

    public function show($slug, ProductRepositoryInterface $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);
        $relatedProducts = $productRepository->getRelatedProducts($product);

        return view('client.pages.product-detail', compact('product', 'relatedProducts'));
    }

    // Route expects `productDetail` — keep a named method to match routes
    public function productDetail($slug, ProductRepositoryInterface $productRepository)
    {
        return $this->show($slug, $productRepository);
    }
}
