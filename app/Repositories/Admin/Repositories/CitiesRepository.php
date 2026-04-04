<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\city;
use App\Repositories\Admin\Interfaces\CitiesRepositoryInterface;

class CitiesRepository implements CitiesRepositoryInterface
{
    public function getAllCities()
    {
        return city::all();
    }

    public function getActiveCities()
    {
        return city::where('status', 'active')->get();
    }

    public function findCityById($id)
    {
        return city::findOrFail($id);
    }

    public function createCity($request, $data = null)
    {
        $data = $data ?? $request->validated();
        return city::create($data);
    }

    public function updateCity($id, $request, $data = null)
    {
        $city = city::findOrFail($id);
        $data = $data ?? $request->validated();
        $city->update($data);
        return $city;
    }

    public function deleteCity($id)
    {
        $city = city::findOrFail($id);
        return $city->delete();
    }

    public function toggleCityStatus($id)
    {
        $city = city::findOrFail($id);
        $city->status = ($city->status === 'active') ? 'inactive' : 'active';
        $city->save();
        return $city;
    }

    public function getDataForDataTable($request = null)
    {
        $query = city::query()->latest();

        if (isset($request['status']) && in_array($request['status'], ['active', 'inactive'])) {
            $query->where('status', $request['status']);
        }

        return $query;

    }
}
