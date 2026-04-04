@php
    $step          = $step ?? 1;
    $cart          = $cart ?? null;
    $itemCount     = $cart ? $cart->items->sum('quantity') : 0;
    $sym           = config('app.CURRENCY_SYMBOL', '$');
    $appliedCoupon = $appliedCoupon ?? null;
@endphp

@php
    $hasBlockingStockIssue = collect($itemsCollection ?? ($cart?->items ?? []))->contains(function ($item) {
        $availableStock = 0;

        if ($item->variant && method_exists($item->variant, 'getSellableStock')) {
            $availableStock = (int) $item->variant->getSellableStock();
        } elseif ($item->product && method_exists($item->product, 'getSellableStock')) {
            $availableStock = (int) $item->product->getSellableStock();
        }

        return $availableStock <= 0;
    });
    $isCartEmpty = $itemCount <= 0;
    $isProceedDisabled = $hasBlockingStockIssue || $isCartEmpty;
@endphp

{{-- ══════════════════════════════════════════ --}}
{{--  STEP 1 – SHOPPING CART                   --}}
{{-- ══════════════════════════════════════════ --}}
@if ($step == 1)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left – Cart items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl">
                @php $itemsCollection = $itemsForView ?? ($cart?->items ?? collect()); @endphp

                @if ($itemsCollection && $itemsCollection->count() > 0)
                    <div class="space-y-6">
                        @foreach ($itemsCollection as $item)
                            @include('client.pages.checkout.partials._cart-item-row', ['item' => $item])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-gray-500 text-lg">{{ statictranslations('Your cart is empty.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right – Order summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                        {{ statictranslations('Order Summary') }}
                    </h2>
                    <div class="flex items-center justify-between bg-linear-to-r from-primary/5 to-primary/10 px-4 py-3 rounded-lg">
                        <span class="text-sm font-semibold text-gray-700">{{ statictranslations('Item Total:') }}</span>
                        <span id="item-total-count" class="text-sm font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent">
                            {{ $itemCount }} {{ $itemCount === 1 ? 'Item' : 'Items' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                    @foreach ($itemsCollection ?? [] as $item)
                        @include('client.pages.checkout.partials._summary-item-row', ['item' => $item, 'showQtyControls' => true])
                    @endforeach
                </div>

                @include('client.pages.checkout.partials._order-summary-rows', ['totals' => $totals, 'idPrefix' => ''])

                {{-- SPA: use data-spa-step instead of href to avoid page reload --}}
                <button type="button" data-spa-step="2"
                    id="proceedToCheckoutBtn"
                    @disabled($isProceedDisabled)
                    class="block w-full px-6 py-5 bg-linear-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5 mt-2 {{ $isProceedDisabled ? 'opacity-60 cursor-not-allowed pointer-events-none' : '' }}">
                    {{ statictranslations('Proceed to Checkout') }}
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
                <p id="checkout-stock-block-message"
                    data-stock-block="{{ $hasBlockingStockIssue ? '1' : '0' }}"
                    class="mt-3 text-sm font-medium text-red-600 {{ $isProceedDisabled ? '' : 'hidden' }}">
                    @if ($hasBlockingStockIssue)
                        {{ statictranslations('Please remove out-of-stock items before continuing to checkout.') }}
                    @elseif ($isCartEmpty)
                        {{ statictranslations('Your cart is empty. Add items to continue to checkout.') }}
                    @endif
                </p>
            </div>
        </div>
    </div>

{{-- ══════════════════════════════════════════ --}}
{{--  STEP 2 – CHECKOUT FORM                   --}}
{{-- ══════════════════════════════════════════ --}}
@elseif ($step == 2)
    @if ($errors->any())
        <div id="checkout-validation-summary" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
            <p class="font-semibold">{{ statictranslations('Please fix the highlighted fields before continuing.') }}</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div id="checkout-validation-summary" class="hidden mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm"></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left – Forms --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Contact Information --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                    {{ statictranslations('Contact Information') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Email Address *') }}</label>
                        <input type="email" id="email" name="email"
                            value="{{ Auth::check() ? Auth::user()->email : '' }}" required
                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Phone Number *') }}</label>
                        <input type="tel" id="phone" name="phone"
                            value="{{ Auth::check() ? Auth::user()->phone : '' }}" required
                            pattern="[0-9\s\+\-\(\)]+"
                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            @include('client.pages.checkout.partials._shipping-address-section', [
                'addresses'        => $addresses,
                'cities'           => $cities,
            ])

            {{-- Billing Address --}}
            @include('client.pages.checkout.partials._billing-address-section', [
                'billingAddresses' => $billingAddresses,
                'cities'           => $cities,
            ])

            {{-- Payment Method --}}
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                    {{ statictranslations('Payment Method') }}
                </h2>
                <div class="space-y-4 mb-6">
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                        <input type="radio" name="payment_method" value="card" class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                        <span class="ml-4 font-semibold text-gray-900">NAPS</span>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                        <span class="ml-4 font-semibold text-gray-900">{{ statictranslations('Cash on Delivery') }}</span>
                    </label>
                </div>
                <div id="cardDetails" class="space-y-5">
                    <div>
                        <label for="cardNumber" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Card Number *') }}</label>
                        <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" required
                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label for="expiry" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Expiry Date *') }}</label>
                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required pattern="\d{2}/\d{2}"
                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label for="cvv" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('CVV *') }}</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required pattern="[0-9]{3}"
                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end left col --}}

        {{-- Right – Coupon + Order Summary --}}
        <div class="lg:col-span-1">
            @include('client.pages.checkout.partials._coupon-widget', [
                'appliedCoupon' => $appliedCoupon,
                'idSuffix'      => '-step2',
            ])

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                    {{ statictranslations('Order Summary') }}
                </h2>
                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                    @foreach ($itemsForView ?? [] as $item)
                        @include('client.pages.checkout.partials._summary-item-row', ['item' => $item, 'showQtyControls' => true])
                    @endforeach
                </div>
                @include('client.pages.checkout.partials._order-summary-rows', ['totals' => $totals, 'idPrefix' => '-step2'])

                <button type="button" id="placeOrderBtn"
                    class="w-full px-6 py-5 bg-linear-to-r from-primary to-primary/80 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5">
                    {{ statictranslations('Place Secure Order') }}
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </button>
                <p class="text-xs text-center text-gray-500 mt-4 flex items-center justify-center">
                    <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ statictranslations('Your payment information is secure') }}
                </p>
            </div>
        </div>

    </div>

