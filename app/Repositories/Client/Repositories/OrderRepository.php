<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use App\Repositories\Client\Interfaces\CartRepositoryInterface as CartRepo;
use App\Services\PriceCalculatorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepository implements OrderRepositoryInterface
{
    public function getOrdersWithRelations(array $filters, array $relations = [], int $perPage = 10, array $extra = [])
    {
        $query = Order::query();
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        if (!empty($extra['search'])) {
            $search = $extra['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                    ->orWhereHas('items.product', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('items.variant', function ($q3) use ($search) {
                        $q3->where('variant_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(variant_name, ' ', sku) like ?", ["%$search%"]);
                    });
            });
        }

        if (!empty($extra['date_from'])) {
            $query->whereDate('created_at', '>=', $extra['date_from']);
        }
        if (!empty($extra['date_to'])) {
            $query->whereDate('created_at', '<=', $extra['date_to']);
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        switch ($extra['sort'] ?? 'latest') {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_high':
                $query->orderBy('total', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('total', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function findOrderWithRelations(int $orderId, array $relations = [])
    {
        $query = Order::query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($orderId);
    }

    public function getLatestOrderForUser(int $userId, array $relations = [])
    {
        $query = Order::where('user_id', $userId);
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->latest()->first();
    }

    public function findOrder(int $orderId)
    {
        return Order::find($orderId);
    }

    public function create(array $data)
    {
        return Order::create($data);
    }

    public function createItem(array $data)
    {
        return OrderItem::create($data);
    }

    public function update(int $orderId, array $data)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update($data);
            return $order;
        }
        return false;
    }

    public function cancelOrder(int $orderId, ?string $reason = null)
    {
        $order = Order::with('items')->find($orderId);
        if (! $order) return false;

        if (in_array($order->status, ['cancelled', 'refunded'], true)) {
            return false;
        }

        if (! in_array(strtolower($order->status ?? ''), ['pending'], true)) {
            return false;
        }

        DB::transaction(function () use ($order, $reason) {
            $txns = InventoryTransaction::where('reference_type', 'order')
                ->where('reference_id', $order->id)
                ->where('type', 'sale')
                ->get()
                ->groupBy('inventory_id');

            foreach ($txns as $inventoryId => $group) {
                $sum = $group->sum('quantity');
                $inv = Inventory::find($inventoryId);
                if (! $inv) continue;

                $before = $inv->quantity;
                $beforeReserved = $inv->reserved_quantity ?? 0;
                $inv->quantity = $inv->quantity + $sum;
                $inv->reserved_quantity = max(0, $beforeReserved - $sum);
                $inv->save();

                try {
                    InventoryTransaction::create([
                        'inventory_id' => $inv->id,
                        'type' => 'return',
                        'quantity' => $sum,
                        'rate' => $inv->cost_price ?? 0,
                        'batch_no' => $inv->batch_no,
                        'created_by' => Auth::id() ?? ($order->user_id ?? null),
                        'description' => 'Order cancellation: ' . $order->order_number,
                        'reference_type' => 'order_cancellation',
                        'reference_id' => $order->id,
                        'before_quantity' => $before,
                        'after_quantity' => $inv->quantity,
                        'quantity_change' => $sum,
                    ]);
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            }
            $order->status = 'cancelled';
            $order->cancel_reason = $reason;
            $order->save();
        });

        return true;
    }
}
