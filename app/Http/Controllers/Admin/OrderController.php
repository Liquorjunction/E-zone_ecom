<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Naps\ReadNapsRequest;
use App\Http\Requests\Admin\Orders\CancelOrderRequest;
use App\Http\Requests\Admin\Orders\ReadOrderRequest;
use App\Http\Requests\Admin\Orders\OrderInvoiceRequest;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface as OrderRepo;
use App\Services\InvoiceExportService;
use App\Services\MXPlusService;
use App\Services\OrderStatusService as OrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.pages.orders.index');
    }

    public function show(ReadOrderRequest $request, OrderRepo $orderRepo, OrderStatus $orderStatusService)
    {
        try {
            $id = $request->input('id');
            $order = $orderRepo->getOrderById($id);
            // Get allowed transitions for the current status
            $statusLabels = [
                'pending' => 'Pending',
                'order_confirmation' => 'Order Confirmation',
                'processing' => 'Processing',
                'ready_to_ship' => 'Ready to Ship',
                'shipped' => 'Shipped',
                'partially_shipped' => 'Partially Shipped',
                'out_for_delivery' => 'Out for Delivery',
                'delivered' => 'Delivered',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'returned' => 'Returned',
                'failed_delivery' => 'Failed Delivery',
            ];
            $service = $orderStatusService;
            $transitions = $service->getAvailableTransitions($order->status);
            $availableStatuses = array_unique(array_merge([$order->status], $transitions));
            return view('admin.pages.orders.show', compact('order', 'availableStatuses', 'statusLabels'));
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit(ReadOrderRequest $request, OrderRepo $orderRepo)
    {
        try {
            $id = $request->input('id');
            $order = $orderRepo->getOrderById($id);
            return view('admin.pages.orders.edit', compact('order'));
        } catch (\Exception $e) {
            return redirect()->back()->with(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cancelOrder(CancelOrderRequest $request, OrderRepo $orderRepo, OrderStatus $orderStatusService)
    {
        try {
            $id = $request->input('id');
            $cancelReason = $request->input('cancel_reason');
            $order = $orderRepo->getOrderById($id);

            if (!$order) {
                return redirect()->back()->with(['success' => false, 'message' => 'Order not found.'], 404);
            }

            $adminId = Auth::guard('admin')->id();
            $orderStatusService->changeStatus($order, 'cancelled', $cancelReason, false, $adminId);

            $message = 'Order cancelled successfully.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'order_id' => $id]);
            }
            return redirect()->back()->with(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Cancel order error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with(['success' => false, 'message' => 'Failed to cancel order.'], 500);
        }
    }

    public function updateOrderStatus(ReadOrderRequest $request, OrderRepo $orderRepo, OrderStatus $orderStatusService)
    {
        try {
            $id = $request->input('id', $request->route('id'));
            $status = $request->input('status');
            $order = $orderRepo->getOrderById($id);

            if (!$order) {
                return redirect()->back()->with(['success' => false, 'message' => 'Order not found.'], 404);
            }

            $adminId = Auth::guard('admin')->id();
            $orderStatusService->changeStatus($order, $status, null, false, $adminId);

            $message = 'Order status updated successfully.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'order_id' => $id, 'status' => $status]);
            }
            return redirect()->back()->with(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Update order status error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with(['success' => false, 'message' => 'Failed to update status.'], 500);
        }
    }

    public function dataTable(ReadOrderRequest $request, OrderRepo $orderRepo, OrderStatus $orderStatusService)
    {
        try {
            $statusLabels = [
                'pending' => 'Pending',
                'order_confirmation' => 'Order Confirmation',
                'processing' => 'Processing',
                'ready_to_ship' => 'Ready to Ship',
                'shipped' => 'Shipped',
                'partially_shipped' => 'Partially Shipped',
                'out_for_delivery' => 'Out for Delivery',
                'delivered' => 'Delivered',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'returned' => 'Returned',
                'failed_delivery' => 'Failed Delivery',
            ];
            $markPaidStatuses = ['delivered', 'completed'];
            return DataTables::of($orderRepo->getDataForDataTable())
                ->addColumn('order_number', fn($order) => $order->order_number ?? 'N/A')
                ->addColumn('customer_name', function ($order) use ($orderRepo) {
                    return $orderRepo->getCustomerNameByOrderId($order->id) ?? 'N/A';
                })
                ->addColumn('email', fn($order) => $order->email ?? optional($order->user)->email ?? 'N/A')
                ->addColumn('total', fn($order) => $order->total ?? 'N/A')
                ->addColumn('created_at', fn($order) => $order->created_at->format('d M Y, h:i A'))
                ->addColumn('status', function ($order) use ($orderStatusService, $statusLabels, $request) {
                    // Only show dropdown for users with permission and for allowed transitions
                    $service = $orderStatusService;
                    $transitions = $service->getAvailableTransitions($order->status);
                    $availableStatuses = array_unique(array_merge([$order->status], $transitions));
                    return view('admin.partials.datatable-orderStatus', [
                        'order' => $order,
                        'statusLabels' => $statusLabels,
                        'availableStatuses' => $availableStatuses,
                        'canUpdate' => $request->user()->can('order.update'),
                    ])->render();
                })
                ->addColumn('cancelled_by', function ($order) {
                    if (($order->status ?? null) !== 'cancelled') {
                        return '<span class="badge bg-light text-muted border">Not Cancelled</span>';
                    }

                    $adminName = optional($order->cancelledBy)->name;
                    if (!empty($adminName)) {
                        return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">Admin: ' . e($adminName) . '</span>';
                    }

                    if (!empty($order->cancelled_by)) {
                        return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">Admin #' . e((string) $order->cancelled_by) . '</span>';
                    }

                    return '<span class="badge bg-warning-subtle text-dark border border-warning-subtle">Client</span>';
                })
                ->addColumn('payment_method', fn($order) => $order->payment_method ?? 'N/A')
                ->addColumn('payment_status', function ($order) use ($markPaidStatuses) {
                    if (!empty($order->transaction_id)) {
                        return 'Paid';
                    } else if (in_array($order->status, $markPaidStatuses) && $order->payment_method === 'cod') {
                        return 'Paid';
                    } else if ($order->status === 'cancelled') {
                        return 'Cancelled';
                    }

                    return 'Pending';
                })
                ->addColumn('action', function ($order) use ($request, $orderStatusService) {
                    $canRead = $request->user()->can('order.read');
                    $canUpdate = $request->user()->can('order.update');
                    $canDispatch = $request->user()->can('dispatch.create');

                    $actions = ['id' => $order->id];

                    $service = $orderStatusService;
                    $transitions = $service->getAvailableTransitions($order->status);

                    if (in_array('dispatched', $transitions, true) && $canDispatch) {
                        $actions['dispatch_btn'] = true;
                    }

                    if ($canUpdate) {
                        foreach ($transitions as $transition) {
                            if ($transition === 'order_confirmation') {
                                $actions['accept_url'] = route('admin.orders.update-order-status', ['id' => $order->id, 'status' => 'order_confirmation']);
                            }
                            if ($transition === 'cancelled') {
                                $actions['cancel_url'] = route('admin.orders.cancel', ['id' => $order->id]);
                            }
                        }
                    }

                    if ($canRead) {
                        $actions['view_url'] = route('admin.orders.show', ['id' => $order->id]);
                        $actions['invoice_url'] = route('admin.orders.download-invoice', ['id' => $order->id]);
                    }

                    if ($order->payment_method === 'card' && $request->user()->can('napsStatus.read')) {
                        $actions['naps_status_url'] = route('admin.orders.naps-order-detail', ['order' => $order->id]);
                    }

                    return view('admin.partials.datatable-actions', $actions)->render();
                })
                ->rawColumns(['status', 'cancelled_by', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Order dataTable error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch orders data.'], 500);
        }
    }

    public function downloadInvoice(OrderInvoiceRequest $request, OrderRepo $orderRepo, InvoiceExportService $invoiceExportService)
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $order = $orderRepo->findOrderWithRelations($request->input('id'), ['items.product', 'items.variant', 'user']);

        // This endpoint is for admin users; permission middleware/request authorization
        // already enforce access. Do not compare admin ID with customer user_id.
        if (! $order) {
            abort(403);
        }

        $pdf = $invoiceExportService->renderInvoicePdf($order);
        $filename = $invoiceExportService->filename($order);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function napsOrderDetail(ReadNapsRequest $request, OrderRepo $orderRepo, MXPlusService $mXPlusService)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $orderId = $request->input('order');
        $order = $orderRepo->findOrderWithRelations($orderId, ['items.product', 'items.variant']);

        // Prepare data for the API call
        // Ensure these fields exist in your Order model/table
        $statusData = [
            'orderid' => $order->order_number,
            'amount' => $order->total,
            'authnumber' => $order->naps_auth_number ?? '', // Saved during payment
            'paymentid' => $order->transaction_id ?? '', // Saved during payment
        ];

        try {
            $apiResponse = $mXPlusService->getPaymentStatus($statusData);
            Log::error('MXPlus Payment Status API Response', [
                'response' => $apiResponse,
            ]);
            // API Returns statuscode: 00=Paid, 01=Declined, 02=Captured, 03=Settled, 07=Refunded
            if (isset($apiResponse['status'])) {
                $currentStatus = $apiResponse['status']; // e.g., "Paid"

                if (isset($apiResponse['statuscode']) && ($apiResponse['statuscode'] === '00' || $apiResponse['statuscode'] === '02')) {
                    // UPDATE THE DATABASE HERE
                    $order->update([
                        'status' => 'paid',
                        'payment_status' => $apiResponse['status'] ?? 'Paid',
                        // 'transaction_id' => $apiResponse['transactionid'] ?? $order->transaction_id,
                        'naps_auth_number' => $apiResponse['authnumber'] ?? $order->naps_auth_number, // Save it now so it's there for next time
                    ]);
                }

                // Return JSON response with modal HTML for AJAX display
                $modalHtml = view('admin.partials.payment-status-modal', [
                    'order' => $order,
                    'napsStatus' => $apiResponse
                ])->render();

                return response()->json([
                    'success' => true,
                    'modal_html' => $modalHtml
                ]);
            }
            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Could not retrieve status from NAPS.'])
                : back()->with('error', 'Could not retrieve status from NAPS.');
        } catch (\Exception $e) {
            Log::error('NAPS Status API Error', ['error' => $e->getMessage()]);

            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'API Error: ' . $e->getMessage()])
                : back()->with('error', 'API Error: ' . $e->getMessage());
        }
    }
}
