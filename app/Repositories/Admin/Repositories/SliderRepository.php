<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\HeroSlider;
use App\Repositories\Admin\Interfaces\SliderRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class SliderRepository implements SliderRepositoryInterface
{
    public function getAllSliders()
    {
        return HeroSlider::orderBy('sort_order', 'asc')->get();
    }

    public function getActiveSliders()
    {
        return HeroSlider::where('is_active', 1)->orderBy('sort_order', 'asc')->get();
    }

    public function getSliderById($id)
    {
        return HeroSlider::findOrFail($id);
    }

    public function storeSlider($request, $data = null)
    {
        // Use passed data (with translations) or fallback to validated request data
        $data = $data ?? $request->validated();

        // Ensure status is properly formatted if not already (checkbox handling)
        if (!isset($data['is_active'])) {
            $data['is_active'] = $request->has('status') && $request->status == 'active' ? 1 : 0;
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sliders', 'public');
            $data['image'] = $imagePath;
        }

        return HeroSlider::create($data);
    }

    public function updateSlider($request, $id, $data = null)
    {
        $slider = HeroSlider::findOrFail($id);
        $data = $data ?? $request->validated();

        // Ensure status is properly formatted
        if (!isset($data['is_active'])) {
            $data['is_active'] = $request->has('status') && $request->status == 'active' ? 1 : 0;
        }

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
            // Prevent null override if no image is uploaded
            unset($data['image']);
        }

        $slider->update($data);

        return $slider;
    }

    public function deleteSlider($id)
    {
        $slider = HeroSlider::findOrFail($id);

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
        return HeroSlider::query()->latest();
    }
}
