<?php


namespace App\Repositories\Admin\Interfaces;
interface WarehouseRepositoryInterface
{
    public function getAllWarehouses();
    public function getActiveWarehouses();
    public function getWarehouseById($id);
    public function createWarehouse($request, $data = null);
    public function updateWarehouse($id, $request, $data = null);
    public function deleteWarehouse($id);
    public function updateStatus($id);
    public function getDataForDataTable($request = null);
}
