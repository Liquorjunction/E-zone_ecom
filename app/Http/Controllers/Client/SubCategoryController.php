<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\SubCategoryRepositoryInterface as SubCategoryRepo;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request, SubCategoryRepo $subCategoryRepo)
    {
        $subCategories = $subCategoryRepo->getSubCategoriesData();
        return view('client.pages.allsubcategory', compact('subCategories'));
    }

    public function productsBySubCategory(Request $request, string $slug, SubCategoryRepo $subCategoryRepo)
    {
        $subcategory = $subCategoryRepo->getSubCategoryBySlug($slug);
        $variants = $subCategoryRepo->getVariantsBySubCategorySlug($slug, 12);
        return view('client.pages.subcategory-products', [
            'subcategory' => $subcategory,
            'variants' => $variants,
            'subcategory_slug' => $slug,
            'category_slug' => optional(optional($subcategory)->category)->slug,
        ]);
    }
}
