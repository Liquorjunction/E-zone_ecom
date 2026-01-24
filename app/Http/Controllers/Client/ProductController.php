<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
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
        $bestsellers = $productRepository->getBestsellerVariants(12);
        $bestsellersIds = $bestsellers->pluck('id')->toArray();

        return view('client.pages.products', compact('products', 'bestsellers', 'bestsellersIds'));
    }

    public function deals(Request $request, ProductRepo $productRepository)
    {
        $products = $productRepository->getDealVariants($request);
        $bestsellers = $productRepository->getBestsellerVariants(12);
        $bestsellersIds = $bestsellers->pluck('id')->toArray();

        return view('client.pages.deals', compact('products', 'bestsellers', 'bestsellersIds'));
    }

    public function show($slug, ProductRepo $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);
        $relatedProducts = $productRepository->getRelatedProducts($product);
        $bestsellers = $productRepository->getBestsellerVariants(8);
        $bestsellersIds = $bestsellers->pluck('id')->toArray();

        $variantDiscounts = [];
        if ($product->productVariants && $product->productVariants->count() > 0) {
            foreach ($product->productVariants as $variant) {
                if (!$variant->relationLoaded('product')) {
                    $variant->setRelation('product', $product);
                }
                $variantDiscounts[$variant->id] = $this->discountService->calculateProductDiscount($variant);
            }
        }

        $firstVariant = $product->productVariants->first();
        $discountInfo = $firstVariant && isset($variantDiscounts[$firstVariant->id])
            ? $variantDiscounts[$firstVariant->id]
            : null;

        if (request()->has('variant')) {
            $requestedSlug = request()->get('variant');
            $requestedVariant = $product->productVariants->where('slug', $requestedSlug)->first();
            if ($requestedVariant && isset($variantDiscounts[$requestedVariant->id])) {
                $discountInfo = $variantDiscounts[$requestedVariant->id];
            }
        }

        if (request()->wantsJson()) {
            return response()->json([
                'product' => $product,
                'discount_info' => $discountInfo,
                'variant_discounts' => $variantDiscounts,
                'bestsellers_ids' => $bestsellersIds
            ]);
        }

        return view('client.pages.product-detail', compact('product', 'relatedProducts', 'discountInfo', 'variantDiscounts', 'bestsellers', 'bestsellersIds'));
    }

    public function getVariantDiscount(Request $request)
    {
        try {
            $variantId = $request->input('variant_id');

            if (!$variantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant ID is required'
                ], 400);
            }

            $variant = ProductVariant::with('product')->find($variantId);

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product variant not found'
                ], 404);
            }

            $discountInfo = $this->discountService->calculateProductDiscount($variant);

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
