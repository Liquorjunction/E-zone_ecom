<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
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

        // Calculate discount information for ALL product variants
        $variantDiscounts = [];
        if ($product->productVariants && $product->productVariants->count() > 0) {
            foreach ($product->productVariants as $variant) {
                $variantDiscounts[$variant->id] = $this->discountService->calculateProductDiscount($product, $variant->id);
            }
        }

        // Get discount info for the first variant for initial display
        $firstVariant = $product->productVariants->first();
        $discountInfo = $firstVariant && isset($variantDiscounts[$firstVariant->id])
            ? $variantDiscounts[$firstVariant->id]
            : null;

        // Calculate discount for related products
        foreach ($relatedProducts as $relatedProduct) {
            $relatedVariant = $relatedProduct->productVariants->first();
            if ($relatedVariant) {
                $relatedProduct->discountInfo = $this->discountService->calculateProductDiscount($relatedProduct, $relatedVariant->id);
            }
        }

        return view('client.pages.product-detail', compact('product', 'relatedProducts', 'discountInfo', 'variantDiscounts'));
    }

    public function productDetail($slug, ProductRepo $productRepository)
    {
        try {
            $product = $productRepository->findActiveBySlug($slug);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $relatedProducts = $productRepository->getRelatedProducts($product);
            $discount = $this->discountService->getActiveDiscounts($product);
            $bestDiscount = $this->discountService->findBestDiscountForProduct($product, $product->productVariants->first()->id ?? null);

            // dd(compact('product', 'relatedProducts', 'bestDiscount'));


            return $this->show($slug, $productRepository , compact('product', 'relatedProducts', 'bestDiscount'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching product details: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get discount information for a specific product variant
     */
    public function getVariantDiscount(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $variantId = $request->input('variant_id');

            if (!$productId || !$variantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product ID and Variant ID are required'
                ], 400);
            }

            $product = Product::with('productVariants')->find($productId);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $discountInfo = $this->discountService->calculateProductDiscount($product, $variantId);

            return response()->json([
                'success' => true,
                'discount' => $discountInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
