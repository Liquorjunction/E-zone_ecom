<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Interfaces\StockReportRepositoryInterface as StockReportRepo;

class StockReportController extends Controller
{
    /**
     * Display the stock report page.
     */
    public function index(StockReportRepo $stockReportRepository)
    {
        $stocks = $stockReportRepository->getStockReport();
        return view('admin.pages.reports.stockReport', compact('stocks'));
    }
}
