<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Models\InventoryTransaction; // Optional if you implement history later
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryRepository implements InventoryRepositoryInterface
{
    /**
     * Handle bulk inventory creation.
     * Generates a unique Batch No if not provided.
     */
    public function createBatch(array $batchData)
    {
        return DB::transaction(function () use ($batchData) {
            // 1. Determine Batch Number (Use existing if update, or generate new)
            $batchNo = $batchData['batch_no'] ?? 'BATCH-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $locationType = $batchData['location_type'];
            $warehouseId = ($locationType === 'warehouse') ? $batchData['warehouse_id'] : null;
            $storeId = ($locationType === 'store') ? $batchData['store_id'] : null;
            $status = $batchData['status'] ?? 'active';
            $notes = $batchData['notes'] ?? null;

            $createdItems = [];

            foreach ($batchData['items'] as $item) {
                // Skip invalid rows
                if (empty($item['product_id']) || !isset($item['quantity'])) {
                    continue;
                }

                $createdItems[] = Inventory::create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'location_type' => $locationType,
                    'warehouse_id' => $warehouseId,
                    'store_id' => $storeId,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'] ?? 0,
                    'batch_no' => $batchNo,
                    'status' => $status,
                    'notes' => $notes,
                ]);
            }

            return $createdItems;
        });
    }

    /**
     * Get grouped batches for the DataTable.
     * Groups by 'batch_no' to show one row per Inward Entry.
     */
    public function getBatchesForDataTable(array $filters = [])
    {
        // We select batch_no and use Aggregate functions (MAX) for other fields
        // to prevent MySQL "Full Group By" strict mode errors.
        $query = Inventory::query()
            ->select(
                'batch_no',
                DB::raw('MAX(location_type) as location_type'),
                DB::raw('MAX(store_id) as store_id'),
                DB::raw('MAX(warehouse_id) as warehouse_id'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(status) as status'),
                DB::raw('MAX(id) as id'), // Valid ID for generating edit/delete routes
                DB::raw('COUNT(id) as items_count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->with(['store', 'warehouse']) // Eager load relationships for location names
            ->groupBy('batch_no');

        // Apply filters
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->having('status', $filters['status']);
        }

        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where('batch_no', 'like', "%{$search}%");
        }

        return $query;
    }

    /**
     * Get all inventory records.
     */
    public function all(array $filters = [])
    {
        return Inventory::with(['variant.product', 'store', 'warehouse'])->latest()->get();
    }

    /**
     * Find a specific inventory record.
     */
    public function find(int $id)
    {
        return Inventory::with(['variant.product', 'store', 'warehouse'])->find($id);
    }

    /**
     * Create a single inventory record.
     */
    public function create(array $data)
    {
        // Wrap single create in batch logic to ensure consistency
        $batchData = array_merge($data, [
            'items' => [
                [
                    'product_id' => $data['product_id'],
                    'product_variant_id' => $data['product_variant_id'] ?? null,
                    'quantity' => $data['quantity'],
                    'cost_price' => $data['cost_price'] ?? 0,
                ]
            ]
        ]);

        return $this->createBatch($batchData);
    }

    /**
     * Update an existing inventory record.
     */
    public function update(int $id, array $data)
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            return $inventory->update($data);
        }
        return false;
    }

    /**
     * Toggle status.
     */
    public function updateStatus($id)
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            $newStatus = $inventory->status === 'active' ? 'inactive' : 'active';
            return $inventory->update(['status' => $newStatus]);
        }
        return false;
    }

    /**
     * Delete an inventory record.
     */
    public function delete(int $id)
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            return $inventory->delete();
        }
        return false;
    }

    /**
     * Get Data for DataTable (Non-grouped / Single items view).
     * Only used if you switch back to listing individual items.
     */
    public function getDataforDataTable(array $filters = [])
    {
        $query = Inventory::query()->with(['variant.product', 'store', 'warehouse']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Find a specific stock entry by product and location.
     */
    public function findByProduct(int $productId, ?int $variantId = null, ?int $warehouseId = null, ?int $storeId = null)
    {
        $query = Inventory::query();

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->first();
    }

    /**
     * Retrieve items that are below a certain quantity threshold.
     */
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
