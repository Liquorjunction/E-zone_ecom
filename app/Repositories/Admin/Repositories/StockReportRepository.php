<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Repositories\Admin\Interfaces\StockReportRepositoryInterface;

class StockReportRepository implements StockReportRepositoryInterface
{
    public function getStockReport()
    {
        // Eager load relationships
        $inventories = Inventory::with([
            'product.brand',
            'product.category',
            'product.subCategory',
            'variant',
            'warehouse',
            'store',
            'orderItems.order' // eager load order for status filtering
        ])->orderByDesc('updated_at')->get();

        // Map with production-grade calculations

        $inventories->transform(function ($inventory) {
            // Filter orderItems by both product_id and variant_id for accuracy
            $filteredOrderItems = $inventory->orderItems->filter(function ($item) use ($inventory) {
                return $item->product_id == $inventory->product_id && $item->variant_id == $inventory->product_variant_id;
            });

            // Only count sold quantity for completed/paid orders (not cancelled/returned)
            $soldQty = $filteredOrderItems
                ->filter(function ($item) {
                    return $item->order && in_array($item->order->status, ['completed', 'paid', 'delivered']);
                })
                ->sum('quantity');

            // Reserved quantity: sum of items in partial orders
            $reservedQty = $filteredOrderItems
                ->filter(function ($item) {
                    return $item->order && $item->order->status === 'partial';
                })
                ->sum('quantity');

            // Add back returned quantity from inventory transactions of type 'return'
            $returnedQty = $inventory->transactions
                ->where('type', 'return')
                ->sum('quantity_change');

            // Total available = (quantity - reserved) + returned
            $availableQty = ($inventory->quantity - $reservedQty) + $returnedQty;

            // Total qty = sold + available
            $totalQty = $soldQty + $availableQty;

            // Attach calculated fields for the view
            $inventory->sold_qty = $soldQty;
            $inventory->returned_qty = $returnedQty;
            $inventory->available_qty = $availableQty;
            $inventory->total_qty = $totalQty;
            $inventory->reserved_qty = $reservedQty;

            return $inventory;
        });

        return $inventories;
    }
}
