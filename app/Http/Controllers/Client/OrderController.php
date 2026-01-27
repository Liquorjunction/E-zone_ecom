<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Orders\OrderCheckoutRequest;
use App\Http\Requests\Client\Orders\OrderShowRequest;
use App\Http\Requests\Client\Orders\OrderCancelRequest;
use App\Http\Requests\Client\Orders\OrderDetailRequest;
use App\Http\Requests\Client\Orders\OrderInvoiceRequest;
use App\Models\Cart;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Models\Address;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface as OrderRepo;
use App\Repositories\Client\Interfaces\CartRepositoryInterface as CartRepo;
use App\Services\PriceCalculatorService;

class OrderController extends Controller
{

    public function index(Request $request, OrderRepo $orderRepo)
    {
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        $status = $request->query('status');

        $allowedStatuses = ['processing', 'shipped', 'delivered', 'cancelled'];
        $filters = ['user_id' => Auth::id()];
        if ($status && in_array($status, $allowedStatuses)) {
            $filters['status'] = $status;
        }
        $orders = $orderRepo->getOrdersWithRelations($filters, ['items.product', 'items.variant'], 10);

        if ($request->ajax()) {
            $html = view('client.pages.dashboard.partials.orders-list', [
                'orders' => $orders,
                'currentStatus' => $status,
            ])->render();
            return response()->json(['html' => $html]);
        }

        return view('client.pages.dashboard.orders', [
            'orders' => $orders,
            'currentStatus' => $status,
        ]);
    }

    public function checkout(OrderCheckoutRequest $request, OrderService $orders, PriceCalculatorService $priceService)
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
        $data['payment_method'] = $data['payment_method'] ?? $data['payment'] ?? null;
        $data['shipping_method'] = $data['shipping_method'] ?? 'standard';

        if (!isset($data['email']) || empty($data['email'])) {
            $data['email'] = Auth::user()?->email ?? null;
        }

        $data['cardNumber'] = $data['cardNumber'] ?? null;
        $data['expiry'] = $data['expiry'] ?? null;
        $data['cvv'] = $data['cvv'] ?? null;

        if (empty($data['shipping_address']) || !is_array($data['shipping_address'])) {
            $data['shipping_address'] = [
                'name' => trim(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? '')) ?: null,
                'street' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['zip'] ?? $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
            ];
        }

        $phone = $data['phone'] ?? Arr::get($data, 'shipping_address.phone');
        if ($phone) {
            $data['shipping_address']['phone'] = $phone;
        }
        $data['phone_no'] = $phone ?? ($data['phone_no'] ?? null);

        $billingSame = (bool) ($data['billing_same_as_shipping'] ?? false);
        if ($billingSame) {
            $data['billing_address'] = $data['shipping_address'] ?? null;
        } else {
            if (Auth::check()) {
                $savedBillingAddressId = $data['saved_billing_address_id'] ?? null;

                if (!empty($savedBillingAddressId)) {
                    $savedBilling = Address::where('id', $savedBillingAddressId)
                        ->where('user_id', Auth::id())
                        ->first();
                    if ($savedBilling) {
                        $data['billing_address'] = [
                            'name' => $savedBilling->name,
                            'street' => $savedBilling->street,
                            'city' => $savedBilling->city,
                            'state' => $savedBilling->state,
                            'postal_code' => $savedBilling->postal_code,
                            'country' => $savedBilling->country,
                            'phone' => $savedBilling->phone,
                        ];
                    }
                }
            }
        }

        if (empty($data['billing_address']) || !is_array($data['billing_address'])) {
            $billingName = trim(($data['billing_firstName'] ?? '') . ' ' . ($data['billing_lastName'] ?? '')) ?: null;

            $data['billing_address'] = [
                'name' => $billingName ?: ($data['shipping_address']['name'] ?? null),
                'street' => $data['billing_address_line'] ?? $data['billing_address'] ?? null,
                'city' => $data['billing_city'] ?? null,
                'state' => $data['billing_state'] ?? null,
                'postal_code' => $data['billing_zip'] ?? $data['billing_postal_code'] ?? null,
                'country' => $data['billing_country'] ?? null,
            ];
        }

        $billingPhone = $data['billing_phone'] ?? Arr::get($data, 'billing_address.phone') ?? Arr::get($data, 'shipping_address.phone');
        if ($billingPhone) {
            $data['billing_address']['phone'] = $billingPhone;
        }

        if (Auth::check()) {
            $savedAddressId = $data['saved_address_id'] ?? null;

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
                $shouldSaveAddress = (bool) ($data['save_address'] ?? false);

                if (!empty($data['shipping_address']) && is_array($data['shipping_address']) && $shouldSaveAddress) {
                    $addrData = $data['shipping_address'];
                    $country = $addrData['country'] ?? $data['country'] ?? '';

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

        $order = $orders->createFromCart($cart, $data, $priceService);

        Log::info('Client order created', ['order_id' => $order->id ?? null, 'user' => Auth::id()]);

        return redirect()->route('checkout', ['step' => 3])->with('order_id', $order->id ?? null);
    }


    public function show(OrderShowRequest $request, CartService $cartService, CartRepo $cartRepo, OrderRepo $orderRepo)
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
        $billingAddresses = collect();
        if (Auth::check()) {
            $addresses = Address::where('user_id', Auth::id())
                ->where('type', 'shipping')
                ->orWhereNull('type')
                ->get();

            $billingAddresses = Address::where('user_id', Auth::id())
                ->where('type', 'billing')
                ->orWhereNull('type')
                ->get();
        }

        $order = null;
        if ($step === 3) {
            $orderId = $request->session()->get('order_id');
            if ($orderId) {
                $order = $orderRepo->findOrderWithRelations($orderId, ['items.product', 'items.variant']);
            }

            if (!$order && Auth::check()) {
                $order = $orderRepo->getLatestOrderForUser(Auth::id(), ['items.product', 'items.variant']);
            }
        }

        if ($cart) {
            $totals = $cartRepo->calculateCartTotals($cart);
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
            'billingAddresses' => $billingAddresses,
            'order' => $order,
            'totals' => $totals,
            'itemsForView' => $itemsForView,
        ]);
    }

    public function detail(OrderDetailRequest $request, $orderId, OrderRepo $orderRepo)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $order = $orderRepo->findOrderWithRelations($orderId, ['items.product', 'items.variant']);
        if (!$order || $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('client.pages.order-detail', ['order' => $order]);
    }

    public function invoice(OrderInvoiceRequest $request, $id, OrderRepo $orderRepo)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $order = $orderRepo->findOrderWithRelations($id, ['items.product', 'items.variant']);
        if (!$order || $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('client.pages.order-invoice', ['order' => $order]);
    }

    public function cancel(OrderCancelRequest $request, $id, OrderRepo $orderRepo)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $order = $orderRepo->findOrder($id);
        if (!$order || $order->user_id !== Auth::id()) {
            abort(403);
        }

        $reason = $request->input('reason');
        $ok = $orderRepo->cancelOrder($order->id, $reason);

        if ($ok) {
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->withErrors(['cancel' => 'Unable to cancel the order.']);
    }
}

