<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Orders\OrderCheckoutRequest;
use App\Models\Cart;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\Order as OrderModel;

class OrderController extends Controller
{
    public function checkout(OrderCheckoutRequest $request, OrderService $orders)
    {
        if (Auth::check()) {
            $cart = Cart::with('items')->where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::with('items')->where('session_id', $request->session()->getId())->first();
        }

        if (!$cart || $cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        $data = $request->validated();

        // Normalize payment input: form uses `payment_method` (radio) — accept `payment` too.
        $data['payment_method'] = $request->input('payment_method') ?? $request->input('payment') ?? ($data['payment_method'] ?? null);

        // Attach raw card fields so PaymentService can use them (if using card)
        $data['cardNumber'] = $request->input('cardNumber');
        $data['expiry'] = $request->input('expiry');
        $data['cvv'] = $request->input('cvv');

        // If shipping_address not provided as an array, assemble from individual inputs
        if (empty($data['shipping_address']) || !is_array($data['shipping_address'])) {
            $data['shipping_address'] = [
                'name' => trim(($request->input('firstName') ?? '') . ' ' . ($request->input('lastName') ?? '')) ?: null,
                'street' => $request->input('address') ?? null,
                'city' => $request->input('city') ?? null,
                'state' => $request->input('state') ?? null,
                'postal_code' => $request->input('zip') ?? $request->input('postal_code') ?? null,
                'country' => $request->input('country') ?? null,
            ];
        }

        // Include phone in shipping address and normalize phone into data for order
        $phone = $request->input('phone') ?? $data['shipping_address']['phone'] ?? null;
        if ($phone) {
            $data['shipping_address']['phone'] = $phone;
        }
        $data['phone_no'] = $phone ?? ($data['phone_no'] ?? null);

        // Handle saved address selection or saving new address for authenticated users
        if (Auth::check()) {
            $savedAddressId = $request->input('saved_address_id');

            if (!empty($savedAddressId)) {
                // Use an existing saved address (ensure it belongs to the user)
                $saved = Address::where('id', $savedAddressId)->where('user_id', Auth::id())->first();
                if ($saved) {
                    $data['shipping_address'] = [
                        'name' => $saved->name,
                        'street' => $saved->street,
                        'city' => $saved->city,
                        'state' => $saved->state,
                        'postal_code' => $saved->postal_code,
                        'country' => $saved->country,
                    ];
                }
            } else {
                // No saved address selected; only create a new saved address if user opted in via checkbox
                if (!empty($data['shipping_address']) && is_array($data['shipping_address']) && $request->filled('save_address')) {
                    $addrData = $data['shipping_address'];
                    $country = $addrData['country'] ?? $request->input('country') ?? '';

                    // Attempt to find an existing identical address for this user to avoid duplicates
                    $exists = Address::where('user_id', Auth::id())
                        ->where('street', $addrData['street'] ?? '')
                        ->where('city', $addrData['city'] ?? '')
                        ->where('state', $addrData['state'] ?? '')
                        ->where('postal_code', $addrData['postal_code'] ?? $addrData['zip'] ?? '')
                        ->where('country', $country)
                        ->first();

                    if (! $exists) {
                        $address = new Address();
                        $address->user_id = Auth::id();
                        $address->name = $addrData['name'] ?? null;
                        $address->street = $addrData['street'] ?? null;
                        $address->city = $addrData['city'] ?? null;
                        $address->state = $addrData['state'] ?? null;
                        $address->postal_code = $addrData['postal_code'] ?? $addrData['zip'] ?? null;
                        $address->country = $country ?: '';
                        $address->save();
                    }
                }
            }
        }

        // Create order via service (keeps logging and higher-level behavior)
        $order = $orders->createFromCart($cart, $data);

        Log::info('Client order created', ['order_id' => $order->id ?? null, 'user' => Auth::id()]);

        return redirect()->route('checkout', ['step' => 3])->with('order_id', $order->id ?? null);
    }

    /**
     * Show the checkout page (form).
     */
    public function show(Request $request, CartService $cartService)
    {
        $step = (int) $request->get('step', 1);

        // Load cart for steps that require it (step 1 & 2)
        $cart = null;
        if ($step === 1 || $step === 2) {
            $cart = $cartService->getCart($request);

            // If cart is empty, redirect back to cart page
            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
            }
        } else {
            // try to still show cart if available for summary on step 3
            $cart = $cartService->getCart($request);
        }

        // Load saved addresses for authenticated users
        $addresses = collect();
        if (Auth::check()) {
            $addresses = Address::where('user_id', Auth::id())->get();
        }

        // If step 3, try to fetch the created order from session or user's latest
        $order = null;
        if ($step === 3) {
            $orderId = $request->session()->get('order_id');
            if ($orderId) {
                $order = OrderModel::with('items')->find($orderId);
            }

            if (!$order && Auth::check()) {
                $order = OrderModel::with('items')->where('user_id', Auth::id())->latest()->first();
            }
        }

        return view('client.pages.checkout-process', [
            'cart' => $cart,
            'step' => $step,
            'addresses' => $addresses,
            'order' => $order,
        ]);
    }

    // success() removed — success is handled via the multi-step `checkout` page (step=3)
}
