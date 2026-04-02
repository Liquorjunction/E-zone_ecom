<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\OrderItem;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function all(array $filters = [])
    {
        return Inventory::with(['variant.product', 'store', 'warehouse'])->latest()->get();
    }

    public function find(int $id)
    {
        return Inventory::with(['variant.product', 'store', 'warehouse'])->find($id);
    }

    public function create(array $data)
    {
        return Inventory::create($data);
    }

    public function update(int $id, array $data)
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            $inventory->update($data);
            return $inventory->refresh();
        }
        return false;
    }

    public function delete(int $id)
    {
        return Inventory::destroy($id);
    }

    public function deleteByBatchNo(string $batchNo)
    {
        return Inventory::where('batch_no', $batchNo)->delete();
    }

    public function getByBatchNo(string $batchNo)
    {
        return Inventory::where('batch_no', $batchNo)->get();
    }

    public function getForReservation(int $productId, ?int $variantId, string $orderDirection = 'asc')
    {
        $query = Inventory::query();
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }
        return $query->orderBy('created_at', $orderDirection)->get();
    }

    public function getForDispatchRelease(int $productId, ?int $variantId, string $orderDirection = 'asc')
    {
        $query = Inventory::query();
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }
        return $query->orderBy('created_at', $orderDirection)->get();
    }

    public function getLatestInward(int $productId, ?int $variantId)
    {
        $query = Inventory::query();
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }
        return $query->orderBy('created_at', 'desc')->first();
    }

    public function isBatchSold(string $batchNo)
    {
        $inventories = Inventory::where('batch_no', $batchNo)->get();

        if ($inventories->isEmpty()) {
            return false;
        }

        if ($inventories->where('reserved_quantity', '>', 0)->count() > 0) {
            return true;
        }

        $inventoryIds = $inventories->pluck('id')->toArray();

        $hasBeenDispatched = InventoryTransaction::whereIn('inventory_id', $inventoryIds)
            ->whereIn('type', ['dispatch', 'sale', 'sell'])
            ->exists();

        return $hasBeenDispatched;
    }

    public function getBatchesForDataTable(array $filters = [])
    {
        $query = Inventory::query()
            ->select('batch_no')
            ->selectRaw('MAX(location_type) as location_type')
            ->selectRaw('MAX(store_id) as store_id')
            ->selectRaw('MAX(warehouse_id) as warehouse_id')
            ->selectRaw('MAX(created_at) as created_at')
            ->selectRaw('MAX(status) as status')
            ->selectRaw('MAX(id) as id')
            ->selectRaw('COUNT(id) as items_count')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(quantity * cost_price) as subtotal_cost')
            ->with(['store', 'warehouse'])
            ->groupBy('batch_no');

        // Location type: warehouse or store
        if (!empty($filters['location_type'])) {
            $query->where('location_type', $filters['location_type']);
        }

        // Specific location ID (store_id or warehouse_id)
        if (!empty($filters['location_id']) && !empty($filters['location_type'])) {
            if ($filters['location_type'] === 'store') {
                $query->where('store_id', $filters['location_id']);
            } elseif ($filters['location_type'] === 'warehouse') {
                $query->where('warehouse_id', $filters['location_id']);
            }
        }

        // Date range
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('created_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ]);
        } elseif (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
        } elseif (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        $batches = $query->get();

        foreach ($batches as $batch) {
            $batch->is_sold = $this->isBatchSold($batch->batch_no);
        }

        // Exclude sold batches if filter is set
        if (!empty($filters['exclude_sold'])) {
            $batches = $batches->filter(fn($batch) => !$batch->is_sold)->values();
        }

        // Completely Sold batches (quantity = 0) can be optionally excluded
        if (!empty($filters['exclude_completely_sold'])) {
            $batches = $batches->filter(fn($batch) => $batch->total_quantity > 0)->values();
        }

        return $batches;
    }

    public function findByProduct(int $productId, ?int $variantId = null, ?int $warehouseId = null, ?int $storeId = null)
    {
        $query = Inventory::query();
        if ($variantId) $query->where('product_variant_id', $variantId);
        else $query->where('product_id', $productId);

        if ($warehouseId) $query->where('warehouse_id', $warehouseId);
        if ($storeId) $query->where('store_id', $storeId);

        return $query->first();
    }

    public function findWithLock(int $id)
    {
        return Inventory::lockForUpdate()->find($id);
    }

    public function getLowStock(array $filters = [])
    {
        $threshold = $filters['threshold'] ?? 10;
        $query = Inventory::with(['variant.product'])
            ->where('quantity', '<=', $threshold)
            ->where('status', 'active');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    public function getLockedForCheckout(int $productId, ?int $variantId)
    {
        $query = Inventory::query();
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }
        return $query->orderBy('created_at', 'asc')->lockForUpdate()->get();
    }

    public function getInventoryForOrderItem(OrderItem $orderItem)
    {
        $query = Inventory::with(['store', 'warehouse'])
            ->where('quantity', '>', 0);

        if ($orderItem->variant_id) {
            $query->where('product_variant_id', $orderItem->variant_id);
        } else {
            $query->where('product_id', $orderItem->product_id)
                ->whereNull('product_variant_id');
        }

        return $query->orderByDesc('quantity')->get();
    }
}
