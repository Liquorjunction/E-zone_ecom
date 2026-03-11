@extends('client.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout-process.css') }}">
@endpush

@section('content')

    @php
        $step = $step ?? (int) request()->get('step', 1);
        $cart = $cart ?? null;
        $itemCount = $cart ? $cart->items->sum('quantity') : 0;
        $sym = config('app.CURRENCY_SYMBOL', '$');

        // The applied coupon comes from the session (via $appliedCoupon passed from controller).
        // On step 3 there is no coupon in session anymore (it was cleared after order placed).
        $appliedCoupon = $appliedCoupon ?? null;
    @endphp

    {{-- Pass currency symbol to JavaScript --}}
    <script>
        window.env = {
            CURRENCY_SYMBOL: @json(config('app.CURRENCY_SYMBOL', '$')),
            CURRENCY_CODE: @json(config('app.CURRENCY_CODE', 'USD')),
        };
    </script>

    <div class="bg-linear-to-br from-gray-50 via-blue-50/30 to-white min-h-screen py-12">
        <div id="checkout-root" class="container mx-auto px-4 max-w-7xl" data-remove-url="{{ route('cart.remove') }}"
            data-update-url="{{ route('cart.update') }}" data-step="{{ $step }}"
            data-shipping-method="{{ $totals['shipping_method'] ?? 'standard' }}">
            @if ($errors->has('checkout'))
                <div id="checkout-error-banner"
                    class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-center shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-red-800">Payment Failed</p>
                        <p class="text-sm text-red-700">{{ $errors->first('checkout') }}</p>
                    </div>
                </div>
            @endif
            {{-- ══════════ PAGE HEADER ══════════ --}}
            <div class="mb-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-3 tracking-tight">
                            @if ($step == 1)
                                Shopping Cart
                            @elseif ($step == 2)
                                Checkout
                            @else
                                Order Placed
                            @endif
                        </h1>
                        <p class="text-gray-600 text-lg">
                            @if ($step == 1)
                                Review your items before proceeding
                            @elseif ($step == 2)
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
                                @elseif ($step == 2)
                                    Checkout
                                @else
                                    Placed
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════ STEP PROGRESS BAR ══════════ --}}
            <div class="mb-12 bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center justify-between relative max-w-3xl mx-auto">

                    <div class="absolute top-6 left-0 right-0 h-1 bg-gray-200 z-0" style="margin: 0 8%;"></div>

                    <div class="absolute top-6 left-0 h-1 z-0 transition-all duration-700 ease-in-out
                    @if ($step == 2) bg-linear-to-r from-primary to-primary/80 @endif
                    @if ($step == 3) bg-linear-to-r from-green-500 to-green-400 @endif"
                        style="@if ($step == 2) width: calc(50% - 8%); margin-left: 8%; @elseif($step == 3) width: calc(100% - 16%); margin-left: 8%; @endif">
                    </div>

                    @foreach ([['label' => 'Shopping Cart', 'num' => 1], ['label' => 'Checkout', 'num' => 2], ['label' => 'Order Complete', 'num' => 3]] as $s)
                        <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500">
                            <div
                                class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
                            @if ($step == $s['num']) bg-linear-to-br from-primary to-primary/80 text-white ring-4 ring-primary/20
                            @elseif ($step > $s['num']) bg-linear-to-br from-green-500 to-green-400 text-white
                            @elseif ($s['num'] == 3 && $step == 3) bg-linear-to-br from-green-500 to-green-400 text-white ring-4 ring-green-500/20
                            @else bg-gray-200 text-gray-600 @endif">
                                @if ($step > $s['num'] || ($step == 3 && $s['num'] == 3))
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    {{ $s['num'] }}
                                @endif
                            </div>
                            <span
                                class="mt-3 text-sm font-semibold transition-colors duration-300
                            @if ($step == $s['num']) text-primary
                            @elseif ($step > $s['num']) text-green-500
                            @else text-gray-500 @endif">
                                {{ $s['label'] }}
                            </span>
                        </div>
                    @endforeach

                </div>
            </div>

            <form method="POST" action="{{ route('checkout.process') }}">
                @csrf

                {{-- ══════════════════════════════════════════ --}}
                {{--  STEP 1 – SHOPPING CART                   --}}
                {{-- ══════════════════════════════════════════ --}}
                @if ($step == 1)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        {{-- Left – Cart items --}}
                        <div class="lg:col-span-2 space-y-6">
                            <div
                                class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl">
                                @php $itemsCollection = $itemsForView ?? ($cart?->items ?? collect()); @endphp

                                @if ($itemsCollection && $itemsCollection->count() > 0)
                                    <div class="space-y-6">
                                        @foreach ($itemsCollection as $item)
                                            @include('client.pages.checkout.partials._cart-item-row', [
                                                'item' => $item,
                                            ])
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

                        {{-- Right – Order summary --}}
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                                {{-- Header --}}
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
                                            class="text-sm font-bold bg-linear-to-r from-primary to-primary/80 bg-clip-text text-transparent">
                                            {{ $itemCount }} {{ $itemCount === 1 ? 'Item' : 'Items' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Mini item list --}}
                                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                                    @foreach ($itemsCollection ?? [] as $item)
                                        @include('client.pages.checkout.partials._summary-item-row', [
                                            'item' => $item,
                                            'showQtyControls' => true,
                                        ])
                                    @endforeach
                                </div>

                                {{-- Price rows --}}
                                @include('client.pages.checkout.partials._order-summary-rows', [
                                    'totals' => $totals,
                                    'idPrefix' => '',
                                ])

                                {{-- Proceed button --}}
                                <a href="{{ route('checkout', ['step' => 2]) }}"
                                    class="block w-full px-6 py-5 bg-linear-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5 mt-2">
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

                    {{-- ══════════════════════════════════════════ --}}
                    {{--  STEP 2 – CHECKOUT FORM                   --}}
                    {{-- ══════════════════════════════════════════ --}}
                @elseif ($step == 2)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        {{-- Left – Forms --}}
                        <div class="lg:col-span-2 space-y-6">

                            {{-- Contact Information --}}
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
                                        <input type="email" id="email" name="email"
                                            value="{{ Auth::check() ? Auth::user()->email : '' }}" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone
                                            Number *</label>
                                        <input type="tel" id="phone" name="phone"
                                            value="{{ Auth::check() ? Auth::user()->phone : '' }}" required
                                            pattern="[0-9\s\+\-\(\)]+"
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Shipping Address --}}
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

                                @if (Auth::check() && $addresses->count() > 0)
                                    <div class="mb-5">
                                        <div class="flex items-center justify-between mb-2">
                                            <label for="shippingAddressSelect"
                                                class="block text-sm font-semibold text-gray-700">
                                                Saved Addresses
                                                <span
                                                    class="text-xs font-normal text-gray-500 ml-1">({{ $addresses->count() }}
                                                    available)</span>
                                            </label>
                                            <!-- Removed '+ Use Different Address' button as per requirements -->
                                        </div>
                                        <div class="relative">
                                            {{-- ID must be "shippingAddressSelect" — checkout-address-dropdown.js depends on this exact ID --}}
                                            <select id="shippingAddressSelect" name="saved_address_id"
                                                class="block w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none bg-white pr-12 text-gray-900 font-medium">
                                                <option value="">-- Select a saved address --</option>
                                                @foreach ($addresses as $addr)
                                                    <option value="{{ $addr->id }}"
                                                        data-addr='@json($addr)'
                                                        {{ $addr->is_default ? 'selected' : '' }}>
                                                        {{ $addr->address_label ? '🏠 ' . $addr->address_label . ' - ' : '' }}{{ $addr->name }}
                                                        ({{ $addr->city }}, {{ $addr->state }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>

                                        {{-- Selected address display — populated by checkout-address-dropdown.js --}}
                                        <div id="selectedShippingDisplay"
                                            class="hidden mt-4 p-4 bg-linear-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl">
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-green-800 mb-1">Shipping To:</p>
                                                    <p id="displayShippingName" class="font-bold text-gray-900"></p>
                                                    <p id="displayShippingStreet" class="text-sm text-gray-700"></p>
                                                    <p id="displayShippingCity" class="text-sm text-gray-700"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif (Auth::check())
                                    <div
                                        class="text-center py-10 bg-linear-to-br from-gray-50 to-purple-50 rounded-2xl border-2 border-dashed border-gray-300 mb-5">
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

                                {{--
                                Manual address fields:
                                - Guests: always visible (no dropdown)
                                - Auth users WITH saved addresses: hidden by default, shown only when "Use Different Address" is clicked
                                - Auth users with NO saved addresses: always visible
                                JS toggles .hidden and removes `required` from inputs when hiding.
                            --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 {{ Auth::check() && $addresses->count() > 0 ? 'hidden' : '' }}"
                                    id="manualAddressFields">
                                    <div>
                                        <label for="firstName"
                                            class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                                        <input type="text" id="firstName" name="firstName" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div>
                                        <label for="lastName" class="block text-sm font-semibold text-gray-700 mb-2">Last
                                            Name *</label>
                                        <input type="text" id="lastName" name="lastName" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="address"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Street Address *</label>
                                        <input type="text" id="address" name="address" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City
                                            *</label>
                                        <input type="text" id="city" name="city" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State
                                            / Region *</label>
                                        <input type="text" id="state" name="state" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div>
                                        <label for="zip"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Postal Code *</label>
                                        <input type="text" id="zip" name="zip" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div>
                                        <label for="country"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Country *</label>
                                        <input type="text" id="country" name="country" value="Morocco" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                    </div>
                                </div>

                                @if (Auth::check())
                                    <label class="inline-flex items-center mt-5">
                                        <input type="checkbox" name="save_address" value="1"
                                            class="form-checkbox h-4 w-4 text-primary rounded mr-3">
                                        <span class="text-sm font-medium text-gray-700">Save this address for future
                                            orders</span>
                                    </label>
                                @endif
                            </div>

                            {{-- Billing Address --}}
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
                                    @if (Auth::check() && $billingAddresses->count() > 0)
                                        <div>
                                            <div class="flex items-center justify-between mb-4">
                                                <label for="billingAddressSelect"
                                                    class="block text-sm font-semibold text-gray-700">
                                                    Select Billing Address
                                                    <span
                                                        class="text-xs font-normal text-gray-500 ml-1">({{ $billingAddresses->count() }}
                                                        available)</span>
                                                </label>
                                                <button type="button" id="addBillingAddressBtn"
                                                    class="px-4 py-2.5 bg-linear-to-r from-primary to-primary/90 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add New
                                                </button>
                                            </div>
                                            <div class="relative">
                                                <select id="billingAddressSelect" name="saved_billing_address_id"
                                                    class="block w-full px-4 py-4 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none bg-white pr-12 text-gray-900 font-medium">
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

                                            {{-- Selected billing address display — populated by checkout-address-dropdown.js --}}
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
                                    @elseif (Auth::check())
                                        <div
                                            class="text-center py-10 bg-linear-to-br from-gray-50 to-purple-50 rounded-2xl border-2 border-dashed border-gray-300">
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
                                    @else
                                        {{-- Guest billing manual fields --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">First
                                                    Name</label>
                                                <input type="text" id="billing_firstName" name="billing_firstName"
                                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Last
                                                    Name</label>
                                                <input type="text" id="billing_lastName" name="billing_lastName"
                                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Street
                                                    Address</label>
                                                <input type="text" id="billing_address_line"
                                                    name="billing_address_line"
                                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                                                <input type="text" id="billing_city" name="billing_city"
                                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">State</label>
                                                <input type="text" id="billing_state" name="billing_state"
                                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Postal
                                                    Code</label>
                                                <input type="text" id="billing_zip" name="billing_zip"
                                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-gray-700 mb-2">Country</label>
                                                <input type="text" id="billing_country" name="billing_country"
                                                    value="Morocco"
                                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            </div>
                                        </div>
                                    @endif

                                    {{--
                                    Hidden fields are ONLY rendered for logged-in users (who use the dropdown).
                                    Guests use the visible fields above directly (no hidden duplicates).
                                    checkout-address-dropdown.js populates these by ID on dropdown change.
                                    Names match what OrderService expects: billing_firstName, billing_address_line, etc.
                                --}}
                                    @if (Auth::check())
                                        <input type="hidden" id="billing_firstName" name="billing_firstName"
                                            value="">
                                        <input type="hidden" id="billing_lastName" name="billing_lastName"
                                            value="">
                                        <input type="hidden" id="billing_address_line" name="billing_address_line"
                                            value="">
                                        <input type="hidden" id="billing_city" name="billing_city" value="">
                                        <input type="hidden" id="billing_state" name="billing_state" value="">
                                        <input type="hidden" id="billing_zip" name="billing_zip" value="">
                                        <input type="hidden" id="billing_country" name="billing_country"
                                            value="">
                                        <input type="hidden" id="billing_phone" name="billing_phone" value="">
                                    @endif
                                </div>
                            </div>

                            {{-- Shipping Method --}}
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
                                            <span class="block text-sm text-gray-600">5–7 business days</span>
                                        </span>
                                        <span class="font-bold text-green-600">FREE</span>
                                    </label>
                                    <label
                                        class="flex items-center p-5 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-primary hover:bg-primary/5">
                                        <input type="radio" name="shipping_method" value="express"
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 flex-1">
                                            <span class="block font-semibold text-gray-900">Express Shipping</span>
                                            <span class="block text-sm text-gray-600">2–3 business days</span>
                                        </span>
                                        <span class="font-bold text-gray-900">{{ $sym }} 12.99</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Payment Method --}}
                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                    Payment Method
                                </h2>
                                <div class="space-y-4 mb-6">
                                    <label
                                        class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                                        <input type="radio" name="payment_method" value="card"
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 font-semibold text-gray-900">NAPS</span>
                                    </label>
                                    <label
                                        class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                                        <input type="radio" name="payment_method" value="cod"
                                            class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                                        <span class="ml-4 font-semibold text-gray-900">Cash on Delivery</span>
                                    </label>
                                </div>

                                {{-- Card details (shown only when card is selected) --}}
                                <div id="cardDetails" class="space-y-5">
                                    <div>
                                        <label for="cardNumber"
                                            class="block text-sm font-semibold text-gray-700 mb-2">Card Number *</label>
                                        <input type="text" id="cardNumber" name="cardNumber"
                                            placeholder="1234 5678 9012 3456" required
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-5">
                                        <div>
                                            <label for="expiry"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date
                                                *</label>
                                            <input type="text" id="expiry" name="expiry" placeholder="MM/YY"
                                                required pattern="\d{2}/\d{2}"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            <span
                                                class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                        </div>
                                        <div>
                                            <label for="cvv"
                                                class="block text-sm font-semibold text-gray-700 mb-2">CVV *</label>
                                            <input type="text" id="cvv" name="cvv" placeholder="123"
                                                maxlength="3" required pattern="[0-9]{3}"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                                            <span
                                                class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- end left col --}}

                        {{-- Right – Coupon + Order Summary --}}
                        <div class="lg:col-span-1">

                            {{-- Coupon widget – reads applied coupon from session --}}
                            @include('client.pages.checkout.partials._coupon-widget', [
                                'appliedCoupon' => $appliedCoupon,
                                'idSuffix' => '-step2',
                            ])

                            {{-- Order Summary card --}}
                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-primary to-primary/60 mr-3"></span>
                                    Order Summary
                                </h2>

                                {{-- Mini item list --}}
                                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                                    @foreach ($itemsForView ?? [] as $item)
                                        @include('client.pages.checkout.partials._summary-item-row', [
                                            'item' => $item,
                                            'showQtyControls' => true,
                                        ])
                                    @endforeach
                                </div>

                                {{-- Price rows (step 2 sidebar uses -step2 prefix to avoid duplicate IDs) --}}
                                @include('client.pages.checkout.partials._order-summary-rows', [
                                    'totals' => $totals,
                                    'idPrefix' => '-step2',
                                ])

                                {{-- Place order button --}}
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

                        </div>{{-- end right col --}}

                    </div>

                    {{-- ══════════════════════════════════════════ --}}
                    {{--  STEP 3 – ORDER CONFIRMED                 --}}
                    {{-- ══════════════════════════════════════════ --}}
                @elseif ($step == 3)
                    @php $itemCount = $order ? $order->items->sum('quantity') : 0; @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        {{-- Left – Confirmation + shipping info --}}
                        <div class="lg:col-span-2 space-y-6">

                            {{-- Success banner --}}
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
                                <p class="text-xl text-gray-600 mb-2">Your order has been successfully placed and is now
                                    being processed.</p>
                                <p class="text-lg text-gray-600">Order #<span class="font-bold text-primary">
                                        {{ isset($order) ? $order->order_number ?? '#' . $order->id : '#—' }}
                                    </span></p>

                                <div class="bg-white rounded-2xl shadow-lg p-8 text-left mt-8 border border-gray-100">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Order Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                                        <div><span class="font-semibold text-gray-700 mr-2">Order Number:</span>
                                            {{ $order->order_number ?? '—' }}</div>
                                        <div><span class="font-semibold text-gray-700 mr-2">Order Date:</span>
                                            {{ $order?->created_at?->format('M d, Y') ?? date('M d, Y') }}</div>
                                        <div><span class="font-semibold text-gray-700 mr-2">Payment Method:</span>
                                            {{ $order->payment_method ?? '—' }}</div>
                                        <div><span class="font-semibold text-gray-700 mr-2">Shipping Method:</span>
                                            {{ $order->shipping_method ?? 'Standard Shipping' }}</div>
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

                            {{-- Action buttons --}}
                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a href="{{ route('home') }}"
                                        class="flex-1 px-8 py-5 bg-linear-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                        Continue Shopping
                                    </a>
                                    <a href="{{ isset($order) ? route('order-detail', ['order' => $order->id]) : '#' }}"
                                        class="flex-1 px-8 py-5 border-2 border-primary text-primary text-center font-bold rounded-2xl hover:bg-primary hover:text-white transition-all duration-300 transform hover:scale-105">
                                        View Order Details
                                    </a>
                                </div>
                            </div>

                            {{-- Shipping address display --}}
                            @if (isset($order) && $order->shipping_address)
                                @php
                                    $addr = is_array($order->shipping_address)
                                        ? $order->shipping_address
                                        : json_decode($order->shipping_address, true);
                                @endphp
                                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
                                    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200 mb-5">
                                        <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Shipping Address
                                        </h3>
                                        <div class="bg-white rounded-xl p-4 space-y-1 text-sm text-gray-700">
                                            @if ($addr && isset($addr['name']))
                                                <p class="font-semibold text-gray-900">{{ $addr['name'] }}</p>
                                                @if (isset($addr['street']))
                                                    <p>{{ $addr['street'] }}</p>
                                                @endif
                                                <p>{{ collect([$addr['city'] ?? null, $addr['state'] ?? null, $addr['postal_code'] ?? null])->filter()->implode(', ') }}
                                                </p>
                                                @if (isset($addr['country']))
                                                    <p>{{ $addr['country'] }}</p>
                                                @endif
                                            @else
                                                <p class="text-gray-600">No shipping address available</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-200">
                                        <h3 class="font-bold text-gray-900 text-lg mb-3">Shipping Method</h3>
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

                        </div>{{-- end left col --}}

                        {{-- Right – Order summary (read-only) --}}
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <span
                                        class="w-1.5 h-8 rounded-full bg-linear-to-b from-green-500 to-green-400 mr-3"></span>
                                    Order Summary
                                </h2>

                                {{-- Order items (read-only, no qty controls) --}}
                                <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                                    @foreach ($order ? $order->items : [] as $item)
                                        @include('client.pages.checkout.partials._summary-item-row', [
                                            'item' => $item,
                                            'showQtyControls' => false,
                                        ])
                                    @endforeach
                                </div>

                                {{-- Price breakdown from saved order --}}
                                <div class="space-y-4 mb-6">
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                        <span class="text-gray-600 font-medium">MRP</span>
                                        <span
                                            class="font-bold text-gray-900">{{ $sym }}{{ number_format($order->subtotal ?? 0, 2) }}</span>
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
                                                class="font-bold">-{{ $sym }}{{ number_format($order->discount_amount, 2) }}</span>
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
                                                    @if ($order->coupon_code)
                                                        <span
                                                            class="text-xs block text-purple-500">{{ $order->coupon_code }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span
                                                class="font-bold">-{{ $sym }}{{ number_format($order->coupon_discount_amount, 2) }}</span>
                                        </div>
                                    @endif

                                    <div class="flex justify-between text-gray-600 text-base">
                                        <span>Shipping</span>
                                        <span class="font-bold text-green-600">
                                            {{ ($order->shipping_cost ?? 0) > 0 ? $sym . number_format($order->shipping_cost, 2) : 'FREE' }}
                                        </span>
                                    </div>

                                    <div class="border-t-2 border-gray-100 pt-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xl font-bold text-gray-900">Total</span>
                                            <span
                                                class="text-xl font-bold bg-linear-to-r from-green-500 to-green-400 bg-clip-text text-transparent">
                                                {{ $sym }}{{ number_format($order->total ?? 0, 2) }}
                                            </span>
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

                            </div>
                        </div>

                    </div>
                @endif

            </form>
        </div>
    </div>

    {{-- ══════════ ADD ADDRESS MODAL ══════════ --}}
    <div id="addAddressModal" style="z-index: 99999;"
        class="hidden fixed inset-0 items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div id="addAddressContent"
            class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0">

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
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white">
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
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                            <input id="modal_first_name" name="first_name" required placeholder="John"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                            <input id="modal_last_name" name="last_name" required placeholder="Doe"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Street Address *</label>
                        <input id="modal_street" name="street" required placeholder="123 Main Street"
                            class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                            <input id="modal_city" name="city" required placeholder="Casablanca"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">State / Region *</label>
                            <input id="modal_state" name="state" required placeholder="CA"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Postal Code *</label>
                            <input id="modal_postal_code" name="postal_code" required placeholder="20000"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Country *</label>
                            <input id="modal_country" name="country" required value="Morocco"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                            <input id="modal_phone" name="phone" type="tel" placeholder="+1 (555) 123-4567"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
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
        document.addEventListener('DOMContentLoaded', function() {
            window.__checkoutSubmitting = false;

            // Show Laravel validation errors as field-level messages only (no top banner)
            @if ($errors->any())
                const fieldErrors = @json($errors->getMessages());

                const fieldAliasMap = {
                    saved_address_id: 'shippingAddressSelect',
                    saved_billing_address_id: 'billingAddressSelect',
                    billing_address_id: 'billingAddressSelect',
                };

                const hasBillingErrors = Object.keys(fieldErrors).some((key) =>
                    key.startsWith('billing_') || key.startsWith('billing_address')
                );

                if (hasBillingErrors) {
                    const billingSameCheckbox = document.getElementById('billingSameAsShipping');
                    if (billingSameCheckbox && billingSameCheckbox.checked) {
                        billingSameCheckbox.checked = false;
                        billingSameCheckbox.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                }

                function setFieldError(field, message) {
                    if (!field || !message) return;

                    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20',
                        'bg-red-50');

                    const wrapper = field.closest('div');
                    let msgEl = wrapper ? wrapper.querySelector('.error-message') : null;
                    if (!msgEl && wrapper) {
                        msgEl = document.createElement('span');
                        msgEl.className = 'error-message text-red-500 text-sm mt-1 font-medium';
                        wrapper.appendChild(msgEl);
                    }
                    if (msgEl) {
                        msgEl.textContent = message;
                        msgEl.classList.remove('hidden');
                    }
                }

                function findFieldByKey(key) {
                    const mapped = fieldAliasMap[key] || key;
                    return document.getElementById(mapped) ||
                        document.querySelector(`[name="${mapped}"]`) ||
                        document.querySelector(`[name="${key}"]`);
                }

                let firstErroredField = null;

                Object.entries(fieldErrors).forEach(([key, messages]) => {
                    const msg = Array.isArray(messages) ? messages[0] : messages;
                    const field = findFieldByKey(key);
                    if (!field) return;

                    setFieldError(field, msg);
                    if (!firstErroredField) firstErroredField = field;
                });

                if (firstErroredField) {
                    firstErroredField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstErroredField.focus({
                        preventScroll: true
                    });
                }
            @endif

            // Warn user if navigating away with a coupon applied
            let allowNavigation = false;

            function isCouponApplied() {
                return !!document.querySelector('[id^="applied-coupon-display"], [id^="remove-coupon-btn"]');
            }

            window.addEventListener('beforeunload', function(e) {
                if (isCouponApplied() && !allowNavigation) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (!(form instanceof HTMLFormElement)) return;
                if (form.action === @json(route('checkout.process'))) {
                    allowNavigation = true;
                    window.__checkoutSubmitting = true;
                }
            }, true);

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link || !isCouponApplied() || allowNavigation) return;
                const href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('#') || href === window.location.pathname)
                    return;

                e.preventDefault();
                if (typeof showUniversalPopup === 'function') {
                    showUniversalPopup(
                        'You have a coupon applied. Leave without completing your purchase?',
                        'error', 0, {
                            confirmation: true,
                            confirmText: 'Yes, Leave',
                            cancelText: 'No, Stay',
                            onConfirm: () => {
                                allowNavigation = true;
                                window.location.href = href;
                            }
                        }
                    );
                }
            }, true);
        });
    </script>
@endpush
