<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index(Request $request, ProductRepo $productRepository)
    {
        $products = $productRepository->getAllActiveVariants($request);
        return view('client.pages.products', compact('products'));
    }

    public function show($slug, ProductRepo $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);
        $relatedProducts = $productRepository->getRelatedProducts($product);
        $discount = $this->discountService->getDiscount($product);

        return view('client.pages.product-detail', compact('product', 'relatedProducts', 'discount'));
    }

    public function productDetail($slug, ProductRepo $productRepository)
    {
        try {
            $product = $productRepository->findActiveBySlug($slug);
            $relatedProducts = $productRepository->getRelatedProducts($product);
            $discount = $this->discountService->getDiscount($product);
            dd($discount);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            return $this->show($slug, $productRepository, $relatedProducts, $discount);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching product details: ' . $e->getMessage()], 500);
        }
    }
}
