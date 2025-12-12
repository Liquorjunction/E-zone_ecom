<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = Inventory::query();

        // Apply filters if any
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    public function find(int $id)
    {
        return Inventory::find($id);
    }

    public function create(array $data)
    {
        return Inventory::create($data);
    }

    public function update(int $id, array $data)
    {
        $inventory = $this->find($id);
        if ($inventory) {
            $inventory->update($data);
            return $inventory;
        }
        return null;
    }

    public function updateStatus($id)
    {
        $inventory = $this->find($id);
        if ($inventory) {
            $inventory->status = !$inventory->status;
            $inventory->save();
            return $inventory;
        }
        return null;
    }

    public function delete(int $id)
    {
        $inventory = $this->find($id);
        if ($inventory) {
            return $inventory->delete();
        }
        return false;
    }

    public function getDataforDataTable(array $filters = [])
    {
        $query = Inventory::query();

        // Apply filters if any
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    // Custom queries
    public function findByProduct(int $productId, ?int $variantId = null, ?int $warehouseId = null, ?int $storeId = null)
    {
        $query = Inventory::where('product_id', $productId);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->first();
    }

    public function getLowStock(array $filters = [])
    {
        $query = Inventory::where('quantity', '<', 10); // Example threshold

        // Apply additional filters if any
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }
}
