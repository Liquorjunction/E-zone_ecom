<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface as CategoryRepo;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface as SubCategoryRepo;
use App\Repositories\Admin\Interfaces\BrandRepositoryInterface as BrandRepo;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface as StoreRepo;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface as WarehouseRepo;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface as AdminRepo;
use App\Repositories\Admin\Repositories\FaqRepository as FaqRepo;
use Illuminate\Support\Facades\Log;

class OptionController extends Controller
{
    public function getCategoryList(Request $request, CategoryRepo $categoryRepo)
    {
        try {
            $term = $request->input('q', '');
            $categories = $categoryRepo->getCategoryList($term);
            $categoryOptions = [];
            foreach ($categories as $category) {
                $categoryOptions[] = [
                    'id' => $category->id,
                    'text' => $category->getTranslation('category-name', 'en'),
                ];
            }
            return response()->json(['results' => $categoryOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getCategoryList: Failed to fetch categories', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch categories.'], 500);
        }
    }

    public function getSubCategoryListByCategoryId(Request $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $term = $request->input('q', '');
            $categoryId = $request->input('category_id');
            $subCategories = $subCategoryRepo->getSubCategoryListByCategoryId($term, $categoryId);
            $subCategoryOptions = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryOptions[] = [
                    'id' => $subCategory->id,
                    'text' => $subCategory->getTranslation('sub-category-name', 'en'),
                ];
            }
            return response()->json(['results' => $subCategoryOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getSubCategoryListByCategoryId: Failed to fetch sub-categories', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch sub-categories.'], 500);
        }
    }

    public function getSubCategoryList(Request $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $term = $request->input('q', '');
            $subCategories = $subCategoryRepo->getSubCategoryList($term);
            $subCategoryOptions = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryOptions[] = [
                    'id' => $subCategory->id,
                    'text' => $subCategory->getTranslation('sub-category-name', 'en'),
                ];
            }
            return response()->json(['results' => $subCategoryOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getSubCategoryList: Failed to fetch sub-categories', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch sub-categories.'], 500);
        }
    }

    public function getWarehouseList(Request $request, WarehouseRepo $warehouseRepo)
    {
        try {
            $term = $request->input('q', '');
            $warehouses = $warehouseRepo->getWarehouseList($term);
            $warehouseOptions = [];
            foreach ($warehouses as $warehouse) {
                $warehouseOptions[] = [
                    'id' => $warehouse->id,
                    'text' => $warehouse->getTranslation('warehouse_name', 'en'),
                ];
            }
            return response()->json(['results' => $warehouseOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getWarehouseList: Failed to fetch warehouses', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch warehouses.'], 500);
        }
    }

    public function getStoreList(Request $request, StoreRepo $storeRepo)
    {
        try {
            $term = $request->input('q', '');
            $stores = $storeRepo->getStoreList($term);
            $storeOptions = [];
            foreach ($stores as $store) {
                $storeOptions[] = [
                    'id' => $store->id,
                    'text' => $store->getTranslation('store_name', 'en'),
                ];
            }
            return response()->json(['results' => $storeOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getStoreList: Failed to fetch stores', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch stores.'], 500);
        }
    }

    public function getBrandList(Request $request, BrandRepo $brandRepo)
    {
        try {
            $term = $request->input('q', '');
            $brands = $brandRepo->getBrandList($term);
            $brandOptions = [];
            foreach ($brands as $brand) {
                $brandOptions[] = [
                    'id' => $brand->id,
                    'text' => $brand->getTranslation('name', 'en'),
                ];
            }
            return response()->json(['results' => $brandOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getBrandList: Failed to fetch brands', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch brands.'], 500);
        }
    }

    public function getProductList(Request $request, ProductRepo $productRepo)
    {
        try {
            $term = trim((string) $request->input('q', ''));
            $products = $productRepo->getAllActiveProducts();

            if ($term !== '') {
                $products = $products->filter(function ($product) use ($term) {
                    $name = $product->getTranslation('name', app()->getLocale())
                        ?? $product->getTranslation('name', 'en')
                        ?? '';

                    return stripos($name, $term) !== false;
                })->values();
            }

            $productOptions = [];
            foreach ($products as $product) {
                $productOptions[] = [
                    'id' => $product->id,
                    'text' => $product->getTranslation('name', app()->getLocale())
                        ?? $product->getTranslation('name', 'en')
                        ?? ('Product #' . $product->id),
                ];
            }

            return response()->json(['results' => $productOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getProductList: Failed to fetch products', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch products.'], 500);
        }
    }

    public function getVariantListByProductId(Request $request, ProductRepo $productRepo)
    {
        try {
            $productId = (int) $request->input('product_id');
            $term = trim((string) $request->input('q', ''));

            if ($productId <= 0) {
                return response()->json(['results' => []]);
            }

            $variants = $productRepo->getVariantsByProductId($productId);

            if ($term !== '') {
                $variants = $variants->filter(function ($variant) use ($term) {
                    $name = $variant->variant_name;

                    if (is_array($name)) {
                        $name = $name[app()->getLocale()] ?? $name['en'] ?? (reset($name) ?: '');
                    }

                    return stripos((string) $name, $term) !== false;
                })->values();
            }

            $variantOptions = [];
            foreach ($variants as $variant) {
                $name = $variant->variant_name;
                if (is_array($name)) {
                    $name = $name[app()->getLocale()] ?? $name['en'] ?? (reset($name) ?: '');
                }

                $variantOptions[] = [
                    'id' => $variant->id,
                    'text' => $name ?: ('Variant #' . $variant->id),
                ];
            }

            return response()->json(['results' => $variantOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getVariantList: Failed to fetch variants', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch variants.'], 500);
        }
    }

    public function getVariantList(Request $request, ProductRepo $productRepo)
    {
        try {
            $term = trim((string) $request->input('q', ''));
            $variants = $productRepo->getVarientList($term);
            $variantOptions = [];
            foreach ($variants as $variant) {
                $variantOptions[] = [
                    'id' => $variant->id,
                    'text' => $variant->variant_name,
                ];
            }
            return response()->json(['results' => $variantOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.getVariantList: Failed to fetch variants', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch variants.'], 500);
        }
    }

    public function faqCategoryList(Request $request, FaqRepo $faqRepo)
    {
        try {
            $term = trim((string) $request->input('q', ''));
            $categories = $faqRepo->getFaqCategoryList($term);
            $categoryOptions = [];
            foreach ($categories as $category) {
                $categoryOptions[] = [
                    'id' => $category->id,
                    'text' => $category->getTranslation('name', 'en'),
                ];
            }
            return response()->json(['results' => $categoryOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.faqCategoryList: Failed to fetch FAQ categories', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch FAQ categories.'], 500);
        }
    }

    public function rolesList(Request $request, AdminRepo $adminRepository)
    {
        try {
            $term = trim((string) $request->input('q', ''));
            $roles = $adminRepository->getRolesList($term);
            $roleOptions = [];
            foreach ($roles as $role) {
                $roleOptions[] = [
                    'id' => $role->id,
                    'text' => $role->name,
                ];
            }
            return response()->json(['results' => $roleOptions]);
        } catch (\Exception $e) {
            Log::error('OptionController.rolesList: Failed to fetch roles', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch roles.'], 500);
        }
    }
}
