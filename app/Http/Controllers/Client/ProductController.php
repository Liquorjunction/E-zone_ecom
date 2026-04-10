<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Categories;
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
        $availabilityCounts = $productRepository->getAvailabilityCounts();
        $minPrice = $productRepository->getMinPrice();
        $maxPrice = $productRepository->getMaxPrice();

        $brands = Brand::where('status', 'active')->get();
        $categories = Categories::where('status', 'active')->with('subcategories')->get();
        $subCategories = [];

        return view('client.pages.products', compact('products', 'bestsellers', 'bestsellersIds', 'availabilityCounts', 'brands', 'categories', 'subCategories', 'minPrice', 'maxPrice'));
    }

    public function deals(Request $request, ProductRepo $productRepository)
    {
        $products = $productRepository->getDealVariants($request);
        $bestsellers = $productRepository->getBestsellerVariants(12);
        $bestsellersIds = $bestsellers->pluck('id')->toArray();
        $availabilityCounts = $productRepository->getAvailabilityCounts();
        $minPrice = $productRepository->getMinPrice();
        $maxPrice = $productRepository->getMaxPrice();

        $brands = Brand::where('status', 'active')->get();
        $categories = Categories::where('status', 'active')->with('subcategories')->get();
        $subCategories = [];

        return view('client.pages.deals', compact('products', 'bestsellers', 'bestsellersIds', 'availabilityCounts', 'brands', 'categories', 'subCategories', 'minPrice', 'maxPrice'));
    }

    public function show($slug, ProductRepo $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);
        return $this->renderProductDetail($product, null, $productRepository);
    }

    public function showByBrand(string $brandSlug, string $slug, ProductRepo $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);

        if (!$this->isValidProductBrandContext($product, $brandSlug)) {
            abort(404);
        }

        return $this->renderProductDetail($product, [
            'type' => 'brand',
            'brand_slug' => $brandSlug,
        ], $productRepository);
    }

    public function showByCategory(string $categorySlug, string $slug, ProductRepo $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);

        if (!$this->isValidProductCategoryContext($product, $categorySlug)) {
            abort(404);
        }

        return $this->renderProductDetail($product, [
            'type' => 'category',
            'category_slug' => $categorySlug,
        ], $productRepository);
    }

    public function showByCategorySubcategory(string $categorySlug, string $subcategorySlug, string $slug, ProductRepo $productRepository)
    {
        $product = $productRepository->findActiveBySlug($slug);

        if (!$this->isValidProductCategorySubcategoryContext($product, $categorySlug, $subcategorySlug)) {
            abort(404);
        }

        return $this->renderProductDetail($product, [
            'type' => 'category_subcategory',
            'category_slug' => $categorySlug,
            'subcategory_slug' => $subcategorySlug,
        ], $productRepository);
    }

    private function renderProductDetail(Product $product, $breadcrumbContext, ProductRepo $productRepository)
    {
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
                'breadcrumb_context' => $breadcrumbContext,
                'discount_info' => $discountInfo,
                'variant_discounts' => $variantDiscounts,
                'bestsellers_ids' => $bestsellersIds
            ]);
        }

        return view('client.pages.product-detail', compact('product', 'relatedProducts', 'discountInfo', 'variantDiscounts', 'bestsellers', 'bestsellersIds', 'breadcrumbContext'));
    }

    private function isValidProductBrandContext(Product $product, string $brandSlug): bool
    {
        return strtolower(trim((string) optional($product->brand)->slug)) === strtolower(trim($brandSlug));
    }

    private function isValidProductCategoryContext(Product $product, string $categorySlug): bool
    {
        return strtolower(trim((string) optional($product->category)->slug)) === strtolower(trim($categorySlug));
    }

    private function isValidProductCategorySubcategoryContext(Product $product, string $categorySlug, string $subcategorySlug): bool
    {
        $normalizedCategorySlug = strtolower(trim($categorySlug));
        $normalizedSubcategorySlug = strtolower(trim($subcategorySlug));

        $productCategorySlug = strtolower((string) optional($product->category)->slug);
        $productSubcategorySlug = strtolower((string) optional($product->subCategory)->slug);
        $subcategoryCategorySlug = strtolower((string) optional(optional($product->subCategory)->category)->slug);

        return $normalizedCategorySlug !== ''
            && $normalizedSubcategorySlug !== ''
            && $normalizedCategorySlug === $productCategorySlug
            && $normalizedSubcategorySlug === $productSubcategorySlug
            && $normalizedCategorySlug === $subcategoryCategorySlug;
    }

    public function getVariantDiscount(Request $request)
    {
        try {
            $variantId = $request->input('variant_id');

            if (!$variantId) {
                return response()->json([
                    'success' => false,
                    'message' => statictranslations('Variant ID is required')
                ], 400);
            }

            $variant = ProductVariant::with('product')->find($variantId);

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => statictranslations('Product variant not found')
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
                'message' => statictranslations('An error occurred: ') . $e->getMessage()
            ], 500);
        }
    }
}
