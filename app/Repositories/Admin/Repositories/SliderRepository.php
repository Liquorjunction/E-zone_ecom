<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\HeroSlider;
use App\Repositories\Admin\Interfaces\SliderRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class SliderRepository implements SliderRepositoryInterface
{
    public function getAllSliders()
    {
        return HeroSlider::orderBy('sort_order', 'asc')->get(); // SoftDeletes automatically excludes deleted records
    }

    public function getSliderById($id)
    {
        return HeroSlider::findOrFail($id);
    }

    public function storeSlider($request)
    {
        $data = $request->validated();

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sliders', 'public');
            $data['image'] = $imagePath;
        }

        // Handle Status (already prepared in Request, but good to be safe)
        // If prepared in request: $data['is_active'] is already 1 or 0.

        return HeroSlider::create($data);
    }

    public function updateSlider($request, $id)
    {
        $slider = HeroSlider::findOrFail($id);
        $data = $request->validated();

        // Handle Image Update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('sliders', 'public');
            $data['image'] = $imagePath;
        } else {
            // Remove image key from data if no new image uploaded so it doesn't overwrite with null
            unset($data['image']);
        }

        $slider->update($data);

        return $slider;
    }

    public function deleteSlider($id)
    {
        $slider = HeroSlider::findOrFail($id);

        // Delete Image from storage
        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        return $slider->delete();
    }

    public function updateStatus($id)
    {
        $slider = HeroSlider::findOrFail($id);
        $slider->is_active = !$slider->is_active;
        $slider->save();

        return $slider;
    }

    public function getDataForDataTable()
    {
        // Return query for Yajra DataTables
        return HeroSlider::query()->latest();
    }
}
