<?php

namespace App\Repositories\Admin\Interfaces;

interface WhyShopWithUsRepositoryInterface
{
    public function getWhyShopWithUsList();
    public function getActiveWhyShopWithUsList();
    public function getWhyShopWithUsById($id);
    public function createWhyShopWithUs($request, $data = null);
    public function updateWhyShopWithUs($request, $data = null, $id);
    public function deleteWhyShopWithUs($id);
    public function changeWhyShopWithUsStatus($id);
    public function getDataForDataTable();
}
