<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Warehouse;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface;

class WarehouseRepository implements WarehouseRepositoryInterface
{
    public function getAllWarehouses()
    {
        return Warehouse::orderBy('created_at', 'desc')->get();
    }

    public function getWarehouseById($id)
    {
        return Warehouse::findOrFail($id);
    }

    public function createWarehouse($request, $data = null)
    {
        $data = $data ?? $request->validated();

        // Assign temporary Warehouse ID (will be updated after creation)
        $data['warehouse_id'] = 'TEMP-' . uniqid();

        // Ensure status is valid enum value
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';

        // Create warehouse first to get the ID
        $warehouse = Warehouse::create($data);

        // Generate warehouse_id based on warehouse name (English) and ID
        $warehouseName = strtoupper(str_replace(' ', '-', $data['warehouse_name']['en']));
        $warehouse->warehouse_id = 'WAREHOUSE-' . $warehouseName . '-' . $warehouse->id;
        $warehouse->save();

        return $warehouse;
    }

    public function updateWarehouse($id, $request, $data = null)
    {
        $warehouse = Warehouse::findOrFail($id);
        $data = $data ?? $request->validated();

        // Ensure status is valid enum value
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';

        $warehouse->update($data);
        return $warehouse;
    }

    public function deleteWarehouse($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return $warehouse->delete();
    }

    public function updateStatus($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->status = ($warehouse->status === 'active') ? 'inactive' : 'active';
        $warehouse->save();
        return $warehouse;
    }

    public function getDataForDataTable()
    {
        return Warehouse::select(['id', 'warehouse_name', 'warehouse_id', 'location', 'status', 'created_at']);
    }
}
