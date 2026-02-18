<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\dispatch\StoreDispatchRequest;
use App\Services\Admin\DispatchService;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    protected $dispatchService;

    public function __construct(DispatchService $dispatchService)
    {
        $this->dispatchService = $dispatchService;
    }

    public function index()
    {
        // For Showing Dispatch History
        return view('admin.pages.dispatch.index');
    }

    public function create(\App\Repositories\Admin\Interfaces\OrderRepositoryInterface $orderRepo)
    {
        // Load all orders for dropdown
        $orders = $orderRepo->getAllOrders();
        // Optionally, load stores and warehouses for dropdowns
        $stores = \App\Models\Store::all();
        $warehouses = \App\Models\Warehouse::all();
        // Optionally, pre-select order items if order_id is present in query
        $orderItems = [];
        $selectedOrder = null;
        $orderId = request('order_id');
        if ($orderId) {
            $selectedOrder = $orderRepo->getOrderById($orderId);
            if ($selectedOrder && $selectedOrder->items) {
                $orderItems = $selectedOrder->items->all(); // always array
            }
        }
        return view('admin.pages.dispatch.create', compact('orders', 'stores', 'warehouses', 'orderItems', 'selectedOrder'));
    }

    public function store(StoreDispatchRequest $request)
    {
        $data = $request->validated();
        // Transform items to match DispatchService and model fields
        $items = [];
        if (isset($data['dispatch_qty'])) {
            foreach ($data['dispatch_qty'] as $orderItemId => $qty) {
                $items[] = [
                    'order_item_id' => $orderItemId,
                    'quantity' => $qty,
                    'inventory_id' => $data['inventory_id'][$orderItemId] ?? null,
                    'dispatch_from_store_id' => $data['dispatch_from_store_id'][$orderItemId] ?? null,
                    'dispatch_from_warehouse_id' => $data['dispatch_from_warehouse_id'][$orderItemId] ?? null,
                ];
            }
        }
        $payload = [
            'order_id' => $data['order_id'],
            'items' => $items,
            'dispatch_method' => $data['dispatch_method'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ];
        try {
            $dispatchedItems = $this->dispatchService->createDispatch($payload);
            return response()->json([
                'success' => true,
                'message' => 'Dispatch created successfully.',
                'data' => $dispatchedItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create dispatch: ' . $e->getMessage()
            ], 500);
        }
    }
}
