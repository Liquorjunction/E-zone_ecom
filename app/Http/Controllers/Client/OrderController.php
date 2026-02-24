<?php

namespace App\Http\Controllers\Client;

use App\DTOs\CartIdentifier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Orders\OrderCheckoutRequest;
use App\Http\Requests\Client\Orders\OrderShowRequest;
use App\Http\Requests\Client\Orders\OrderCancelRequest;
use App\Http\Requests\Client\Orders\OrderDetailRequest;
use App\Http\Requests\Client\Orders\OrderInvoiceRequest;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\Client\Interfaces\AddressRepositoryInterface as AddressRepo;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface as OrderRepo;
use App\Repositories\Client\Interfaces\CartRepositoryInterface as CartRepo;
use App\Repositories\Client\Interfaces\ReviewRepositoryInterface;

class OrderController extends Controller
{
    public function index(Request $request, OrderRepo $orderRepo)
    {
        if (!Auth::check()) {
            if ($request->ajax()) return response()->json(['error' => 'Unauthorized'], 401);
            return redirect()->route('login');
        }

        $status = $request->query('status');
        $filters = ['user_id' => Auth::id()];
        if ($status && in_array($status, ['processing', 'shipped', 'delivered', 'cancelled'])) {
            $filters['status'] = $status;
        }

        $orders = $orderRepo->getOrdersWithRelations($filters, ['items.product', 'items.variant'], 10, [
            'search' => $request->query('search'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'sort' => $request->query('sort', 'latest'),
        ]);

        if ($request->ajax()) {
            $html = view('client.pages.dashboard.partials.orders-list', compact('orders', 'status'))->render();
            return response()->json(['html' => $html]);
        }

        return view('client.pages.dashboard.orders', ['orders' => $orders, 'currentStatus' => $status]);
    }

    public function checkout(OrderCheckoutRequest $request, OrderService $orderService, CartService $cartService)
    {
        $cartId = CartIdentifier::fromRequest($request);
        $cart = $cartService->getCart($cartId);

        if (!$cart || $cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        try {
            $order = $orderService->processCheckout(
                $cart,
                $request->validated(),
                Auth::id(),
                Auth::user()?->email
            );

            Log::info('Client order created', ['order_id' => $order->id ?? null, 'user' => Auth::id()]);
            return redirect()->route('checkout', ['step' => 3])->with('order_id', $order->id ?? null);

        } catch (\Exception $e) {
            Log::error('Checkout failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['checkout' => $e->getMessage()]);
        }
    }

    public function show(OrderShowRequest $request, CartService $cartService, CartRepo $cartRepo, OrderRepo $orderRepo, AddressRepo $addressRepo)
    {
        $step = (int) $request->get('step', 1);
        $cartId = CartIdentifier::fromRequest($request);
        $cart = $cartService->getCart($cartId);

        if (($step === 1 || $step === 2) && (!$cart || $cart->items->isEmpty())) {
            return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $addresses = collect();
        $billingAddresses = collect();

        if (Auth::check()) {
            $addresses = $addressRepo->getAddressesByType(Auth::id(), 'shipping');
            $billingAddresses = $addressRepo->getAddressesByType(Auth::id(), 'billing');
        }
        $order = null;
        $orders = collect();
        if ($step === 3) {
            $orderId = $request->session()->get('order_id');
            if ($orderId) {
                $order = $orderRepo->findOrderWithRelations($orderId, ['items.product', 'items.variant']);
            } elseif (Auth::check()) {
                $order = $orderRepo->getLatestOrderForUser(Auth::id(), ['items.product', 'items.variant']);
            }
            if (Auth::check()) $orders = $cartRepo->getAllOrdersForUser(Auth::id());
        }

        $totals = $cart ? $cartRepo->calculateCartTotals($cart) : [
            'subtotal' => $order->subtotal ?? 0, 'total_discount' => $order->discount_amount ?? 0,
            'coupon_discount' => $order->coupon_discount_amount ?? 0, 'tax' => $order->tax_amount ?? 0,
            'shipping' => $order->shipping_cost ?? 0, 'total' => $order->total ?? 0, 'items' => []
        ];

        $itemsForView = $cart ? $cart->items : ($order ? $order->items : collect());

        return view('client.pages.checkout-process', compact('cart', 'step', 'addresses', 'billingAddresses', 'order', 'orders', 'totals', 'itemsForView'));
    }

    public function detail(OrderDetailRequest $request, OrderRepo $orderRepo, ReviewRepositoryInterface $reviewRepo)
    {
        if (!Auth::check()) return redirect()->route('login');

        $order = $orderRepo->findOrderWithRelations($request->input('order'), ['items.product', 'items.variant']);
        if (!$order || $order->user_id !== Auth::id()) abort(404, 'Order not found or access denied.');

        $userReviews = [];
        foreach ($order->items as $item) {
            if ($review = $reviewRepo->getUserReviewByOrderItem(Auth::id(), $item->id)) {
                $userReviews[$item->id] = $review;
            }
        }

        return view('client.pages.order-detail', compact('order', 'userReviews'));
    }

    public function invoice(OrderInvoiceRequest $request, OrderRepo $orderRepo)
    {
        if (!Auth::check()) return redirect()->route('login');

        $order = $orderRepo->findOrderWithRelations($request->input('id'), ['items.product', 'items.variant']);
        if (!$order || $order->user_id !== Auth::id()) abort(403);

        return view('client.pages.order-invoice', compact('order'));
    }

    public function cancel(OrderCancelRequest $request, OrderService $orderService)
    {
        if (!Auth::check()) return redirect()->route('login');

        $ok = $orderService->cancelClientOrder($request->input('id'), $request->input('reason'), Auth::id());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $ok,
                'status' => $ok ? 'success' : 'error',
                'message' => $ok ? 'Order cancelled successfully.' : 'Unable to cancel the order.'
            ], $ok ? 200 : 422);
        }

        return $ok ? back()->with('success', 'Order cancelled successfully.') : back()->withErrors(['cancel' => 'Unable to cancel the order.']);
    }
}
