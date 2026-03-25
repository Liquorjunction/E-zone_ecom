<?php

namespace App\Repositories\Admin\Interfaces;

interface DispatchRepositoryInterface
{
    public function getAllDispatches(array $filters = [], int $perPage = 15);
    public function getDispatchById(int $id);
    public function createDispatch(array $data);
    public function updateDispatch(int $id, array $data);
    public function getDispatchesByOrderId(int $orderId);
    public function getDispatchesByNamefromAdminTable(string $name);
    public function getDataforDatatable();
}
