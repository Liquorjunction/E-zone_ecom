<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\ManageCustomerRepositoryInterface;
use App\Models\User;

class ManageCustomerRepository implements ManageCustomerRepositoryInterface
{
    public function getAllCustomer($request)
    {
        $customers = User::get();
        return $customers;
    }

    public function getCustomerById($id)
    {
        return User::find($id);
    }

    public function updateCustomer($request, $id, $data = null)
    {
        $customer = User::findorFail($id);
        $data = $data ?? $request->validated();
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomer($id)
    {
        $customer = User::findorFail($id);
        $customer->delete();
    }

    public function getDataForDataTable($request)
    {
        return User::all();
    }

    public function updateCustomerStatus($id)
    {
        $customer = User::findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->save();
        return $customer;
    }
}
