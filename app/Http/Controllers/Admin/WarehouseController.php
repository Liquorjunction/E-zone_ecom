<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Admin\Warehouse\UpdateWarehouseRequest;
use App\Http\Requests\Admin\Warehouse\DestroyWarehouseRequest;
use App\Http\Requests\Admin\Warehouse\ReadWarehouseRequest;
use App\Http\Requests\Admin\Warehouse\EditWarehouseRequest;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface as WarehouseRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function index()
    {
        try {
            return view('admin.pages.warehouses.index');
        } catch (\Exception $e) {
            Log::error('Failed to load warehouses index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to load warehouses: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('admin.pages.warehouses.create');
        } catch (\Exception $e) {
            Log::error('Failed to load warehouse create form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.warehouses.index')->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    public function store(StoreWarehouseRequest $request, WarehouseRepo $warehouseRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['warehouse_name', 'location'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }

                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------

            $warehouse = $warehouseRepo->createWarehouse($request, $data);
            return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create warehouse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->with('error', 'Failed to create warehouse: ' . $e->getMessage());
        }
    }

    public function edit(EditWarehouseRequest $request, WarehouseRepo $warehouseRepo)
    {
        try {
            $id = $request->validated()['id'];
            $warehouse = $warehouseRepo->getWarehouseById($id);
            return view('admin.pages.warehouses.edit', compact('warehouse'));
        } catch (\Exception $e) {
            Log::error('Failed to load warehouse for editing', [
                'warehouse_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.warehouses.index')->with('error', 'Failed to load warehouse for editing: ' . $e->getMessage());
        }
    }

    public function update(UpdateWarehouseRequest $request, WarehouseRepo $warehouseRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['warehouse_name', 'location'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }

                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------

            $warehouseRepo->updateWarehouse($id, $request, $data);
            return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update warehouse', [
                'warehouse_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->with('error', 'Failed to update warehouse: ' . $e->getMessage());
        }
    }

    public function destroy(DestroyWarehouseRequest $request, WarehouseRepo $warehouseRepo)
    {
        try {
            $id = $request->validated()['id'];
            $warehouseRepo->deleteWarehouse($id);
            return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete warehouse', [
                'warehouse_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.warehouses.index')->with('error', 'Failed to delete warehouse: ' . $e->getMessage());
        }
    }

    public function toggleStatus(ReadWarehouseRequest $request, WarehouseRepo $warehouseRepo)
    {
        try {
            $id = $request->validated()['id'];
            $warehouseRepo->updateStatus($id);
            return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle warehouse status', [
                'warehouse_id' => $request->validated()['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.warehouses.index')->with('error', 'Failed to toggle warehouse status: ' . $e->getMessage());
        }
    }

    public function getDataForDataTable(WarehouseRepo $warehouseRepo, ReadWarehouseRequest $request)
    {
        try {
            $warehouses = $warehouseRepo->getDataForDataTable();
            return DataTables::of($warehouses)
                ->addIndexColumn()
                ->addColumn('warehouse_name', function ($warehouse) {
                    return $warehouse->getTranslation('warehouse_name', 'en');
                })
                ->addColumn('warehouse_id', function ($warehouse) {
                    return $warehouse->warehouse_id;
                })
                ->addColumn('location', function ($warehouse) {
                    return $warehouse->getTranslation('location', 'en');
                })
                ->addColumn('status', function ($warehouse) use ($request) {
                    if ($request->user()->can('warehouse.update')) {
                        $data['toggle_url'] = $request->user()->can('warehouse.update') ? route('admin.warehouses.toggleStatus', ['id' => $warehouse->id]) : '';
                        $data['status'] = $warehouse->status;
                        $data['id'] = $warehouse->id;
                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $warehouse->status])->render();
                    }
                })
                ->addColumn('action', function ($warehouse) use ($request) {
                    $data['edit_url'] = $request->user()->can('warehouse.update') ? route('admin.warehouses.edit', ['id' => $warehouse->id]) : '';
                    $data['id'] = $warehouse->id;

                    if($request->user()->can('warehouse.delete') && $warehouse->inventories_count > 0){
                        $data['can_delete'] = false;
                    } else {
                        $data['delete_url'] = $request->user()->can('warehouse.delete') ? route('admin.warehouses.destroy', ['id' => $warehouse->id]) : '';
                    }
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Failed to fetch warehouses for datatable', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch data: ' . $e->getMessage()], 500);
        }
    }
}
