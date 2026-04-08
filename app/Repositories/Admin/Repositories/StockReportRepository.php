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

        // Group by SKU (variant SKU preferred, fallback to product SKU)
        $grouped = $inventories->groupBy(function ($inventory) {
            return optional($inventory->variant)->sku ?? optional($inventory->product)->sku ?? 'N/A';
        });

        $result = [];
        $rowKey = 0;
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
                $dispatchedQty = (float) abs(
                    $inventory->transactions
                        ->where('type', 'dispatch')
                        ->sum('quantity_change')
                );
                $returnedQty = (float) $inventory->transactions
                    ->whereIn('type', ['return', 'rto_restock', 'restock'])
                    ->sum('quantity_change');
                $soldQty = max(0, $dispatchedQty - $returnedQty);
                $availableQty = max(0, (float) $inventory->quantity - $reservedQty);
                $totalQty = (float) $inventory->quantity;

                $skuTotals['total_qty'] += $totalQty;
                $skuTotals['sold_qty'] += $soldQty;
                $skuTotals['reserved_qty'] += $reservedQty;
                $skuTotals['available_qty'] += $availableQty;
                $skuTotals['returned_qty'] += $returnedQty;

                $locationType = 'N/A';
                $locationName = 'N/A';

                if ($inventory->warehouse_id && $inventory->warehouse) {
                    $locationType = 'Warehouse';
                    $locationName = $inventory->warehouse->warehouse_name;
                } elseif ($inventory->store_id && $inventory->store) {
                    $locationType = 'Store';
                    $locationName = $inventory->store->store_name;
                }

                $rows[] = [
                    'batch_no' => $inventory->batch_no ?? 'N/A',
                    'product_name' => optional($inventory->product)->name ?? 'N/A',
                    'variant_name' => optional($inventory->variant)->variant_name ?? 'N/A',
                    'brand_name' => optional(optional($inventory->product)->brand)->name ?? 'N/A',
                    'location_type' => $locationType,
                    'location_name' => $locationName,
                    'total_qty' => $totalQty,
                    'sold_qty' => $soldQty,
                    'reserved_qty' => $reservedQty,
                    'available_qty' => $availableQty,
                    'returned_qty' => $returnedQty,
                ];
            }

            // Use the first inventory for product/variant/brand/category info
            $first = $items->first();
            $categoryName = optional($first->product?->category)?->getTranslation('category-name', 'en') ?? 'N/A';
            $subCategoryName = optional($first->product?->subCategory)?->getTranslation('sub-category-name', 'en') ?? 'N/A';

            $result[] = [
                'row_key' => $rowKey,
                'sku' => $sku,
                'product_name' => optional($first->product)->name ?? 'N/A',
                'variant_name' => optional($first->variant)->variant_name ?? 'N/A',
                'brand_name' => optional(optional($first->product)->brand)->name ?? 'N/A',
                'category_name' => $categoryName,
                'sub_category_name' => $subCategoryName,
                'batch_no' => $rows[0]['batch_no'] ?? null,
                'has_children' => count($rows) > 1,
                'child_count' => count($rows),
                'child_rows' => $rows,
                'total_qty' => $skuTotals['total_qty'],
                'sold_qty' => $skuTotals['sold_qty'],
                'reserved_qty' => $skuTotals['reserved_qty'],
                'available_qty' => $skuTotals['available_qty'],
                'returned_qty' => $skuTotals['returned_qty'],
            ];

            $rowKey++;
        }

        return collect($result);
    }
}
