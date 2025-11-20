<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\SubCategories;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function getAllSubCategories()
    {
        return SubCategories::orderBy('sort_order', 'asc')->get();
    }

    public function getSubCategoryById($id)
    {
        return SubCategories::find($id);
    }

    public function storeSubCategory($request)
    {
        return SubCategories::create($request->validated());
    }

    public function updateSubCategory($request, $id)
    {
        $subCategory = SubCategories::findOrFail($id);
        $subCategory->update($request->validated());

        return $subCategory;
    }

    public function deleteSubCategory($id)
    {
        return SubCategories::findOrFail($id)->delete();
    }

    public function updateStatus($id)
    {
        $subCategory = SubCategories::findOrFail($id);
        $subCategory->is_active = !$subCategory->is_active;
        $subCategory->save();

        return $subCategory;
    }

    public function getDataForDataTable()
    {
        // Return query for Yajra DataTables
        return SubCategories::query()->latest();
    }

}
