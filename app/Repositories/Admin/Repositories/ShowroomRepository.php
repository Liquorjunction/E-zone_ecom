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

    public function getActiveShowrooms()
    {
        return Showrooms::where('status', 'active')->get();
    }

    public function getShowroomById($id)
    {
        return Showrooms::find($id);
    }

    public function storeShowroom($request, array $data)
    {

        $data['status'] = $request->input('status', 'inactive');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('showrooms', 'public');
        }

        $showroom = Showrooms::create($data);


        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $imagePath = $image->store('showrooms', 'public');
                ShowroomImages::create([
                    'showroom_id' => $showroom->id,
                    'image' => $imagePath,
                    'status' => 'active'
                ]);
            }
        }

        return $showroom;
    }

    public function updateShowroom($request, $id, array $data)
    {
        $showroom = Showrooms::findOrFail($id);

        if ($request->hasFile('cover_image')) {
            if ($showroom->cover_image && Storage::disk('public')->exists($showroom->cover_image)) {
                Storage::disk('public')->delete($showroom->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('showrooms', 'public');
        }

        if ($request->hasFile('gallery_images')) {
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

        // Delete gallery images
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
        return Showrooms::select('id', 'name', 'description', 'status', 'cover_image')->get();
    }

    public function storeShowroomImage($request, $showroomId)
    {
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $imagePath = $image->store('showrooms', 'public');
                ShowroomImages::create([
                    'showroom_id' => $showroomId,
                    'image' => $imagePath,
                    'status' => 'active'
                ]);
            }
        }
    }

    public function deleteShowroomImage($imageId)
    {
        $image = ShowroomImages::findOrFail($imageId);

        if (Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();
    }
}
