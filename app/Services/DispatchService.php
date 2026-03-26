<?php

namespace App\Services;

use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\InventoryTransactionsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class DispatchService
{
    protected $dispatchRepository;
    protected $orderRepository;
    protected $orderStatusService;
    protected $inventoryRepo;
    protected $transactionRepo;

    public function __construct(
        DispatchRepositoryInterface $dispatchRepository,
        OrderRepositoryInterface $orderRepository,
        OrderStatusService $orderStatusService,
        InventoryRepositoryInterface $inventoryRepo,
        InventoryTransactionsRepositoryInterface $transactionRepo
    ) {
        $this->dispatchRepository = $dispatchRepository;
        $this->orderRepository = $orderRepository;
        $this->orderStatusService = $orderStatusService;
        $this->inventoryRepo = $inventoryRepo;
        $this->transactionRepo = $transactionRepo;
    }

    public function processBatchDispatch(array $data, int $adminId)
    {
        DB::beginTransaction();

        try {
            $orderId         = $data['order_id'];
            $dispatchMethod  = $data['dispatch_method'] ?? 'courier';
            $remarks         = $data['remarks'] ?? null;
            $itemsDispatched = 0;

            // New structure: $data['dispatches'] is an array of rows, each with:
            //   order_item_id, inventory_id, quantity
            // A single order item may appear in multiple rows (multi-location split).
            foreach ($data['dispatches'] as $row) {
                $orderItemId = $row['order_item_id'];
                $inventoryId = $row['inventory_id'];
                $quantity    = (int) $row['quantity'];

                if ($quantity <= 0) continue;

                $orderItem = $this->orderRepository->getOrderItemById($orderItemId);
                $inventory = $this->inventoryRepo->findWithLock($inventoryId);

                // Guard: how much of this item has already been dispatched/delivered?
                $alreadyDispatched = $this->dispatchRepository
                    ->getDispatchesByOrderId($orderId)
                    ->where('order_item_id', $orderItemId)
                    ->whereIn('status', ['dispatched', 'delivered'])
                    ->sum('quantity');

                $remainingToDispatch = $orderItem->quantity - $alreadyDispatched;

                if ($quantity > $remainingToDispatch) {
                    throw new Exception(
                        "Quantity exceeds remaining dispatchable qty for product ID {$orderItem->product_id}. " .
                        "Remaining: {$remainingToDispatch}, requested: {$quantity}."
                    );
                }

                if ($inventory->quantity < $quantity) {
                    throw new Exception(
                        "Insufficient inventory for product ID {$orderItem->product_id}. " .
                        "Available: {$inventory->quantity}, requested: {$quantity}."
                    );
                }


                // --- Reservation/Release Logic Start ---
                // Release reservation from other locations if dispatching from a different location
                $remainingToRelease = $quantity;
                $allInventories = $this->inventoryRepo->getForReservation(
                    $orderItem->product_id,
                    $orderItem->product_variant_id ?? null,
                    'asc'
                );
                // Release from all other inventories except the dispatch location
                foreach ($allInventories as $invRow) {
                    if ($invRow->id == $inventory->id) continue;
                    $reserved = $invRow->reserved_quantity ?? 0;
                    if ($reserved > 0 && $remainingToRelease > 0) {
                        $releaseQty = min($reserved, $remainingToRelease);
                        $this->inventoryRepo->update($invRow->id, [
                            'reserved_quantity' => max(0, $reserved - $releaseQty),
                        ]);
                        if (method_exists($this->transactionRepo, 'create')) {
                            $this->transactionRepo->create([
                                'batch_no' => $invRow->batch_no,
                                'inventory_id' => $invRow->id,
                                'type' => 'release',
                                'quantity' => $releaseQty,
                                'rate' => $invRow->cost_price ?? 0,
                                'created_by' => $adminId,
                                'reference_type' => 'order',
                                'reference_id' => $orderId,
                                'before_quantity' => $reserved,
                                'after_quantity' => max(0, $reserved - $releaseQty),
                                'quantity_change' => -1 * $releaseQty,
                                'description' => "Reservation released due to dispatch from another location for Order #{$orderId}",
                            ]);
                        }
                        $remainingToRelease -= $releaseQty;
                    }
                }

                // 2. Reduce reserved_quantity at dispatch location if any reservation exists there
                $reservedHere = $inventory->reserved_quantity ?? 0;
                $reservedToReduce = min($reservedHere, $quantity);
                if ($reservedToReduce > 0) {
                    $this->inventoryRepo->update($inventory->id, [
                        'reserved_quantity' => max(0, $reservedHere - $reservedToReduce),
                    ]);
                    if (method_exists($this->transactionRepo, 'create')) {
                        $this->transactionRepo->create([
                            'batch_no' => $inventory->batch_no,
                            'inventory_id' => $inventory->id,
                            'type' => 'reserve_consume',
                            'quantity' => $reservedToReduce,
                            'rate' => $inventory->cost_price ?? 0,
                            'created_by' => $adminId,
                            'reference_type' => 'order',
                            'reference_id' => $orderId,
                            'before_quantity' => $reservedHere,
                            'after_quantity' => max(0, $reservedHere - $reservedToReduce),
                            'quantity_change' => -1 * $reservedToReduce,
                            'description' => "Reservation consumed by dispatch for Order #{$orderId}",
                        ]);
                    }
                }

                $beforeQty = $inventory->quantity;
                $this->inventoryRepo->update($inventory->id, [
                    'quantity' => $beforeQty - $quantity,
                ]);

                $this->transactionRepo->create([
                    'batch_no'         => $inventory->batch_no,
                    'inventory_id'     => $inventory->id,
                    'type'             => 'dispatch',
                    'quantity'         => $quantity,
                    'rate'             => $inventory->cost_price ?? 0,
                    'created_by'       => $adminId,
                    'reference_type'   => 'order',
                    'reference_id'     => $orderId,
                    'before_quantity'  => $beforeQty,
                    'after_quantity'   => $beforeQty - $quantity,
                    'quantity_change'  => -$quantity,
                    'description'      => "Item dispatched for Order #{$orderId}",
                ]);

                $this->dispatchRepository->createDispatch([
                    'order_id'                    => $orderId,
                    'order_item_id'               => $orderItemId,
                    'inventory_id'                => $inventoryId,
                    'quantity'                    => $quantity,
                    'status'                      => 'dispatched',
                    'dispatch_method'             => $dispatchMethod,
                    'dispatch_from_store_id'      => $inventory->store_id,
                    'dispatch_from_warehouse_id'  => $inventory->warehouse_id,
                    'dispatched_at'               => now(),
                    'dispatched_by'               => $adminId,
                    'remarks'                     => $remarks,
                ]);

                $itemsDispatched++;
            }

            if ($itemsDispatched === 0) {
                throw new Exception("No valid items were selected for dispatch.");
            }

            $this->syncOrderStatus($orderId, $adminId);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status, int $adminId)
    {
        DB::beginTransaction();
        try {
            $dispatch = $this->dispatchRepository->getDispatchById($id);
            $oldStatus = $dispatch->status;

            $dispatch->status = $status;
            $dispatch->status_updated_by = $adminId;
            $dispatch->status_updated_at = now();
            $dispatch->save();

            if ($status === 'returned' && $oldStatus !== 'returned') {
                $inventory = $this->inventoryRepo->findWithLock($dispatch->inventory_id);

                if ($inventory) {
                    $beforeQty = $inventory->quantity;
                    $this->inventoryRepo->update($inventory->id, ['quantity' => $beforeQty + $dispatch->quantity]);

                    $this->transactionRepo->create([
                        'batch_no' => $inventory->batch_no,
                        'inventory_id' => $inventory->id,
                        'type' => 'rto_restock',
                        'quantity' => $dispatch->quantity,
                        'rate' => $inventory->cost_price ?? 0,
                        'created_by' => $adminId,
                        'reference_type' => 'dispatch',
                        'reference_id' => $dispatch->id,
                        'before_quantity' => $beforeQty,
                        'after_quantity' => $beforeQty + $dispatch->quantity,
                        'quantity_change' => $dispatch->quantity,
                        'description' => "RTO Restock for Dispatch #{$dispatch->id}",
                    ]);
                }
            }

            $this->syncOrderStatus($dispatch->order_id, $adminId);

            DB::commit();
            return $dispatch;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function syncOrderStatus(int $orderId, int $adminId)
    {
        $order = $this->orderRepository->getOrderWithItemsAndInventory($orderId);
        $dispatches = $this->dispatchRepository->getDispatchesByOrderId($orderId);

        $totalOrdered = $order->orderItems->sum('quantity');
        $totalDispatched = $dispatches->whereIn('status', ['dispatched', 'delivered'])->sum('quantity');
        $totalDelivered = $dispatches->where('status', 'delivered')->sum('quantity');

        $targetStatus = null;

        if ($totalDelivered >= $totalOrdered) {
            $targetStatus = 'delivered';
        } elseif ($totalDispatched >= $totalOrdered) {
            $targetStatus = 'dispatched';
        } elseif ($totalDispatched > 0) {
            $targetStatus = 'partially_dispatched';
        }

        if ($targetStatus && $order->status !== $targetStatus) {
            if ($this->orderStatusService->canTransition($order->status, $targetStatus)) {
                $this->orderStatusService->changeStatus($order, $targetStatus, null, false, $adminId);
            } else {
                $this->orderStatusService->changeStatus($order, $targetStatus, null, true, $adminId);
            }
        }
    }
}
