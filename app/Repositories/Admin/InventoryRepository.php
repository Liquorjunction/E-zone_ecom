<?php

namespace App\Repositories\Admin;

use App\Models\Inventory;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function all(array $filters = [])
    {
        return Inventory::query()->filter($filters)->get();
    }

    public function find(int $id)
    {
        return Inventory::findOrFail($id);
    }

    public function create(array $data)
    {
        // Defensive: ensure only one of warehouse_id or store_id is provided
        if (!empty($data['warehouse_id']) && !empty($data['store_id'])) {
            throw new \InvalidArgumentException('Provide either warehouse_id or store_id, not both.');
        }
        return Inventory::create($data);
    }

    public function update(int $id, array $data)
    {
        $inventory = $this->find($id);
        if (!empty($data['warehouse_id']) && !empty($data['store_id'])) {
            throw new \InvalidArgumentException('Provide either warehouse_id or store_id, not both.');
        }
        $inventory->update($data);
        return $inventory;
    }

    public function updateStatus($id)
    {
        $inventory = $this->find($id);
        $inventory->status = $inventory->status === 'active' ? 'inactive' : 'active';
        $inventory->save();
        return $inventory;
    }

    public function delete(int $id)
    {
        $inventory = $this->find($id);
        return $inventory->delete();
    }

    public function getDataforDataTable(array $filters = [])
    {
        return Inventory::query()->filter($filters);
    }

    public function findByProduct(int $productId, ?int $variantId = null, ?int $warehouseId = null, ?int $storeId = null)
    {
        $query = Inventory::where('product_id', $productId);
        if ($variantId) $query->where('variant_id', $variantId);
        if ($warehouseId) $query->where('warehouse_id', $warehouseId);
        if ($storeId) $query->where('store_id', $storeId);
        return $query->first();
    }

    public function getLowStock(array $filters = [])
    {
        return Inventory::query()->where('stock', '<', 10)->filter($filters)->get();
    }
}
