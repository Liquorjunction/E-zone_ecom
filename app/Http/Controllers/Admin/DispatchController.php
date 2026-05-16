<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dispatch\ReadDispatchRequest;
use App\Http\Requests\Admin\Dispatch\EditDispatchRequest;
use App\Http\Requests\Admin\Dispatch\StoreDispatchRequest;
use App\Http\Requests\Admin\Dispatch\UpdateDispatchStatusRequest;
use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface as DispatchRepo;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface as InventoryRepo;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface as OrderRepo;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface as StoreRepo;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface as WarehouseRepo;
use App\Services\DispatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DispatchController extends Controller
{
    public function index()
    {
        return view('admin.pages.dispatch.index');
    }

    public function create(EditDispatchRequest $request, OrderRepo $orderRepository, StoreRepo $storeRepo, WarehouseRepo $warehouseRepo)
    {
        $order = $orderRepository->getOrderWithItemsAndInventory($request->order_id);

        $stores = $storeRepo->getAllActiveStores();
        $warehouses = $warehouseRepo->getActiveWarehouses();

        return view('admin.pages.dispatch.create', compact('order', 'stores', 'warehouses'));
    }

    public function store(StoreDispatchRequest $request, DispatchService $dispatchService)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            $dispatchService->processBatchDispatch($request->validated(), $adminId);

            return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch processed successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(UpdateDispatchStatusRequest $request, $id, DispatchService $dispatchService)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            $dispatchService->updateStatus($id, $request->status, $adminId);

            return back()->with('success', 'Dispatch status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getItemInventory(Request $request, OrderRepo $orderRepository, InventoryRepo $inventoryRepo)
    {
        $request->validate(['order_item_id' => 'required|exists:order_items,id']);

        $orderItem = $orderRepository->getOrderItemById($request->order_item_id);

        // Fetch every active inventory record that holds stock for this variant/product
        $inventories = $inventoryRepo->getInventoryForOrderItem($orderItem);

        $result = $inventories->map(function ($inv) {
            $locationType = $inv->store_id ? 'store' : 'warehouse';

            if ($inv->store_id && $inv->store) {
                $label = method_exists($inv->store, 'getTranslation')
                    ? $inv->store->getTranslation('store_name', 'en')
                    : ($inv->store->store_name ?? $inv->store->name ?? 'Store #' . $inv->store_id);
            } elseif ($inv->warehouse_id && $inv->warehouse) {
                $label = method_exists($inv->warehouse, 'getTranslation')
                    ? $inv->warehouse->getTranslation('warehouse_name', 'en')
                    : ($inv->warehouse->warehouse_name ?? $inv->warehouse->name ?? 'Warehouse #' . $inv->warehouse_id);
            } else {
                $label = 'Unknown location';
            }

            return [
                'id'                 => $inv->id,
                'location_type'      => $locationType,
                'location_label'     => $label,
                'available_quantity' => max(0, $inv->quantity ?? 0),
                'batch_no'           => $inv->batch_no,
            ];
        })
        // Only surface locations that actually have stock
        ->filter(fn($inv) => $inv['available_quantity'] > 0)
        ->values();

        return response()->json(['inventory' => $result]);
    }

    public function getStatusList(DispatchRepo $dispatchRepository)
    {
        try {
            $statuses = $dispatchRepository->getDispatchStatusList();

            return response()->json([
                'success' => true,
                'statuses' => $statuses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function datatable(ReadDispatchRequest $request, DispatchRepo $dispatchRepository)
    {
        if ($request->ajax()) {
            $query = $dispatchRepository->getDataforDatatable();

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->filled('status')) {
                        $query->where('status', $request->input('status'));
                    }

                    if ($request->filled('order_number')) {
                        $keyword = trim((string) $request->input('order_number'));
                        $normalizedKeyword = ltrim($keyword, '#');

                        $query->whereHas('order', function ($q) use ($keyword, $normalizedKeyword) {
                            $q->where('order_number', 'like', "%{$keyword}%")
                                ->orWhere('id', 'like', "%{$keyword}%");

                            if ($normalizedKeyword !== $keyword && $normalizedKeyword !== '') {
                                $q->orWhere('order_number', 'like', "%{$normalizedKeyword}%")
                                    ->orWhere('id', 'like', "%{$normalizedKeyword}%");
                            }
                        });
                    }

                    $keyword = $request->input('search.value');
                    if (!empty($keyword)) {
                        $keyword = trim((string) $keyword);
                        $normalizedKeyword = ltrim($keyword, '#');

                        $query->where(function ($q) use ($keyword, $normalizedKeyword) {
                            $q->where('order_dispatch.id', 'like', "%{$keyword}%")
                                ->orWhere('order_dispatch.order_id', 'like', "%{$keyword}%")
                                ->orWhereHas('order', function ($orderQuery) use ($keyword) {
                                    $orderQuery->where('order_number', 'like', "%{$keyword}%")
                                        ->orWhere('id', 'like', "%{$keyword}%");
                                })
                                ->orWhereHas('orderItem.product', function ($productQuery) use ($keyword) {
                                    $productQuery->where('name->en', 'like', "%{$keyword}%")
                                        ->orWhere('name->fr', 'like', "%{$keyword}%")
                                        ->orWhere('name->ar', 'like', "%{$keyword}%");
                                });

                            if ($normalizedKeyword !== $keyword && $normalizedKeyword !== '') {
                                $q->orWhere('order_dispatch.id', 'like', "%{$normalizedKeyword}%")
                                    ->orWhere('order_dispatch.order_id', 'like', "%{$normalizedKeyword}%")
                                    ->orWhereHas('order', function ($orderQuery) use ($normalizedKeyword) {
                                        $orderQuery->where('order_number', 'like', "%{$normalizedKeyword}%")
                                            ->orWhere('id', 'like', "%{$normalizedKeyword}%");
                                    })
                                    ->orWhereHas('orderItem.product', function ($productQuery) use ($normalizedKeyword) {
                                        $productQuery->where('name->en', 'like', "%{$normalizedKeyword}%")
                                            ->orWhere('name->fr', 'like', "%{$normalizedKeyword}%")
                                            ->orWhere('name->ar', 'like', "%{$normalizedKeyword}%");
                                    });
                            }
                        });
                    }
                })
                ->addColumn('dispatch_id', function ($dispatch) {
                    $method = $dispatch->dispatch_method ? '<div class="small text-muted text-uppercase mt-1" style="font-size: 0.7rem;"><i class="bi bi-truck"></i> ' . $dispatch->dispatch_method . '</div>' : '';
                    return '<span class="fw-medium text-secondary">#DSP-' . $dispatch->id . '</span>' . $method;
                })
                ->addColumn('order_number', function ($dispatch) {
                    $orderNum = optional($dispatch->order)->order_number ?? $dispatch->order_id;
                    $url = route('admin.orders.show', $dispatch->order_id);
                    return '<a href="' . $url . '" class="fw-semibold text-decoration-none">#' . $orderNum . '</a>';
                })
                ->addColumn('product', function ($dispatch) {
                    $product = optional($dispatch->orderItem)->product;
                    $productName = $product ? (method_exists($product, 'getTranslation') ? $product->getTranslation('name', 'en') : $product->name) : 'Unknown Product';
                    return '<div class="fw-medium text-dark text-truncate" style="max-width: 250px;" title="' . $productName . '">' . $productName . '</div>
                            <div class="small text-muted mt-1">Qty: <span class="fw-bold text-dark">' . $dispatch->quantity . '</span></div>';
                })
                ->addColumn('location', function ($dispatch) {
                    if ($dispatch->store) {
                        $name = method_exists($dispatch->store, 'getTranslation') ? $dispatch->store->getTranslation('store_name', 'en') : $dispatch->store->store_name;
                        return '<span class="badge bg-success-subtle text-success border border-success border-opacity-25"><i class="bi bi-shop me-1"></i> ' . $name . '</span>';
                    } elseif ($dispatch->warehouse) {
                        $name = method_exists($dispatch->warehouse, 'getTranslation') ? $dispatch->warehouse->getTranslation('warehouse_name', 'en') : $dispatch->warehouse->warehouse_name;
                        return '<span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25"><i class="bi bi-building me-1"></i> ' . $name . '</span>';
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('status_badge', function ($dispatch) {
                    $statusClass = match (strtolower($dispatch->status ?? '')) {
                        'dispatched' => 'bg-info',
                        'delivered' => 'bg-success',
                        'returned' => 'bg-warning text-dark',
                        'cancelled' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    return '<span class="badge ' . $statusClass . '">' . ucfirst($dispatch->status) . '</span>';
                })
                ->addColumn('date', function ($dispatch) use ($dispatchRepository) {
                    $date = $dispatch->dispatched_at ? \Carbon\Carbon::parse($dispatch->dispatched_at)->format('d M Y, h:i A') : '—';
                    $by = $dispatchRepository->getDispatchesByNamefromAdminTable($dispatch->dispatched_by);
                    return '<div class="small fw-medium">' . $date . '</div><div class="small text-muted mt-1">By: ' . $by . '</div>';
                })
                ->rawColumns(['dispatch_id', 'order_number', 'product', 'location', 'status_badge', 'date'])
                ->make(true);

            return $datatables;
        }
    }
}
