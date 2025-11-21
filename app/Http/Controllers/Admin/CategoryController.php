<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            $categoryRepo->storeCategory($request);
            return redirect()->route('admin.category-subcategory.categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $category = Categories::findOrFail($id);
        return view('admin.pages.category.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, $id, CategoryRepo $categoryRepo)
    {
        try {
            $categoryRepo->updateCategory($request, $id);
            return redirect()->route('admin.category-subcategory.categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id, CategoryRepo $categoryRepo)
    {
        try {
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

    public function toggleStatus($id, CategoryRepo $categoryRepo)
    {
        $categoryRepo->updateStatus($id);
        return redirect()->back()->with(['success' => true, 'message' => 'Category status updated successfully']);
    }
}
