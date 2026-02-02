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

        if ($request->has('search')) {
            $search = $request->input('search');
            $customers->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->has('is_active')) {
            $customers->where('is_active', $request->input('is_active'));
        }

        if ($request->has('date_range')) {
            $dates = explode(',', $request->input('date_range'));
            if (count($dates) == 2) {
                $customers->whereBetween('created_at', [$dates[0], $dates[1]]);
            }
        }

        if ($request->has('sort_by') && $request->has('sort_order')) {
            $customers->orderBy($request->input('sort_by'), $request->input('sort_order'));
        }

        $perPage = $request->input('per_page', 10);
        return $customers->paginate($perPage);
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
