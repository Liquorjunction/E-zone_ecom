<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Models\ProductVariant;
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
        // If caller passed only product_id (product master), try to resolve a variant
        if (empty($data['product_variant_id']) && !empty($data['product_id'])) {
            $variant = ProductVariant::where('product_id', $data['product_id'])->where('status', 'active')->first();
            if ($variant) {
                $data['product_variant_id'] = $variant->id;
            }
            // Remove product_id because Inventory table stores variant reference
            unset($data['product_id']);
        }

        return Inventory::create($data);
    }

    public function update(int $id, array $data)
    {
        $inventory = $this->find($id);
        if ($inventory) {
            // Normalize product_id -> product_variant_id when updating
            if (empty($data['product_variant_id']) && !empty($data['product_id'])) {
                $variant = ProductVariant::where('product_id', $data['product_id'])->where('status', 'active')->first();
                if ($variant) {
                    $data['product_variant_id'] = $variant->id;
                }
                unset($data['product_id']);
            }

            $inventory->update($data);
            return $inventory;
        }
        return null;
    }

    public function updateStatus($id)
    {
        $inventory = $this->find($id);
        if ($inventory) {
            // Toggle between 'active' and 'inactive'. If currently 'depleted' or other, set to 'active'.
            $inventory->status = $inventory->status === 'active' ? 'inactive' : 'active';
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
        $query = Inventory::query();

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            // Filter inventories whose variant belongs to the given product
            $query->whereHas('variant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
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
