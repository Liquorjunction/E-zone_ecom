<?php

namespace App\Repositories\Admin\Interfaces;

interface DispatchRepositoryInterface
{
    public function getAllDispatches($filters = [], $perPage = 15);
    public function getDispatchById($id);
    public function createDispatch($id, $request);
    public function batchCreateDispatches($request);
    public function updateDispatch($id, $request);
    public function deleteDispatch($id);
    public function getOrderItemQuantity($orderItemId);
    public function getTotalDispatchedQuantity($orderItemId);
    public function getDispatchesForDataTable(array $filters = []);
}
