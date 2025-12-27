<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
                ->addColumn('payment_method', function ($order) {
                    return $order->payment_method ?? 'N/A';
                })
                ->addColumn('action', function ($order) use ($request) {
                    $canRead = $request->user()->can('order.read');
                    $canUpdate = $request->user()->can('order.update');

                    return view('admin.partials.datatable-actions', [
                        'id' => $order->id,
                        'view_url' => $canRead ? route('admin.orders.show', ['id' => $order->id]) : null,
                        'edit_url' => $canUpdate ? route('admin.orders.edit', ['id' => $order->id]) : null,
                    ])->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Order dataTable error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch orders data.'], 500);
        }
    }
}
