<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\DestroyInventoryRequest;
use App\Http\Requests\Admin\Inventory\EditInventoryRequest;
use App\Http\Requests\Admin\Inventory\ReadInventoryRequest;
use App\Http\Requests\Admin\Inventory\SeedAllInventoryRequest;
use App\Http\Requests\Admin\Inventory\StoreBatchInventoryRequest;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface as InventoryRepo;
use App\Repositories\Admin\Interfaces\InventoryTransactionsRepositoryInterface as InventoryTransactionsRepo;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface as StoreRepo;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface as WarehouseRepo;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function index(WarehouseRepo $warehouseRepo, StoreRepo $storeRepo)
    {
        $warehouses = $warehouseRepo->getActiveWarehouses();
        $stores     = $storeRepo->getAllActiveStores();

        return view('admin.pages.inventory.index', compact('warehouses', 'stores'));
    }

    public function create(ProductRepo $productRepo, WarehouseRepo $warehouseRepo, StoreRepo $storeRepo)
    {
        abort_if(!Auth::guard('admin')->user()->can('inventories.create'), 403, 'Unauthorized action.');

        try {
            $products   = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $stores     = $storeRepo->getAllActiveStores();

            return view('admin.pages.inventory.create', compact('products', 'warehouses', 'stores'));
        } catch (\Exception $e) {
            Log::error('Error loading inventory create form: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'An error occurred while loading the create form.');
        }
    }

    public function store(StoreBatchInventoryRequest $request, InventoryService $inventoryService)
    {
        try {
            $data    = $request->validated();
            $adminId = Auth::guard('admin')->id();
            $inventoryService->createBatch($data, $adminId);

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Batch inventory created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating inventory batch: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while adding stock: ' . $e->getMessage());
        }
    }

    public function seedAll(SeedAllInventoryRequest $request, InventoryService $inventoryService)
    {
        try {
            $data = $request->validated();
            $adminId = Auth::guard('admin')->id();
            $result = $inventoryService->seedAllProductsWithInventory($data, $adminId);

            $message = sprintf(
                'Inventory seeded as a new batch for all products and variants. Created: %d.',
                $result['created'] ?? 0
            );

            return redirect()->route('admin.inventory.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error seeding inventory for all products: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while seeding inventory: ' . $e->getMessage());
        }
    }

    public function edit(
        EditInventoryRequest $request,
        InventoryRepo $inventoryRepo,
        ProductRepo $productRepo,
        WarehouseRepo $warehouseRepo,
        StoreRepo $storeRepo
    ) {
        try {
            $id            = $request->input('id');
            $referenceItem = $inventoryRepo->find($id);

            if (!$referenceItem) {
                return redirect()->route('admin.inventory.index')
                    ->with('error', 'Inventory record not found.');
            }

            $batchItems = $inventoryRepo->getByBatchNo($referenceItem->batch_no);
            $batchItems->load(['product', 'variant']);

            $products   = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $stores     = $storeRepo->getAllActiveStores();

            return view('admin.pages.inventory.create', compact(
                'referenceItem', 'batchItems', 'products', 'warehouses', 'stores'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading inventory edit form: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'An error occurred while loading the edit form.');
        }
    }

    public function update(StoreBatchInventoryRequest $request, InventoryService $inventoryService)
    {
        try {
            $data    = $request->validated();
            $id      = $request->input('id');
            $adminId = Auth::guard('admin')->id();
            $inventoryService->updateBatch($id, $data, $adminId);

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Batch updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating inventory batch: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the batch.');
        }
    }

    public function destroy(DestroyInventoryRequest $request, InventoryRepo $inventoryRepo)
    {
        try {
            $id      = $request->input('id');
            $adminId = Auth::guard('admin')->id();
            $item    = $inventoryRepo->find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory record not found.',
                ], 404);
            }

            $inventoryRepo->deleteByBatchNo($item->batch_no, $adminId);

            return response()->json([
                'success' => true,
                'message' => 'Batch deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting inventory batch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting.',
            ], 500);
        }
    }

    public function show(
        ReadInventoryRequest $request,
        InventoryRepo $inventoryRepo,
        InventoryTransactionsRepo $inventoryTransactionRepo
    ) {
        try {
            $id        = $request->input('id');
            $inventory = $inventoryRepo->find($id);

            if (!$inventory) {
                return redirect()->route('admin.inventory.index')
                    ->with('error', 'Inventory not found.');
            }

            $batchItems = $inventoryRepo->getByBatchNo($inventory->batch_no);
            $batchItems->load(['product', 'variant']);

            $allTransactions    = $inventoryTransactionRepo->getByBatchNo($inventory->batch_no);
            $productBatchHistory = $allTransactions->groupBy('inventory_id');

            return view('admin.pages.inventory.show', compact(
                'inventory', 'batchItems', 'productBatchHistory'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading inventory details: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'An error occurred while loading details.');
        }
    }

    public function dataTable(ReadInventoryRequest $request, InventoryRepo $inventoryRepo)
    {
        try {
            $batches = $inventoryRepo->getBatchesForDataTable($request->all());

            return DataTables::of($batches)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A'))
                ->addColumn('batch_no', fn($row) => '<span class="badge bg-primary">' . ($row->batch_no ?? 'N/A') . '</span>')
                ->addColumn('location', function ($row) {
                    if ($row->location_type === 'store') {
                        return $row->store->store_name ?? 'Store';
                    }
                    return $row->warehouse->warehouse_name ?? 'Warehouse';
                })
                ->addColumn('items_summary', fn($row) => '<span class="badge bg-info">' . $row->items_count . ' Items</span>')
                ->addColumn('total_quantity', fn($row) => $row->total_quantity)
                ->addColumn('subtotal_cost', function ($row) {
                    $symbol = config('app.CURRENCY_SYMBOL', '$');
                    return '<span class="badge bg-success">' . $symbol . number_format($row->subtotal_cost, 2) . '</span>';
                })
                ->addColumn('action', function ($row) use ($request, $inventoryRepo) {
                    $canRead   = $request->user()->can('inventories.read');
                    $canUpdate = $request->user()->can('inventories.update');
                    $canDelete = $request->user()->can('inventories.delete');
                    $isSold    = $inventoryRepo->isBatchSold($row->batch_no);

                    return view('admin.partials.datatable-actions', [
                        'id'         => $row->id,
                        'view_url'   => $canRead   ? route('admin.inventory.show',    ['id' => $row->id]) : null,
                        'edit_url'   => ($canUpdate && !$isSold) ? route('admin.inventory.edit',    ['id' => $row->id]) : null,
                        'delete_url' => ($canDelete && !$isSold) ? route('admin.inventory.destroy', ['id' => $row->id]) : null,
                    ])->render();
                })
                ->rawColumns(['batch_no', 'items_summary', 'subtotal_cost', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading inventory datatable: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load inventory data.'], 500);
        }
    }

    public function getVariants(Request $request, ProductRepo $productRepo)
    {
        if (!Auth::guard('admin')->user()->can('inventories.read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$request->product_id) {
            return response()->json([]);
        }

        $variants = $productRepo->getVariantsByProductId($request->product_id);

        return response()->json($variants);
    }
}
