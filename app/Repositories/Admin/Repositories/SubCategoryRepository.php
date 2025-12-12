<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\SubCategories;
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

        // Handle Status
        $data['status'] = $request->input('status', 'inactive');

        return SubCategories::create($data);
    }

    public function updateSubCategory($request, $id, $data = null)
    {
        $subCategory = SubCategories::findOrFail($id);
        $data = $data ?? $request->validated();

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

    public function getDataForDataTable()
    {
        return SubCategories::with('category')->latest();
    }
}
