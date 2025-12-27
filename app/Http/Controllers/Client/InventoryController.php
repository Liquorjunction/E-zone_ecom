<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface as InventoryRepo;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Return transactions for a given variant or product.
     * Accepts `variant_id` or `product_id` in request.
     */
    public function transactions(Request $request)
    {
        $variantId = $request->input('variant_id');
        $productId = $request->input('product_id');

        $query = InventoryTransaction::query()->with('inventory');

        if ($variantId) {
            $inventoryIds = Inventory::where('product_variant_id', $variantId)->pluck('id');
            $query->whereIn('inventory_id', $inventoryIds->toArray());
        } elseif ($productId) {
            $inventoryIds = Inventory::where('product_id', $productId)->pluck('id');
            $query->whereIn('inventory_id', $inventoryIds->toArray());
        } else {
            return response()->json(['success' => false, 'message' => 'variant_id or product_id required'], 422);
        }

        $txns = $query->orderBy('created_at', 'desc')->limit(200)->get();

        return response()->json(['success' => true, 'data' => $txns]);
    }

    /** Reserve stock for a user/action */
    public function reserve(Request $request, InventoryRepo $repo)
    {
        $data = $request->validate([
            'product_id' => 'required_without:variant_id|nullable|integer',
            'variant_id' => 'required_without:product_id|nullable|integer',
            'quantity' => 'required|numeric|min:0.001',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        $reserved = $repo->reserveStock($data['product_id'] ?? 0, $data['variant_id'] ?? null, (float)$data['quantity'], $data['reference_type'] ?? null, $data['reference_id'] ?? null);

        return response()->json(['success' => true, 'reserved' => $reserved]);
    }

    public function release(Request $request, InventoryRepo $repo)
    {
        $data = $request->validate([
            'product_id' => 'required_without:variant_id|nullable|integer',
            'variant_id' => 'required_without:product_id|nullable|integer',
            'quantity' => 'required|numeric|min:0.001',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        $released = $repo->releaseReservation($data['product_id'] ?? 0, $data['variant_id'] ?? null, (float)$data['quantity'], $data['reference_type'] ?? null, $data['reference_id'] ?? null);

        return response()->json(['success' => true, 'released' => $released]);
    }

    public function processReturn(Request $request, InventoryRepo $repo)
    {
        $data = $request->validate([
            'product_id' => 'required_without:variant_id|nullable|integer',
            'variant_id' => 'required_without:product_id|nullable|integer',
            'quantity' => 'required|numeric|min:0.001',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        $inv = $repo->processReturn($data['product_id'] ?? 0, $data['variant_id'] ?? null, (float)$data['quantity'], $data['reference_type'] ?? null, $data['reference_id'] ?? null);

        return response()->json(['success' => true, 'inventory' => $inv]);
    }
}
