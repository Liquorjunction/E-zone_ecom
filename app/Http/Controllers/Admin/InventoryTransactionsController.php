<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Interfaces\InventoryTransactionsRepositoryInterface as InventoryTransactionsRepo;
use App\Http\Requests\Admin\InventoryTransactions\ReadInventoryTransactionsRequest;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class InventoryTransactionsController extends Controller
{
    public function index()
    {
        return view('admin.pages.inventory_transactions.index');
    }

    public function getDataTable(InventoryTransactionsRepo $inventoryTransactionsRepo)
    {
        try {
            return DataTables::of($inventoryTransactionsRepo->getdataforDataTable())
                ->addIndexColumn()
                ->addColumn('product_name', function ($transaction) {
                    $product_name = $transaction->product_name;
                    $varient_name = $transaction->variant_name;

                    // Extract English product name
                    $product_en = 'N/A';
                    if (is_string($product_name)) {
                        $decoded = json_decode($product_name, true);
                        if (is_array($decoded) && isset($decoded['en'])) {
                            $product_en = $decoded['en'];
                        } else {
                            $product_en = $product_name;
                        }
                    } elseif (is_array($product_name) && isset($product_name['en'])) {
                        $product_en = $product_name['en'];
                    } elseif (is_object($product_name) && isset($product_name->en)) {
                        $product_en = $product_name->en;
                    } elseif ($product_name) {
                        $product_en = $product_name;
                    }

                    // Extract English variant name
                    $variant_en = null;
                    if (is_string($varient_name)) {
                        $decoded = json_decode($varient_name, true);
                        if (is_array($decoded) && isset($decoded['en'])) {
                            $variant_en = $decoded['en'];
                        } else {
                            $variant_en = $varient_name;
                        }
                    } elseif (is_array($varient_name) && isset($varient_name['en'])) {
                        $variant_en = $varient_name['en'];
                    } elseif (is_object($varient_name) && isset($varient_name->en)) {
                        $variant_en = $varient_name->en;
                    } elseif ($varient_name) {
                        $variant_en = $varient_name;
                    }

                    // Combine product name and variant name
                    if ($variant_en) {
                        return $product_en . ' - ' . $variant_en;
                    }
                    return $product_en;
                })
                ->addColumn('batch_no', function ($transaction) {
                    return $transaction->batch_no ?? 'N/A';
                })
                ->addColumn('reference_type', function ($transaction) {
                    return $transaction->reference_type ?? 'N/A';
                })
                ->addColumn('before_quantity', function ($transaction) {
                    return $transaction->before_quantity ?? 'N/A';
                })
                ->addColumn('after_quantity', function ($transaction) {
                    return $transaction->after_quantity ?? 'N/A';
                })
                ->addColumn('quantity_change', function ($transaction) {
                    return $transaction->quantity_change ?? 'N/A';
                })
                ->addColumn('quantity', function ($transaction) {
                    return $transaction->quantity ?? 'N/A';
                })
                ->addColumn('type', function ($transaction) {
                    return $transaction->type ?? 'N/A';
                })
                ->addColumn('created_at', function ($transaction) {
                    return $transaction->created_at ? $transaction->created_at->format('d M Y, h:i A') : 'N/A';
                })
                ->addColumn('location_type', function ($transaction) {
                    // Adjust the field name as per your actual data structure
                    if (isset($transaction->location_type)) {
                        return ucfirst($transaction->location_type); // e.g., 'Store' or 'Warehouse'
                    }
                    // Example: if you use a boolean or another field
                    // return $transaction->is_store ? 'Store' : 'Warehouse';
                    return 'N/A';
                })
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch data for DataTable.'], 500);
        }
    }
}
