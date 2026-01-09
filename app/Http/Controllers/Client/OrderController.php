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
use App\Repositories\Client\Interfaces\OrderRepositoryInterface as OrderRepositoryInterface;

class OrderController extends Controller
{
    public function checkout(OrderCheckoutRequest $request, OrderService $orders)
    {
        // Fetch the cart for the current user or session
        if (Auth::check()) {
            $cart = Cart::with('items')->where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::with('items')->where('session_id', $request->session()->getId())->first();
        }

        if (!$cart || $cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        $data = $request->validated();
        $data['payment_method'] = $request->input('payment_method') ?? $request->input('payment') ?? ($data['payment_method'] ?? null);
        $data['shipping_method'] = $request->input('shipping_method') ?? 'standard';

        if (!isset($data['email']) || empty($data['email'])) {
            $data['email'] = Auth::user()?->email ?? $request->input('email') ?? null;
        }

        $data['cardNumber'] = $request->input('cardNumber');
        $data['expiry'] = $request->input('expiry');
        $data['cvv'] = $request->input('cvv');

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

        $phone = $request->input('phone') ?? $data['shipping_address']['phone'] ?? null;
        if ($phone) {
            $data['shipping_address']['phone'] = $phone;
        }
        $data['phone_no'] = $phone ?? ($data['phone_no'] ?? null);

        if (Auth::check()) {
            $savedAddressId = $request->input('saved_address_id');

            if (!empty($savedAddressId)) {
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
                if (!empty($data['shipping_address']) && is_array($data['shipping_address']) && $request->filled('save_address')) {
                    $addrData = $data['shipping_address'];
                    $country = $addrData['country'] ?? $request->input('country') ?? '';

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

        $order = $orders->createFromCart($cart, $data);

        Log::info('Client order created', ['order_id' => $order->id ?? null, 'user' => Auth::id()]);

        return redirect()->route('checkout', ['step' => 3])->with('order_id', $order->id ?? null);
    }

    public function show(Request $request, CartService $cartService)
    {
        $step = (int) $request->get('step', 1);

        $cart = null;
        if ($step === 1 || $step === 2) {
            $cart = $cartService->getCart($request);

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
            }
        } else {
            $cart = $cartService->getCart($request);
        }

        $addresses = collect();
        if (Auth::check()) {
            $addresses = Address::where('user_id', Auth::id())->get();
        }

        $order = null;
        if ($step === 3) {
            $orderId = $request->session()->get('order_id');
            if ($orderId) {
                $order = OrderModel::with(['items.product','items.variant'])->find($orderId);
            }

            if (!$order && Auth::check()) {
                $order = OrderModel::with(['items.product','items.variant'])->where('user_id', Auth::id())->latest()->first();
            }
        }

        if ($cart) {
            $totals = $cart->calculateTotals();
        } elseif ($order && $order->relationLoaded('items')) {
            $totals = [
                'subtotal' => $order->subtotal ?? 0,
                'total_discount' => $order->discount_amount ?? 0,
                'coupon_discount' => $order->coupon_discount_amount ?? 0,
                'tax' => $order->tax_amount ?? 0,
                'shipping' => $order->shipping_cost ?? 0,
                'total' => $order->total ?? 0,
                'items' => [],
            ];
        } else {
            $totals = [
                'subtotal' => 0,
                'total_discount' => 0,
                'coupon_discount' => 0,
                'tax' => 0,
                'shipping' => 0,
                'total' => 0,
                'items' => []
            ];
        }

        $itemsForView = $cart ? $cart->items : ($order ? $order->items : collect());

        return view('client.pages.checkout-process', [
            'cart' => $cart,
            'step' => $step,
            'addresses' => $addresses,
            'order' => $order,
            'totals' => $totals,
            'itemsForView' => $itemsForView,
        ]);
    }

    public function detail(OrderModel $order)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('client.pages.order-detail', ['order' => $order]);
    }

    public function invoice($id)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $order = OrderModel::with(['items.product', 'items.variant'])->findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('client.pages.order-invoice', ['order' => $order]);
    }

    public function cancel(Request $request, $id, OrderRepositoryInterface $orders)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $order = OrderModel::findOrFail($id);
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $reason = $request->input('reason');
        $ok = $orders->cancelOrder($order->id, $reason);

        if ($ok) {
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->withErrors(['cancel' => 'Unable to cancel the order.']);
    }
}
