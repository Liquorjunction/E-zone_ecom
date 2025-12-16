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
    /**
     * Display the inventory index page.
     */
    public function index(ReadInventoryRequest $request)
    {
        return view('admin.pages.inventory.index');
    }

    /**
     * Show the form for creating a new resource (Batch Inward).
     */
    public function create(
        ProductRepo $productRepo,
        WarehouseRepo $warehouseRepo,
        StoreRepo $storeRepo
    ) {
        // Manual Permission Check
        abort_if(!Auth::guard('admin')->user()->can('inventories.create'), 403, 'Unauthorized action.');

        try {
            $products = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $stores = $storeRepo->getAllActiveStores();

            // Variants are loaded via AJAX (getVariants) to improve page load speed
            return view('admin.pages.inventory.create', compact('products', 'warehouses', 'stores'));
        } catch (\Exception $e) {
            Log::error('Error loading inventory create form: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'An error occurred while loading the create form.');
        }
    }

    /**
     * Store a newly created resource in storage (Batch Processing).
     */
    public function store(
        StoreBatchInventoryRequest $request,
        InventoryRepo $inventoryRepo
    ) {
        try {
            $data = $request->validated();

            // Repository handles Transaction, Auto-Batch Number, and History
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
     * Show the form for editing the specified resource (Batch Mode).
     * This finds the batch belonging to the selected ID and opens the create view pre-filled.
     */
    public function edit(
        EditInventoryRequest $request,
        InventoryRepo $inventoryRepo,
        ProductRepo $productRepo,
        WarehouseRepo $warehouseRepo,
        StoreRepo $storeRepo
    ) {
        try {
            $id = $request->route('inventory');

            // 1. Find the reference item to get the Batch No
            $referenceItem = $inventoryRepo->find($id);

            if (!$referenceItem) {
                return redirect()->route('admin.inventory.index')->with('error', 'Inventory record not found.');
            }

            // 2. Fetch ALL items belonging to this Batch using the batch_no
            // We use direct Eloquent here or a repo method if available
            $batchItems = Inventory::with(['variant', 'product'])
                ->where('batch_no', $referenceItem->batch_no)
                ->get();

            $products = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $stores = $storeRepo->getAllActiveStores();

            // Reuse the 'create' view for batch editing, passing the reference item and list
            return view('admin.pages.inventory.create', compact('referenceItem', 'batchItems', 'products', 'warehouses', 'stores'));

        } catch (\Exception $e) {
            Log::error('Error loading inventory edit form: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')->with('error', 'An error occurred while loading the edit form.');
        }
    }

    /**
     * Update the specified resource in storage (Batch Update).
     * NOTE: We use StoreBatchInventoryRequest because we are submitting a list of items,
     * similar to the store method.
     */
    public function update(
        StoreBatchInventoryRequest $request,
        InventoryRepo $inventoryRepo
    ) {
        try {
            $data = $request->validated();

            // Get ID from route to identify the old batch
            $id = $request->route('inventory');
            $referenceItem = $inventoryRepo->find($id);

            if(!$referenceItem) {
                return redirect()->back()->with('error', 'Original batch not found.');
            }

            // Use Database Transaction to ensure data integrity
            DB::transaction(function () use ($inventoryRepo, $data, $referenceItem) {
                // 1. Delete old items in this batch
                Inventory::where('batch_no', $referenceItem->batch_no)->delete();

                // 2. Reuse the existing batch number to preserve history reference if desired
                $data['batch_no'] = $referenceItem->batch_no;

                // 3. Re-create items using the standard batch creation logic
                // Pass true/flag if needed to indicate this is an update vs new
                $inventoryRepo->createBatch($data);
            });

            return redirect()->route('admin.inventory.index')->with('success', 'Batch updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating inventory batch: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the batch.');
        }
    }

    /**
     * Show details of a specific batch.
     */
    public function show(
        ReadInventoryRequest $request,
        InventoryRepo $inventoryRepo
    ) {
        try {
            $id = $request->route('inventory');
            $inventory = $inventoryRepo->find($id);

            if (!$inventory) {
                return redirect()->route('admin.inventory.index')->with('error', 'Inventory not found.');
            }

            // Fetch all items in this batch to show detailed list
            $batchItems = Inventory::where('batch_no', $inventory->batch_no)
                ->with(['product', 'variant'])
                ->get();

            return view('admin.pages.inventory.show', compact('inventory', 'batchItems'));
        } catch (\Exception $e) {
            Log::error('Error loading inventory details: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')->with('error', 'An error occurred while loading details.');
        }
    }

    /**
     * Data handler for Yajra DataTables (Grouped by Batch).
     */
    public function dataTable(
        ReadInventoryRequest $request,
        InventoryRepo $inventoryRepo
    ) {
        try {
            // Ensure Repository has getBatchesForDataTable method, or use query here
            // Assuming we added getBatchesForDataTable to Interface/Repo as discussed
            $query = $inventoryRepo->getBatchesForDataTable($request->all());

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y, h:i A');
                })
                ->addColumn('batch_no', function ($row) {
                    return '<span class="badge bg-primary">' . ($row->batch_no ?? 'N/A') . '</span>';
                })
                ->addColumn('location', function ($row) {
                    if ($row->location_type === 'store') {
                        return $row->store->store_name ?? $row->store->name ?? 'Store N/A';
                    } elseif ($row->location_type === 'warehouse') {
                        return $row->warehouse->warehouse_name ?? $row->warehouse->name ?? 'Warehouse N/A';
                    }
                    return 'N/A';
                })
                ->addColumn('items_summary', function ($row) {
                    // Requires 'items_count' and 'total_quantity' in the query selection
                    return '<span class="badge bg-info">' . $row->items_count . ' Items</span> <span class="text-muted small">(' . number_format($row->total_quantity) . ' Qty)</span>';
                })
                ->editColumn('status', function ($row) {
                    // Status badge (static for batch view)
                    $status = $row->status;
                    $class = $status === 'active' ? 'success' : 'danger';
                    return '<span class="badge bg-' . $class . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('action', function ($row) use ($request) {
                    $canRead   = $request->user()->can('inventories.read');
                    $canUpdate = $request->user()->can('inventories.update');
                    $canDelete = $request->user()->can('inventories.delete');

                    // Pass the ID of the grouped row (MAX(id)) to trigger edit/delete logic
                    return view('admin.partials.datatable-actions', [
                        'id' => $row->id,
                        'view_url'   => $canRead ? route('admin.inventory.show', $row->id) : null,
                        'edit_url'   => $canUpdate ? route('admin.inventory.edit', $row->id) : null,
                    ])->render();
                })
                ->rawColumns(['batch_no', 'items_summary', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading inventory datatable: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load inventory data.'], 500);
        }
    }

    /**
     * AJAX Helper: Get Variants for a Product.
     * Required for the Create Page dropdown.
     */
    public function getVariants(Request $request)
    {
        // Permission check
        if (!Auth::guard('admin')->user()->can('inventories.read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$request->product_id) {
            return response()->json([]);
        }

        $variants = ProductVariant::where('product_id', $request->product_id)
            ->select('id', 'variant_name')
            ->get();

        return response()->json($variants);
    }
}
