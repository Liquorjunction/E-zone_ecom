<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\dispatch\StoreDispatchRequest;
use App\Http\Requests\Admin\dispatch\ReadDispatchRequest;
use App\Http\Requests\Admin\dispatch\DestroyDispatchRequest;
use App\Http\Requests\Admin\dispatch\EditDispatchRequest;
use App\Http\Requests\Admin\dispatch\UpdateDispatchRequest;
use App\Repositories\Admin\Interfaces\DispatchRepositoryInterface as DispatchRepo;
use Illuminate\Support\Facades\DB;

class DisptchController extends Controller
{
    public function index()
    {

        // For Showing Dispatch History
        return view('admin.pages.dispatch.index');
    }

    public function create()
    {
        // For Showing Create Dispatch popup
        return view('admin.pages.dispatch.create');
    }

    public function store(StoreDispatchRequest $request, DispatchRepo $dispatchRepo)
    {
        try {
            DB::beginTransaction();
            $orderId = $request->input('order_id');
            $items = $request->input('items');
            $allDispatched = true;
            $dispatchedItems = [];

            foreach ($items as $item) {
                // Use repository methods for all operations
                $dispatch = $dispatchRepo->createDispatch([
                    'order_id' => $orderId,
                    'order_item_id' => $item['order_item_id'],
                    'dispatch_qty' => $item['dispatch_qty'],
                    'inventory_id' => $item['inventory_id'],
                    'dispatch_method' => $request->input('dispatch_method'),
                    'remarks' => $request->input('remarks'),
                ]);
                $dispatchedItems[] = $dispatch;

                // Check if this item is fully dispatched using repository
                $orderedQty = $dispatchRepo->getOrderItemQuantity($item['order_item_id']);
                $totalDispatched = $dispatchRepo->getTotalDispatchedQuantity($item['order_item_id']);
                if ($totalDispatched < $orderedQty) {
                    $allDispatched = false;
                }
            }

            // Update order status using repository
            $dispatchRepo->updateOrderStatus($orderId, $allDispatched ? 'processing' : 'pending');

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Dispatch created successfully.', 'data' => $dispatchedItems]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create dispatch: ' . $e->getMessage()], 500);
        }
    }
}
