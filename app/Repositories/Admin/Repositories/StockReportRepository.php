<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Repositories\Admin\Interfaces\StockReportRepositoryInterface;

class StockReportRepository implements StockReportRepositoryInterface
{
    public function getStockReport()
    {
        $inventories = Inventory::with([
            'product.brand',
            'product.category',
            'product.subCategory',
            'variant',
            'warehouse',
            'store',
            'orderItems.order'
        ])->orderByDesc('updated_at')->get();

        $inventories->transform(function ($inventory) {
            $reservedQty = (float) ($inventory->reserved_quantity ?? 0);

            $filteredOrderItems = $inventory->orderItems->filter(function ($item) use ($inventory) {
                return $item->product_id == $inventory->product_id && $item->variant_id == $inventory->product_variant_id;
            });
            $soldQty = $filteredOrderItems
                ->filter(function ($item) {
                    return $item->order && in_array($item->order->status, ['completed', 'paid', 'delivered']);
                })
                ->sum('quantity');

            $returnedQty = $inventory->transactions
                ->where('type', 'return')
                ->sum('quantity_change');

            $availableQty = (float) $inventory->quantity;

            $totalQty = $availableQty + $reservedQty + $soldQty;

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
