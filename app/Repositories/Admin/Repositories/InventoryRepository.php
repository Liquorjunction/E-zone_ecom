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
            ->select(
                'batch_no',
                DB::raw('MAX(location_type) as location_type'),
                DB::raw('MAX(store_id) as store_id'),
                DB::raw('MAX(warehouse_id) as warehouse_id'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(status) as status'),
                DB::raw('MAX(id) as id'),
                DB::raw('COUNT(id) as items_count'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * cost_price) as subtotal_cost')
            )
            ->with(['store', 'warehouse'])
            ->groupBy('batch_no');

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->having('status', $filters['status']);
        }

        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where('batch_no', 'like', "%{$search}%");
        }

        $batches = $query->get();
        foreach ($batches as $batch) {
            $batch->is_sold = $this->isBatchSold($batch->batch_no);
        }
        return $batches;
    }

    public function getDataforDataTable(array $filters = [])
    {
        $query = Inventory::query()->with(['variant.product', 'store', 'warehouse'])->withCount('orderItems');
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        return $query;
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
}
