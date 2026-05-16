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
        if (isset($request['status']) && in_array($request['status'], ['active', 'inactive'])) {
            $customers->where('is_active', $request['status'] === 'active');
        }

        // Date range filter
        $startDate = $request['start_date'] ?? $request['date_from'] ?? null;
        $endDate = $request['end_date'] ?? $request['date_to'] ?? null;

        if (!empty($startDate) && !empty($endDate)) {
            $customers->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        } elseif (!empty($startDate)) {
            $customers->whereDate('created_at', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $customers->whereDate('created_at', '<=', $endDate);
        }

        // Email verified filter
        if (!empty($request['email_verified']) || (($request['verified'] ?? null) === 'verified')) {
            $customers->whereNotNull('email_verified_at');
        } elseif (($request['verified'] ?? null) === 'unverified') {
            $customers->whereNull('email_verified_at');
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
