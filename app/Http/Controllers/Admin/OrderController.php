<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Orders\CancelOrderRequest;
use App\Http\Requests\Admin\Orders\ReadOrderRequest;
use App\Models\Order;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function list(OrderRepositoryInterface $orderRepo)
    {
        $orders = $orderRepo->getAllOrders();
        $result = [];
        foreach ($orders as $order) {
            $result[] = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name ?? ($order->user->email ?? 'N/A'),
            ];
        }
        return response()->json($result);
    }

    // AJAX: Get order items for a given order (query param: ?order_id=)
    public function items(OrderRepositoryInterface $orderRepo)
    {
        $orderId = request('order_id');
        $order = $orderRepo->getOrderById($orderId);
        if (!$order) return response()->json([]);
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->id,
                'product_name' => $item->product->name ?? '',
                'variant_name' => $item->variant->name ?? '',
                'quantity' => $item->quantity,
            ];
        }
        return response()->json($items);
    }

    // AJAX: Get inventories for a given order item (query param: ?order_item_id=)
    public function orderItemInventories()
    {
        $orderItemId = request('order_item_id');
        $item = \App\Models\OrderItem::with('inventories')->find($orderItemId);
        if (!$item) return response()->json([]);
        $inventories = [];
        foreach ($item->inventories as $inv) {
            $inventories[] = [
                'id' => $inv->id,
                'location' => $inv->location,
                'quantity' => $inv->quantity,
            ];
        }
        return response()->json($inventories);
    }

    public function index()
    {
        return view('admin.pages.orders.index');
    }

    public function show(ReadOrderRequest $request, OrderRepositoryInterface $orderRepo)
    {
        try {
            $id = $request->input('id');
            $order = $orderRepo->getOrderById($id);

            return view('admin.pages.orders.show', compact('order'));
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(ReadOrderRequest $request, OrderRepositoryInterface $orderRepo)
    {
        try {
            $id = $request->input('id');
            $order = $orderRepo->getOrderById($id);

            return view('admin.pages.orders.edit', compact('order'));
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(ReadOrderRequest $request, OrderRepositoryInterface $orderRepo)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status');
            $order = $orderRepo->updateOrderStatus($id, $status);

            return redirect()->back()->with(['success' => true, 'message' => 'Order status updated successfully.']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateOrderStatus(ReadOrderRequest $request, OrderRepositoryInterface $orderRepo)
    {
        try {
            $id = $request->input('id', $request->route('id'));
            $status = $request->input('status');
            $order = $orderRepo->updateOrderStatus($id, $status);

            // If AJAX, return JSON for frontend to open dispatch modal
            if ($request->ajax() || $request->wantsJson()) {
                if ($status === 'accepted') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Order accepted successfully.',
                        'open_dispatch_modal' => true,
                        'order_id' => $id,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Order status updated successfully.'
                ]);
            }

            // Fallback for non-AJAX
            return redirect()->back()->with(['success' => true, 'message' => 'Order status updated successfully.']);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancelOrder(CancelOrderRequest $request, OrderRepositoryInterface $orderRepo)
    {
        try {
            $id = $request->input('id', $request->route('id'));
            $cancelReason = $request->input('cancel_reason');
            $orderRepo->cancelOrder($id, $cancelReason);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Order cancelled successfully.']);
            }

            return redirect()->back()->with(['success' => true, 'message' => 'Order cancelled successfully.']);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function dataTable(ReadOrderRequest $request, OrderRepositoryInterface $orderRepo)
    {
        try {
            return DataTables::of($orderRepo->getDataForDataTable())
                ->addIndexColumn()
                ->addColumn('order_number', function ($order) {
                    return $order->order_number ?? 'N/A';
                })
                ->addColumn('created_at', function ($order) {
                    return $order->created_at->format('d M Y, h:i A');
                })
                ->addColumn('customer_name', function ($order) use ($orderRepo) {
                    $customerName = $orderRepo->getCustomerNameByOrderId($order->id);
                    return $customerName ?? 'N/A';
                })
                ->addColumn('total', function ($order) {
                    return $order->total ?? 'N/A';
                })
                ->addColumn('status', function ($order) {
                    return $order->status ?? 'N/A';
                })
                ->addColumn('cancel_reason', function ($order) {
                    return $order->cancel_reason ?? 'N/A';
                })
                ->addColumn('payment_method', function ($order) {
                    return $order->payment_method ?? 'N/A';
                })
                ->addColumn('action', function ($order) use ($request) {
                    $canRead = $request->user()->can('order.read');
                    $canUpdate = $request->user()->can('order.update');
                    $canDispatch = $request->user()->can('dispatch.create');

                    $actions = [
                        'id' => $order->id,
                    ];

                    // Define sets for statuses
                    $viewOnlyStatuses = ['cancelled', 'delivered', 'processing', 'ready_to_ship', 'shipped', 'out_for_delivery', 'completed'];

                    if ($order->status === 'accepted' && $canDispatch) {
                        $actions['dispatch_btn'] = true;
                    }
                    if ($order->status === 'pending') {
                        if ($canUpdate) {
                            $actions['accept_url'] = route('admin.orders.update-order-status', ['id' => $order->id, 'status' => 'accepted']);
                            $actions['cancel_url'] = route('admin.orders.cancel', ['id' => $order->id]);
                        }
                    } elseif (in_array($order->status, $viewOnlyStatuses, true)) {
                        if ($canRead) {
                            $actions['view_url'] = route('admin.orders.show', ['id' => $order->id]);
                        }
                    } else {
                        if ($canRead) {
                            $actions['view_url'] = route('admin.orders.show', ['id' => $order->id]);
                        }
                        if ($canUpdate) {
                            $actions['cancel_url'] = route('admin.orders.cancel', ['id' => $order->id]);
                        }
                    }

                    return view('admin.partials.datatable-actions', $actions)->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Order dataTable error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch orders data.'], 500);
        }
    }

    public function itemsForDispatch($orderId)
    {
        $order = \App\Models\Order::with(['items.product', 'items.variant'])->find($orderId);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }
        $items = [];
        foreach ($order->items as $item) {
            // Fetch inventories for this product and variant, eager load store/warehouse
            $inventories = \App\Models\Inventory::where('product_id', $item->product_id)
                ->where('product_variant_id', $item->variant_id)
                ->with(['store:id,store_name', 'warehouse:id,warehouse_name'])
                ->get(['id', 'store_id', 'warehouse_id', 'location_type', 'quantity']);

            // Map inventories to include display name
            $inventories = $inventories->map(function ($inv) {
                $name = null;
                if ($inv->location_type === 'store' && $inv->store) {
                    $name = $inv->store->store_name;
                } elseif ($inv->location_type === 'warehouse' && $inv->warehouse) {
                    $name = $inv->warehouse->warehouse_name;
                } else {
                    $name = 'Unknown';
                }
                return [
                    'id' => $inv->id,
                    'name' => $name,
                    'location_type' => $inv->location_type,
                    'quantity' => $inv->quantity,
                    'store_id' => $inv->store_id,
                    'warehouse_id' => $inv->warehouse_id,
                ];
            });

            $items[] = [
                'id' => $item->id,
                'product_name' => $item->product->name ?? '',
                'variant' => $item->variant->name ?? '',
                'ordered_qty' => $item->quantity,
                'inventories' => $inventories,
            ];
        }
        return response()->json(['success' => true, 'items' => $items]);
    }
}
