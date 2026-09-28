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
use App\Services\InvoiceExportService;
use App\Services\OrderService;
use App\Services\PriceCalculatorService;
use App\Support\Ga4Ecommerce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\Client\Interfaces\AddressRepositoryInterface as AddressRepo;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface as OrderRepo;
use App\Repositories\Client\Interfaces\CartRepositoryInterface as CartRepo;
use App\Repositories\Client\Interfaces\ReviewRepositoryInterface;
use App\Services\SessionCouponService;

class OrderController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Orders list (dashboard)
    // ─────────────────────────────────────────────────────────────

    public function index(Request $request, OrderRepo $orderRepo)
    {
        if (!Auth::check()) {
            return $request->ajax()
                ? response()->json(['error' => 'Unauthorized'], 401)
                : redirect()->route('login');
        }

        $status  = $request->query('status');
        $filters = ['user_id' => Auth::id()];

        if ($status && in_array($status, ['pending','order_confirmation','shipped','out_for_delivery','delivered','cancelled'], true)) {
            $filters['status'] = $status;
        }

        $orders = $orderRepo->getOrdersWithRelations($filters, ['items.product','items.variant'], 10, [
            'search'    => $request->query('search'),
            'date_from' => $request->query('date_from'),
            'date_to'   => $request->query('date_to'),
            'sort'      => $request->query('sort', 'latest'),
        ]);

        if ($request->ajax()) {
            $html = view('client.pages.dashboard.partials.orders-list', compact('orders','status'))->render();
            return response()->json(['html' => $html]);
        }

        return view('client.pages.dashboard.orders', ['orders' => $orders, 'currentStatus' => $status]);
    }

    // ─────────────────────────────────────────────────────────────
    // Checkout GET — show step
    // ─────────────────────────────────────────────────────────────

    public function show(
        OrderShowRequest          $request,
        CartService               $cartService,
        CartRepo                  $cartRepo,
        OrderRepo                 $orderRepo,
        AddressRepo               $addressRepo,
        PriceCalculatorService    $priceCalculator,
        SessionCouponService      $sessionCoupon
    ) {
        $step   = (int) $request->get('step', 1);
        $cartId = CartIdentifier::fromRequest($request);
        $isBuyNowFlow = $request->boolean('buy_now');
        $request->session()->put('checkout_mode', $isBuyNowFlow ? 'buy_now' : 'normal');

        if ($step === 2 && !Auth::check()) {
            $loginUrl = route('login');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'  => false,
                    'redirect' => $loginUrl,
                    'message'  => statictranslations('Please login to continue checkout.'),
                ], 401);
            }

            return redirect()->guest($loginUrl)
                ->with('warning', statictranslations('Please login to continue checkout.'));
        }

        if ($isBuyNowFlow) {
            $cart = $cartService->getActiveBuyNowCart();
        } else {
            if ($step <= 2) {
                $cartService->recoverIncompleteBuyNowToPreviousCart($cartId);
            }
            $cart = $cartService->getCart($cartId);
        }

        if (in_array($step, [1, 2]) && (!$cart || $cart->items->isEmpty())) {
            if ($request->ajax()) {
                return response()->json(['redirect' => route('cart')], 302);
            }
            return redirect()->route('cart')->withErrors(['cart' => 'Your cart is empty.']);
        }

        $addresses        = collect();
        $billingAddresses = collect();
        $cities           = collect();

        if (Auth::check()) {
            $addresses        = $addressRepo->getAddressesByType(Auth::id(), 'shipping');
            $billingAddresses = $addressRepo->getAddressesByType(Auth::id(), 'billing');
        }
        $cities = $addressRepo->getActiveCities() ?: collect();

        $order  = null;
        $orders = collect();

        if ($step === 3) {
            $orderId = $request->session()->get('order_id');
            if ($orderId) {
                $order = $orderRepo->findOrderWithRelations($orderId, ['items.product','items.variant']);
            } elseif (Auth::check()) {
                $order = $orderRepo->getLatestOrderForUser(Auth::id(), ['items.product','items.variant']);
            }
            if (Auth::check()) {
                $orders = $cartRepo->getAllOrdersForUser(Auth::id());
            }
        }

        $shippingMethod = $request->input('shipping_method', 'standard');
        if ($cart) {
            $totals = $priceCalculator->calculate($cart, $shippingMethod);
        } elseif ($order) {
            $totals = $priceCalculator->calculate($order);
        } else {
            $totals = $this->emptyTotals();
        }

        $itemsForView = $cart ? $cart->items : ($order ? $order->items : collect());
        $appliedCoupon = $cart ? $sessionCoupon->getForCart($cart->id) : null;
        $ga4Ecommerce = in_array($step, [1, 2], true)
            ? Ga4Ecommerce::cartPayload($cart, $totals)
            : null;
        $ga4Purchase = ($step === 3 && $order && $request->session()->has('order_id'))
            ? Ga4Ecommerce::purchasePayload($order)
            : null;

        // ── AJAX request: return only the step content HTML ──────────────
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('client.pages.checkout.partials._step-content', compact(
                'step', 'cart', 'itemsForView', 'addresses', 'billingAddresses',
                'cities', 'order', 'orders', 'totals'
            ))->with('appliedCoupon', $appliedCoupon)->render();

            return response()->json([
                'success'  => true,
                'step'     => $step,
                'html'     => $html,
                'order_id' => $order?->id,
                'ecommerce' => $ga4Ecommerce,
            ]);
        }

        // ── Normal full-page request ─────────────────────────────────────
        return view('client.pages.checkout-process', compact(
            'cart', 'step', 'addresses', 'billingAddresses', 'cities',
            'order', 'orders', 'totals', 'itemsForView', 'ga4Ecommerce', 'ga4Purchase'
        ))->with('appliedCoupon', $appliedCoupon);
    }

    // ─────────────────────────────────────────────────────────────
    // Checkout POST — process order
    // ─────────────────────────────────────────────────────────────

    public function checkout(
        OrderCheckoutRequest   $request,
        OrderService           $orderService,
        CartService            $cartService,
        OrderRepo              $orderRepo,
        AddressRepo            $addressRepo,
        PriceCalculatorService $priceCalculator
    ) {
        if (!Auth::check()) {
            $loginUrl = route('login');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'  => false,
                    'redirect' => $loginUrl,
                    'message'  => statictranslations('Please login to place your order.'),
                ], 401);
            }

            return redirect()->guest($loginUrl)
                ->with('warning', statictranslations('Please login to place your order.'));
        }

        $cartId = CartIdentifier::fromRequest($request);
        $checkoutMode = (string) $request->session()->get('checkout_mode', 'normal');
        $cart = $checkoutMode === 'buy_now'
            ? $cartService->getActiveBuyNowCart()
            : $cartService->getCart($cartId);

        if (!$cart || $cart->items->isEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
            }
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
            $request->session()->forget('checkout_mode');

            if ($checkoutMode === 'buy_now') {
                $cartService->clearBuyNowFlowState();
            }

            // ── AJAX ─────────────────────────────────────────────────────
            if ($request->ajax() || $request->wantsJson()) {
                // Build the step-3 success HTML to swap in without a reload
                $step             = 3;
                $addresses        = collect();
                $billingAddresses = collect();
                $cities           = $addressRepo->getActiveCities() ?: collect();
                $totals           = $priceCalculator->calculate($order);
                $itemsForView     = $order->items;
                $orders           = collect();

                if (Auth::check()) {
                    $addresses        = $addressRepo->getAddressesByType(Auth::id(), 'shipping');
                    $billingAddresses = $addressRepo->getAddressesByType(Auth::id(), 'billing');
                }

                $html = view('client.pages.checkout.partials._step-content', compact(
                    'step', 'order', 'itemsForView', 'addresses', 'billingAddresses',
                    'cities', 'totals', 'orders'
                ))->with('appliedCoupon', null)->render();

                return response()->json([
                    'success'          => true,
                    'step'             => 3,
                    'html'             => $html,
                    'order_id'         => $order->id,
                    'order_number'     => $order->order_number,
                    'order_detail_url' => route('order-detail', ['order' => $order->id]),
                    'ecommerce'        => Ga4Ecommerce::purchasePayload($order),
                ]);
            }

            // ── Normal form submit fallback ───────────────────────────────
            return redirect()
                ->route('checkout', ['step' => 3])
                ->with('order_id', $order->id ?? null);

        } catch (\Exception $e) {
            Log::error('Checkout failed', ['error' => $e->getMessage()]);

            if ($checkoutMode === 'buy_now') {
                $cartService->recoverIncompleteBuyNowToPreviousCart($cartId);
                $request->session()->forget('checkout_mode');
            }

            if ($request->ajax() || $request->wantsJson()) {
                // Return step-3 failure HTML
                $step             = 3;
                $order            = null;
                $addresses        = collect();
                $billingAddresses = collect();
                $cities           = $addressRepo->getActiveCities() ?: collect();
                $totals           = $this->emptyTotals();
                $itemsForView     = collect();
                $orders           = collect();

                $errors = new \Illuminate\Support\MessageBag(['checkout' => [$e->getMessage()]]);

                $html = view('client.pages.checkout.partials._step-content', compact(
                    'step', 'order', 'itemsForView', 'addresses', 'billingAddresses',
                    'cities', 'totals', 'orders', 'errors'
                ))->with('appliedCoupon', null)->render();

                return response()->json([
                    'success' => false,
                    'step'    => 3,
                    'html'    => $html,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('checkout', ['step' => 3])
                ->withErrors(['checkout' => $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Address section partial re-render (AJAX after new address saved)
    // ─────────────────────────────────────────────────────────────

    public function addressSection(Request $request, AddressRepo $addressRepo)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $type   = $request->input('type', 'shipping'); // 'shipping' or 'billing'
        $cities = $addressRepo->getActiveCities() ?: collect();

        if ($type === 'billing') {
            $billingAddresses = $addressRepo->getAddressesByType(Auth::id(), 'billing');
            $html = view('client.pages.checkout.partials._billing-address-section', compact('billingAddresses','cities'))->render();
        } else {
            $addresses = $addressRepo->getAddressesByType(Auth::id(), 'shipping');
            $html = view('client.pages.checkout.partials._shipping-address-section', compact('addresses','cities'))->render();
        }

        return response()->json(['success' => true, 'html' => $html, 'type' => $type]);
    }

    // ─────────────────────────────────────────────────────────────
    // Other existing actions (unchanged)
    // ─────────────────────────────────────────────────────────────

    public function detail(OrderDetailRequest $request, OrderRepo $orderRepo, ReviewRepositoryInterface $reviewRepo, \App\Services\OrderStatusService $orderStatusService)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = $orderRepo->findOrderWithRelations($request->input('order'), ['items.product','items.variant']);

        if (!$order || $order->user_id !== Auth::id()) {
            abort(404, 'Order not found or access denied.');
        }

        $userReviews = [];
        foreach ($order->items as $item) {
            if ($review = $reviewRepo->getUserReviewByOrderItem(Auth::id(), $item->id)) {
                $userReviews[$item->id] = $review;
            }
        }

        return view('client.pages.order-detail', compact('order','userReviews','orderStatusService'));
    }

    public function invoice(OrderInvoiceRequest $request, OrderRepo $orderRepo, PriceCalculatorService $priceCalculator)
    {
        if (!Auth::check()) return redirect()->route('login');

        $supportedLocales = ['en', 'fr', 'ar'];
        $requestedLocale = (string) ($request->query('locale') ?: $request->session()->get('locale', app()->getLocale()));
        $locale = in_array($requestedLocale, $supportedLocales, true) ? $requestedLocale : app()->getLocale();
        if (in_array($locale, $supportedLocales, true)) {
            $request->session()->put('locale', $locale);
            $request->session()->put('locale_user_selected', true);
            app()->setLocale($locale);
        }

        $order = $orderRepo->findOrderWithRelations($request->input('id'), ['items.product','items.variant']);
        if (!$order || $order->user_id !== Auth::id()) abort(403);
        $totals = $priceCalculator->calculate($order);
        return view('client.pages.order-invoice', compact('order','totals'));
    }

    public function downloadInvoice(OrderInvoiceRequest $request, OrderRepo $orderRepo, InvoiceExportService $invoiceExportService)
    {
        if (!Auth::check()) return redirect()->route('login');
        $order = $orderRepo->findOrderWithRelations($request->input('id'), ['items.product','items.variant','user']);
        if (!$order || $order->user_id !== Auth::id()) abort(403);
        $supportedLocales = ['en', 'fr', 'ar'];
        $requestedLocale = (string) ($request->query('locale') ?: $request->session()->get('locale', app()->getLocale()));
        $locale = in_array($requestedLocale, $supportedLocales, true) ? $requestedLocale : app()->getLocale();
        if (in_array($locale, $supportedLocales, true)) {
            $request->session()->put('locale', $locale);
            $request->session()->put('locale_user_selected', true);
        }

        Log::info('Client invoice locale debug', [
            'endpoint' => 'invoice_download',
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'query_locale' => $request->query('locale'),
            'session_locale' => $request->session()->get('locale'),
            'session_locale_user_selected' => $request->session()->get('locale_user_selected'),
            'resolved_locale' => $locale,
            'app_locale_before_render' => app()->getLocale(),
            'url' => $request->fullUrl(),
        ]);

        $pdf      = $invoiceExportService->renderInvoicePdf($order, $locale);
        $filename = $invoiceExportService->filename($order);
        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function cancel(OrderCancelRequest $request, OrderService $orderService)
    {
        if (!Auth::check()) return redirect()->route('login');

        $ok = $orderService->cancelClientOrder($request->input('id'), $request->input('reason'), Auth::id());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $ok,
                'status'  => $ok ? 'success' : 'error',
                'message' => $ok ? 'Order cancelled successfully.' : 'Unable to cancel the order.',
            ], $ok ? 200 : 422);
        }

        return $ok
            ? back()->with('success', 'Order cancelled successfully.')
            : back()->withErrors(['cancel' => 'Unable to cancel the order.']);
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    private function emptyTotals(): array
    {
        return [
            'subtotal'                    => 0,
            'total_item_discount'         => 0,
            'subtotal_after_item_discount'=> 0,
            'coupon_discount'             => 0,
            'total_after_coupon'          => 0,
            'shipping_method'             => 'standard',
            'shipping_cost'               => 0,
            'final_total'                 => 0,
            'items'                       => [],
        ];
    }
}
