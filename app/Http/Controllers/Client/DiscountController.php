<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\DiscountRepositoryInterface as DiscountRepo;
use App\Http\Requests\Client\Discount\ReadDiscountRequest;

class DiscountController extends Controller
{
    public function index(DiscountRepo $discountRepo, ReadDiscountRequest $request)
    {
        $filters = $request->only(['type']);
        $discounts = $discountRepo->getActiveDiscounts($filters);
        return response()->json(['data' => $discounts]);
    }

    public function show(ReadDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $id = $request->input('id');
        if (is_null($id)) {
            return response()->json(['message' => 'id is required'], 422);
        }
        $discount = $discountRepo->getDiscountDetails($id);
        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }
        return response()->json(['data' => $discount]);
    }

    public function applicableForProduct(ReadDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $productId = $request->input('product_id');
        if (is_null($productId)) {
            return response()->json(['message' => 'product_id is required'], 422);
        }
        $context = $request->all();
        $discounts = $discountRepo->getApplicableDiscountsForProduct($productId, $context);
        return response()->json(['data' => $discounts]);
    }

    public function applicableForCategory(ReadDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $categoryId = $request->input('category_id');
        if (is_null($categoryId)) {
            return response()->json(['message' => 'category_id is required'], 422);
        }
        $context = $request->all();
        $discounts = $discountRepo->getApplicableDiscountsForCategory($categoryId, $context);
        return response()->json(['data' => $discounts]);
    }

    public function applicableForSubcategory(ReadDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $subcategoryId = $request->input('subcategory_id');
        if (is_null($subcategoryId)) {
            return response()->json(['message' => 'subcategory_id is required'], 422);
        }
        $context = $request->all();
        $discounts = $discountRepo->getApplicableDiscountsForSubcategory($subcategoryId, $context);
        return response()->json(['data' => $discounts]);
    }

    public function applicableForBrand(ReadDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $brandId = $request->input('brand_id');
        if (is_null($brandId)) {
            return response()->json(['message' => 'brand_id is required'], 422);
        }
        $context = $request->all();
        $discounts = $discountRepo->getApplicableDiscountsForBrand($brandId, $context);
        return response()->json(['data' => $discounts]);
    }

    public function calculate(ReadDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $id = $request->input('id');
        if (is_null($id)) {
            return response()->json(['message' => 'id is required'], 422);
        }
        $originalPrice = $request->input('original_price');
        if (is_null($originalPrice)) {
            return response()->json(['message' => 'original_price is required'], 422);
        }
        $context = $request->all();
        $result = $discountRepo->calculateDiscount($id, (float) $originalPrice, $context);
        return response()->json(['data' => $result]);
    }
}
