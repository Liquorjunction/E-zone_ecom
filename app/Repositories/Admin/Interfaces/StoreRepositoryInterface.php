<?php

namespace App\Repositories\Admin\Interfaces;

interface StoreRepositoryInterface
{
    public function getAllStores();
    public function getAllActiveStores();
    public function getStoreById($id);
    public function createStore($request, $data = null);
    public function updateStore($id, $request, $data = null);
    public function deleteStore($id);
    public function updateStatus($id);
    public function getDataForDataTable();
    public function getStoreList($term = null);
}
