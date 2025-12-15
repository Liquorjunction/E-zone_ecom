<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\StoreInventoryRequest;
use App\Http\Requests\Admin\Inventory\UpdateInventoryRequest;
use App\Http\Requests\Admin\Inventory\DestroyInventoryRequest;
use App\Http\Requests\Admin\Inventory\ReadInventoryRequest;
use App\Http\Requests\Admin\Inventory\EditInventoryRequest;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface as InventoryRepo;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface as ProductRepo;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface as WarehouseRepo;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface as StoreRepo;
use App\Models\Inventory;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.inventory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ProductRepo $productRepo, WarehouseRepo $warehouseRepo, StoreRepo $storeRepo)
    {
        try {
            // Fetch products, warehouses, variants, and stores for the create form from the repositories

            $products = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $variants = $productRepo->getProductVariants( null); // Fetch all variants
            $stores = $storeRepo->getAllActiveStores();

            return view('admin.pages.inventory.create', compact('products', 'warehouses', 'variants', 'stores'));
        } catch (\Exception $e) {
            Log::error('Error loading inventory create form: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')->with('error', 'An error occurred while loading the create form.');
        }
    }

    public function store(StoreInventoryRequest $request, InventoryRepo $inventoryRepo)
    {
        try {
            $data = $request->validated();
            $inventory = $inventoryRepo->create($data);

            if ($inventory) {
                return redirect()->route('admin.inventory.index')->with('success', 'Inventory item created successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to create inventory item.');
            }
        } catch (\Exception $e) {
            Log::error('Error creating inventory item: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while creating the inventory item.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditInventoryRequest $request, InventoryRepo $inventoryRepo , ProductRepo $productRepo, WarehouseRepo $warehouseRepo, StoreRepo $storeRepo)
    {
        try {
            $id = $request->validated()['id'];
            $inventory = $inventoryRepo->find($id);

            // Fetch products, warehouses, variants, and stores for the edit form from the repositories
            $products = $productRepo->getAllActiveProducts();
            $warehouses = $warehouseRepo->getActiveWarehouses();
            $productId = $inventory->variant->product_id ?? null;
            $variants = $productRepo->getProductVariants($productId);
            $stores = $storeRepo->getAllActiveStores();

            return view('admin.pages.inventory.edit', compact('inventory', 'products', 'warehouses', 'variants', 'stores'));
        } catch (\Exception $e) {
            Log::error('Error fetching inventory item for edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching inventory item details.');
        }
    }

    public function show(ReadInventoryRequest $request, InventoryRepo $inventoryRepo)
    {
        try {
            $id = $request->input('id') ?? $request->route('id');
            $inventory = $inventoryRepo->find($id);
            return view('admin.pages.inventory.show', compact('inventory'));
        } catch (\Exception $e) {
            Log::error('Error showing inventory item: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while retrieving the inventory item.');
        }
    }

    public function update(UpdateInventoryRequest $request, InventoryRepo $inventoryRepo, $id)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();
            $inventoryRepo->update($id, $data);

            return redirect()->route('admin.inventory.index')->with('success', 'Inventory item updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating inventory item: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while updating the inventory item.');
        }
    }

    public function destroy(DestroyInventoryRequest $request, InventoryRepo $inventoryRepo, $id)
    {
        try {
            $id = $request->validated()['id'];
            $inventoryRepo->delete($id);

            return redirect()->route('admin.inventory.index')->with('success', 'Inventory item deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting inventory item: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while deleting the inventory item.');
        }
    }

        /**
     * Toggle the status of the inventory item (active/inactive).
     */
    public function toggleStatus(ReadInventoryRequest $request, InventoryRepo $inventoryRepo)
    {
        try {
            $id = $request->input('id') ?? $request->route('id');
            $inventoryRepo->updateStatus($id);
            return redirect()->route('admin.inventory.index')->with('success', 'Inventory status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle inventory status', [
                'inventory_id' => $request->input('id') ?? $request->route('id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.inventory.index')->with('error', 'Failed to toggle inventory status: ' . $e->getMessage());
        }
    }

    /**
     * Get inventory data for DataTable.
     */
    public function getDataForDataTable(InventoryRepo $inventoryRepo, ReadInventoryRequest $request)
    {
        try {
            $inventories = $inventoryRepo->getDataforDataTable();
            return DataTables::of($inventories)
                ->addIndexColumn()
                ->addColumn('product', function ($inventory) {
                    return $inventory->product->name ?? '-';
                })
                ->addColumn('warehouse', function ($inventory) {
                    return $inventory->warehouse->warehouse_name ?? '-';
                })
                ->addColumn('quantity', function ($inventory) {
                    return $inventory->quantity;
                })
                ->addColumn('status', function ($inventory) use ($request) {
                    $data['toggle_url'] = $request->user()->can('inventories.update') ? route('admin.inventory.toggleStatus', ['id' => $inventory->id]) : '';
                    $data['status'] = $inventory->status;
                    $data['id'] = $inventory->id;
                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($inventory) use ($request) {
                    $data['edit_url'] = $request->user()->can('inventories.update') ? route('admin.inventory.edit', ['id' => $inventory->id]) : '';
                    $data['delete_url'] = $request->user()->can('inventories.delete') ? route('admin.inventory.destroy', ['id' => $inventory->id]) : '';
                    $data['id'] = $inventory->id;
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Failed to fetch inventory for datatable', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Alias for routes that expect `dataTable` method name.
     * Kept for backward compatibility with routing definitions.
     */
    public function dataTable(InventoryRepo $inventoryRepo, ReadInventoryRequest $request)
    {
        return $this->getDataForDataTable($inventoryRepo, $request);
    }

    /**
     * Return active variants for a given product as JSON.
     */
    public function variants(Request $request)
    {
        $productId = $request->input('product_id');

        if (empty($productId)) {
            return response()->json([], 200);
        }

        $variants = ProductVariant::where('product_id', $productId)
            ->where('status', 'active')
            ->get(['id', 'variant_name']);

        return response()->json($variants, 200);
    }
}
