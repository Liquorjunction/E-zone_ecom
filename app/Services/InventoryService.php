<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\InventoryTransactionsRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InventoryService
{
    protected $inventoryRepo;
    protected $inventoryTransactionRepo;

    public function __construct(InventoryRepositoryInterface $inventoryRepo, InventoryTransactionsRepositoryInterface $inventoryTransactionRepo)
    {
        $this->inventoryRepo = $inventoryRepo;
        $this->inventoryTransactionRepo = $inventoryTransactionRepo;
    }

    public function create(array $data, int $adminId = 1)
    {
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

        return $this->createBatch($batchData, $adminId);
    }

    public function createBatch(array $batchData, int $adminId = 1)
    {
        return DB::transaction(function () use ($batchData, $adminId) {
            $batchNo = $batchData['batch_no'] ?? 'BATCH-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $locationType = $batchData['location_type'];
            $warehouseId = ($locationType === 'warehouse') ? $batchData['warehouse_id'] : null;
            $storeId = ($locationType === 'store') ? $batchData['store_id'] : null;
            $status = $batchData['status'] ?? 'active';
            $notes = $batchData['notes'] ?? null;

            $createdItems = [];

            foreach ($batchData['items'] as $item) {
                if (empty($item['product_id']) || !isset($item['quantity'])) continue;

                $inventory = $this->inventoryRepo->create([
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

                try {
                    $this->inventoryTransactionRepo->create([
                        'inventory_id' => $inventory->id,
                        'type' => 'inward',
                        'quantity' => $inventory->quantity,
                        'rate' => $inventory->cost_price ?? 0,
                        'batch_no' => $batchNo,
                        'created_by' => $adminId,
                        'description' => 'Batch import: ' . $batchNo,
                        'before_quantity' => null,
                        'after_quantity' => $inventory->quantity,
                        'quantity_change' => $inventory->quantity,
                    ]);
                } catch (\Throwable $e) {}
            }

            return $createdItems;
        });
    }

    public function update(int $id, array $data, int $adminId = 1)
    {
        $inventory = $this->inventoryRepo->find($id);
        if ($inventory) {
            $oldQuantity = $inventory->quantity;
            $inventory = $this->inventoryRepo->update($id, $data);

            if (array_key_exists('quantity', $data)) {
                $delta = $inventory->quantity - $oldQuantity;
                if ($delta != 0) {
                    try {
                        $this->inventoryTransactionRepo->create([
                            'inventory_id' => $inventory->id,
                            'type' => ($delta > 0) ? 'inward' : 'outward',
                            'quantity' => abs($delta),
                            'rate' => $inventory->cost_price ?? 0,
                            'batch_no' => $inventory->batch_no,
                            'created_by' => $adminId,
                            'description' => 'Inventory quantity updated',
                            'before_quantity' => $oldQuantity,
                            'after_quantity' => $inventory->quantity,
                            'quantity_change' => $delta,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Error creating inventory transaction: ' . $e->getMessage());
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function updateBatch($id, array $data, int $adminId = 1)
    {
        $referenceItem = $this->inventoryRepo->find($id);
        if (!$referenceItem) throw new \Exception('Original batch not found.');

        $batchNo = $referenceItem->batch_no;

        return DB::transaction(function () use ($data, $batchNo, $adminId) {
            $oldItems = $this->inventoryRepo->getByBatchNo($batchNo);
            $oldMap = [];
            foreach ($oldItems as $item) {
                $key = $item->product_id . '-' . ($item->product_variant_id ?? '0');
                $oldMap[$key] = $item;
            }

            $newMap = [];
            foreach ($data['items'] as $item) {
                $key = $item['product_id'] . '-' . (($item['product_variant_id'] ?? '0'));
                $newMap[$key] = $item;
            }

            foreach ($oldMap as $key => $old) {
                if (isset($newMap[$key])) {
                    $new = $newMap[$key];
                    $qtyChange = $new['quantity'] - $old->quantity;
                    if ($qtyChange != 0 || ($old->cost_price != ($new['cost_price'] ?? 0))) {
                        $this->inventoryTransactionRepo->create([
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
                    $this->inventoryTransactionRepo->create([
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

            foreach ($newMap as $key => $new) {
                if (!isset($oldMap[$key])) {
                    $this->inventoryTransactionRepo->create([
                        'inventory_id' => null,
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

            $this->inventoryRepo->deleteByBatchNo($batchNo);

            $data['batch_no'] = $batchNo;
            $created = $this->createBatch($data, Auth::id());

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

    public function updateStatus($id)
    {
        $inventory = $this->inventoryRepo->find($id);
        if ($inventory) {
            $newStatus = $inventory->status === 'active' ? 'inactive' : 'active';
            return $this->inventoryRepo->update($id, ['status' => $newStatus]);
        }
        return false;
    }

    public function delete(int $id, int $adminId = 1)
    {
        $inventory = $this->inventoryRepo->find($id);
        if ($inventory) {
            if ($inventory->quantity > 0) {
                try {
                    $this->inventoryTransactionRepo->create([
                        'inventory_id' => $inventory->id,
                        'type' => 'outward',
                        'quantity' => -1 * $inventory->quantity,
                        'rate' => $inventory->cost_price ?? 0,
                        'batch_no' => $inventory->batch_no,
                        'created_by' => $adminId,
                        'description' => 'Inventory record deleted',
                        'before_quantity' => $inventory->quantity,
                        'after_quantity' => 0,
                        'quantity_change' => -1 * $inventory->quantity,
                    ]);
                } catch (\Throwable $e) {}
            }
            return $this->inventoryRepo->delete($id);
        }
        return false;
    }

    // FIFO Style Reservation
    public function reserveStock(int $productId, ?int $variantId, float $quantity, ?string $referenceType = null, ?int $referenceId = null, int $adminId = 1)
    {
        $remaining = $quantity;
        $rows = $this->inventoryRepo->getForReservation($productId, $variantId, 'asc');

        foreach ($rows as $inv) {
            if ($remaining <= 0) break;

            $availableToReserve = max(0, $inv->quantity - $inv->reserved_quantity);
            if ($availableToReserve <= 0) continue;

            $take = min($availableToReserve, $remaining);
            $before = $inv->reserved_quantity;

            $this->inventoryRepo->update($inv->id, ['reserved_quantity' => $before + $take]);

            try {
                $this->inventoryTransactionRepo->create([
                    'inventory_id' => $inv->id,
                    'type' => 'reserve',
                    'quantity' => $take,
                    'rate' => $inv->cost_price ?? 0,
                    'batch_no' => $inv->batch_no,
                    'created_by' => $adminId,
                    'description' => 'Reserved for '.$referenceType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'before_quantity' => $before,
                    'after_quantity' => $before + $take,
                    'quantity_change' => $take,
                ]);
            } catch (\Throwable $e) {}

            $remaining -= $take;
        }

        $reserved = $quantity - $remaining;
        return $reserved;
    }

    // LIFO Style Release
    public function releaseReservation(int $productId, ?int $variantId, float $quantity, ?string $referenceType = null, ?int $referenceId = null, int $adminId = 1)
    {
        $remaining = $quantity;
        $rows = $this->inventoryRepo->getForReservation($productId, $variantId, 'desc');

        foreach ($rows as $inv) {
            if ($remaining <= 0) break;

            $reserved = max(0, $inv->reserved_quantity);
            if ($reserved <= 0) continue;

            $take = min($reserved, $remaining);
            $before = $inv->reserved_quantity;

            $this->inventoryRepo->update($inv->id, ['reserved_quantity' => max(0, $before - $take)]);

            try {
                $this->inventoryTransactionRepo->create([
                    'inventory_id' => $inv->id,
                    'type' => 'release',
                    'quantity' => $take,
                    'rate' => $inv->cost_price ?? 0,
                    'batch_no' => $inv->batch_no,
                    'created_by' => $adminId,
                    'description' => 'Reservation released for '.$referenceType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'before_quantity' => $before,
                    'after_quantity' => max(0, $before - $take),
                    'quantity_change' => -1 * $take,
                ]);
            } catch (\Throwable $e) {}

            $remaining -= $take;
        }

        return $quantity - $remaining;
    }

    public function processReturn(int $productId, ?int $variantId, float $quantity, ?string $referenceType = null, ?int $referenceId = null, int $adminId = 1)
    {
        $inv = $this->inventoryRepo->getLatestInward($productId, $variantId);

        if (! $inv) {
            $inv = $this->inventoryRepo->create([
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
        $this->inventoryRepo->update($inv->id, ['quantity' => $before + $quantity]);

        try {
            $this->inventoryTransactionRepo->create([
                'inventory_id' => $inv->id,
                'type' => 'return',
                'quantity' => $quantity,
                'rate' => $inv->cost_price ?? 0,
                'batch_no' => $inv->batch_no,
                'created_by' => $adminId,
                'description' => 'Customer return',
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'before_quantity' => $before,
                'after_quantity' => $before + $quantity,
                'quantity_change' => $quantity,
            ]);
        } catch (\Throwable $e) {}

        return $inv;
    }
}
