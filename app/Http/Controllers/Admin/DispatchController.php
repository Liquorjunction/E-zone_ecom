<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dispatch\ReadDispatchRequest;
use App\Http\Requests\Admin\Dispatch\EditDispatchRequest;
use App\Http\Requests\Admin\Dispatch\StoreDispatchRequest;
use App\Http\Requests\Admin\Dispatch\UpdateDispatchStatusRequest;
use App\Models\Store;
use App\Models\Warehouse;
use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface as DispatchRepo;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface as OrderRepo;
use App\Services\DispatchService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DispatchController extends Controller
{
    public function index(ReadDispatchRequest $request, DispatchRepo $dispatchRepository)
    {
        $filters = $request->only(['status', 'order_number']);
        $dispatches = $dispatchRepository->getAllDispatches($filters);

        return view('admin.pages.dispatch.index', compact('dispatches'));
    }

    public function create(EditDispatchRequest $request, OrderRepo $orderRepository)
    {
        $order = $orderRepository->getOrderWithItemsAndInventory($request->order_id);
        $stores = Store::all();
        $warehouses = Warehouse::all();
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

    public function datatable(ReadDispatchRequest $request, DispatchRepo $dispatchRepository)
    {
        if ($request->ajax()) {
            $query = $dispatchRepository->getDataforDatatable();

            return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('status') && $request->status != '') {
                        $query->where('status', $request->status);
                    }
                    if ($request->has('order_number') && $request->order_number != '') {
                        $query->whereHas('order', function ($q) use ($request) {
                            $q->where('order_number', 'like', "%{$request->order_number}%");
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
                    $statusClass = match (strtolower($dispatch->status)) {
                        'dispatched' => 'bg-info',
                        'delivered' => 'bg-success',
                        'returned' => 'bg-warning text-dark',
                        'cancelled' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    return '<span class="badge ' . $statusClass . '">' . ucfirst($dispatch->status) . '</span>';
                })
                ->addColumn('date', function ($dispatch) {
                    $date = $dispatch->dispatched_at ? \Carbon\Carbon::parse($dispatch->dispatched_at)->format('d M Y, h:i A') : '—';
                    $by = optional($dispatch->dispatchedBy)->name ?? 'System';
                    return '<div class="small fw-medium">' . $date . '</div><div class="small text-muted mt-1">By: ' . $by . '</div>';
                })
                ->addColumn('action', function ($dispatch) {
                    if (strtolower($dispatch->status) === 'dispatched') {
                        $deliverUrl = route('admin.dispatch.update-status', $dispatch->id);
                        $csrf = csrf_field();
                        $method = method_field('PUT');
                        return '
                        <div class="btn-group">
                            <form action="' . $deliverUrl . '" method="POST" class="d-inline">
                                ' . $csrf . $method . '
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark Delivered" onclick="return confirm(\'Confirm delivery of this package?\')">
                                    <i class="bi bi-check2-circle"></i> Deliver
                                </button>
                            </form>
                            <form action="' . $deliverUrl . '" method="POST" class="d-inline ms-1">
                                ' . $csrf . $method . '
                                <input type="hidden" name="status" value="returned">
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Mark Returned (RTO)" onclick="return confirm(\'Confirm return? This will automatically restock the inventory back to its source location.\')">
                                    <i class="bi bi-arrow-return-left"></i> Return
                                </button>
                            </form>
                        </div>';
                    }
                    return '';
                })
                ->rawColumns(['dispatch_id', 'order_number', 'product', 'location', 'status_badge', 'date', 'action'])
                ->make(true);
        }

        return view('admin.pages.dispatch.index');
    }
}
