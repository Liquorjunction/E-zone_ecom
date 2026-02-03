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

    public function batchDelete(array $ids)
    {
        $customers = User::whereIn('id', $ids)->get();
        foreach ($customers as $customer) {
            $customer->delete();
        }
        return true;
    }

    public function getDataForDataTable($request)
    {
        $customers = User::query();

        // Status filter
        if ($request->filled('status')) {
            $customers->where('is_active', $request->input('status'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $customers->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $customers->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sorting
        if ($request->has('sort_by') && $request->has('sort_order')) {
            $customers->orderBy($request->input('sort_by'), $request->input('sort_order'));
        }

        return $customers;
    }

    public function updateCustomerStatus($id)
    {
        $customer = User::findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->save();
        return $customer;
    }

    public function batchUpdateStatus(array $ids, $status)
    {
        User::whereIn('id', $ids)->update(['is_active' => $status]);
        return true;
    }
}
