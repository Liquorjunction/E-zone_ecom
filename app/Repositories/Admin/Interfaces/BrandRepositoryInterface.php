<?php

namespace App\Repositories\Admin\Interfaces;

interface BrandRepositoryInterface
{
    public function getBrands();
    public function getActiveBrands();
    public function createBrand($request, $data = null);
    public function updateBrand($request, $id, $data = null);
    public function deleteBrand($id);
    public function getBrand($id);
    public function updateBrandStatus($id);
    public function getDataforDatatable($request = null);
    public function getBrandList($term = null);
}
