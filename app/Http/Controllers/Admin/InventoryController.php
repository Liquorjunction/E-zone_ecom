<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\DestroyInventoryRequest;
use App\Http\Requests\Admin\Inventory\EditInventoryRequest;
use App\Http\Requests\Admin\Inventory\ReadInventoryRequest;
use App\Http\Requests\Admin\Inventory\StoreBatchInventoryRequest;
use App\Models\Inventory;
use App\Models\ProductVariant;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface as InventoryRepo;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface as StoreRepo;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface as WarehouseRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function index(ReadInventoryRequest $request)
    {
        return view('admin.pages.inventory.index');
    }

    public function create(
        ProductRepo $productRepo,
        WarehouseRepo $warehouseRepo,
        StoreRepo $storeRepo
    ) {
        abort_if(!Auth::guard('admin')->user()->can('inventories.create'), 403, 'Unauthorized action.');

        try {
            $products = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $stores = $storeRepo->getAllActiveStores();

            return view('admin.pages.inventory.create', compact('products', 'warehouses', 'stores'));
        } catch (\Exception $e) {
            Log::error('Error loading inventory create form: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'An error occurred while loading the create form.');
        }
    }

    public function store(StoreBatchInventoryRequest $request, InventoryRepo $inventoryRepo)
    {
        try {
            $data = $request->validated();
            $inventoryRepo->createBatch($data);

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Batch inventory created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating inventory batch: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while adding stock: ' . $e->getMessage());
        }
    }

    /**
     * Edit Batch.
     * Uses $request->input('id') to identify the batch.
     */
    public function edit(
        EditInventoryRequest $request, // Validates 'id' exists
        InventoryRepo $inventoryRepo,
        ProductRepo $productRepo,
        WarehouseRepo $warehouseRepo,
        StoreRepo $storeRepo
    ) {
        try {
            // Fetch ID from query string ?id=...
            $id = $request->input('id');

            $referenceItem = $inventoryRepo->find($id);

            if (!$referenceItem) {
                return redirect()->route('admin.inventory.index')->with('error', 'Inventory record not found.');
            }

            // Fetch Items in this batch
            $batchItems = Inventory::with(['variant', 'product'])
                ->where('batch_no', $referenceItem->batch_no)
                ->get();

            $products = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $stores = $storeRepo->getAllActiveStores();

            return view('admin.pages.inventory.create', compact('referenceItem', 'batchItems', 'products', 'warehouses', 'stores'));

        } catch (\Exception $e) {
            Log::error('Error loading inventory edit form: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')->with('error', 'An error occurred while loading the edit form.');
        }
    }

    /**
     * Update Batch.
     * Uses hidden input 'id' in the form to identify the batch.
     */
    public function update(
        StoreBatchInventoryRequest $request,
        InventoryRepo $inventoryRepo
    ) {
        try {
            $data = $request->validated();
            $id = $request->input('id');
            $inventoryRepo->updateBatch($id, $data);
            return redirect()->route('admin.inventory.index')->with('success', 'Batch updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating inventory batch: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the batch.');
        }
    }

    public function destroy(
        DestroyInventoryRequest $request,
        InventoryRepo $inventoryRepo
    ) {
        try {
            $id = $request->input('id');
            $item = $inventoryRepo->find($id);

            if ($item) {
                Inventory::where('batch_no', $item->batch_no)->delete();
            }

            if ($request->ajax()) {
                return response()->json(['success' => 'Batch deleted successfully.']);
            }

            return redirect()->route('admin.inventory.index')->with('success', 'Batch deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting inventory batch: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'An error occurred while deleting.'], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while deleting the inventory.');
        }
    }

    public function show(
        ReadInventoryRequest $request,
        InventoryRepo $inventoryRepo
    ) {
        try {
            $id = $request->input('id');
            $inventory = $inventoryRepo->find($id);

            if (!$inventory) {
                return redirect()->route('admin.inventory.index')->with('error', 'Inventory not found.');
            }

            $batchItems = Inventory::where('batch_no', $inventory->batch_no)
                ->with(['product', 'variant'])
                ->get();


            // Build productBatchHistory: [inventory_id => [transactions...]]
            $productBatchHistory = [];
            foreach ($batchItems as $item) {
                $inventoryId = $item->id;
                $productBatchHistory[$inventoryId] = \App\Models\InventoryTransaction::where('inventory_id', $inventoryId)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            return view('admin.pages.inventory.show', compact('inventory', 'batchItems', 'productBatchHistory'));
        } catch (\Exception $e) {
            Log::error('Error loading inventory details: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')->with('error', 'An error occurred while loading details.');
        }
    }

    public function dataTable(ReadInventoryRequest $request, InventoryRepo $inventoryRepo)
    {
        try {
            $query = $inventoryRepo->getBatchesForDataTable($request->all());

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y, h:i A'))
                ->addColumn('batch_no', fn($row) => '<span class="badge bg-primary">'.($row->batch_no ?? 'N/A').'</span>')
                ->addColumn('location', function ($row) {
                    if ($row->location_type === 'store') return $row->store->store_name ?? 'Store';
                    return $row->warehouse->warehouse_name ?? 'Warehouse';
                })
                ->addColumn('items_summary', fn($row) => '<span class="badge bg-info">'.$row->items_count.' Items</span>')
                ->addColumn('total_quantity', fn($row) => $row->total_quantity)
                ->addColumn('subtotal_cost', function($row) {
                    $symbol = config('app.CURRENCY_SYMBOL', '$');
                    return '<span class="badge bg-success">' . $symbol . number_format($row->subtotal_cost, 2) . '</span>';
                })
                ->addColumn('action', function ($row) use ($request) {
                    $canRead = $request->user()->can('inventories.read');
                    $canUpdate = $request->user()->can('inventories.update');
                    $canDelete = $request->user()->can('inventories.delete');

                    // Check if any product in batch is sold
                    $batchVariantIds = \App\Models\Inventory::where('batch_no', $row->batch_no)->pluck('product_variant_id')->toArray();
                    $soldCount = \App\Models\OrderItem::whereIn('variant_id', $batchVariantIds)->count();

                    $editUrl = ($canUpdate && $soldCount == 0) ? route('admin.inventory.edit', ['id' => $row->id]) : null;
                    $deleteUrl = ($canDelete && $soldCount == 0) ? route('admin.inventory.destroy', ['id' => $row->id]) : null;

                    return view('admin.partials.datatable-actions', [
                        'id' => $row->id,
                        'view_url' => $canRead ? route('admin.inventory.show', ['id' => $row->id]) : null,
                        'edit_url' => $editUrl,
                        'delete_url' => $deleteUrl,
                    ])->render();
                })
                ->rawColumns(['batch_no', 'items_summary', 'subtotal_cost', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading inventory datatable: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load inventory data.'], 500);
        }
    }

    public function getVariants(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('inventories.read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if (!$request->product_id) return response()->json([]);

        return response()->json(ProductVariant::where('product_id', $request->product_id)->select('id', 'variant_name')->get());
    }
}
