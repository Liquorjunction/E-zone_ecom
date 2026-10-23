<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Categories;
use App\Models\SubCategories;
use Illuminate\Validation\ValidationException;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function getAllSubCategories()
    {
        return SubCategories::with('category')->get();
    }

    public function getActiveSubCategories()
    {
        return SubCategories::where('status', 'active')->with('category')->get();
    }

    public function getSubCategoriesByCategoryId($categoryId)
    {
        return SubCategories::where('category_id', $categoryId)->with('category')->get();
    }

    public function getSubCategoryById($id)
    {
        return SubCategories::findOrFail($id);
    }

    public function storeSubCategory($request, $data = null)
    {
        // Use passed data (with translations) or fallback
        $data = $data ?? $request->validated();

        // Handle Image Upload
        if ($request->hasFile('sub-category-icon')) {
            $imagePath = $request->file('sub-category-icon')->store('subcategories', 'public');
            $data['sub-category-icon'] = $imagePath;
        }

        // Duplicate check: English sub-category name within the same category
        $english = data_get($data, 'sub-category-name.en');
        $categoryId = data_get($data, 'category_id');
        if ($english && $categoryId) {
            $query = SubCategories::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(`sub-category-name`, '$.en')) = ?", [$english])
                ->where('category_id', $categoryId);
            if ($query->exists()) {
                throw ValidationException::withMessages(['sub-category-name.en' => 'The English sub-category name has already been taken for this category.']);
            }
        }

        // Handle Status
        $data['status'] = $request->input('status', 'inactive');

        return SubCategories::create($data);
    }

    public function updateSubCategory($request, $id, $data = null)
    {
        $subCategory = SubCategories::findOrFail($id);
        $data = $data ?? $request->validated();

        // Duplicate check: English sub-category name within same category (exclude current)
        $english = data_get($data, 'sub-category-name.en');
        $categoryId = data_get($data, 'category_id');
        if ($english && $categoryId) {
            $query = SubCategories::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(`sub-category-name`, '$.en')) = ?", [$english])
                ->where('category_id', $categoryId)
                ->where('id', '!=', $id);
            if ($query->exists()) {
                throw ValidationException::withMessages(['sub-category-name.en' => 'The English sub-category name has already been taken for this category.']);
            }
        }

        // Handle Image Update
        if ($request->hasFile('sub-category-icon')) {
            if ($subCategory->{'sub-category-icon'} && Storage::disk('public')->exists($subCategory->{'sub-category-icon'})) {
                Storage::disk('public')->delete($subCategory->{'sub-category-icon'});
            }

            $imagePath = $request->file('sub-category-icon')->store('subcategories', 'public');
            $data['sub-category-icon'] = $imagePath;
        } else {
            unset($data['sub-category-icon']);
        }

        // Handle Status
        $data['status'] = $request->input('status', 'inactive');

        $subCategory->update($data);

        return $subCategory;
    }

    public function deleteSubCategory($id)
    {
        $subCategory = SubCategories::findOrFail($id);

        if ($subCategory->{'sub-category-icon'} && Storage::disk('public')->exists($subCategory->{'sub-category-icon'})) {
            Storage::disk('public')->delete($subCategory->{'sub-category-icon'});
        }

        return $subCategory->delete();
    }

    public function updateStatus($id)
    {
        $subCategory = SubCategories::findOrFail($id);
        $subCategory->status = ($subCategory->status === 'active') ? 'inactive' : 'active';
        $subCategory->save();

        return $subCategory;
    }

    public function getSubCategoryList($term = null)
    {
        $query = SubCategories::select('id', 'sub-category-name')
            ->where('status', 'active');

        if (!empty($term)) {
            $like = '%' . $term . '%';
            $query->where(function ($builder) use ($like) {
                $builder->orWhere('sub-category-name->en', 'LIKE', $like)
                    ->orWhere('sub-category-name->fr', 'LIKE', $like)
                    ->orWhere('sub-category-name->ar', 'LIKE', $like);
            });
        }

        return $query->get();
    }

    public function getSubCategoryListByCategoryId($term = null, $categoryId = null)
    {
        $query = SubCategories::select('id', 'sub-category-name')
            ->where('status', 'active')
            ->where('category_id', $categoryId);

        if (!empty($term)) {
            $like = '%' . $term . '%';
            $query->where(function ($builder) use ($like) {
                $builder->orWhere('sub-category-name->en', 'LIKE', $like)
                    ->orWhere('sub-category-name->fr', 'LIKE', $like)
                    ->orWhere('sub-category-name->ar', 'LIKE', $like);
            });
        }

        return $query->get();
    }

    public function getDataForDataTable($request = null)
    {
        $query = SubCategories::with('category')
            ->withCount('products')
            ->latest();

        if (isset($request['status']) && $request['status'] !== '') {
            $query->where('status', $request['status'] == '1' ? 'active' : 'inactive');
        }

        return $query;
    }
}
