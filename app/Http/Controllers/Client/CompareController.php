<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Compare\AddCompareRequest;
use App\Http\Requests\Client\Compare\RemoveCompareRequest;
use App\Services\CompareService;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request, CompareService $compareService)
    {
        $data = $compareService->buildIndexData();

        return view('client.pages.compare', [
            'compareVariants' => $data['compareVariants'],
            'features' => $data['features'],
        ]);
    }

    public function add(AddCompareRequest $request, CompareService $compareService)
    {
        $variantId = (int) $request->input('product_variant_id');
        $result = $compareService->addVariant($variantId);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'count' => $result['count'],
            'ids' => $result['ids'],
        ], $result['status']);
    }

    public function remove(RemoveCompareRequest $request, CompareService $compareService)
    {
        $variantId = (int) $request->input('product_variant_id');
        $result = $compareService->removeVariant($variantId);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'count' => $result['count'],
                'ids' => $result['ids'],
            ], $result['status']);
        }

        return back()->with('success', $result['message']);
    }

    public function get(Request $request, CompareService $compareService)
    {
        return response()->json($compareService->getState());
    }

    public function count(Request $request, CompareService $compareService)
    {
        return response()->json($compareService->getCount());
    }

    public function clear(Request $request, CompareService $compareService)
    {
        return response()->json($compareService->clear());
    }
}
