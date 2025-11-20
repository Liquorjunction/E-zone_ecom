<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\DestroyCategoryRequest;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Requests\Admin\SubCategory\DestroySubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\StoreSubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\UpdateSubCategoryRequest;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface as CategoryRepo;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface as SubCategoryRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Config\Exception\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class CategorySubCategoryController extends Controller
{
    public function index()
    {
        return view('admin.pages.categoryandsubcategory.index');
    }

    public function create()
    {
        $categories = Categories::all(); // For subcategory category selection
        return view('admin.pages.categoryandsubcategory.create', compact('categories'));
    }

    public function storeCategory(StoreCategoryRequest $request, CategoryRepo $categoryRepo)
    {
        try {
            $categoryRepo->storeCategory($request);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'Category created successfully.');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function storeSubCategory(StoreSubCategoryRequest $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $subCategoryRepo->storeSubCategory($request);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'SubCategory created successfully.');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit()
    {
        $id = request('id');
        $type = request('type');
        if ($type === 'category') {
            $item = Categories::findOrFail($id);
        } elseif ($type === 'subcategory') {
            $item = SubCategories::findOrFail($id);
            $categories = Categories::all();
        } else {
            return redirect()->back()->withErrors(['error' => 'Invalid type']);
        }
        return view('admin.pages.categoryandsubcategory.edit', compact('item', 'type', 'categories'));
    }

    public function update(CategoryRepo $categoryRepo, SubCategoryRepo $subCategoryRepo)
    {
        $id = request('id');
        $type = request('type');
        if ($type === 'category') {
            $request = app(UpdateCategoryRequest::class);
            try {
                $categoryRepo->updateCategory($request, $id);
                return redirect()->route('admin.category-subcategory.index')->with('success', 'Category updated successfully.');
            } catch (ValidationException $e) {
                Log::error($e->getMessage());
                return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
            }
        } elseif ($type === 'subcategory') {
            $request = app(UpdateSubCategoryRequest::class);
            try {
                $subCategoryRepo->updateSubCategory($request, $id);
                return redirect()->route('admin.category-subcategory.index')->with('success', 'SubCategory updated successfully.');
            } catch (ValidationException $e) {
                Log::error($e->getMessage());
                return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
            }
        }
        return redirect()->back()->withErrors(['error' => 'Invalid type']);
    }

   public function destroy(CategoryRepo $categoryRepo, SubCategoryRepo $subCategoryRepo)
    {
        // Fetch ID from query string (e.g. ?id=1) instead of route parameter
        $id = request('id');
        $type = request('type');

        if ($type === 'category') {
            $request = app(DestroyCategoryRequest::class);
            $categoryRepo->deleteCategory($id);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'Category deleted successfully.');
        } elseif ($type === 'subcategory') {
            $request = app(DestroySubCategoryRequest::class);
            $subCategoryRepo->deleteSubCategory($id);
            return redirect()->route('admin.category-subcategory.index')->with('success', 'SubCategory deleted successfully.');
        }
        return redirect()->back()->withErrors(['error' => 'Invalid type']);
    }

    public function dataCategory(CategoryRepo $categoryRepo)
    {
        try {
            $categories = $categoryRepo->getDataForDataTable();
            $getTable = DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('image', function ($category) {
                    if ($category->image && Storage::disk('public')->exists($category->image)) {
                        return asset('storage/' . $category->image);
                    }
                })
                ->addColumn('is_active', function ($category) {
                    $data['toggle_url'] = route('admin.category-subcategory.toggleStatus', ['type' => 'category', 'id' => $category->id]);
                    $data['status'] = $category->is_active;
                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($category) {
                    $data['edit_url'] = route('admin.category-subcategory.edit', ['id' => $category->id, 'type' => 'category']);
                    $data['delete_url'] = route('admin.category-subcategory.destroy', ['id' => $category->id, 'type' => 'category']);
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'image', 'is_active'])
                ->make(true);

            return $getTable;
        } catch (\Exception $e) {
            Log::error('Error in CategorySubCategoryController@dataCategory: '.$e->getMessage());
            throw $e;
        }
    }

    public function dataSubCategory(SubCategoryRepo $subCategoryRepo)
    {
        try {
            $subCategories = $subCategoryRepo->getDataForDataTable();
            $getTable = DataTables::of($subCategories)
                ->addIndexColumn()
                ->addColumn('image', function ($subCategory) {
                    if ($subCategory->image && Storage::disk('public')->exists($subCategory->image)) {
                        return asset('storage/' . $subCategory->image);
                    }
                })
                ->addColumn('is_active', function ($subCategory) {
                    $data['toggle_url'] = route('admin.category-subcategory.toggleStatus', ['type' => 'subcategory', 'id' => $subCategory->id]);
                    $data['status'] = $subCategory->is_active;
                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($subCategory) {
                    $data['edit_url'] = route('admin.category-subcategory.edit', ['id' => $subCategory->id, 'type' => 'subcategory']);
                    $data['delete_url'] = route('admin.category-subcategory.destroy', ['id' => $subCategory->id, 'type' => 'subcategory']);
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'image', 'is_active'])
                ->make(true);

            return $getTable;
        } catch (\Exception $e) {
            Log::error('Error in CategorySubCategoryController@dataSubCategory: '.$e->getMessage());
            throw $e;
        }
    }

    public function toggleStatus(CategoryRepo $categoryRepo, SubCategoryRepo $subCategoryRepo, $type, $id)
    {
        if ($type === 'category') {
            $categoryRepo->updateStatus($id);
        } elseif ($type === 'subcategory') {
            $subCategoryRepo->updateStatus($id);
        }
        return response()->json(['success' => true]);
    }
}
