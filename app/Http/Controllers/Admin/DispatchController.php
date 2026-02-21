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
}
