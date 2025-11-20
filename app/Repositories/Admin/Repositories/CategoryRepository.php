<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Categories;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories()
    {
        return Categories::orderBy('sort_order', 'asc')->get();
    }

    public function getCategoryById($id)
    {
        return Categories::find($id);
    }

    public function storeCategory($request)
    {
        return Categories::create($request->validated());
    }

    public function updateCategory($request, $id)
    {
        $category = Categories::findOrFail($id);
        $category->update($request->validated());

        return $category;
    }

    public function deleteCategory($id)
    {
        return Categories::findOrFail($id)->delete();
    }

    public function updateStatus($id)
    {
        $category = Categories::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return $category;
    }

    public function getDataForDataTable()
    {
        // Return query for Yajra DataTables
        return Categories::query()->latest();
    }

}
