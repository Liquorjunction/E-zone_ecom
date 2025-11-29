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

            return redirect()->back()->with(['success' => true, 'message' => 'SubCategory deleted successfully']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
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
            ->addColumn('image', function ($subCategory) {
                if ($subCategory->{'sub-category-icon'} && Storage::disk('public')->exists($subCategory->{'sub-category-icon'})) {
                    return '<img src="'.asset('storage/'.$subCategory->{'sub-category-icon'}).'" alt="SubCategory Image" class="img-thumbnail" width="50">';
                }

                return 'N/A';
            })
            ->addColumn('is_active', function ($subCategory) use ($request) {
                $data['toggle_url'] = $request->user()->can('category.update') ? route('admin.category-subcategory.subcategories.toggleStatus', ['id' => $subCategory->id]) : '';
                $data['status'] = $subCategory->status;
                $data['id'] = $subCategory->id;

                return view('admin.partials.datatable-status', $data)->render();
            })
            ->addColumn('action', function ($subCategory) use ($request) {
                $data['edit_url'] = $request->user()->can('category.update') ? route('admin.category-subcategory.subcategories.edit', ['id' => $subCategory->id]) : '';
                $data['delete_url'] = $request->user()->can('category.delete') ? route('admin.category-subcategory.subcategories.destroy', ['id' => $subCategory->id]) : '';
                $data['id'] = $subCategory->id;

                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['action', 'image', 'is_active'])
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
