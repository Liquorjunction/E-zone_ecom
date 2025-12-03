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
            $shippingandreturns = ShippingandReturns::all();
            return view('client.pages.shipping-returns', compact('shippingandreturns'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to fetch Shipping and Returns.']);
        }
    }
}
