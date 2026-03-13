<?php

namespace App\Repositories\Admin\Interfaces;

interface CitiesRepositoryInterface
{
    public function getAllCities();
    public function getActiveCities();
    public function findCityById($id);
    public function createCity($request, $data = null);
    public function updateCity($id, $request, $data = null);
    public function deleteCity($id);
    public function toggleCityStatus($id);
    public function getDataForDataTable();
}
