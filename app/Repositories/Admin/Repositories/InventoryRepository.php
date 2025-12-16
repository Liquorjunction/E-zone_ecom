<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryRepository implements InventoryRepositoryInterface
{
    /**
     * Handle bulk inventory creation with transactions.
     */
    public function createBatch(array $batchData)
    {
        return DB::transaction(function () use ($batchData) {
            // 1. Generate a Global Batch Number for this Import
            $batchNo = 'BATCH-'.date('Ymd').'-'.strtoupper(Str::random(4));

            $locationType = $batchData['location_type'];
            $warehouseId = ($locationType === 'warehouse') ? $batchData['warehouse_id'] : null;
            $storeId = ($locationType === 'store') ? $batchData['store_id'] : null;
            $adminId = Auth::guard('admin')->id(); // Track who performed the action

            foreach ($batchData['items'] as $item) {
                // 2. Define Unique Key for Stock (Product + Variant + Location)
                $conditions = [
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'location_type' => $locationType,
                    'warehouse_id' => $warehouseId,
                    'store_id' => $storeId,
                ];

                // 3. Find existing stock or prepare new instance
                $inventory = Inventory::firstOrNew($conditions);

                // Set defaults if new
                if (! $inventory->exists) {
                    $inventory->quantity = 0;
                    $inventory->status = 'active';
                }

                // 4. Update Aggregate Inventory
                $inventory->quantity += $item['quantity'];
                $inventory->cost_price = $item['cost_price']; // Update to latest cost
                $inventory->batch_no = $batchNo; // Update reference to latest batch
                $inventory->notes = $batchData['notes'] ?? $inventory->notes;
                $inventory->save();

                // 5. Create Transaction Record (History)
                InventoryTransaction::create([
                    'inventory_id' => $inventory->id,
                    'type' => 'inward',
                    'quantity' => $item['quantity'],
                    'rate' => $item['cost_price'],
                    'batch_no' => $batchNo,
                    'created_by' => $adminId,
                    'description' => $batchData['notes'] ?? 'Batch Import',
                ]);
            }

            return true;
        });
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
        return Inventory::with(['variant.product', 'store', 'warehouse', 'transactions'])
            ->findOrFail($id);
    }

    /**
     * Create a single inventory record.
     * Wraps the logic into a "batch of one" to ensure transactions are created.
     */
    public function create(array $data)
    {
        // Transform single data structure to batch structure to reuse logic
        $batchData = [
            'location_type' => $data['location_type'],
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'store_id' => $data['store_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'items' => [
                [
                    'product_id' => $data['product_id'],
                    'product_variant_id' => $data['product_variant_id'] ?? null,
                    'quantity' => $data['quantity'],
                    'cost_price' => $data['cost_price'] ?? 0,
                ],
            ],
        ];

        return $this->createBatch($batchData);
    }

    /**
     * Update an existing inventory record.
     * Note: Direct updates usually shouldn't change quantity to preserve audit trails,
     * but we allow it here for corrections.
     */
    public function update(int $id, array $data)
    {
        $inventory = Inventory::findOrFail($id);

        // If quantity is being changed manually, you might want to log a 'adjustment' transaction here.
        // For simplicity, we just update the record.
        return $inventory->update($data);
    }

    /**
     * Toggle or update the status of an inventory record.
     */
    public function updateStatus($id)
    {
        $inventory = Inventory::findOrFail($id);
        $newStatus = ($inventory->status === 'active') ? 'inactive' : 'active';

        return $inventory->update(['status' => $newStatus]);
    }

    /**
     * Delete an inventory record.
     */
    public function delete(int $id)
    {
        $inventory = Inventory::findOrFail($id);

        return $inventory->delete();
    }

    /**
     * Get Query Builder for Yajra DataTables.
     */
    public function getDataforDataTable(array $filters = [])
    {
        $query = Inventory::query()
            ->with(['variant.product', 'store', 'warehouse'])
            ->select('inventories.*'); // Select * to avoid ambiguity

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['location_type'])) {
            $query->where('location_type', $filters['location_type']);
        }

        return $query;
    }

    /**
     * Find a specific stock entry by product and location.
     */
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

    /**
     * Retrieve items that are below a certain quantity threshold.
     */
    public function getLowStock(array $filters = [])
    {
        $threshold = $filters['threshold'] ?? 10; // Default low stock limit

        return Inventory::with(['variant.product'])
            ->where('quantity', '<=', $threshold)
            ->where('status', 'active')
            ->get();
    }

    public function getBatchesForDataTable(array $filters = [])
    {
        // Group by Batch to show one row per Inward Entry
        $query = Inventory::query()
            ->select(
                'batch_no',
                'location_type',
                'store_id',
                'warehouse_id',
                'created_at',
                'status',
                DB::raw('MAX(id) as id'), // Valid ID for generating routes
                DB::raw('COUNT(id) as total_items'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->with(['store', 'warehouse']) // Eager load for location names
            ->groupBy('batch_no', 'location_type', 'store_id', 'warehouse_id', 'created_at', 'status');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }
}