{{-- ══════════════════════════════════════════ --}}
{{--  STEP 3 – ORDER CONFIRMED / FAILED        --}}
{{-- ══════════════════════════════════════════ --}}
@elseif ($step == 3)
    @php $itemCount = $order ? $order->items->sum('quantity') : 0; @endphp

    @if (!$order || $errors->has('checkout'))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-red-50 rounded-3xl shadow-xl border-2 border-red-200 p-12 text-center">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-red-100 flex items-center justify-center shadow-2xl animate-bounce-slow">
                        <svg class="w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-extrabold text-red-800 mb-4">{{ statictranslations('Order Failed!') }}</h2>
                    <p class="text-xl text-red-700 mb-2" id="checkout-error-message">
                        {{ $errors->first('checkout') ?? statictranslations('Your payment could not be processed. Please try again.') }}
                    </p>
                    <div class="bg-white rounded-2xl shadow-lg p-8 text-left mt-8 border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ statictranslations('What can you do?') }}</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>{{ statictranslations('Check your payment details and try again.') }}</li>
                            <li>{{ statictranslations('Try a different payment method.') }}</li>
                            <li>{{ statictranslations('If the problem persists, contact support.') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button" data-spa-step="2"
                            class="flex-1 px-8 py-5 bg-linear-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            {{ statictranslations('Try Again') }}
                        </button>
                        <a href="{{ route('cart') }}"
                            class="flex-1 px-8 py-5 border-2 border-primary text-primary text-center font-bold rounded-2xl hover:bg-primary hover:text-white transition-all duration-300 transform hover:scale-105">
                            {{ statictranslations('Return to Cart') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center justify-center">
                        <span class="w-1.5 h-8 rounded-full bg-red-500 mr-3"></span>
                        {{ statictranslations('Order Summary') }}
                    </h2>
                    <p class="text-gray-500">{{ statictranslations('No order was placed.') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-linear-to-br from-green-50 to-white rounded-3xl shadow-xl border-2 border-green-200 p-12 text-center">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-linear-to-br from-green-500 to-green-400 flex items-center justify-center shadow-2xl animate-bounce-slow">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-extrabold text-gray-900 mb-4">{{ statictranslations('Order Successfully Placed!') }}</h2>
                    <p class="text-xl text-gray-600 mb-2">{{ statictranslations('Your order has been successfully placed and is now being processed.') }}</p>
                    <p class="text-lg text-gray-600">{{ statictranslations('Order') }} #<span class="font-bold text-primary">{{ $order->order_number ?? '#' . $order->id }}</span></p>

                    <div class="bg-white rounded-2xl shadow-lg p-8 text-left mt-8 border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">{{ statictranslations('Order Details') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                            <div><span class="font-semibold text-gray-700 mr-2">{{ statictranslations('Order Number') }}:</span>{{ $order->order_number ?? '—' }}</div>
                            <div><span class="font-semibold text-gray-700 mr-2">{{ statictranslations('Order Date') }}:</span>{{ $order?->created_at?->format('M d, Y') ?? date('M d, Y') }}</div>
                            <div><span class="font-semibold text-gray-700 mr-2">{{ statictranslations('Payment Method') }}:</span>{{ $order->payment_method ?? '—' }}</div>
                            <div><span class="font-semibold text-gray-700 mr-2">{{ statictranslations('Shipping Method') }}:</span>{{ $order->shipping_method ?? 'Standard Shipping' }}</div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mt-6 text-left">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="font-bold text-blue-900 mb-1">{{ statictranslations("What's Next?") }}</h4>
                                <p class="text-sm text-blue-800">{{ statictranslations("We've sent a confirmation email to your inbox. You can track your order status in your account dashboard.") }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('home') }}"
                            class="flex-1 px-8 py-5 bg-linear-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            {{ statictranslations('Continue Shopping') }}
                        </a>
                        <a href="{{ route('order-detail', ['order' => $order->id]) }}"
                            class="flex-1 px-8 py-5 border-2 border-primary text-primary text-center font-bold rounded-2xl hover:bg-primary hover:text-white transition-all duration-300 transform hover:scale-105">
                            {{ statictranslations('View Order Details') }}
                        </a>
                    </div>
                </div>

                @if ($order->shipping_address)
                    @php
                        $addr = is_array($order->shipping_address)
                            ? $order->shipping_address
                            : json_decode($order->shipping_address, true);
                    @endphp
                    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200 mb-5">
                            <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ statictranslations('Shipping Address') }}
                            </h3>
                            <div class="bg-white rounded-xl p-4 space-y-1 text-sm text-gray-700">
                                @if ($addr && isset($addr['name']))
                                    <p class="font-semibold text-gray-900">{{ $addr['name'] }}</p>
                                    @if (isset($addr['street']))<p>{{ $addr['street'] }}</p>@endif
                                    <p>{{ collect([$addr['city'] ?? null, $addr['postal_code'] ?? null])->filter()->implode(', ') }}</p>
                                    @if (isset($addr['country']))<p>{{ $addr['country'] }}</p>@endif
                                @else
                                    <p class="text-gray-600">{{ statictranslations('No shipping address available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right – Order summary (read-only) --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1.5 h-8 rounded-full bg-linear-to-b from-green-500 to-green-400 mr-3"></span>
                        {{ statictranslations('Order Summary') }}
                    </h2>
                    <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                        @foreach ($order->items as $item)
                            @include('client.pages.checkout.partials._summary-item-row', ['item' => $item, 'showQtyControls' => false])
                        @endforeach
                    </div>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                            <span class="text-gray-600 font-medium">{{ statictranslations('MRP') }}</span>
                            <span class="font-bold text-gray-900">{{ $sym }}{{ number_format($order->subtotal ?? 0, 2) }}</span>
                        </div>
                        @if (($order->discount_amount ?? 0) > 0)
                            <div class="flex justify-between text-green-600 text-base bg-green-50 px-4 py-3 rounded-xl">
                                <span>{{ statictranslations('Discount on MRP') }}</span>
                                <span class="font-bold">-{{ $sym }}{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        @if (($order->coupon_discount_amount ?? 0) > 0)
                            <div class="flex justify-between items-center text-purple-600 text-base bg-purple-50 px-4 py-3 rounded-xl">
                                <span class="font-semibold">{{ statictranslations('Coupon Discount') }} - </span>
                                <span class="font-bold">-{{ $sym }}{{ number_format($order->coupon_discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="border-t-2 border-gray-100 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-gray-900">Total</span>
                                <span class="text-xl font-bold bg-linear-to-r from-green-500 to-green-400 bg-clip-text text-transparent">
                                    {{ $sym }}{{ number_format($order->total ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if (($order->payment_method ?? '') !== 'cod')
                        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                            <div class="flex items-center text-green-700">
                                <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-semibold">{{ statictranslations('Payment Successful') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
