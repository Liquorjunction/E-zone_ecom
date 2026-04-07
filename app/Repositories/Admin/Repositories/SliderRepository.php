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

        // Ensure at least one active slider always exists: if none active yet, make this one active
        if (!HeroSlider::where('is_active', 1)->exists()) {
            $data['is_active'] = 1;
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

        if (!isset($data['is_active'])) {
            $data['is_active'] = $request->has('status') && $request->status == 'active' ? 1 : 0;
        }

        if ($slider->is_active && isset($data['is_active']) && (int) $data['is_active'] === 0) {
            $activeCount = HeroSlider::where('is_active', 1)->count();
            if ($activeCount <= 1) {
                throw new \RuntimeException('At least one hero slider must remain active.');
            }
        }

        // Handle Image Update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $imagePath = $request->file('image')->store('sliders', 'public');
            $data['image'] = $imagePath;
        } else {
            unset($data['image']);
        }

        $slider->update($data);

        return $slider;
    }

    public function deleteSlider($id)
    {
        $slider = HeroSlider::findOrFail($id);

        // Guard: Do not allow deleting the last active slider
        if ($slider->is_active) {
            $activeCount = HeroSlider::where('is_active', 1)->count();
            if ($activeCount <= 1) {
                throw new \RuntimeException('Cannot delete the last active hero slider. Activate another slider first.');
            }
        }

        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        return $slider->delete();
    }

    public function batchDeleteSliders(array $ids)
    {
        // Check if we're trying to delete all active sliders
        $activeSliders = HeroSlider::whereIn('id', $ids)->where('is_active', 1)->get();
        $totalActive = HeroSlider::where('is_active', 1)->count();

        // If all active sliders are being deleted, prevent it
        if ($activeSliders->count() > 0 && $activeSliders->count() >= $totalActive) {
            throw new \RuntimeException('Cannot delete all active sliders. At least one slider must remain active.');
        }

        $sliders = HeroSlider::whereIn('id', $ids)->get();
        foreach ($sliders as $slider) {
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }
            $slider->delete();
        }

        return true;
    }

    public function updateStatus($id)
    {
        $slider = HeroSlider::findOrFail($id);
        // Guard: Do not allow turning off the last active slider
        if ($slider->is_active) {
            $activeCount = HeroSlider::where('is_active', 1)->count();
            if ($activeCount <= 1) {
                throw new \RuntimeException('At least one hero slider must remain active.');
            }
        }

        $slider->is_active = !$slider->is_active;
        $slider->save();

        return $slider;
    }

    public function batchUpdateStatus(array $ids, bool $isActive)
    {
        // If deactivating, ensure at least one slider remains active
        if (!$isActive) {
            $activeCount = HeroSlider::where('is_active', 1)->count();
            $deactivatingCount = HeroSlider::whereIn('id', $ids)->where('is_active', 1)->count();

            if ($activeCount - $deactivatingCount < 1) {
                throw new \RuntimeException('At least one hero slider must remain active.');
            }
        }

        HeroSlider::whereIn('id', $ids)->update(['is_active' => $isActive]);

        return true;
    }

    public function getDataForDataTable($request)
    {
        $query = HeroSlider::query()->latest();

        $status = is_array($request) ? ($request['status'] ?? null) : $request->input('status');

        // Apply filters if needed
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('is_active', $status === 'active');
        }

        return $query;
    }
}
