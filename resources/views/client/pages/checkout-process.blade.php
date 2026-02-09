@extends('client.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout-process.css') }}">
@endpush

@section('content')
    @php
        $step = $step ?? (int) request()->get('step', 1);
        $cart = $cart ?? null;
        $itemCount = $cart ? $cart->items->sum('quantity') : 0;
    @endphp

    <script>
        window.env = {
            CURRENCY_SYMBOL: @json(config('app.CURRENCY_SYMBOL', '$')),
            CURRENCY_CODE: @json(config('app.CURRENCY_CODE', 'USD')),
        };
    </script>


    <div class="bg-linear-to-br from-gray-50 via-blue-50/30 to-white min-h-screen py-12">
        <div id="checkout-root" class="container mx-auto px-4 max-w-7xl" data-remove-url="{{ route('cart.remove') }}"
            data-update-url="{{ route('cart.update') }}">

            <div class="mb-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-3 tracking-tight">
                            @if ($step == 1)
                                Shopping Cart
                            @elseif($step == 2)
                                Checkout
                            @else
                                Order Confirmed
                            @endif
                        </h1>
                        <p class="text-gray-600 text-lg">
                            @if ($step == 1)
                                Review your items before proceeding
                            @elseif($step == 2)
                                Complete your purchase securely
                            @else
                                Thank you for your order!
                            @endif
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div
                            class="flex items-center space-x-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm">
                            <a href="#" class="hover:text-primary transition-colors">Home</a>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            @if ($step > 1)
                                <a href="#" class="hover:text-primary transition-colors">Cart</a>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            @endif
                            @if ($step == 3)
                                <a href="#" class="hover:text-primary transition-colors">Checkout</a>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            @endif
                            <span class="text-primary font-semibold">
                                @if ($step == 1)
                                    Cart
                                @elseif($step == 2)
                                    Checkout
                                @else
                                    Confirmed
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-12 bg-white rounded-3xl shadow-lg border border-gray-100 p-8 backdrop-blur-sm">
                <div class="flex items-center justify-between relative max-w-3xl mx-auto">

                    <div class="absolute top-6 left-0 right-0 h-1 bg-gray-200 z-0" style="margin: 0 8%;"></div>

                    <div class="absolute top-6 left-0 h-1 z-0 transition-all duration-700 ease-in-out
          @if ($step == 1) w-0 @endif
          @if ($step == 2) bg-linear-to-r from-primary to-primary/80 @endif
          @if ($step == 3) bg-linear-to-r from-green-500 to-green-400 @endif
        "
                        style="@if ($step == 2) width: calc(50% - 8%); margin-left: 8%; @elseif($step == 3) width: calc(100% - 16%); margin-left: 8%; @endif">
                    </div>

                    <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500">
                        <div
                            class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
            @if ($step == 1) bg-linear-to-br from-primary to-primary/80 text-white ring-4 ring-primary/20 @endif
            @if ($step > 1) bg-linear-to-br from-green-500 to-green-400 text-white @endif
          ">
                            @if ($step > 1)
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                1
                            @endif
                        </div>
                        <span
                            class="mt-3 text-sm font-semibold transition-colors duration-300
            @if ($step == 1) text-primary @endif
            @if ($step > 1) text-green-500 @endif
            @if ($step < 1) text-gray-500 @endif
          ">Shopping
                            Cart</span>
                    </div>

                    <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500">
                        <div
                            class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
            @if ($step == 2) bg-linear-to-br from-primary to-primary/80 text-white ring-4 ring-primary/20 @endif
            @if ($step < 2) bg-gray-200 text-gray-600 @endif
            @if ($step > 2) bg-linear-to-br from-green-500 to-green-400 text-white @endif
          ">
                            @if ($step > 2)
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                2
                            @endif
                        </div>
                        <span
                            class="mt-3 text-sm font-semibold transition-colors duration-300
            @if ($step == 2) text-primary @endif
            @if ($step < 2) text-gray-500 @endif
            @if ($step > 2) text-green-500 @endif
          ">Checkout</span>
                    </div>

                    <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500">
                        <div
                            class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
            @if ($step == 3) bg-linear-to-br from-green-500 to-green-400 text-white ring-4 ring-green-500/20 @endif
            @if ($step < 3) bg-gray-200 text-gray-600 @endif
          ">
                            @if ($step == 3)
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                3
                            @endif
                        </div>
                        <span
                            class="mt-3 text-sm font-semibold transition-colors duration-300
            @if ($step == 3) text-green-500 @endif
            @if ($step < 3) text-gray-500 @endif
          ">Order
                            Complete</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('checkout.process') }}">
                @csrf

                @if ($step == 1)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        <div class="lg:col-span-2 space-y-6">

                            <div
                                class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl">
                                @php
                                    $itemsCollection = $itemsForView ?? ($cart?->items ?? collect());
                                @endphp
                                @if ($itemsCollection && $itemsCollection->count() > 0)
                                    <div class="space-y-6">
                                        @foreach ($itemsCollection as $item)
                                            <div class="cart-item-row flex flex-col sm:flex-row gap-6 p-6 bg-linear-to-r from-gray-50 to-white rounded-2xl border border-gray-100 transition-all duration-300 hover:shadow-md"
                                                data-item-id="{{ $item->id }}">
                                                <div class="shrink-0">
                                                    <img src="{{ optional($item->product)->cover_image ? asset('storage/' . optional($item->product)->cover_image) : 'https://via.placeholder.com/150x150?text=Product' }}"
                                                        alt="{{ optional($item->product)->name ?? 'Product' }}"
                                                        class="w-full sm:w-36 h-36 object-cover rounded-2xl shadow-md">
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex justify-between items-start mb-3">
                                                        <div>
                                                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                                                {{ optional($item->product)->name ?? 'Product' }}
                                                                @if (optional($item->variant)->variant_name)
                                                                    <span class="text-gray-600 text-sm font-normal">-
                                                                        {{ optional($item->variant)->variant_name }}</span>
                                                                @endif
                                                            </h3>
                                                            <span
                                                                class="inline-block text-xs font-semibold px-3 py-1.5 rounded-full bg-linear-to-r from-gray-100 to-gray-200 text-gray-700">{{ optional($item->product)->brand->name ?? '' }}
                                                            </span>
                                                        </div>

                                                        {{-- The Remove Button (AJAX) --}}
                                                        <button type="button"
                                                            class="remove-item-btn flex items-center text-red-500 hover:text-red-700 transition-colors duration-200"
                                                            data-item-id="{{ $item->id }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Remove
                                                        </button>

                                                    </div>
                                                    <div class="space-y-2 text-sm text-gray-600 mb-5">
                                                        <p class="flex items-center">
                                                            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            <span class="font-medium">In Stock</span>
                                                        </p>
                                                    </div>
                                                    @php
                                                        $availableStock = 0;
                                                        if ($item->variant) {
                                                            $availableStock = (int) ($item->variant->total_stock ?? $item->variant->inventory()->sum('quantity') ?? 0);
                                                        } elseif ($item->product) {
                                                            if (method_exists($item->product, 'getTotalStockAttribute')) {
                                                                $availableStock = (int) $item->product->getTotalStockAttribute();
                                                            } elseif (method_exists($item->product, 'inventory')) {
                                                                $availableStock = (int) $item->product->inventory()->sum('quantity');
                                                            }
                                                        }
                                                    @endphp
                                                    <div
                                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                                        <div class="flex items-center">
                                                            <span
                                                                class="text-sm font-semibold text-gray-700 mr-4">Quantity:</span>
                                                            <div
                                                                class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
                                                                <button type="button"
                                                                    class="qty-decrease px-5 py-3 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold transition-all duration-300"
                                                                    data-item-id="{{ $item->id }}"
                                                                    @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @endif>−</button>
                                                                <input type="number" value="{{ $item->quantity }}"
                                                                    min="1"
                                                                    class="cart-quantity-input w-16 text-center border-0 focus:outline-none font-bold text-gray-900 bg-white"
                                                                    data-item-id="{{ $item->id }}"
                                                                    @if ($availableStock > 0) max="{{ $availableStock }}" data-max-stock="{{ $availableStock }}" @endif>
                                                                <button type="button"
                                                                    class="qty-increase px-5 py-3 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold transition-all duration-300"
                                                                    data-item-id="{{ $item->id }}"
                                                                    @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @endif>+</button>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            @php
                                                                // Use stored discount data from cart_items table
                                                                $unitPrice = $item->unit_price ?? 0;
                                                                $unitFinal = $item->final_price ?? $unitPrice;
                                                                $lineTotal = $unitPrice * $item->quantity;
                                                                $lineDiscount =
                                                                    ($item->discount_amount ?? 0) * $item->quantity;
                                                                $lineFinal = $unitFinal * $item->quantity;
                                                                $hasDiscount = ($item->discount_amount ?? 0) > 0;
                                                                $discountName =
                                                                    $hasDiscount && $item->discount
                                                                        ? $item->discount->getTranslation(
                                                                            'name',
                                                                            app()->getLocale(),
                                                                        )
                                                                        : null;
                                                            @endphp

                                                            @if ($hasDiscount)
                                                                {{-- Original line total with strikethrough --}}
                                                                <div>
                                                                    <div
                                                                        class="inline-block text-lg text-gray-400 line-through mb-1">
                                                                        {{ config('app.CURRENCY_SYMBOL') }}
                                                                        {{ number_format($lineTotal, 2) }}
                                                                    </div>
                                                                </div>

                                                                {{-- Discount badge --}}
                                                                @if ($lineDiscount > 0)
                                                                    <div
                                                                        class="inline-flex items-center text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-full mb-1">
                                                                        <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                            viewBox="0 0 20 20">
                                                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                                            <path fill-rule="evenodd"
                                                                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                        Save {{ config('app.CURRENCY_SYMBOL') }}
                                                                        {{ number_format($lineDiscount, 2) }}
                                                                    </div>
                                                                @endif

                                                                {{-- Final price after discount --}}
                                                                <p class="text-3xl font-bold text-red-600 item-line-total"
                                                                    id="item-line-total-{{ $item->id }}">
                                                                    {{ config('app.CURRENCY_SYMBOL') }}
                                                                    {{ number_format($lineFinal, 2) }}</p>
                                                            @else
                                                                {{-- No discount - show regular price --}}
                                                                <p class="text-3xl font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent item-line-total"
                                                                    id="item-line-total-{{ $item->id }}">
                                                                    {{ config('app.CURRENCY_SYMBOL') }}
                                                                    {{ number_format($lineFinal, 2) }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-16">
                                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <p class="text-gray-500 text-lg">Your cart is empty.</p>
                                    </div>
                                @endif
                            </div>


                        </div>

                        <div class="lg:col-span-1">
                            {{-- <div class="mb-6 bg-linear-to-r from-primary/5 to-blue-50 rounded-3xl shadow-lg border border-primary/20 p-6" id="coupon-container">
              <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                Have a Coupon Code?
              </h3>
              @if ($cart && $cart->coupon_code)
                <div class="bg-white rounded-xl p-4 border-2 border-purple-200" id="applied-coupon-display">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                      </div>
                      <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $cart->coupon_code }}</p>
                        <p class="text-xs text-gray-600">Coupon Applied</p>
                      </div>
                    </div>
                    <button type="button" id="remove-coupon-btn" class="text-red-500 hover:text-red-700 transition-colors px-3 py-1 rounded-lg hover:bg-red-50 text-sm font-semibold">
                      Remove
                    </button>
                  </div>
                </div>
              @else
                <div class="flex gap-3" id="coupon-input-form">
                  <input type="text" id="coupon-code-input" placeholder="Enter coupon code" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm uppercase">
                  <button type="button" id="apply-coupon-btn" class="px-6 py-3 bg-linear-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">Apply</button>
                </div>
              @endif
              <div id="coupon-message" class="mt-3 text-sm hidden"></div>
            </div> --}}

                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                                <div class="mb-6">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                        <span
                                            class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                        Order Summary
                                    </h2>
                                    <div
                                        class="flex items-center justify-between bg-linear-to-r from-primary/5 to-primary/10 px-4 py-3 rounded-lg">
                                        <span class="text-sm font-semibold text-gray-700">Item Total:</span>
                                        <span id="item-total-count"
                                            class="text-sm font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent">{{ $itemCount }}
                                            {{ $itemCount === 1 ? 'Quantity' : 'Quantity' }}</span>
                                    </div>
                                </div>

                                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                                    @php
                                        $summaryItems = $itemsForView ?? ($cart?->items ?? collect());
                                    @endphp
                                    @if ($summaryItems && $summaryItems->count() > 0)
                                        @foreach ($summaryItems as $item)
                                            @php
                                                $availableStock = 0;
                                                if ($item->variant) {
                                                    $availableStock = (int) ($item->variant->total_stock ?? $item->variant->inventory()->sum('quantity') ?? 0);
                                                } elseif ($item->product) {
                                                    if (method_exists($item->product, 'getTotalStockAttribute')) {
                                                        $availableStock = (int) $item->product->getTotalStockAttribute();
                                                    } elseif (method_exists($item->product, 'inventory')) {
                                                        $availableStock = (int) $item->product->inventory()->sum('quantity');
                                                    }
                                                }
                                            @endphp
                                            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200 summary-item-row items-center justify-between"
                                                data-summary-item-id="{{ $item->id }}"
                                                @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @endif>
                                                <div class="flex gap-4 flex-1 min-w-0">
                                                    <img src="{{ optional($item->product)->cover_image ? asset('storage/' . optional($item->product)->cover_image) : 'https://via.placeholder.com/80x80' }}"
                                                        alt="{{ optional($item->product)->name ?? 'Product' }}"
                                                        class="w-20 h-20 rounded-lg object-cover shadow-sm shrink-0">
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="font-semibold text-gray-900 text-xs mb-1 truncate">
                                                            {{ optional($item->product)->name ?? 'Product' }}
                                                        </h4>
                                                        @if (optional($item->variant)->variant_name)
                                                            <div class="text-gray-600 text-xs font-normal mb-1">
                                                                {{ $item->variant->variant_name }}
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <p class="text-xs text-gray-600">Qty:</p>
                                                            <div
                                                                class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white">
                                                                <button type="button"
                                                                    class="summary-qty-decrease px-2 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-bold transition-all duration-300"
                                                                    data-item-id="{{ $item->id }}">−</button>
                                                                <span
                                                                    class="summary-quantity px-3 py-1 text-xs font-semibold text-gray-900 bg-white min-w-7.5 text-center">{{ $item->quantity }}</span>
                                                                <button type="button"
                                                                    class="summary-qty-increase px-2 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-bold transition-all duration-300"
                                                                    data-item-id="{{ $item->id }}">+</button>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $unitPrice = $item->unit_price ?? 0;
                                                            $unitFinal = $item->final_price ?? $unitPrice;
                                                            $lineTotal = $unitPrice * $item->quantity;
                                                            $lineDiscount =
                                                                ($item->discount_amount ?? 0) * $item->quantity;
                                                            $lineFinal = $unitFinal * $item->quantity;
                                                            $hasDiscount = ($item->discount_amount ?? 0) > 0;
                                                        @endphp
                                                        @if ($hasDiscount)
                                                            <div>
                                                                <span class="text-xs text-gray-400 line-through">
                                                                    {{ config('app.CURRENCY_SYMBOL') }}
                                                                    {{ number_format($lineTotal, 2) }}
                                                                    <br />
                                                                </span>
                                                                <span
                                                                    class="summary-item-price text-sm font-bold text-red-600">
                                                                    {{ config('app.CURRENCY_SYMBOL') }}
                                                                    {{ number_format($lineFinal, 2) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <p
                                                                class="summary-item-price text-sm font-bold text-primary mt-1">
                                                                {{ config('app.CURRENCY_SYMBOL') }}
                                                                {{ number_format($lineFinal, 2) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="summary-remove-item-btn flex items-center justify-center text-red-500 hover:text-red-700 transition-colors duration-200 shrink-0"
                                                    data-item-id="{{ $item->id }}" title="Remove item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="space-y-5 mb-8">
                                    {{-- <div class="flex justify-between text-gray-600 text-lg">
                  <span>Subtotal (<span id="order-item-count">{{ $itemCount }}</span> item{{ $itemCount !== 1 ? 's' : '' }})</span>
                  <span id="order-subtotal" class="font-bold">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($totals['subtotal'] ?? 0, 2) }}</span>
                </div> --}}

                                    {{-- @if ($totalDiscount > 0)
                  <div class="flex justify-between text-green-600 text-lg bg-green-50 px-4 py-3 rounded-xl">
                    <span class="flex items-center">
                      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      Discount on MRP
                    </span>
                    <span id="order-discount" class="font-bold">-{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($totals['total_discount'] ?? 0, 2) }}</span>
                  </div>

                  <div class="flex justify-between text-gray-900 text-lg font-semibold border-t border-b border-gray-200 py-3">
                    <span>MRP After Discount</span>
                    <span id="order-subtotal-after-discount">{{ config('app.CURRENCY_SYMBOL') }} {{ number_format(($totals['subtotal'] ?? 0) - ($totals['total_discount'] ?? 0), 2) }}</span>
                  </div>
                @endif --}}

                                    {{-- @if ($couponDiscount > 0)
                  <div class="flex justify-between items-center text-purple-600 text-lg bg-purple-50 px-4 py-3 rounded-xl">
                    <div class="flex items-center">
                      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                      </svg>
                      <div>
                        <span class="font-semibold">Coupon Discount</span>
                        @if ($cart && $cart->coupon_code)
                          <span class="text-xs block text-purple-500">{{ $cart->coupon_code }}</span>
                        @endif
                      </div>
                    </div>
                    <span id="order-coupon-discount" class="font-bold">-{{ config('app.CURRENCY_SYMBOL') }} {{ number_format($totals['coupon_discount'] ?? 0, 2) }}</span>
                  </div>
                @endif --}}

                                    {{-- <div class="flex justify-between text-gray-600 text-lg">
                  <span>Shipping</span>
                  <span id="order-shipping" class="font-bold text-green-600">{{ ($totals['shipping'] ?? 0) > 0 ? config('app.CURRENCY_SYMBOL') . number_format($totals['shipping'],2) : 'FREE' }}</span>
                </div>
                <div class="flex justify-between text-gray-600 text-lg">
                  <span>{{ config('tax.label', 'VAT') }} ({{ config('tax.rate', 20) }}% included)</span>
                  <span id="order-tax" class="font-bold">{{ config('app.CURRENCY_SYMBOL') }}{{ number_format($tax, 2) }}</span>
                </div> --}}
                                    <div class="border-t-2 border-gray-100 pt-5">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xl font-bold text-gray-900">Total</span>
                                            <span id="order-total"
                                                class="text-xl font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent">{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format($totals['final_total'] ?? ($totals['total'] ?? 0), 2) }}</span>
                                        </div>

                                        {{-- @if ($totalDiscount > 0)
                    <div class="mt-3 text-center">
                      <span class="inline-flex items-center text-sm font-semibold text-green-700 bg-green-100 px-4 py-2 rounded-full">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        You save {{ config('app.CURRENCY_SYMBOL') }} {{ number_format($totalDiscount, 2) }} today!
                      </span>
                    </div>
                  @endif --}}
                                    </div>
                                </div>
                                <a href="{{ route('checkout', ['step' => 2]) }}"
                                    class="block w-full px-6 py-5 bg-linear-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5">
                                    Proceed to Checkout
                                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($step == 2)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        <div class="lg:col-span-2 space-y-6">

                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                    Contact Information
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email
                                            Address *</label>
                                        <input type="email" id="email" name="email" value="{{ Auth::check() ? Auth::user()->email : '' }}" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 invalid:border-red-500 invalid:focus:ring-red-500/20">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone
                                            Number *</label>
                                        <input type="tel" id="phone" name="phone" value="{{ Auth::check() ? Auth::user()->phone : '' }}" required pattern="[0-9\s\+\-\(\)]+"
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 invalid:border-red-500 invalid:focus:ring-red-500/20">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                            <span
                                                class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                            Shipping Address
                                        </h2>
                                        <p class="text-gray-500 text-sm mt-1">Where should we deliver your order?</p>
                                    </div>
                                    @if (Auth::check())
                                        <button type="button" id="addShippingAddressBtn"
                                            class="px-4 py-2.5 bg-linear-to-r from-primary to-primary/90 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add New
                                        </button>
                                    @endif
                                </div>
                                <div class="space-y-5">
                                    @if (Auth::check() && $addresses->count() > 0)
                                        <div>
                                            <label for="shippingAddressSelect"
                                                class="block text-sm font-semibold text-gray-700 mb-3">
                                                Select Saved Address
                                                <span
                                                    class="text-xs font-normal text-gray-500 ml-1">({{ $addresses->count() }}
                                                    available)</span>
                                            </label>

                                            <div class="relative">
                                                <select id="shippingAddressSelect" name="shipping_address_id" required
                                                    class="block w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none bg-white pr-12 text-gray-900 font-medium hover:border-gray-300">
                                                    <option value="">-- Select shipping address --</option>
                                                    @foreach ($addresses as $addr)
                                                        <option value="{{ $addr->id }}"
                                                            data-addr='@json($addr)'
                                                            {{ $addr->is_default ? 'selected' : '' }}>
                                                            {{ $addr->address_label ? '📍 ' . $addr->address_label . ' - ' : '' }}{{ $addr->name }}
                                                            ({{ $addr->city }}, {{ $addr->state }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>
                                            @error('shipping_address_id')
                                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                            <!-- Selected Address Display -->
                                            <div id="selectedShippingDisplay"
                                                class="hidden mt-4 p-4 bg-linear-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl">
                                                <div class="flex items-start gap-3">
                                                    <div
                                                        class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-semibold text-green-800 mb-1">Shipping To:
                                                        </p>
                                                        <p id="displayShippingName" class="font-bold text-gray-900"></p>
                                                        <p id="displayShippingStreet" class="text-sm text-gray-700"></p>
                                                        <p id="displayShippingCity" class="text-sm text-gray-700"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(Auth::check())
                                        <div
                                            class="text-center py-10 bg-linear-to-br from-gray-50 to-blue-50 rounded-2xl border-2 border-dashed border-gray-300">
                                            <div
                                                class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-700 font-semibold mb-2">No Saved Addresses</p>
                                            <p class="text-gray-500 text-sm mb-4">Add your first shipping address to
                                                continue</p>
                                            <button type="button" id="addShippingAddressBtnEmpty"
                                                class="px-6 py-3 bg-linear-to-r from-primary to-primary/90 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all inline-flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Shipping Address
                                            </button>
                                        </div>
                                    @endif

                                    <script>
                                        // Handle shipping address dropdown selection
                                        (function() {
                                            const select = document.getElementById('shippingAddressSelect');
                                            if (!select) return;

                                            select.addEventListener('change', function() {
                                                const option = this.options[this.selectedIndex];
                                                if (!option || !option.dataset.addr) return;

                                                try {
                                                    const addr = JSON.parse(option.dataset.addr);

                                                    // Parse name into first and last
                                                    const nameParts = addr.name ? addr.name.split(' ') : ['', ''];
                                                    const firstName = nameParts[0] || '';
                                                    const lastName = nameParts.slice(1).join(' ') || '';

                                                    // Populate hidden fields for submission
                                                    document.getElementById('firstName').value = firstName;
                                                    document.getElementById('lastName').value = lastName;
                                                    document.getElementById('address').value = addr.street || '';
                                                    document.getElementById('city').value = addr.city || '';
                                                    document.getElementById('state').value = addr.state || '';
                                                    document.getElementById('zip').value = addr.postal_code || '';
                                                } catch (e) {
                                                    console.error('Error parsing address:', e);
                                                }
                                            });

                                            // Trigger change on load if default is selected
                                            if (select.value) {
                                                select.dispatchEvent(new Event('change'));
                                            }
                                        })();
                                    </script>

                                    <!-- Shipping Address Preview Card -->
                                    <div id="shippingAddressPreview"
                                        class="hidden mt-4 bg-white rounded-2xl overflow-hidden shadow-md border-l-4 border-green-500">
                                        <div
                                            class="bg-linear-to-r from-green-50 to-green-25 px-6 py-4 border-b border-green-100">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-green-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <span class="block font-semibold text-gray-900">Shipping
                                                            Address</span>
                                                        <span class="block text-xs text-gray-600">Confirmed</span>
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="changeAddressBtn text-primary hover:text-primary/80 text-sm font-semibold transition-colors">Change</button>
                                            </div>
                                        </div>
                                        <div class="px-6 py-5">
                                            <p id="previewShippingName" class="font-semibold text-gray-900 mb-2"></p>
                                            <p id="previewShippingStreet" class="text-gray-700 text-sm mb-1"></p>
                                            <p id="previewShippingCity" class="text-gray-700 text-sm"></p>
                                        </div>
                                    </div>

                                    <script>
                                        (function() {
                                            const checkbox = document.getElementById('billingSameAsShipping');
                                            const section = document.getElementById('billingFields');
                                            if (!checkbox || !section) return;

                                            const toggleBilling = function() {
                                                const same = checkbox.checked;
                                                section.classList.toggle('hidden', same);
                                                const inputs = section.querySelectorAll('input, select, textarea');
                                                inputs.forEach(function(el) {
                                                    if (same) {
                                                        if (el.required) el.setAttribute('data-required', '1');
                                                        el.required = false;
                                                    } else {
                                                        if (el.dataset.required === '1') el.required = true;
                                                    }
                                                });
                                            };

                                            checkbox.addEventListener('change', toggleBilling);
                                            toggleBilling();
                                        })();
                                    </script>

                                    <!-- Hidden form fields for shipping address (populated by selected card) -->
                                    <input type="hidden" id="firstName" name="firstName" value="">
                                    <input type="hidden" id="lastName" name="lastName" value="">
                                    <input type="hidden" id="address" name="address" value="">
                                    <input type="hidden" id="city" name="city" value="">
                                    <input type="hidden" id="state" name="state" value="">
                                    <input type="hidden" id="zip" name="zip" value="">
                                    <input type="hidden" id="country" name="country" value="Morocco">
                                </div>
                            </div>

                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                            <span
                                                class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                            Billing Address
                                        </h2>
                                        <p class="text-gray-500 text-sm mt-1">Payment and billing information</p>
                                    </div>
                                    <label
                                        class="inline-flex items-center px-4 py-2 bg-blue-50 rounded-xl border-2 border-blue-200 cursor-pointer hover:bg-blue-100 transition-all">
                                        <input type="checkbox" id="billingSameAsShipping" name="billing_same_as_shipping"
                                            value="1" class="form-checkbox h-4 w-4 text-primary rounded" checked>
                                        <span class="ml-2 text-sm font-semibold text-gray-700">Same as shipping</span>
                                    </label>
                                </div>

                                <div id="billingFields" class="space-y-5 hidden">
                                    @if (Auth::check())
                                        <div class="flex items-center justify-between mb-4">
                                            <label for="billingAddressSelect"
                                                class="block text-sm font-semibold text-gray-700">
                                                Select Billing Address
                                                @if ($billingAddresses->count() > 0)
                                                    <span
                                                        class="text-xs font-normal text-gray-500 ml-1">({{ $billingAddresses->count() }}
                                                        available)</span>
                                                @endif
                                            </label>
                                            <button type="button" id="addBillingAddressBtn"
                                                class="px-4 py-2.5 bg-linear-to-r from-primary to-primary/90 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add New
                                            </button>
                                        </div>
                                    @endif

                                    @if (Auth::check() && $billingAddresses->count() > 0)
                                        <div>
                                            <div class="relative">
                                                <select id="billingAddressSelect" name="billing_address_id"
                                                    class="block w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none bg-white pr-12 text-gray-900 font-medium hover:border-gray-300">
                                                    <option value="">-- Select billing address --</option>
                                                    @foreach ($billingAddresses as $addr)
                                                        <option value="{{ $addr->id }}"
                                                            data-addr='@json($addr)'
                                                            {{ $addr->is_default ? 'selected' : '' }}>
                                                            {{ $addr->address_label ? '💳 ' . $addr->address_label . ' - ' : '' }}{{ $addr->name }}
                                                            ({{ $addr->city }}, {{ $addr->state }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>
                                            @error('billing_address_id')
                                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                            <!-- Selected Billing Address Display -->
                                            <div id="selectedBillingDisplay"
                                                class="hidden mt-4 p-4 bg-linear-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl">
                                                <div class="flex items-start gap-3">
                                                    <div
                                                        class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-semibold text-blue-800 mb-1">Billing To:</p>
                                                        <p id="displayBillingName" class="font-bold text-gray-900"></p>
                                                        <p id="displayBillingStreet" class="text-sm text-gray-700"></p>
                                                        <p id="displayBillingCity" class="text-sm text-gray-700"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(Auth::check())
                                        <div
                                            class="text-center py-10 bg-linear-to-br from-gray-50 to-purple-50 rounded-2xl border-2 border-dashed border-gray-300">
                                            <div
                                                class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-700 font-semibold mb-2">No Billing Addresses</p>
                                            <p class="text-gray-500 text-sm mb-4">Add a billing address for payment</p>
                                            <button type="button" id="addBillingAddressBtnEmpty"
                                                class="px-6 py-3 bg-linear-to-r from-primary to-primary/90 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all inline-flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Billing Address
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Hidden form fields for billing address (populated by dropdown) - OUTSIDE billingFields div -->
                                <input type="hidden" id="billing_firstName" name="billing_firstName" value="">
                                <input type="hidden" id="billing_lastName" name="billing_lastName" value="">
                                <input type="hidden" id="billing_address_line" name="billing_address_line"
                                    value="">
                                <input type="hidden" id="billing_city" name="billing_city" value="">
                                <input type="hidden" id="billing_state" name="billing_state" value="">
                                <input type="hidden" id="billing_zip" name="billing_zip" value="">
                                <input type="hidden" id="billing_country" name="billing_country" value="Morocco">
                                <input type="hidden" id="billing_phone" name="billing_phone" value="">
                            </div>

                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                    Shipping Method
                                </h2>
                                <div class="space-y-4">
                                    <label
                                        class="flex items-center p-5 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-primary hover:bg-primary/5">
                                        <input type="radio" name="shipping_method" value="standard" checked
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 flex-1">
                                            <span class="block font-semibold text-gray-900">Standard Shipping</span>
                                            <span class="block text-sm text-gray-600">5-7 business days</span>
                                        </span>
                                        <span class="font-bold text-green-600">FREE</span>
                                    </label>
                                    <label
                                        class="flex items-center p-5 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-primary hover:bg-primary/5">
                                        <input type="radio" name="shipping_method" value="express"
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 flex-1">
                                            <span class="block font-semibold text-gray-900">Express Shipping</span>
                                            <span class="block text-sm text-gray-600">2-3 business days</span>
                                        </span>
                                        <span class="font-bold text-gray-900">{{ config('app.CURRENCY_SYMBOL') }}
                                            12.99</span>
                                    </label>
                                </div>
                            </div>

                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                    Payment Method
                                </h2>
                                <div class="space-y-4 mb-6">
                                    <label
                                        class="flex items-center p-4 border-2 border-primary rounded-xl cursor-pointer bg-primary/5">
                                        <input type="radio" name="payment_method" value="card" checked
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 font-semibold text-gray-900">Credit / Debit Card</span>
                                    </label>

                                    <label
                                        class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                                        <input type="radio" name="payment_method" value="paypal"
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 font-semibold text-gray-900">PayPal</span>
                                    </label>

                                    <label
                                        class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                                        <input type="radio" name="payment_method" value="cod"
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 font-semibold text-gray-900">Cash on Delivery</span>
                                    </label>
                                </div>

                                <div id="cardDetails" class="space-y-5">
                                    <div>
                                        <label for="cardNumber"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Card Number *</label>
                                        <input type="text" id="cardNumber" name="cardNumber"
                                            placeholder="1234 5678 9012 3456" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 invalid:border-red-500 invalid:focus:ring-red-500/20">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-5">
                                        <div>
                                            <label for="expiry"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date
                                                *</label>
                                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required pattern="\d{2}/\d{2}"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 invalid:border-red-500 invalid:focus:ring-red-500/20">
                                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                        </div>
                                        <div>
                                            <label for="cvv"
                                                class="block text-sm font-semibold text-gray-700 mb-2">CVV *</label>
                                            <input type="text" id="cvv" name="cvv" maxlength="3" required pattern="[0-9]{3}"
                                                placeholder="123"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 invalid:border-red-500 invalid:focus:ring-red-500/20">
                                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="lg:col-span-1">

                            <div class="mb-6 bg-linear-to-r from-primary/5 to-blue-50 rounded-3xl shadow-lg border border-primary/20 p-6"
                                id="coupon-container-step2">
                                <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                    Have a Coupon Code?
                                </h3>
                                @if ($cart && $cart->coupon_code)
                                    <div class="bg-white rounded-xl p-4 border-2 border-purple-200"
                                        id="applied-coupon-display-step2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 text-sm">{{ $cart->coupon_code }}
                                                    </p>
                                                    <p class="text-xs text-gray-600">Coupon Applied</p>
                                                </div>
                                            </div>
                                            <button type="button" id="remove-coupon-btn-step2"
                                                class="text-red-500 hover:text-red-700 transition-colors px-3 py-1 rounded-lg hover:bg-red-50 text-sm font-semibold">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex gap-3" id="coupon-input-form-step2">
                                        <input type="text" id="coupon-code-input-step2"
                                            placeholder="Enter coupon code"
                                            class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm uppercase">
                                        <button type="button" id="apply-coupon-btn-step2"
                                            class="px-6 py-3 bg-linear-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">Apply</button>
                                    </div>
                                @endif
                                <div id="coupon-message-step2" class="mt-3 text-sm hidden"></div>
                            </div>

                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                    Order Summary
                                </h2>
                                <div
                                    class="flex items-center justify-between bg-linear-to-r from-primary/5 to-primary/10 px-4 py-3 rounded-lg">
                                    <span class="text-sm font-semibold text-gray-700">Item Total:</span>
                                    <span id="item-total-count"
                                        class="text-sm font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent">{{ $itemCount }}
                                        {{ $itemCount === 1 ? 'Quantity' : 'Quantity' }}</span>
                                </div>

                                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                                    @php
                                        $summaryItems = $itemsForView ?? ($cart?->items ?? collect());
                                    @endphp
                                    @if ($summaryItems && $summaryItems->count() > 0)
                                        @foreach ($summaryItems as $item)
                                            @php
                                                $availableStock = 0;
                                                if ($item->variant) {
                                                    $availableStock = (int) ($item->variant->total_stock ?? $item->variant->inventory()->sum('quantity') ?? 0);
                                                } elseif ($item->product) {
                                                    if (method_exists($item->product, 'getTotalStockAttribute')) {
                                                        $availableStock = (int) $item->product->getTotalStockAttribute();
                                                    } elseif (method_exists($item->product, 'inventory')) {
                                                        $availableStock = (int) $item->product->inventory()->sum('quantity');
                                                    }
                                                }
                                            @endphp
                                            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200 summary-item-row items-center justify-between"
                                                data-summary-item-id="{{ $item->id }}"
                                                @if ($availableStock > 0) data-max-stock="{{ $availableStock }}" @endif>
                                                <div class="flex gap-4 flex-1 min-w-0">
                                                    <img src="{{ optional($item->product)->cover_image ? asset('storage/' . optional($item->product)->cover_image) : 'https://via.placeholder.com/80x80' }}"
                                                        alt="{{ optional($item->product)->name ?? 'Product' }}"
                                                        class="w-20 h-20 rounded-lg object-cover shadow-sm shrink-0">
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="font-semibold text-gray-900 text-xs mb-1 truncate">
                                                            {{ optional($item->product)->name ?? 'Product' }}
                                                        </h4>
                                                        @if (optional($item->variant)->variant_name)
                                                            <div class="text-gray-600 text-xs font-normal mb-1">
                                                                {{ $item->variant->variant_name }}
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <p class="text-xs text-gray-600">Qty:</p>
                                                            <div
                                                                class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white">
                                                                <button type="button"
                                                                    class="summary-qty-decrease px-2 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-bold transition-all duration-300"
                                                                    data-item-id="{{ $item->id }}">−</button>
                                                                <span
                                                                    class="summary-quantity px-3 py-1 text-xs font-semibold text-gray-900 bg-white min-w-7.5 text-center">{{ $item->quantity }}</span>
                                                                <button type="button"
                                                                    class="summary-qty-increase px-2 py-1 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-bold transition-all duration-300"
                                                                    data-item-id="{{ $item->id }}">+</button>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $unitPrice = $item->unit_price ?? 0;
                                                            $unitFinal = $item->final_price ?? $unitPrice;
                                                            $lineTotal = $unitPrice * $item->quantity;
                                                            $lineDiscount =
                                                                ($item->discount_amount ?? 0) * $item->quantity;
                                                            $lineFinal = $unitFinal * $item->quantity;
                                                            $hasDiscount = ($item->discount_amount ?? 0) > 0;
                                                        @endphp
                                                        @if ($hasDiscount)
                                                            <div>
                                                                <span class="text-xs text-gray-400 line-through">
                                                                    {{ config('app.CURRENCY_SYMBOL') }}
                                                                    {{ number_format($lineTotal, 2) }}
                                                                    <br />
                                                                </span>
                                                                <span
                                                                    class="summary-item-price text-sm font-bold text-red-600">
                                                                    {{ config('app.CURRENCY_SYMBOL') }}
                                                                    {{ number_format($lineFinal, 2) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <p
                                                                class="summary-item-price text-sm font-bold text-primary mt-1">
                                                                {{ config('app.CURRENCY_SYMBOL') }}
                                                                {{ number_format($lineFinal, 2) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="summary-remove-item-btn flex items-center justify-center text-red-500 hover:text-red-700 transition-colors duration-200 shrink-0"
                                                    data-item-id="{{ $item->id }}" title="Remove item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="space-y-4 mb-6">
                                    <div class="space-y-3">
                                        <span class="block text-sm font-semibold text-gray-700 mb-3">Price Details</span>

                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                            <span class="text-gray-600 font-medium">MRP</span>
                                            <span id="order-subtotal"
                                                class="font-bold text-gray-900">{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format($totals['subtotal'] ?? 0, 2) }}</span>
                                        </div>
                                    </div>

                                    @if (($totals['total_discount'] ?? 0) > 0)
                                        <div
                                            class="flex justify-between text-green-600 text-base bg-green-50 px-4 py-3 rounded-xl">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                    <path fill-rule="evenodd"
                                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Discount on MRP
                                            </span>
                                            <span id="order-discount"
                                                class="font-bold">-{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format($totals['total_discount'] ?? 0, 2) }}</span>
                                        </div>

                                        {{-- <div
                                            class="flex justify-between text-gray-900 text-base font-semibold border-t border-b border-gray-200 py-3">
                                            <span>MRP After Discount</span>
                                            <span id="order-subtotal-after-discount">{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format(($totals['subtotal'] ?? 0) - ($totals['total_discount'] ?? 0), 2) }}</span>
                                        </div> --}}
                                    @endif

                                    @if (($totals['coupon_discount'] ?? 0) > 0)
                                        <div
                                            class="flex justify-between items-center text-purple-600 text-base bg-purple-50 px-4 py-3 rounded-xl">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                </svg>
                                                <div>
                                                    <span class="font-semibold">Coupon Discount</span>
                                                    @if ($cart && $cart->coupon_code)
                                                        <span
                                                            class="text-xs block text-purple-500">{{ $cart->coupon_code }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span id="order-coupon-discount"
                                                class="font-bold">-{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format($totals['coupon_discount'] ?? 0, 2) }}</span>
                                        </div>
                                    @endif

                                    <div class="flex justify-between text-gray-600 text-base">
                                        <span>Shipping</span>
                                        <span id="order-shipping"
                                            class="font-bold text-green-600">{{ ($totals['shipping'] ?? 0) > 0 ? config('app.CURRENCY_SYMBOL') . number_format($totals['shipping'], 2) : 'FREE' }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600 text-base">
                                        {{-- <span>{{ config('tax.label', 'VAT') }} ({{ config('tax.rate', 20) }}%
                                            included)</span> --}}
                                        {{-- <span id="order-tax" class="font-bold">{{ config('app.CURRENCY_SYMBOL') }}
                                            {{ number_format($tax, 2) }}</span> --}}
                                    </div>
                                    <div class="border-t-2 border-gray-100 pt-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xl font-bold text-gray-900">Total</span>
                                            <span id="summary-total"
                                                class="text-xl font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent">{{ config('app.CURRENCY_SYMBOL') }}
                                                {{ number_format($totals['final_total'] ?? ($totals['total'] ?? 0), 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="step" value="3"
                                    class="w-full px-6 py-5 bg-linear-to-r from-primary to-primary/80 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5">
                                    Place Secure Order
                                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </button>
                                <p class="text-xs text-center text-gray-500 mt-4 flex items-center justify-center">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Your payment information is secure
                                </p>
                            </div>

                        </div>
                    </div>
                @elseif($step == 3)
                    @php
                        $itemCount = $order ? $order->items->sum('quantity') : 0;
                    @endphp
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-6">
                            <div
                                class="bg-linear-to-br from-green-50 to-white rounded-3xl shadow-xl border-2 border-green-200 p-12 text-center">
                                <div
                                    class="w-24 h-24 mx-auto mb-6 rounded-full bg-linear-to-br from-green-500 to-green-400 flex items-center justify-center shadow-2xl animate-bounce-slow">
                                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-extrabold text-gray-900 mb-4">Order Successfully Placed!</h2>
                                <p class="text-xl text-gray-600 mb-2">Your order has been confirmed.</p>
                                <p class="text-lg text-gray-600">Order #<span
                                        class="font-bold text-primary">{{ isset($order) ? $order->order_number ?? '#' . $order->id : '#EZ-2024-001' }}</span>
                                </p>
                                <div class="bg-white rounded-2xl shadow-lg p-8 text-left mt-8 border border-gray-100">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Order Details
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                                        <div class="flex items-start">
                                            <span class="font-semibold text-gray-700 mr-2">Order Number:</span>
                                            <span
                                                class="text-gray-900">{{ isset($order) ? $order->order_number ?? '#' . $order->id : '#EZ-2024-001' }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="font-semibold text-gray-700 mr-2">Order Date:</span>
                                            <span
                                                class="text-gray-900">{{ isset($order) ? $order->created_at->format('M d, Y') : date('M d, Y') }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="font-semibold text-gray-700 mr-2">Payment Method:</span>
                                            <span
                                                class="text-gray-900">{{ isset($order) ? $order->payment_method ?? 'Credit Card' : 'Credit Card' }}</span>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="font-semibold text-gray-700 mr-2">Shipping Method:</span>
                                            <span
                                                class="text-gray-900">{{ isset($order) ? $order->shipping_method ?? 'Standard Shipping' : 'Standard Shipping' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mt-6 text-left">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-1 shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <h4 class="font-bold text-blue-900 mb-1">What's Next?</h4>
                                            <p class="text-sm text-blue-800">We've sent a confirmation email to your inbox.
                                                You can track your order status in your account dashboard.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('home') }}"
                                        class="flex-1 px-8 py-5 bg-linear-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        Continue Shopping
                                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </a>
                                    <a href="{{ isset($order) ? route('order-detail', ['order' => $order->id]) : '#' }}"
                                        class="flex-1 px-8 py-5 border-2 border-primary text-primary text-center font-bold rounded-2xl hover:bg-primary hover:text-white transition-all duration-300 transform hover:scale-105">
                                        View Order Details
                                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-green-500 to-green-400 mr-3"></span>
                                    Order Summary
                                </h2>
                                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                                    @foreach ($order ? $order->items : [] as $itm)
                                        <div class="flex gap-4 p-4 bg-gray-50 rounded-xl">
                                            <img src="{{ optional($itm->product)->cover_image ? asset('storage/' . optional($itm->product)->cover_image) : 'https://via.placeholder.com/80x80' }}"
                                                alt="{{ optional($itm->product)->name ?? 'Product' }}"
                                                class="w-20 h-20 rounded-lg object-cover shadow-sm">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-gray-900 text-sm mb-1 truncate">
                                                    {{ optional($itm->product)->name ?? 'Product' }}
                                                </h4>
                                                @if (optional($itm->variant)->variant_name)
                                                    <div class="text-gray-600 text-xs font-normal mb-1">
                                                        {{ $itm->variant->variant_name }}
                                                    </div>
                                                @endif
                                                <p class="text-xs text-gray-600">Qty: {{ $itm->quantity }}</p>
                                                @php
                                                    $itemOriginalPrice = ($itm->unit_price ?? 0) * $itm->quantity;
                                                    $itemDiscountedPrice = ($itm->final_price ?? ($itm->unit_price ?? 0)) * $itm->quantity;
                                                    $hasItemDiscount = ($itm->discount_amount ?? 0) > 0;
                                                @endphp
                                                @if($hasItemDiscount)
                                                    <div>
                                                        <span class="text-xs text-gray-400 line-through">
                                                            {{ config('app.CURRENCY_SYMBOL') }}
                                                            {{ number_format($itemOriginalPrice, 2) }}
                                                        </span>
                                                        <br />
                                                        <span class="summary-item-price text-sm font-bold text-red-600">
                                                            {{ config('app.CURRENCY_SYMBOL') }}
                                                            {{ number_format($itemDiscountedPrice, 2) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <p class="summary-item-price text-sm font-bold text-primary mt-1">
                                                        {{ config('app.CURRENCY_SYMBOL') }}
                                                        {{ number_format($itemDiscountedPrice, 2) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="space-y-4 mb-6">
                                    <div class="space-y-3">
                                        <span class="block text-sm font-semibold text-gray-700 mb-3">Price Details</span>
                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                            <span class="text-gray-600 font-medium">MRP</span>
                                            <span
                                                class="font-bold text-gray-900">{{ config('app.CURRENCY_SYMBOL') }}{{ number_format($order->subtotal ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    @if (($order->discount_amount ?? 0) > 0)
                                        <div
                                            class="flex justify-between text-green-600 text-base bg-green-50 px-4 py-3 rounded-xl">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                    <path fill-rule="evenodd"
                                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Discount on MRP
                                            </span>

                                            <span
                                                class="font-bold">-{{ config('app.CURRENCY_SYMBOL') }}{{ number_format($order->discount_amount ?? 0, 2) }}</span>
                                        </div>
                                    @endif
                                    @if (($order->coupon_discount_amount ?? 0) > 0)
                                        <div
                                            class="flex justify-between items-center text-purple-600 text-base bg-purple-50 px-4 py-3 rounded-xl">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                </svg>
                                                <div>
                                                    <span class="font-semibold">Coupon Discount</span>
                                                    @if ($order && $order->coupon_code)
                                                        <span
                                                            class="text-xs block text-purple-500">{{ $order->coupon_code }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span
                                                class="font-bold">-{{ config('app.CURRENCY_SYMBOL') }}{{ number_format($order->coupon_discount_amount ?? 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between text-gray-600 text-base">
                                        <span>Shipping</span>
                                        <span
                                            class="font-bold text-green-600">{{ ($order->shipping_cost ?? 0) > 0 ? config('app.CURRENCY_SYMBOL') . number_format($order->shipping_cost, 2) : 'FREE' }}</span>
                                    </div>
                                    <div class="border-t-2 border-gray-100 pt-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xl font-bold text-gray-900">Total</span>
                                            <span
                                                class="text-xl font-bold bg-linear-to-r from-green-500 to-green-400 bg-clip-text text-transparent">{{ config('app.CURRENCY_SYMBOL') }}{{ number_format($order->total ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if (isset($order) && ($order->payment_method ?? '') !== 'cod')
                                    <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                                        <div class="flex items-center text-green-700">
                                            <svg class="w-5 h-5 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-semibold">Payment Successful</span>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($order) && $order->shipping_address)
                                    <div class="mt-6 bg-blue-50 rounded-2xl p-6 border border-blue-200">
                                        <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Shipping Address
                                        </h3>
                                        <div class="bg-white rounded-xl p-4 space-y-2 text-sm text-gray-700">
                                            @php
                                                $addr = is_array($order->shipping_address)
                                                    ? $order->shipping_address
                                                    : json_decode($order->shipping_address, true);
                                            @endphp
                                            @if ($addr && isset($addr['name']))
                                                <p><span class="font-semibold text-gray-900">{{ $addr['name'] }}</span>
                                                </p>
                                                @if (isset($addr['street']))
                                                    <p>{{ $addr['street'] }}</p>
                                                @endif
                                                @if (isset($addr['city']) || isset($addr['state']) || isset($addr['postal_code']))
                                                    <p>{{ collect([$addr['city'] ?? null, $addr['state'] ?? null, $addr['postal_code'] ?? null])->filter()->implode(', ') }}
                                                    </p>
                                                @endif
                                                @if (isset($addr['country']))
                                                    <p>{{ $addr['country'] }}</p>
                                                @endif
                                            @else
                                                <p class="text-gray-600">No shipping address available</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-6 bg-amber-50 rounded-2xl p-6 border border-amber-200">
                                        <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                            </svg>
                                            Shipping Method
                                        </h3>
                                        <div class="bg-white rounded-xl p-4">
                                            <p class="text-lg font-semibold text-gray-900 capitalize">
                                                {{ $order->shipping_method ?? 'Standard Shipping' }}
                                                @if ($order->shipping_method === 'express')
                                                    <span class="text-amber-600 text-sm font-normal">(Expedited)</span>
                                                @else
                                                    <span class="text-green-600 text-sm font-normal">(Free)</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </form>
        </div>
    </div>

    {{-- Add Address Modal --}}
    <div id="addAddressModal" style="z-index: 99999;"
        class="hidden fixed inset-0 items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0"
            id="addAddressContent">
            <div
                class="flex items-center justify-between px-8 py-6 border-b border-gray-200 bg-linear-to-r from-primary/5 to-primary/10">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 p-2.5 rounded-xl">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Add Address</h3>
                </div>
                <button type="button" id="closeAddressModal"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="addAddressForm" method="POST" action="{{ url('/client/addresses') }}"
                class="max-h-[calc(90vh-180px)] overflow-y-auto">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id" id="address-id" value="">

                <div class="px-8 py-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Address Type</label>
                            <div class="relative">
                                <select id="modal_address_type" name="type" required
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white invalid:border-red-500 invalid:focus:ring-red-500/20">
                                    <option value="">Select Address Type</option>
                                    <option value="billing">Billing</option>
                                    <option value="shipping">Shipping</option>
                                </select>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Address Label <span
                                    class="text-gray-400 font-normal">(Optional)</span></label>
                            <input id="modal_address_label" name="address_label" placeholder="e.g., Home, Office"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                            <input id="modal_first_name" name="first_name" required placeholder="John"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                            <input id="modal_last_name" name="last_name" required placeholder="Doe"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Street Address *</label>
                        <input id="modal_street" name="street" required placeholder="123 Main Street"
                            class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                            <input id="modal_city" name="city" required placeholder="Casablanca"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">State / Region *</label>
                            <input id="modal_state" name="state" required placeholder="CA"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Postal Code *</label>
                            <input id="modal_postal_code" name="postal_code" required placeholder="20000"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Country *</label>
                            <input id="modal_country" name="country" required value="Morocco" placeholder="Morocco"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                            <input id="modal_phone" name="phone" type="tel" placeholder="+1 (555) 123-4567"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all invalid:border-red-500 invalid:focus:ring-red-500/20" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input id="modal_is_default" name="is_default" type="checkbox" value="1"
                                class="form-checkbox h-4 w-4 text-primary rounded mr-3">
                            <span class="text-sm font-medium text-gray-700">Set as default address</span>
                        </label>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-3 px-8 py-6 bg-gray-50 border-t border-gray-200 rounded-b-3xl">
                    <button type="button" id="cancelAddressModal"
                        class="px-6 py-3 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="submitAddressBtn"
                        class="px-6 py-3 rounded-xl bg-primary text-white font-semibold hover:shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/coupon.js') }}"></script>
    <script src="{{ asset('js/checkout-process.js') }}"></script>
    <script src="{{ asset('js/checkout-address-dropdown.js') }}"></script>

    <script>
        // Show Laravel validation errors on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any())
                const errorMessages = @json($errors->all());
                const message = errorMessages.length > 1 ?
                    `${errorMessages.length} validation errors found` :
                    errorMessages[0];

                showServerErrorBanner(message, errorMessages);
            @endif
        });
    </script>
@endpush
