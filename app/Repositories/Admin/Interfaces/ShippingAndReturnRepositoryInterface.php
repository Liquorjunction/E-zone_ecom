<?php

namespace App\Repositories\Admin\Interfaces;

interface ShippingAndReturnRepositoryInterface
{
    public function getAllShippingAndReturns();
    public function getActiveShippingAndReturns();
    public function getShippingAndReturnById($id);
    public function createShippingAndReturn($request, $data = null);
    public function updateShippingAndReturn($id, $request, $data = null);
    public function deleteShippingAndReturn($id);
    public function updateStatus($id);
    public function getDataForDataTable();
}
