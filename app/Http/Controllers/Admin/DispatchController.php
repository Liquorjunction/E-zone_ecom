<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\dispatch\StoreDispatchRequest;
use App\Services\Admin\DispatchService;
use Illuminate\Http\Request;

class DispatchController extends Controller
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

    public function store(StoreDispatchRequest $request, DispatchService $dispatchService)
    {
        $data = $request->validated();
        if (isset($data['items'])) {
            foreach ($data['items'] as &$item) {
                if (isset($item['dispatch_qty'])) {
                    $item['quantity'] = $item['dispatch_qty'];
                    unset($item['dispatch_qty']);
                }
            }
            unset($item);
        }
        try {
            $dispatchedItems = $dispatchService->createDispatch($data);
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
