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
                // Fix: Pass the variant object directly to ensure precise targeting
                // Ensure parent is linked for category checks
                if (!$variant->relationLoaded('product')) {
                    $variant->setRelation('product', $product);
                }
                $variantDiscounts[$variant->id] = $this->discountService->calculateProductDiscount($variant);
            }
        }

        // Get discount info for the first variant for initial display
        $firstVariant = $product->productVariants->first();
        $discountInfo = $firstVariant && isset($variantDiscounts[$firstVariant->id])
            ? $variantDiscounts[$firstVariant->id]
            : null;

        // If specific variant requested via query param
        if (request()->has('variant')) {
            $requestedSlug = request()->get('variant');
            $requestedVariant = $product->productVariants->where('slug', $requestedSlug)->first();
            if ($requestedVariant && isset($variantDiscounts[$requestedVariant->id])) {
                $discountInfo = $variantDiscounts[$requestedVariant->id];
            }
        }

        // Check if request expects JSON (AJAX for variant switching)
        if (request()->wantsJson()) {
            return response()->json([
                'product' => $product,
                'discount_info' => $discountInfo,
                'variant_discounts' => $variantDiscounts
            ]);
        }

        return view('client.pages.product-detail', compact('product', 'relatedProducts', 'discountInfo', 'variantDiscounts'));
    }

    /**
     * Get discount information for a specific product variant
     */
    public function getVariantDiscount(Request $request)
    {
        try {
            // We prioritize variant_id as it uniquely identifies the sellable item
            $variantId = $request->input('variant_id');

            if (!$variantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant ID is required'
                ], 400);
            }

            // Find the variant directly (Sellable Item)
            // Eager load product (Container) for Category/Brand discount checks
            $variant = ProductVariant::with('product')->find($variantId);

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product variant not found'
                ], 404);
            }

            // Calculate discount using the Variant object
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
