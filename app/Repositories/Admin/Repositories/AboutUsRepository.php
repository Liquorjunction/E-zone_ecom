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

    public function getActiveAboutUsData()
    {
        return AboutUs::where('status', 'active')->get();
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

    public function getDataForDataTable($request)
    {
        $query = AboutUs::query()->latest();

        // Status filter
        if (isset($request['status']) && in_array($request['status'], ['active', 'inactive'])) {
            $query->where('status', $request['status']);
        }

        // Date range filter
        $startDate = $request['start_date'] ?? $request['date_from'] ?? null;
        $endDate = $request['end_date'] ?? $request['date_to'] ?? null;
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query;
    }
}
