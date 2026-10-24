<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\BrandRepositoryInterface;

use App\Models\Brand;
use App\Models\BrandBanner;
use App\Models\BrandCategoryShowcase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        return DB::transaction(function () use ($request, $data) {
            $data = $data ?? ($request instanceof \Illuminate\Foundation\Http\FormRequest ? $request->validated() : $request->all());

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('brands', 'public');
                $data['image'] = $imagePath;
            }

            $brandData = collect($data)->only(['name', 'image', 'slug', 'tagline', 'description', 'status'])->toArray();
            $brand = Brand::create($brandData);

            $this->syncBanners($brand, $request);
            $this->syncCategoryShowcases($brand, $request, $data);

            return $brand;
        });
    }

    public function updateBrand($request, $id, $data = null)
    {
        return DB::transaction(function () use ($request, $id, $data) {
            $brand = Brand::findOrFail($id);
            $data = $data ?? ($request instanceof \Illuminate\Foundation\Http\FormRequest ? $request->validated() : $request->all());

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

            $brandData = collect($data)->only(['name', 'image', 'slug', 'tagline', 'description', 'status'])->toArray();
            $brand->update($brandData);

            $this->syncBanners($brand, $request);
            $this->syncCategoryShowcases($brand, $request, $data);

            return $brand;
        });
    }

    protected function syncBanners(Brand $brand, $request)
    {
        $bannersData = $request->input('banners', []);
        if (!is_array($bannersData)) {
            return;
        }

        foreach ($bannersData as $key => $bannerData) {
            $bannerId = $bannerData['id'] ?? null;
            $isDelete = !empty($bannerData['_delete']) && ($bannerData['_delete'] == '1' || $bannerData['_delete'] === true);

            if ($bannerId) {
                $banner = $brand->banners()->find($bannerId);
                if ($banner) {
                    if ($isDelete) {
                        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                            Storage::disk('public')->delete($banner->image);
                        }
                        $banner->delete();
                        continue;
                    }

                    // Handle banner image update
                    if ($request->hasFile("banners.{$key}.image")) {
                        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                            Storage::disk('public')->delete($banner->image);
                        }
                        $banner->image = $request->file("banners.{$key}.image")->store('brands/banners', 'public');
                    }

                    $banner->alt_text = $bannerData['alt_text'] ?? $banner->alt_text;
                    $banner->sort_order = isset($bannerData['sort_order']) ? (int)$bannerData['sort_order'] : $banner->sort_order;
                    $banner->status = $bannerData['status'] ?? $banner->status;
                    $banner->save();
                }
            } else {
                // New banner
                if ($isDelete) {
                    continue;
                }

                $imagePath = null;
                if ($request->hasFile("banners.{$key}.image")) {
                    $imagePath = $request->file("banners.{$key}.image")->store('brands/banners', 'public');
                }

                if ($imagePath) {
                    $brand->banners()->create([
                        'image' => $imagePath,
                        'alt_text' => $bannerData['alt_text'] ?? null,
                        'sort_order' => isset($bannerData['sort_order']) ? (int)$bannerData['sort_order'] : 0,
                        'status' => $bannerData['status'] ?? 'active',
                    ]);
                }
            }
        }
    }

    protected function syncCategoryShowcases(Brand $brand, $request, $data = null)
    {
        $showcasesData = (isset($data['showcases']) && is_array($data['showcases']))
            ? $data['showcases']
            : $request->input('showcases', []);
        if (!is_array($showcasesData)) {
            return;
        }

        foreach ($showcasesData as $key => $showcaseData) {
            $showcaseId = $showcaseData['id'] ?? null;
            $isDelete = !empty($showcaseData['_delete']) && ($showcaseData['_delete'] == '1' || $showcaseData['_delete'] === true);

            if ($showcaseId) {
                $showcase = $brand->categoryShowcases()->find($showcaseId);
                if ($showcase) {
                    if ($isDelete) {
                        if ($showcase->image && Storage::disk('public')->exists($showcase->image)) {
                            Storage::disk('public')->delete($showcase->image);
                        }
                        $showcase->delete();
                        continue;
                    }

                    // Handle showcase image update
                    if ($request->hasFile("showcases.{$key}.image")) {
                        if ($showcase->image && Storage::disk('public')->exists($showcase->image)) {
                            Storage::disk('public')->delete($showcase->image);
                        }
                        $showcase->image = $request->file("showcases.{$key}.image")->store('brands/showcases', 'public');
                    }

                    $showcase->category_id = !empty($showcaseData['category_id']) ? $showcaseData['category_id'] : null;
                    $showcase->sub_category_id = !empty($showcaseData['sub_category_id']) ? $showcaseData['sub_category_id'] : null;
                    $showcase->title = $showcaseData['title'] ?? $showcase->title;
                    $showcase->description = $showcaseData['description'] ?? $showcase->description;
                    $showcase->button_text = array_key_exists('button_text', $showcaseData) ? $showcaseData['button_text'] : $showcase->button_text;
                    $showcase->button_url = !empty($showcaseData['button_url']) ? $showcaseData['button_url'] : null;
                    $showcase->sort_order = isset($showcaseData['sort_order']) ? (int)$showcaseData['sort_order'] : $showcase->sort_order;
                    $showcase->status = $showcaseData['status'] ?? $showcase->status;

                    if (empty($showcase->slug) && !empty($showcaseData['title']['en'])) {
                        $showcase->slug = Str::slug($showcaseData['title']['en']);
                    }

                    $showcase->save();
                }
            } else {
                // New showcase
                if ($isDelete) {
                    continue;
                }

                $imagePath = null;
                if ($request->hasFile("showcases.{$key}.image")) {
                    $imagePath = $request->file("showcases.{$key}.image")->store('brands/showcases', 'public');
                }

                $titleEn = $showcaseData['title']['en'] ?? 'category';
                $slug = Str::slug($titleEn);

                $brand->categoryShowcases()->create([
                    'category_id' => !empty($showcaseData['category_id']) ? $showcaseData['category_id'] : null,
                    'sub_category_id' => !empty($showcaseData['sub_category_id']) ? $showcaseData['sub_category_id'] : null,
                    'title' => $showcaseData['title'] ?? null,
                    'description' => $showcaseData['description'] ?? null,
                    'button_text' => $showcaseData['button_text'] ?? null,
                    'button_url' => $showcaseData['button_url'] ?? null,
                    'image' => $imagePath,
                    'slug' => $slug,
                    'sort_order' => isset($showcaseData['sort_order']) ? (int)$showcaseData['sort_order'] : 0,
                    'status' => $showcaseData['status'] ?? 'active',
                ]);
            }
        }
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
