<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\AboutUsRepositoryInterface;
use App\Models\AboutUs;

class AboutUsRepository implements AboutUsRepositoryInterface
{
    public function getAboutUsData()
    {
        return AboutUs::all();
    }

    public function storeAboutUsData($request, $data = null)
    {
        $data = $data ?? $request->validated();

        return AboutUs::create($data);
    }

    public function updateAboutUsData($id, $request, $data = null)
    {
        $aboutUs = AboutUs::findOrFail($id);
        $data = $data ?? $request->validated();

        $aboutUs->update($data);

        return $aboutUs;
    }

    public function destroyAboutUsData($id)
    {
        return AboutUs::findOrFail($id)->delete();
    }

    public function toggleAboutUsStatus($id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        $aboutUs->status = $aboutUs->status === 'active' ? 'inactive' : 'active';
        $aboutUs->save();

        return $aboutUs;
    }

    public function getDataForDataTable()
    {
        return AboutUs::query()->latest();
    }
}
