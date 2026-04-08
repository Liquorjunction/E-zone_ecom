<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Interfaces\StockReportRepositoryInterface as StockReportRepo;
use Yajra\DataTables\Facades\DataTables;

class StockReportController extends Controller
{
    /**
     * Display the stock report page.
     */
    public function index()
    {
        return view('admin.pages.reports.stockReport');
    }

    /**
     * Data source for stock report datatable.
     */
    public function data(StockReportRepo $stockReportRepository)
    {
        $stocks = $stockReportRepository->getStockReport();

        return DataTables::of($stocks)
            ->addIndexColumn()
            ->addColumn('expand_button', function ($row) {
                if (!($row['has_children'] ?? false)) {
                    return '';
                }

                return '<button type="button" class="btn btn-sm btn-outline-primary expand-btn" data-sku-id="' . e($row['row_key']) . '" title="Show batch locations">'
                    . '<i class="bi bi-chevron-double-down"></i>'
                    . '</button>';
            })
            ->addColumn('sku_html', function ($row) {
                $skuHtml = '<span class="badge bg-secondary sku-badge">' . e($row['sku']) . '</span>';

                if (!($row['has_children'] ?? false) && !empty($row['batch_no'])) {
                    $skuHtml .= '<br><small class="text-muted">' . e($row['batch_no']) . '</small>';
                }

                if (($row['has_children'] ?? false)) {
                    $skuHtml .= '<br><small class="text-primary fw-semibold">'
                        . '<i class="bi bi-layers me-1"></i>' . e(($row['child_count'] ?? 0) . ' batches')
                        . '</small>';
                }

                return $skuHtml;
            })
            ->addColumn('total_qty_badge', function ($row) {
                return '<span class="badge bg-dark qty-badge" title="Total across all locations">' . e($row['total_qty']) . '</span>';
            })
            ->addColumn('sold_qty_badge', function ($row) {
                return '<span class="badge bg-danger qty-badge" title="Sold (dispatched)">' . e($row['sold_qty']) . '</span>';
            })
            ->addColumn('reserved_qty_badge', function ($row) {
                return '<span class="badge bg-warning text-dark qty-badge" title="Reserved (pending/processing)">' . e($row['reserved_qty']) . '</span>';
            })
            ->addColumn('available_qty_badge', function ($row) {
                return '<span class="badge bg-success qty-badge" title="Available">' . e($row['available_qty']) . '</span>';
            })
            ->rawColumns([
                'expand_button',
                'sku_html',
                'total_qty_badge',
                'sold_qty_badge',
                'reserved_qty_badge',
                'available_qty_badge',
            ])
            ->make(true);
    }
}
