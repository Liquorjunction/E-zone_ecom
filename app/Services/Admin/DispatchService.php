<?php

namespace App\Services\Admin;

use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface as DispatchRepo;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface as InventoryRepo;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface as OrderRepo;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface;
use App\Services\Admin\OrderStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DispatchService
{
    protected $dispatchRepo;
    protected $orderRepo;
    protected $inventoryRepo;
    protected $warehouseRepo;
    protected $storeRepo;
    protected $orderStatusService;

    public function __construct(
        DispatchRepo $dispatchRepo,
        OrderRepo $orderRepo,
        InventoryRepo $inventoryRepo,
        WarehouseRepositoryInterface $warehouseRepo,
        StoreRepositoryInterface $storeRepo,
        OrderStatusService $orderStatusService
    ) {
        $this->dispatchRepo = $dispatchRepo;
        $this->orderRepo = $orderRepo;
        $this->inventoryRepo = $inventoryRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->storeRepo = $storeRepo;
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * Create dispatches for order items and update order status using OrderStatusService.
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function createDispatch(array $data)
    {
        try {
            DB::beginTransaction();
            $orderId = $data['order_id'];
            $items = $data['items'];
            $allDispatched = true;
            $dispatchedItems = [];

            foreach ($items as $item) {
                $dispatchData = [
                    'order_id' => $orderId,
                    'order_item_id' => $item['order_item_id'],
                    'quantity' => $item['quantity'],
                    'inventory_id' => $item['inventory_id'],
                    'dispatch_from_store_id' => $item['dispatch_from_store_id'] ?? null,
                    'dispatch_from_warehouse_id' => $item['dispatch_from_warehouse_id'] ?? null,
                    'dispatch_method' => $data['dispatch_method'] ?? null,
                    'dispatched_by' => Auth::guard('admin')->id(),
                    'dispatched_at' => Carbon::now(),
                    'remarks' => $data['remarks'] ?? null,
                ];
                $dispatch = $this->dispatchRepo->createDispatch($item['order_item_id'], $dispatchData);
                $dispatchedItems[] = $dispatch;

                // Check if this item is fully dispatched
                $orderedQty = $this->dispatchRepo->getOrderItemQuantity($item['order_item_id']);
                $totalDispatched = $this->dispatchRepo->getTotalDispatchedQuantity($item['order_item_id']);
                if ($totalDispatched < $orderedQty) {
                    $allDispatched = false;
                }
            }

            // Use OrderStatusService for status change
            $order = $this->orderRepo->getOrderById($orderId);
            if ($allDispatched) {
                $this->orderStatusService->changeStatus($order, 'dispatched');
            } else {
                $this->orderStatusService->changeStatus($order, 'partially_dispatched');
            }

            DB::commit();
            return $dispatchedItems;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
