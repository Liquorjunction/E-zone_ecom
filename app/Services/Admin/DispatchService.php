<?php

namespace App\Services\Admin;

use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface as DispatchRepo;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface as InventoryRepo;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface as OrderRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DispatchService
{
    protected $dispatchRepo;
    protected $orderRepo;
    protected $inventoryRepo;

    public function __construct(DispatchRepo $dispatchRepo, OrderRepo $orderRepo, InventoryRepo $inventoryRepo,)
    {
        $this->dispatchRepo = $dispatchRepo;
        $this->orderRepo = $orderRepo;
        $this->inventoryRepo = $inventoryRepo;
    }

    public function createDispatch($request)
    {
        try {
            DB::beginTransaction();
            $orderId = $request->input('order_id');
            $items = $request->input('items');
            $allDispatched = true;
            $dispatchedItems = [];

            foreach ($items as $item) {
                $dispatch = $this->dispatchRepo->createDispatch([
                    'order_id' => $orderId,
                    'order_item_id' => $item['order_item_id'],
                    'dispatch_qty' => $item['dispatch_qty'],
                    'inventory_id' => $item['inventory_id'],
                    'dispatch_method' => $request->input('dispatch_method'),
                    'remarks' => $request->input('remarks'),
                ]);
                $dispatchedItems[] = $dispatch;

                // Check if this item is fully dispatched using repository
                $orderedQty = $this->dispatchRepo->getOrderItemQuantity($item['order_item_id']);
                $totalDispatched = $this->dispatchRepo->getTotalDispatchedQuantity($item['order_item_id']);
                if ($totalDispatched < $orderedQty) {
                    $allDispatched = false;
                }
            }

            // Update order status using repository
            if ($allDispatched) {
                $this->orderRepo->updateOrderStatus($orderId, 'dispatched');
            } else {
                $this->orderRepo->updateOrderStatus($orderId, 'partially_dispatched');
            }

            DB::commit();
            return $dispatchedItems;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
