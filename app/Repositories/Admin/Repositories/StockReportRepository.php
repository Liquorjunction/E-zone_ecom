<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Repositories\Admin\Interfaces\StockReportRepositoryInterface;
use Illuminate\Support\Facades\Log;

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

        // Group by SKU (variant SKU preferred, fallback to product SKU)
        $grouped = $inventories->groupBy(function ($inventory) {
            return optional($inventory->variant)->sku ?? optional($inventory->product)->sku ?? 'N/A';
        });

        $result = [];
        foreach ($grouped as $sku => $items) {
            // Aggregate totals for this SKU
            $skuTotals = [
                'total_qty' => 0,
                'sold_qty' => 0,
                'reserved_qty' => 0,
                'available_qty' => 0,
                'returned_qty' => 0,
            ];
            $rows = [];
            foreach ($items as $inventory) {
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

                $skuTotals['total_qty'] += $totalQty;
                $skuTotals['sold_qty'] += $soldQty;
                $skuTotals['reserved_qty'] += $reservedQty;
                $skuTotals['available_qty'] += $availableQty;
                $skuTotals['returned_qty'] += $returnedQty;

                $rows[] = [
                    'inventory' => $inventory,
                    'total_qty' => $totalQty,
                    'sold_qty' => $soldQty,
                    'reserved_qty' => $reservedQty,
                    'available_qty' => $availableQty,
                    'returned_qty' => $returnedQty,
                ];
            }

            // Use the first inventory for product/variant/brand/category info
            $first = $items->first();
            $result[] = [
                'sku' => $sku,
                'product' => optional($first->product),
                'variant' => optional($first->variant),
                'brand' => optional(optional($first->product)->brand),
                'category' => optional(optional($first->product)->category),
                'subCategory' => optional(optional($first->product)->subCategory),
                'rows' => $rows,
                'totals' => $skuTotals,
            ];
        }

        return collect($result);
    }
}
