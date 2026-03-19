<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ShippingAndReturns\ReadShippingAndReturnsRequest;
use App\Repositories\Client\Interfaces\ShippingAndReturnsRepositoryInterface as ShippingAndReturnsRepo;

class ShippingAndReturnsController extends Controller
{
    public function index(ReadShippingAndReturnsRequest $request, ShippingAndReturnsRepo $shippingAndReturnsRepo)
    {
        try {
            $policy = $shippingAndReturnsRepo->getActiveShippingAndReturnsData();
            return view('client.pages.shipping-returns', [
                'policy' => $policy,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => statictranslations('Unable to fetch Shipping and Returns.')]);
        }
    }
}
