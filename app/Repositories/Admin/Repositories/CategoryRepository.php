<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Categories;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories()
    {
        return Categories::all();
    }

    public function getCategoryById($id)
    {
        return Categories::find($id);
    }

    public function storeCategory($request, $data = null)
    {
        // Use passed data (with translations) or fallback
        $data = $data ?? $request->validated();

        // Handle Image Upload
        if ($request->hasFile('category-icon')) {
            $imagePath = $request->file('category-icon')->store('categories', 'public');
            $data['category-icon'] = $imagePath;
        }

        // Handle Status
        $data['status'] = $request->input('status', 'inactive');

        return Categories::create($data);
    }

    public function updateCategory($request, $id, $data = null)
    {
        $category = Categories::findOrFail($id);
        $data = $data ?? $request->validated();

        // Handle Image Update
        if ($request->hasFile('category-icon')) {
            if ($category->{'category-icon'} && Storage::disk('public')->exists($category->{'category-icon'})) {
                Storage::disk('public')->delete($category->{'category-icon'});
            }

            $imagePath = $request->file('category-icon')->store('categories', 'public');
            $data['category-icon'] = $imagePath;
        } else {
            unset($data['category-icon']);
        }

        // Handle Status
        $data['status'] = $request->input('status', 'inactive');

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
        $category->status = ($category->status === 'active') ? 'inactive' : 'active';
        $category->save();

        return $category;
    }

    public function getDataForDataTable()
    {
        return Categories::query()->latest();
    }
}
