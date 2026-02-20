<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Category\ReadCategoryRequest;
use App\Repositories\Client\Interfaces\CategoryRepositoryInterface as CategoryRepo;

class CategoryController extends Controller
{
	public function index(ReadCategoryRequest $request, CategoryRepo $categoryRepo)
	{
		$categories = $categoryRepo->getCategoriesData();
		return view('client.pages.allcategory', compact('categories'));
	}

	public function discountedProductsByCategory(ReadCategoryRequest $request, string $slug, CategoryRepo $categoryRepo)
	{
		$products = $categoryRepo->getDiscountedProductsByCategorySlug($slug, 12);
		return view('client.pages.products', compact('products'));
	}

	public function productsByCategory(ReadCategoryRequest $request, string $slug, CategoryRepo $categoryRepo)
	{
		$products = $categoryRepo->getProductsByCategorySlug($slug, 12);
		return view('client.pages.category-products', [
			'category' => $categoryRepo->getCategoryBySlug($slug),
			'products' => $products,
			'category_slug' => $slug
		]);
	}
}
