<?php

namespace App\Repositories\Admin\Interfaces;

interface ManageCustomerRepositoryInterface
{
    public function getAllCustomer($request);
    public function getCustomerById($id);
    public function updateCustomer($request, $id, $data = null);
    public function deleteCustomer($id);
    public function getDataForDataTable($request);
    public function updateCustomerStatus($id);
}
