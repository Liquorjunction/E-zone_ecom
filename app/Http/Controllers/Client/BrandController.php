<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Brand\ReadBrandRequest;
use App\Repositories\Client\Interfaces\BrandRepositoryInterface as BrandRepo;

class BrandController extends Controller
{
    public function index(ReadBrandRequest $request, BrandRepo $brandRepo)
    {
        $brands = $brandRepo->getActiveBrandsData();
        return view('client.pages.allbrands', compact('brands'));
    }

    public function discountedProductsByBrand(ReadBrandRequest $request, string $slug, BrandRepo $brandRepo)
    {
        $products = $brandRepo->getDiscountedProductsByBrandSlug($slug, 12);
        return view('client.pages.products', compact('products'));
    }

    public function productsByBrand(ReadBrandRequest $request, string $slug, BrandRepo $brandRepo)
    {
        $categoryId = $request->query('category');
        $products = $brandRepo->getVariantsByBrandSlug($slug, 12, $categoryId);
        $brand = $brandRepo->getBrandBySlug($slug);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('client.pages.partials.brand-products-catalog', [
                    'brand' => $brand,
                    'products' => $products,
                    'brand_slug' => $slug,
                    'selected_category_id' => $categoryId,
                ])->render(),
            ]);
        }

        return view('client.pages.brand-products', [
            'brand' => $brand,
            'products' => $products,
            'brand_slug' => $slug,
            'selected_category_id' => $categoryId,
        ]);
    }
}
