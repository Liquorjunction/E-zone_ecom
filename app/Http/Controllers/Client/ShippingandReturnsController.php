<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingandReturns;

class ShippingandReturnsController extends Controller
{
    public function index()
    {
        try {
            // Prefer latest published policy; fallback to first record
            $policy = ShippingandReturns::query()
                ->when(method_exists(ShippingandReturns::class, 'scopeActive'), function ($q) {
                    $q->active();
                })
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();

            return view('client.pages.shipping-returns', [
                'policy' => $policy,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to fetch Shipping and Returns.']);
        }
    }
}
