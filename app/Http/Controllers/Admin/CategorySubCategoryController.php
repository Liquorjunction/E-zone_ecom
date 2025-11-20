<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Requests\Admin\SubCategory\StoreSubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\UpdateSubCategoryRequest;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface as CategoryRepo;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface as SubCategoryRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CategorySubCategoryController extends Controller
{
    public function index()
    {
        return view('admin.pages.categoryandsubcategory.index');
    }

    // ==========================================
    //               CATEGORY METHODS
    // ==========================================

    public function createCategory()
    {
        return view('admin.pages.categoryandsubcategory.create_category');
    }

    public function storeCategory(StoreCategoryRequest $request, CategoryRepo $categoryRepo)
    {
        try {
            $categoryRepo->storeCategory($request);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function editCategory($id)
    {
        $category = Categories::findOrFail($id);
        return view('admin.pages.categoryandsubcategory.edit_category', compact('category'));
    }

    public function updateCategory(UpdateCategoryRequest $request, $id, CategoryRepo $categoryRepo)
    {
        try {
            $categoryRepo->updateCategory($request, $id);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroyCategory($id, CategoryRepo $categoryRepo)
    {
        try {
            $categoryRepo->deleteCategory($id);
            return redirect()->back()->with(['success' => true, 'message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function dataCategory(CategoryRepo $categoryRepo)
    {
        $categories = $categoryRepo->getDataForDataTable();
        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('image', function ($category) {
                if ($category->{'category-icon'} && Storage::disk('public')->exists($category->{'category-icon'})) {
                    return '<img src="' . asset('storage/' . $category->{'category-icon'}) . '" alt="Category Image" class="img-thumbnail" width="50">';
                }
                return 'N/A';
            })
            ->addColumn('is_active', function ($category) {
                $data['toggle_url'] = route('admin.category-subcategory.categories.toggleStatus', ['id' => $category->id]);
                $data['status'] = $category->status;
                $data['id'] = $category->id;
                return view('admin.partials.datatable-status', $data)->render();
            })
            ->addColumn('action', function ($category) {
                $data['edit_url'] = route('admin.category-subcategory.categories.edit', ['id' => $category->id]);
                $data['delete_url'] = route('admin.category-subcategory.categories.destroy', ['id' => $category->id]);
                $data['id'] = $category->id;
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['action', 'image', 'is_active'])
            ->make(true);
    }

    public function toggleCategoryStatus($id, CategoryRepo $categoryRepo)
    {
        $categoryRepo->updateStatus($id);
        return redirect()->back()->with(['success' => true, 'message' => 'Category status updated successfully']);
    }

    // ==========================================
    //             SUBCATEGORY METHODS
    // ==========================================

    public function createSubCategory()
    {
        // This was correct in your original code
        $categories = Categories::where('status', 'active')->get();
        return view('admin.pages.categoryandsubcategory.create_subcategory', compact('categories'));
    }

    public function storeSubCategory(StoreSubCategoryRequest $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $subCategoryRepo->storeSubCategory($request);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'SubCategory created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function editSubCategory($id)
    {
        $subCategory = SubCategories::findOrFail($id);

        // FIX: Changed 'is_active', 1 to 'status', 'active' to match your migration
        $categories = Categories::where('status', 'active')->get();

        return view('admin.pages.categoryandsubcategory.edit_subcategory', compact('subCategory', 'categories'));
    }

    public function updateSubCategory(UpdateSubCategoryRequest $request, $id, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $subCategoryRepo->updateSubCategory($request, $id);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'SubCategory updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroySubCategory($id, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $subCategoryRepo->deleteSubCategory($id);
            return redirect()->back()->with(['success' => true, 'message' => 'SubCategory deleted successfully']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function dataSubCategory(SubCategoryRepo $subCategoryRepo)
    {
        $subCategories = $subCategoryRepo->getDataForDataTable();
        return DataTables::of($subCategories)
            ->addIndexColumn()
            ->addColumn('name', function ($subCategory) {
                // Correctly using kebab-case syntax
                return $subCategory->{'sub-category-name'};
            })
            ->addColumn('category.name', function ($subCategory) {
                // Correctly checking relationship and kebab-case field
                return $subCategory->category ? $subCategory->category->{'category-name'} : 'N/A';
            })
            ->addColumn('image', function ($subCategory) {
                if ($subCategory->{'sub-category-icon'} && Storage::disk('public')->exists($subCategory->{'sub-category-icon'})) {
                    return '<img src="' . asset('storage/' . $subCategory->{'sub-category-icon'}) . '" alt="SubCategory Image" class="img-thumbnail" width="50">';
                }
                return 'N/A';
            })
            ->addColumn('is_active', function ($subCategory) {
                $data['toggle_url'] = route('admin.category-subcategory.subcategories.toggleStatus', ['id' => $subCategory->id]);
                $data['status'] = $subCategory->status;
                $data['id'] = $subCategory->id;
                return view('admin.partials.datatable-status', $data)->render();
            })
            ->addColumn('action', function ($subCategory) {
                $data['edit_url'] = route('admin.category-subcategory.subcategories.edit', ['id' => $subCategory->id]);
                $data['delete_url'] = route('admin.category-subcategory.subcategories.destroy', ['id' => $subCategory->id]);
                $data['id'] = $subCategory->id;
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['action', 'image', 'is_active'])
            ->make(true);
    }

    public function toggleSubCategoryStatus($id, SubCategoryRepo $subCategoryRepo)
    {
        $subCategoryRepo->updateStatus($id);
        return redirect()->back()->with(['success' => true, 'message' => 'SubCategory status updated successfully']);
    }
}
