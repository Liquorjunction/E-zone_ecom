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
            'transactions'
        ])->orderByDesc('updated_at')->get();

        $inventories->transform(function ($inventory) {
            $reservedQty = (float) ($inventory->reserved_quantity ?? 0);

            $soldQty = (float) abs(
                $inventory->transactions
                    ->where('type', 'dispatch')
                    ->sum('quantity_change')
            );

            $returnedQty = (float) $inventory->transactions
                ->whereIn('type', ['return', 'rto_restock'])
                ->sum('quantity_change');

            $availableQty = max(0, (float) $inventory->quantity - $reservedQty);

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
