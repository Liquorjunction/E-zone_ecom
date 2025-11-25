<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\DestroyCategoryRequest;
use App\Http\Requests\Admin\Category\EditCategoryRequest;
use App\Http\Requests\Admin\Category\ReadCategoryRequest;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Categories;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface as CategoryRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.pages.category.index');
    }

    public function create()
    {
        return view('admin.pages.category.create');
    }

    public function store(StoreCategoryRequest $request, CategoryRepo $categoryRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Fill for 'category-name' ---
            if (isset($data['category-name']['en'])) {
                if (empty($data['category-name']['fr'])) {
                    $data['category-name']['fr'] = gtrans($data['category-name']['en'], 'fr');
                }
                if (empty($data['category-name']['ar'])) {
                    $data['category-name']['ar'] = gtrans($data['category-name']['en'], 'ar');
                }
            }
            // --------------------------------------

            $categoryRepo->storeCategory($request, $data);

            return redirect()->route('admin.category-subcategory.categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditCategoryRequest $request)
    {
        $id = $request->input('id');
        $category = Categories::findOrFail($id);
        return view('admin.pages.category.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, CategoryRepo $categoryRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Fill for Update ---
            if (isset($data['category-name']['en'])) {
                if (empty($data['category-name']['fr'])) {
                    $data['category-name']['fr'] = gtrans($data['category-name']['en'], 'fr');
                }
                if (empty($data['category-name']['ar'])) {
                    $data['category-name']['ar'] = gtrans($data['category-name']['en'], 'ar');
                }
            }
            // -----------------------------

            $categoryRepo->updateCategory($request, $id, $data);

            return redirect()->route('admin.category-subcategory.categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyCategoryRequest $request, CategoryRepo $categoryRepo)
    {
        try {
            $id = $request->input('id');
            $categoryRepo->deleteCategory($id);
            return redirect()->back()->with(['success' => true, 'message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function data(ReadCategoryRequest $request, CategoryRepo $categoryRepo)
    {
        $categories = $categoryRepo->getDataForDataTable($request->all());
        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('image', function ($category) {
                if ($category->{'category-icon'} && Storage::disk('public')->exists($category->{'category-icon'})) {
                    return '<img src="' . asset('storage/' . $category->{'category-icon'}) . '" alt="Category Image" class="img-thumbnail" width="50">';
                }
                return 'N/A';
            })
            ->editColumn('category-name', function ($category) {
                // Display English name in Datatable
                return $category->getTranslation('category-name', 'en');
            })
            ->addColumn('is_active', function ($category) use ($request) {
                $data['toggle_url'] = $request->user()->can('category.update') ? route('admin.category-subcategory.categories.toggleStatus', ['id' => $category->id]) : '';
                $data['status'] = $category->status;
                $data['id'] = $category->id;
                return view('admin.partials.datatable-status', $data)->render();
            })
            ->addColumn('action', function ($category) use ($request) {
                $data['edit_url'] = $request->user()->can('category.update') ? route('admin.category-subcategory.categories.edit', ['id' => $category->id]) : '';
                $data['delete_url'] = $request->user()->can('category.delete') ? route('admin.category-subcategory.categories.destroy', ['id' => $category->id]) : '';
                $data['id'] = $category->id;
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['action', 'image', 'is_active'])
            ->make(true);
    }

    public function toggleStatus(EditCategoryRequest $request, CategoryRepo $categoryRepo)
    {
        $id = $request->input('id');
        $categoryRepo->updateStatus($id);
        return redirect()->back()->with(['success' => true, 'message' => 'Category status updated successfully']);
    }
}
