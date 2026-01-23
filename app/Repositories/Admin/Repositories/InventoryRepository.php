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
                $inventory = Inventory::create([
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

                $createdItems[] = $inventory;

                // Record an inward transaction for this created inventory row
                try {
                    InventoryTransaction::create([
                        'inventory_id' => $inventory->id,
                        'type' => 'inward',
                        'quantity' => $inventory->quantity,
                        'rate' => $inventory->cost_price ?? 0,
                        'batch_no' => $batchNo,
                        'created_by' => Auth::id(),
                        'description' => 'Batch import: ' . $batchNo,
                        'before_quantity' => null,
                        'after_quantity' => $inventory->quantity,
                        'quantity_change' => $inventory->quantity,
                    ]);
                } catch (\Throwable $e) {
                    // swallow to avoid failing whole transaction if txn table isn't present yet
                }
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
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * cost_price) as subtotal_cost')
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
            $oldQuantity = $inventory->quantity;
            $inventory->update($data);
            $inventory->refresh();

            // If quantity changed, record an adjustment transaction
            if (array_key_exists('quantity', $data)) {
                $delta = $inventory->quantity - $oldQuantity;
                if ($delta != 0) {
                    try {
                        InventoryTransaction::create([
                            'inventory_id' => $inventory->id,
                            'type' => 'adjustment',
                            'quantity' => $inventory->quantity,
                            'rate' => $data['cost_price'] ?? $inventory->cost_price ?? 0,
                            'batch_no' => $inventory->batch_no,
                            'created_by' => Auth::id(),
                            'description' => 'Manual quantity update',
                            'before_quantity' => $oldQuantity,
                            'after_quantity' => $inventory->quantity,
                            'quantity_change' => $delta,
                        ]);
                    } catch (\Throwable $e) {
                        // ignore transaction creation errors
                    }
                }
            }

            return true;
        }
        return false;
    }

        public function updateBatch($id, array $data)
    {
        $referenceItem = $this->find($id);
        if (!$referenceItem) {
            throw new \Exception('Original batch not found.');
        }
        $batchNo = $referenceItem->batch_no;
        $adminId = Auth::id();
        return DB::transaction(function () use ($data, $referenceItem, $batchNo, $adminId) {
            // Fetch old items for this batch (not soft deleted)
            $oldItems = Inventory::where('batch_no', $batchNo)->get();
            $oldMap = [];
            foreach ($oldItems as $item) {
                $key = $item->product_id . '-' . ($item->product_variant_id ?? '0');
                $oldMap[$key] = $item;
            }
            // Build new map from incoming data
            $newMap = [];
            foreach ($data['items'] as $item) {
                $key = $item['product_id'] . '-' . (($item['product_variant_id'] ?? '0'));
                $newMap[$key] = $item;
            }
            // 1. Handle updated and removed items
            foreach ($oldMap as $key => $old) {
                if (isset($newMap[$key])) {
                    // Exists in both, check for quantity/cost change
                    $new = $newMap[$key];
                    $qtyChange = $new['quantity'] - $old->quantity;
                    if ($qtyChange != 0 || ($old->cost_price != ($new['cost_price'] ?? 0))) {
                        InventoryTransaction::create([
                            'inventory_id' => $old->id,
                            'type' => 'update',
                            'quantity' => $new['quantity'],
                            'rate' => $new['cost_price'] ?? 0,
                            'batch_no' => $batchNo,
                            'created_by' => $adminId,
                            'description' => 'Batch item updated',
                            'before_quantity' => $old->quantity,
                            'after_quantity' => $new['quantity'],
                            'quantity_change' => $qtyChange,
                        ]);
                    }
                } else {
                    // Was removed
                    InventoryTransaction::create([
                        'inventory_id' => $old->id,
                        'type' => 'removed',
                        'quantity' => 0,
                        'rate' => $old->cost_price,
                        'batch_no' => $batchNo,
                        'created_by' => $adminId,
                        'description' => 'Batch item removed',
                        'before_quantity' => $old->quantity,
                        'after_quantity' => 0,
                        'quantity_change' => -$old->quantity,
                    ]);
                }
            }
            // 2. Handle added items
            foreach ($newMap as $key => $new) {
                if (!isset($oldMap[$key])) {
                    InventoryTransaction::create([
                        'inventory_id' => null, // will be set after createBatch
                        'type' => 'added',
                        'quantity' => $new['quantity'],
                        'rate' => $new['cost_price'] ?? 0,
                        'batch_no' => $batchNo,
                        'created_by' => $adminId,
                        'description' => 'Batch item added',
                        'before_quantity' => 0,
                        'after_quantity' => $new['quantity'],
                        'quantity_change' => $new['quantity'],
                    ]);
                }
            }
            // Remove old
            Inventory::where('batch_no', $batchNo)->delete();
            // Create new with same batch number
            $data['batch_no'] = $batchNo;
            $created = $this->createBatch($data);
            // Update inventory_id for added transactions (since we didn't have it before create)
            foreach ($created as $inv) {
                $key = $inv->product_id . '-' . ($inv->product_variant_id ?? '0');
                if (!isset($oldMap[$key])) {
                    InventoryTransaction::where([
                        ['batch_no', '=', $batchNo],
                        ['type', '=', 'added'],
                        ['inventory_id', '=', null],
                        ['quantity', '=', $inv->quantity],
                        ['rate', '=', $inv->cost_price],
                    ])->update(['inventory_id' => $inv->id]);
                }
            }
            return $created;
        });
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
            // Record an outward transaction representing removal of remaining qty
            if ($inventory->quantity > 0) {
                try {
                    InventoryTransaction::create([
                        'inventory_id' => $inventory->id,
                        'type' => 'outward',
                        'quantity' => -1 * $inventory->quantity,
                        'rate' => $inventory->cost_price ?? 0,
                        'batch_no' => $inventory->batch_no,
                        'created_by' => Auth::id(),
                        'description' => 'Inventory record deleted',
                        'before_quantity' => $inventory->quantity,
                        'after_quantity' => 0,
                        'quantity_change' => -1 * $inventory->quantity,
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            return $inventory->delete();
        }
        return false;
    }

    /**
     * Create a transaction record.
     */
    public function recordTransaction(array $data)
    {
        return InventoryTransaction::create($data);
    }

    /**
     * Get transactions for an inventory record.
     */
    public function getTransactions(int $inventoryId)
    {
        return InventoryTransaction::where('inventory_id', $inventoryId)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Reserve stock across inventory rows (FIFO-style). Increases `reserved_quantity` and logs transactions.
     * Returns total reserved amount.
     */
    public function reserveStock(int $productId, ?int $variantId, float $quantity, ?string $referenceType = null, ?int $referenceId = null)
    {
        $remaining = $quantity;

        $query = Inventory::query();
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }

        $rows = $query->orderBy('created_at', 'asc')->get();

        foreach ($rows as $inv) {
            if ($remaining <= 0) break;

            $availableToReserve = max(0, $inv->quantity - $inv->reserved_quantity);
            if ($availableToReserve <= 0) continue;

            $take = min($availableToReserve, $remaining);
            $before = $inv->reserved_quantity;
            $inv->reserved_quantity = $before + $take;
            $inv->save();

            try {
                InventoryTransaction::create([
                    'inventory_id' => $inv->id,
                    'type' => 'reserve',
                    'quantity' => $take,
                    'rate' => $inv->cost_price ?? 0,
                    'batch_no' => $inv->batch_no,
                    'created_by' => Auth::id(),
                    'description' => 'Reserved for '.$referenceType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'before_quantity' => $before,
                    'after_quantity' => $inv->reserved_quantity,
                    'quantity_change' => $take,
                ]);
            } catch (\Throwable $e) {
                // ignore transactional logging failures
            }

            $remaining -= $take;
        }

        return $quantity - $remaining;
    }

    /**
     * Release previously reserved stock. Decreases `reserved_quantity` and logs transactions.
     */
    public function releaseReservation(int $productId, ?int $variantId, float $quantity, ?string $referenceType = null, ?int $referenceId = null)
    {
        $remaining = $quantity;

        $query = Inventory::query();
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }

        // release from latest reserved rows first (LIFO for release)
        $rows = $query->orderBy('created_at', 'desc')->get();

        foreach ($rows as $inv) {
            if ($remaining <= 0) break;

            $reserved = max(0, $inv->reserved_quantity);
            if ($reserved <= 0) continue;

            $take = min($reserved, $remaining);
            $before = $inv->reserved_quantity;
            $inv->reserved_quantity = max(0, $before - $take);
            $inv->save();

            try {
                InventoryTransaction::create([
                    'inventory_id' => $inv->id,
                    'type' => 'release',
                    'quantity' => $take,
                    'rate' => $inv->cost_price ?? 0,
                    'batch_no' => $inv->batch_no,
                    'created_by' => Auth::id(),
                    'description' => 'Reservation released for '.$referenceType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'before_quantity' => $before,
                    'after_quantity' => $inv->reserved_quantity,
                    'quantity_change' => -1 * $take,
                ]);
            } catch (\Throwable $e) {
                // ignore
            }

            $remaining -= $take;
        }

        return $quantity - $remaining;
    }

    /**
     * Process a return/inward: increment inventory quantity and log a return txn.
     */
    public function processReturn(int $productId, ?int $variantId, float $quantity, ?string $referenceType = null, ?int $referenceId = null)
    {
        // Prefer to add to existing inventory row for the variant/product (latest inward batch) else create new
        $query = Inventory::query();
        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->where('product_id', $productId);
        }

        $inv = $query->orderBy('created_at', 'desc')->first();

        if (! $inv) {
            $inv = Inventory::create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'location_type' => 'warehouse',
                'warehouse_id' => null,
                'store_id' => null,
                'quantity' => 0,
                'cost_price' => 0,
                'batch_no' => 'RET-'.date('Ymd').'-'.strtoupper(Str::random(4)),
                'status' => 'active',
            ]);
        }

        $before = $inv->quantity;
        $inv->quantity = $inv->quantity + $quantity;
        $inv->save();

        try {
            InventoryTransaction::create([
                'inventory_id' => $inv->id,
                'type' => 'return',
                'quantity' => $quantity,
                'rate' => $inv->cost_price ?? 0,
                'batch_no' => $inv->batch_no,
                'created_by' => Auth::id(),
                'description' => 'Customer return',
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'before_quantity' => $before,
                'after_quantity' => $inv->quantity,
                'quantity_change' => $quantity,
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        return $inv;
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
