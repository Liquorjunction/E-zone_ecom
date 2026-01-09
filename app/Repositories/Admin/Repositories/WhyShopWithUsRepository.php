<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\WhyShopWithUs;
use App\Repositories\Admin\Interfaces\WhyShopWithUsRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class WhyShopWithUsRepository implements WhyShopWithUsRepositoryInterface
{
    public function getWhyShopWithUsList()
    {
        return WhyShopWithUs::all();
    }

    public function getActiveWhyShopWithUsList()
    {
        return WhyShopWithUs::where('status', true)->get();
    }

    public function getWhyShopWithUsById($id)
    {
        return WhyShopWithUs::find($id);
    }

    public function createWhyShopWithUs($request, $data = null)
    {
        $data = $data ?? (is_array($request) ? $request : $request->validated());

        // Handle icon(image) upload if present
        if (isset($data['icon']) && $data['icon'] instanceof \Illuminate\Http\UploadedFile) {
            $data['icon'] = Storage::disk('public')->putFile('whyshopwithus_icons', $data['icon']);
        }

        return WhyShopWithUs::create($data);

    }

    public function updateWhyShopWithUs($request, $data = null, $id)
    {
        $whyShopWithUs = WhyShopWithUs::findOrFail($id);
        $data = $data ?? (is_array($request) ? $request : $request->validated());

        // Handle icon(image) upload and delete the existing image if present
        if (isset($data['icon']) && $data['icon'] instanceof \Illuminate\Http\UploadedFile) {
            if ($whyShopWithUs->icon && Storage::disk('public')->exists($whyShopWithUs->icon)) {
                Storage::disk('public')->delete($whyShopWithUs->icon);
            }
            $data['icon'] = Storage::disk('public')->putFile('whyshopwithus_icons', $data['icon']);
        }
        $whyShopWithUs->update($data);
        return $whyShopWithUs;
    }

    public function deleteWhyShopWithUs($id)
    {
        $whyShopWithUs = $this->getWhyShopWithUsById($id);
        if ($whyShopWithUs) {
            return $whyShopWithUs->delete();
        }
        return false;
    }

    public function changeWhyShopWithUsStatus($id)
    {
        $whyShopWithUs = $this->getWhyShopWithUsById($id);
        if ($whyShopWithUs) {
            $whyShopWithUs->status = !$whyShopWithUs->status;
            $whyShopWithUs->save();
            return $whyShopWithUs;
        }
        return null;
    }

    public function getDataForDataTable()
    {
        return WhyShopWithUs::select(['id', 'title', 'description', 'icon', 'status', 'created_at', 'updated_at']);
    }
}
