<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Dispatch;
use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DispatchRespository implements DispatchRepositoryInterface
{
    public function getAllDispatches($filters = [], $perPage = 15)
    {
        $dispatches = Dispatch::with(['order', 'orderItem', 'inventory', 'dispatchedBy', 'store', 'warehouse'])
            ->where($filters)
            ->paginate($perPage);
        return $dispatches;
    }

    public function getDispatchById($id)
    {
        return Dispatch::with(['order', 'orderItem', 'inventory', 'dispatchedBy', 'store', 'warehouse'])->findOrFail($id);
    }

    public function createDispatch($id, $request)
    {
        $data = $request->validated();
        $data['dispatched_by'] = Auth::guard('admin')->id();
        $data['order_item_id'] = $id;
        return Dispatch::create($data);
    }

    public function batchCreateDispatches($request)
    {
        $data = $request->validated();
        $dispatches = [];
        foreach ($data['dispatches'] as $dispatchData) {
            $dispatchData['dispatched_by'] = Auth::guard('admin')->id();
            $dispatches[] = Dispatch::create($dispatchData);
        }
        return $dispatches;
    }

    public function updateDispatch($request, $id)
    {
        $data = $request->validated();
        $dispatch = $this->getDispatchById($id);
        $dispatch->update($data);
        return $dispatch;
    }

    public function deleteDispatch($id)
    {
        $dispatch = $this->getDispatchById($id);
        $dispatch->delete();
        return true;
    }

    public function getTotalDispatchedQuantity($orderItemId)
    {
        return Dispatch::where('order_item_id', $orderItemId)->sum('quantity');
    }

    public function getDispatchesForDataTable(array $filters = [])
    {
        return Dispatch::with(['order', 'orderItem', 'inventory', 'dispatchedBy', 'store', 'warehouse'])
            ->where($filters)
            ->get();
    }

    public function getOrderItemQuantity($orderItemId)
    {
        // Example implementation: sum quantity for the given order item
        return Dispatch::where('order_item_id', $orderItemId)->sum('quantity');
    }
}
