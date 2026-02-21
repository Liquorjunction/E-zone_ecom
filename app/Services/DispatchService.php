<?php

namespace App\Services;

use App\Models\Dispatch;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class DispatchService
{
    public function processBatchDispatch(array $data, int $adminId)
    {
        DB::beginTransaction();
        
        try {
            $orderId = $data['order_id'];
            $itemsDispatched = 0;

            foreach ($data['order_item_id'] as $index => $orderItemId) {
                
                $inventoryId = $data['inventory_id'][$index] ?? null;
                $quantity = $data['quantity'][$index] ?? 0;

                if (empty($inventoryId) || empty($quantity) || $quantity <= 0) {
                    continue;
                }

                $orderItem = OrderItem::findOrFail($orderItemId);
                $inventory = Inventory::where('id', $inventoryId)->lockForUpdate()->first();

                $alreadyDispatched = Dispatch::where('order_item_id', $orderItemId)
                    ->whereIn('status', ['dispatched', 'delivered'])
                    ->sum('quantity');
                    
                $remainingToDispatch = $orderItem->quantity - $alreadyDispatched;
                
                if ($quantity > $remainingToDispatch) {
                    throw new Exception("Quantity exceeds remaining dispatchable quantity for Product ID {$orderItem->product_id}. Remaining: {$remainingToDispatch}");
                }

                if ($inventory->quantity < $quantity) {
                     throw new Exception("Insufficient inventory for Product ID {$orderItem->product_id}. Available: {$inventory->quantity}");
                }
                
                $beforeQty = $inventory->quantity;
                $inventory->decrement('quantity', $quantity);
                
                InventoryTransaction::create([
                    'batch_no' => $inventory->batch_no,
                    'inventory_id' => $inventory->id,
                    'type' => 'sale',
                    'quantity' => $quantity,
                    'created_by' => $adminId,
                    'reference_type' => 'order',
                    'reference_id' => $orderId,
                    'before_quantity' => $beforeQty,
                    'after_quantity' => $beforeQty - $quantity,
                    'quantity_change' => -$quantity,
                    'description' => "Item dispatched for Order #{$orderId}",
                ]);
                
                Dispatch::create([
                    'order_id' => $orderId,
                    'order_item_id' => $orderItemId,
                    'inventory_id' => $inventoryId,
                    'quantity' => $quantity,
                    'status' => 'dispatched',
                    'dispatch_method' => $data['dispatch_method'] ?? 'courier',
                    'dispatch_from_store_id' => $inventory->store_id,
                    'dispatch_from_warehouse_id' => $inventory->warehouse_id,
                    'dispatched_at' => now(),
                    'dispatched_by' => $adminId,
                    'remarks' => $data['remarks'] ?? null,
                ]);

                $itemsDispatched++;
            }

            if ($itemsDispatched === 0) {
                throw new Exception("No valid items were selected for dispatch.");
            }
            
            $this->syncOrderStatus($orderId);

            DB::commit();
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status, int $adminId)
    {
        $dispatch = Dispatch::findOrFail($id);
        $dispatch->status = $status;
        $dispatch->status_updated_by = $adminId;
        $dispatch->status_updated_at = now();
        $dispatch->save();

        $this->syncOrderStatus($dispatch->order_id);
        return $dispatch;
    }

    protected function syncOrderStatus(int $orderId)
    {
         $order = Order::with('orderItems')->findOrFail($orderId);
         $dispatches = Dispatch::where('order_id', $orderId)->get();
         
         $totalOrdered = $order->orderItems->sum('quantity');
         $totalDispatched = $dispatches->whereIn('status', ['dispatched', 'delivered'])->sum('quantity');
         $totalDelivered = $dispatches->where('status', 'delivered')->sum('quantity');

         if ($totalDelivered >= $totalOrdered) {
             $order->update(['status' => 'delivered']);
         } elseif ($totalDispatched >= $totalOrdered) {
             $order->update(['status' => 'dispatched']);
         } elseif ($totalDispatched > 0) {
             $order->update(['status' => 'partially_dispatched']);
         }
    }
}