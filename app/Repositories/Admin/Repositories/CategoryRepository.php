<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Categories;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories()
    {
        return Categories::orderBy('id', 'desc')->get();
    }

    public function getCategoryById($id)
    {
        return Categories::find($id);
    }

    public function storeCategory($request)
    {
        $data = $request->validated();

        if ($request->hasFile('category-icon')) {
            $data['category-icon'] = $request->file('category-icon')->store('categories', 'public');
        }

        // FIX: Save 'active'/'inactive' instead of 1/0
        $data['status'] = $request->has('status') ? 'active' : 'inactive';

        return Categories::create($data);
    }

    public function updateCategory($request, $id)
    {
        $category = Categories::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('category-icon')) {
            if ($category->{'category-icon'} && Storage::disk('public')->exists($category->{'category-icon'})) {
                Storage::disk('public')->delete($category->{'category-icon'});
            }
            $data['category-icon'] = $request->file('category-icon')->store('categories', 'public');
        }

        // Status logic here was already correct in your file, keeping it consistent
        $data['status'] = $request->has('status') ? 'active' : 'inactive';

        $category->update($data);
        return $category;
    }

    public function deleteCategory($id)
    {
        $category = Categories::findOrFail($id);
        if ($category->{'category-icon'} && Storage::disk('public')->exists($category->{'category-icon'})) {
            Storage::disk('public')->delete($category->{'category-icon'});
        }
        return $category->delete();
    }

    public function updateStatus($id)
    {
        $category = Categories::findOrFail($id);
        $category->status = $category->status === 'active' ? 'inactive' : 'active';
        $category->save();
        return $category;
    }

    public function getDataForDataTable()
    {
        return Categories::query()->latest();
    }
}
