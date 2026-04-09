<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Categories;
use Illuminate\Validation\ValidationException;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories()
    {
        return Categories::all();
    }

    public function getActiveCategories()
    {
        return Categories::where('status', 'active')->get();
    }

    public function getCategoryById($id)
    {
        return Categories::find($id);
    }

    public function storeCategory($request, $data = null)
    {
        // Use passed data (with translations) or fallback
        $data = $data ?? $request->validated();

        // Duplicate check for English name
        $english = data_get($data, 'category-name.en');
        if ($english) {
            $exists = Categories::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(`category-name`, '$.en')) = ?", [$english])->exists();
            if ($exists) {
                throw ValidationException::withMessages(['category-name.en' => 'The English category name has already been taken.']);
            }
        }

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

        // Duplicate check for English name (exclude current)
        $english = data_get($data, 'category-name.en');
        if ($english) {
            $query = Categories::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(`category-name`, '$.en')) = ?", [$english]);
            $query->where('id', '!=', $id);
            if ($query->exists()) {
                throw ValidationException::withMessages(['category-name.en' => 'The English category name has already been taken.']);
            }
        }

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

    public function getDataForDataTable($request = null)
    {
        $query = Categories::withCount('subcategories');

        if (isset($request['status']) && $request['status'] !== '') {
            $query->where('status', $request['status'] == '1' ? 'active' : 'inactive');
        }

        return $query;
    }

    public function getCategoryList($term = null)
    {
        $query = Categories::where('status', 'active');

        if (!empty($term)) {
            $like = '%' . $term . '%';
            $query->where(function ($builder) use ($like) {
                $builder->orWhere('category-name->en', 'LIKE', $like)
                    ->orWhere('category-name->fr', 'LIKE', $like)
                    ->orWhere('category-name->ar', 'LIKE', $like);
            });
        }

        return $query->get(['id', 'category-name']);
    }
}
