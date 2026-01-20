<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubCategory\DestroySubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\EditSubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\ReadSubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\StoreSubCategoryRequest;
use App\Http\Requests\Admin\SubCategory\UpdateSubCategoryRequest;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface as SubCategoryRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    public function index()
    {
        return view('admin.pages.subcategory.index');
    }

    public function create()
    {
        $categories = Categories::where('status', 'active')->get();

        return view('admin.pages.subcategory.create', compact('categories'));
    }

    public function store(StoreSubCategoryRequest $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $data = $request->validated();

            // Category name and sub-category name should not be the same
            $category = Categories::find($data['category_id']);
            if ($category && $category->getTranslation('category-name', 'en') == $data['sub-category-name']['en']) {
                return redirect()->back()->withErrors(['category_id' => 'Category name and sub-category name should not be the same.'])->withInput();
            }

            // --- Smart Fill for 'sub-category-name' ---
            if (isset($data['sub-category-name']['en'])) {
                if (empty($data['sub-category-name']['fr'])) {
                    $data['sub-category-name']['fr'] = gtrans($data['sub-category-name']['en'], 'fr');
                }
                if (empty($data['sub-category-name']['ar'])) {
                    $data['sub-category-name']['ar'] = gtrans($data['sub-category-name']['en'], 'ar');
                }
            }
            // ------------------------------------------

            $subCategoryRepo->storeSubCategory($request, $data);

            return redirect()->route('admin.category-subcategory.subcategories.index')->with('success', 'SubCategory created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditSubCategoryRequest $request)
    {
        $id = $request->input('id');
        $subCategory = SubCategories::findOrFail($id);
        $categories = Categories::where('status', 'active')->get();

        return view('admin.pages.subcategory.edit', compact('subCategory', 'categories'));
    }

    public function update(UpdateSubCategoryRequest $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Fill for Update ---
            if (isset($data['sub-category-name']['en'])) {
                if (empty($data['sub-category-name']['fr'])) {
                    $data['sub-category-name']['fr'] = gtrans($data['sub-category-name']['en'], 'fr');
                }
                if (empty($data['sub-category-name']['ar'])) {
                    $data['sub-category-name']['ar'] = gtrans($data['sub-category-name']['en'], 'ar');
                }
            }
            // -----------------------------

            $subCategoryRepo->updateSubCategory($request, $id, $data);

            return redirect()->route('admin.category-subcategory.subcategories.index')->with('success', 'SubCategory updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroySubCategoryRequest $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $id = $request->input('id');
            $subCategoryRepo->deleteSubCategory($id);

            return redirect()->back()->with('success', 'SubCategory deleted successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'Error deleting SubCategory');
        }
    }

    public function data(ReadSubCategoryRequest $request, SubCategoryRepo $subCategoryRepo)
    {
        $subCategories = $subCategoryRepo->getDataForDataTable($request->all());

        return DataTables::of($subCategories)
            ->addIndexColumn()
            ->addColumn('name', function ($subCategory) {
                // Show English Name
                return $subCategory->getTranslation('sub-category-name', 'en');
            })
            ->addColumn('category_name', function ($subCategory) {
                return $subCategory->category ? $subCategory->category->getTranslation('category-name', 'en') : 'N/A';
            })
            ->addColumn('is_active', function ($subCategory) use ($request) {
                if ($request->user()->can('subcategory.update')) {
                    $data['toggle_url'] = $request->user()->can('subcategory.update') ? route('admin.category-subcategory.subcategories.toggleStatus', ['id' => $subCategory->id]) : '';
                    $data['status'] = $subCategory->status;
                    $data['id'] = $subCategory->id;
                    return view('admin.partials.datatable-status', $data)->render();
                } else {
                    return view('components.datatable-status-badge', ['status' => $subCategory->status])->render();
                }
            })
            ->addColumn('action', function ($subCategory) use ($request) {
                $data['edit_url'] = $request->user()->can('subcategory.update') ? route('admin.category-subcategory.subcategories.edit', ['id' => $subCategory->id]) : '';

                if ($request->user()->can('subcategory.delete') && isset($subCategory->products_count) && $subCategory->products_count == 0) {
                    $data['delete_url'] = route('admin.category-subcategory.subcategories.destroy', ['id' => $subCategory->id]);
                } else {
                    $data['delete_url'] = '';
                }

                $data['id'] = $subCategory->id;

                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

    public function toggleStatus(EditSubCategoryRequest $request, SubCategoryRepo $subCategoryRepo)
    {
        try {
            $id = $request->input('id');
            $subCategoryRepo->updateStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'SubCategory status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
