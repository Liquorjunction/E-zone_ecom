<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\ShowroomImages;
use App\Models\Showrooms;
use App\Repositories\Admin\Interfaces\ShowroomRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ShowroomRepository implements ShowroomRepositoryInterface
{
    public function getShowrooms()
    {
        return Showrooms::all();
    }

    public function getShowroomById($id)
    {
        return Showrooms::find($id);
    }

    public function storeShowroom($request, array $data)
    {
        // We use the $data passed from controller because it contains the auto-translations.

        // 1. Handle Status default
        $data['status'] = $request->input('status', 'inactive');

        // 2. Handle Cover Image upload BEFORE creating logic to get path, but do not use $data['id'] yet.
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('showrooms', 'public');
        }

        $showroom = Showrooms::create($data);

        // 4. Handle Gallery Images (Now we have $showroom->id)
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $imagePath = $image->store('showrooms', 'public');
                ShowroomImages::create([
                    'showroom_id' => $showroom->id, // Use the ID from the created instance
                    'image' => $imagePath,
                    'status' => 'active' // Assuming default status
                ]);
            }
        }

        return $showroom;
    }

    public function updateShowroom($request, $id, array $data)
    {
        $showroom = Showrooms::findOrFail($id);

        // Use $data passed from controller (contains translations)

        // Handle Cover Image Upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($showroom->cover_image && Storage::disk('public')->exists($showroom->cover_image)) {
                Storage::disk('public')->delete($showroom->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('showrooms', 'public');
        }

        // Handle Gallery Images
        if ($request->hasFile('gallery_images')) {
            // Optional: You might want to delete physical files too, not just DB records
            // For now, we keep your logic of replacing entries.
            ShowroomImages::where('showroom_id', $showroom->id)->delete();

            foreach ($request->file('gallery_images') as $image) {
                $imagePath = $image->store('showrooms', 'public');
                ShowroomImages::create([
                    'showroom_id' => $showroom->id,
                    'image' => $imagePath,
                    'status' => 'active'
                ]);
            }
        }

        // Handle Status
        $data['status'] = $request->input('status', 'inactive');

        $showroom->update($data);

        return $showroom;
    }

    public function deleteShowroom($id)
    {
        $showroom = Showrooms::findOrFail($id);

        // Delete cover image
        if ($showroom->cover_image && Storage::disk('public')->exists($showroom->cover_image)) {
            Storage::disk('public')->delete($showroom->cover_image);
        }

        // Ideally, delete physical gallery images here too before deleting DB records
        $galleryImages = ShowroomImages::where('showroom_id', $showroom->id)->get();
        foreach($galleryImages as $img) {
             if (Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
        }
        ShowroomImages::where('showroom_id', $showroom->id)->delete();

        $showroom->delete();

        return $showroom;
    }

    public function updateShowroomStatus($id)
    {
        $showroom = Showrooms::findOrFail($id);
        $showroom->status = $showroom->status == 'active' ? 'inactive' : 'active';
        $showroom->save();

        return $showroom;
    }

    public function getShowroomBySlug($slug)
    {
        return Showrooms::where('slug', $slug)->first();
    }

    public function getDataForDataTable()
    {
        return Showrooms::select('id', 'name', 'status', 'cover_image')->get();
    }
}
