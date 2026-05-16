<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Admin;
use App\Models\Dispatch;
use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface;

class DispatchRepository implements DispatchRepositoryInterface
{
    public function getAllDispatches(array $filters = [], int $perPage = 15)
    {
        $query = Dispatch::with(['order', 'orderItem.product', 'inventory.store', 'inventory.warehouse', 'dispatchedBy']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['order_number'])) {
             $query->whereHas('order', function ($q) use ($filters) {
                 $q->where('order_number', 'like', '%' . $filters['order_number'] . '%');
             });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getDispatchById(int $id)
    {
        return Dispatch::with(['order', 'orderItem.product', 'inventory.store', 'inventory.warehouse', 'dispatchedBy'])->findOrFail($id);
    }

    public function createDispatch(array $data)
    {
        return Dispatch::create($data);
    }

    public function updateDispatch(int $id, array $data)
    {
        $dispatch = $this->getDispatchById($id);
        $dispatch->update($data);

        return $dispatch;
    }

    public function getDispatchesByOrderId(int $orderId)
    {
        return Dispatch::with(['orderItem', 'inventory'])->where('order_id', $orderId)->get();
    }

    public function getDispatchesByNamefromAdminTable(string $name)
    {
        $dispatchById = Dispatch::where('dispatched_by', $name)->value('dispatched_by');
        $admin = Admin::find($dispatchById);
        return $admin->name;
    }

    public function getDispatchStatusList()
    {
        return Dispatch::select('status')
            ->whereNotNull('status')
            ->whereRaw("TRIM(status) != ''")
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values()
            ->all();
    }

    public function getDataforDatatable()
    {
        $query = Dispatch::with(['order', 'orderItem.product', 'inventory.store', 'inventory.warehouse', 'dispatchedBy'])
            ->select('order_dispatch.*');

        return $query;
    }
}
