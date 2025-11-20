<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function getAllSubCategories()
    {
        return SubCategories::orderBy('id', 'desc')->get();
    }

    public function getSubCategoryById($id)
    {
        return SubCategories::find($id);
    }

    public function storeSubCategory($request)
    {
        $data = $request->validated();

        if ($request->hasFile('sub-category-icon')) {
            $data['sub-category-icon'] = $request->file('sub-category-icon')->store('subcategories', 'public');
        }

        $data['status'] = $request->has('status') ? 1 : 0;

        return SubCategories::create($data);
    }

    public function updateSubCategory($request, $id)
    {
        $subCategory = SubCategories::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('sub-category-icon')) {
            if ($subCategory->{'sub-category-icon'} && Storage::disk('public')->exists($subCategory->{'sub-category-icon'})) {
                Storage::disk('public')->delete($subCategory->{'sub-category-icon'});
            }
            $data['sub-category-icon'] = $request->file('sub-category-icon')->store('subcategories', 'public');
        }

        $data['status'] = $request->has('status') ? 1 : 0;

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
        $subCategory->status = !$subCategory->status;
        $subCategory->save();
        return $subCategory;
    }

    public function getDataForDataTable()
    {
        return SubCategories::query()->with('category')->latest();
    }
}
