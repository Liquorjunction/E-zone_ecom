<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\BrandRepositoryInterface;

use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandRepository implements BrandRepositoryInterface
{
    public function getBrands()
    {
        return Brand::all();
    }

    public function getActiveBrands()
    {
        return Brand::where('status', 'active')->get();
    }

    public function createBrand($request, $data = null)
    {
        $data = $data ?? $request->validated();

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('brands', 'public');
            $data['image'] = $imagePath;
        }

        return Brand::create($data);
    }

    public function updateBrand($request, $id, $data = null)
    {
        $brand = Brand::findOrFail($id);
        $data = $data ?? $request->validated();

        // Handle Image Update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('brands', 'public');
            $data['image'] = $imagePath;
        } else {
            // Prevent null override if no image is uploaded
            unset($data['image']);
        }

        $brand->update($data);

        return $brand;
    }

    public function deleteBrand($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->image && Storage::disk('public')->exists($brand->image)) {
            Storage::disk('public')->delete($brand->image);
        }

        return $brand->delete();
    }

    public function getBrand($id)
    {
        return Brand::findOrFail($id);
    }

    public function updateBrandStatus($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->status = $brand->status === 'active' ? 'inactive' : 'active';
        $brand->save();

        return $brand;
    }

    public function getDataForDatatable($request = null)
    {
        $query = Brand::withCount('products');

        if (isset($request['status']) && $request['status'] !== '') {
            $query->where('status', $request['status'] == '1' ? 'active' : 'inactive');
        }

        return $query;
    }

    public function getBrandList($term = null)
    {
        $query = Brand::where('status', 'active');

        if (!empty($term)) {
            $like = '%' . $term . '%';
            $query->where(function ($builder) use ($like) {
                $builder->orWhere('name->en', 'LIKE', $like)
                    ->orWhere('name->fr', 'LIKE', $like)
                    ->orWhere('name->ar', 'LIKE', $like);
            });
        }

        return $query->get(['id', 'name']);
    }
}
