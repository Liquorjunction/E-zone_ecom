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

    public function updateOrderStatus(ReadOrderRequest $request, OrderRepositoryInterface $orderRepo)
    {
        try {
            $id = $request->input('id', $request->route('id'));
                $status = $request->input('status', $request->route('status') ?? 'accepted');
            $order = $orderRepo->updateOrderStatus($id, $status);
                $message = 'Order status updated successfully.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => $message, 'order_id' => $id, 'status' => $status]);
                }
                return redirect()->back()->with(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
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

                    if (($order->status === 'accepted' || $order->status === 'partially_dispatched') && $canDispatch) {
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
}
