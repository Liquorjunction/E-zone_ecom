@extends('client.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout-process.css') }}">
@endpush

@section('content')
    @php
        $step          = $step ?? (int) request()->get('step', 1);
        $cart          = $cart ?? null;
        $itemCount     = $cart ? $cart->items->sum('quantity') : 0;
        $sym           = config('app.CURRENCY_SYMBOL', '$');
        $appliedCoupon = $appliedCoupon ?? null;
    @endphp

    {{-- ── JS globals ────────────────────────────────────────── --}}
    <script>
        window.env = {
            CURRENCY_SYMBOL : @json(config('app.CURRENCY_SYMBOL', '$')),
            CURRENCY_CODE   : @json(config('app.CURRENCY_CODE',   'USD')),
        };
        window.__isUserLoggedIn = @json(Auth::check());
        window.__guestEmailVerified = @json(session('guest_email_verified', ''));
        window.__ga4Checkout = @json($ga4Ecommerce ?? null);
        window.__ga4Purchase = @json($ga4Purchase ?? null);
        window.__checkoutRoutes = {
            show    : @json(route('checkout')),
            process : @json(route('checkout.process')),
            cart    : @json(route('cart')),
            addresses: @json(url('/client/addresses')),
            addressSectionShipping: @json(route('checkout.address-section', ['type' => 'shipping'])),
            addressSectionBilling : @json(route('checkout.address-section', ['type' => 'billing'])),
            sendOtp : @json(route('checkout.guest.send-otp')),
            verifyOtp : @json(route('checkout.guest.verify-otp')),
            checkExistingUser : @json(route('checkout.check-existing-user')),
            inlineLogin : @json(route('checkout.inline-login')),
        };
        window.__recaptchaSiteKey = @json(config('services.recaptcha.site_key'));
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoaded&render=explicit" async defer></script>

    <div class="bg-linear-to-br from-gray-50 via-blue-50/30 to-white min-h-screen py-12">
        <div id="checkout-root"
            class="container mx-auto px-4 max-w-7xl"
            data-remove-url="{{ route('cart.remove') }}"
            data-update-url="{{ route('cart.update') }}"
            data-login-url="{{ route('login') }}"
            data-step="{{ $step }}"
            data-shipping-method="{{ $totals['shipping_method'] ?? 'standard' }}">

            {{-- ══════════ PAGE HEADER (updated by JS on step change) ══════════ --}}
            <div class="mb-10" id="checkout-header">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 id="checkout-title"
                            class="text-4xl md:text-5xl font-extrabold mb-3 tracking-tight
                            @if ($step == 3 && (!isset($order) || $errors->has('checkout'))) text-red-700 @else text-gray-900 @endif">
                            @if ($step == 1)     {{ statictranslations('Shopping Cart') }}
                            @elseif ($step == 2) {{ statictranslations('Checkout') }}
                            @elseif ($step == 3 && (!isset($order) || $errors->has('checkout'))) {{ statictranslations('Order Failed') }}
                            @else                {{ statictranslations('Order Placed') }}
                            @endif
                        </h1>
                        <p id="checkout-subtitle" class="text-lg
                            @if ($step == 3 && (!isset($order) || $errors->has('checkout'))) text-red-600 @else text-gray-600 @endif">
                            @if ($step == 1)     {{ statictranslations('Review your items before proceeding') }}
                            @elseif ($step == 2) {{ statictranslations('Complete your purchase securely') }}
                            @elseif ($step == 3 && (!isset($order) || $errors->has('checkout'))) {{ statictranslations('Your payment could not be processed.') }}
                            @else                {{ statictranslations('Thank you for your order!') }}
                            @endif
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="flex items-center gap-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm" id="checkout-breadcrumb">
                            <a href="#" class="hover:text-primary transition-colors">{{ statictranslations('Home') }}</a>
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            @if ($step > 1)
                                <a href="#" data-spa-step="1" class="hover:text-primary transition-colors cursor-pointer">{{ statictranslations('Cart') }}</a>
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            @endif
                            @if ($step == 3)
                                <span class="hover:text-primary transition-colors">{{ statictranslations('Checkout') }}</span>
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            @endif
                            <span id="breadcrumb-current" class="text-primary font-semibold">
                                @if ($step == 1) {{ statictranslations('Cart') }}
                                @elseif ($step == 2) {{ statictranslations('Checkout') }}
                                @else {{ statictranslations('Order Placed') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════ STEP PROGRESS BAR (updated by JS) ══════════ --}}
            <div class="mb-12 bg-white rounded-3xl shadow-lg border border-gray-100 p-8" id="checkout-progress">
                <div class="flex items-center justify-between relative max-w-3xl mx-auto">
                    <div class="absolute top-6 start-0 end-0 h-1 bg-gray-200 z-0" style="margin:0 8%"></div>
                    <div id="progress-bar-fill"
                        class="absolute top-6 start-0 h-1 z-0 transition-all duration-700 ease-in-out
                        @if ($step == 2) bg-linear-to-r rtl:bg-linear-to-l from-primary to-primary/80
                        @elseif ($step == 3 && (!isset($order) || $errors->has('checkout'))) bg-linear-to-r rtl:bg-linear-to-l from-red-500 to-red-400
                        @elseif ($step == 3) bg-linear-to-r rtl:bg-linear-to-l from-green-500 to-green-400
                        @endif"
                        style="@if ($step == 2) width:calc(50% - 8%);margin-inline-start:8%; @elseif ($step == 3) width:calc(100% - 16%);margin-inline-start:8%; @endif">
                    </div>
                    @foreach ([['label' => 'Shopping Cart','num' => 1],['label' => 'Checkout','num' => 2],['label' => 'Order Complete','num' => 3]] as $s)
                        <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500" data-step-indicator="{{ $s['num'] }}">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
                                @if ($step == $s['num'] && !($step == 3 && $s['num'] == 3 && (!isset($order) || $errors->has('checkout')))) bg-linear-to-br from-primary to-primary/80 text-white ring-4 ring-primary/20
                                @elseif ($step > $s['num']) bg-linear-to-br from-green-500 to-green-400 text-white
                                @elseif ($step == 3 && $s['num'] == 3 && (!isset($order) || $errors->has('checkout'))) bg-linear-to-br from-red-500 to-red-400 text-white ring-4 ring-red-500/20
                                @else bg-gray-200 text-gray-600 @endif"
                                data-step-circle="{{ $s['num'] }}">
                                @if ($step > $s['num'] || ($step == 3 && $s['num'] == 3 && isset($order) && !$errors->has('checkout')))
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @elseif ($step == 3 && $s['num'] == 3 && (!isset($order) || $errors->has('checkout')))
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                @else
                                    {{ $s['num'] }}
                                @endif
                            </div>
                            <span class="mt-3 text-sm font-semibold transition-colors duration-300
                                @if ($step == $s['num'] && !($step == 3 && $s['num'] == 3 && (!isset($order) || $errors->has('checkout')))) text-primary
                                @elseif ($step > $s['num']) text-green-500
                                @elseif ($step == 3 && $s['num'] == 3 && (!isset($order) || $errors->has('checkout'))) text-red-500
                                @else text-gray-500 @endif"
                                data-step-label="{{ $s['num'] }}">
                                @if ($step == 3 && $s['num'] == 3 && (!isset($order) || $errors->has('checkout')))
                                    {{ statictranslations('Order Failed') }}
                                @else
                                    {{ statictranslations($s['label']) }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════ MAIN STEP CONTENT — swapped by goToStep() ══════════ --}}
            <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}">
                @csrf
                <div id="checkout-step-content">
                    @include('client.pages.checkout.partials._step-content', [
                        'step'             => $step,
                        'cart'             => $cart,
                        'itemsForView'     => $itemsForView,
                        'addresses'        => $addresses,
                        'billingAddresses' => $billingAddresses,
                        'cities'           => $cities,
                        'order'            => $order ?? null,
                        'totals'           => $totals,
                        'appliedCoupon'    => $appliedCoupon,
                        'errors'           => $errors,
                    ])
                </div>
            </form>

        </div>
    </div>

    {{-- ══════════ ADD ADDRESS MODAL ══════════ --}}
    <div id="addAddressModal" style="z-index:99999"
        class="hidden fixed inset-0 items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div id="addAddressContent"
            class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0">

            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-200 bg-linear-to-r from-primary/5 to-primary/10">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 p-2.5 rounded-xl">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ statictranslations('Add Address') }}</h3>
                </div>
                <button type="button" id="closeAddressModal"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="addAddressForm" method="POST" action="{{ url('/client/addresses') }}"
                class="max-h-[calc(90vh-180px)] overflow-y-auto">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id"      id="address-id"  value="">

                <div class="px-8 py-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Address Type') }}</label>
                            <div class="relative">
                                <select id="modal_address_type" name="type" required
                                    class="block w-full ps-4 pe-10 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white">
                                    <option value="">{{ statictranslations('Select Address Type') }}</option>
                                    <option value="billing">{{ statictranslations('Billing') }}</option>
                                    <option value="shipping">{{ statictranslations('Shipping') }}</option>
                                </select>
                                <svg class="absolute end-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ statictranslations('Address Label') }}
                                <span class="text-gray-400 font-normal">{{ statictranslations('(Optional)') }}</span>
                            </label>
                            <input id="modal_address_label" name="address_label" placeholder="{{ statictranslations('e.g., Home, Office') }}"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Full Name') }} *</label>
                            <input id="modal_name" name="name" required placeholder="{{ statictranslations('John Doe') }}"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"/>
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Phone') }}</label>
                            <input id="modal_phone" name="phone" type="tel"
                                inputmode="tel"
                                autocomplete="tel"
                                pattern="[0-9\s\+\-\(\)]+"
                                placeholder="{{ statictranslations('e.g. +212 612345678') }}"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Street Address') }} *</label>
                        <input id="modal_street" name="street" required placeholder="{{ statictranslations('123 Main Street') }}"
                            class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"/>
                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('City') }} *</label>
                            <div class="relative">
                                <select id="modal_city_id" name="city_id" required
                                    class="block w-full ps-4 pe-10 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none bg-white">
                                    <option value="">{{ statictranslations('Select city') }}</option>
                                    @foreach ($cities ?? collect() as $city)
                                        <option value="{{ $city->id }}" data-country="{{ $city->getTranslation('country_name', app()->getLocale()) }}">
                                            {{ $city->getTranslation('city_name', app()->getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="absolute end-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ statictranslations('Postal Code') }}
                                <span class="text-gray-400 font-normal">{{ statictranslations('Optional') }}</span>
                            </label>
                            <input id="modal_postal_code" name="postal_code" placeholder="20000"
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" maxlength="10" inputmode="numeric" pattern="\d*"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Country') }} *</label>
                        <input id="modal_country" name="country" required value="Morocco"
                            class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"/>
                        <span class="error-message text-red-500 text-sm mt-1 font-medium hidden"></span>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input id="modal_is_default" name="is_default" type="checkbox" value="1"
                                class="form-checkbox h-4 w-4 text-primary rounded me-3">
                            <span class="text-sm font-medium text-gray-700">{{ statictranslations('Set as default address') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-8 py-6 bg-gray-50 border-t border-gray-200 rounded-b-3xl">
                    <button type="button" id="cancelAddressModal"
                        class="px-6 py-3 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all">
                        {{ statictranslations('Cancel') }}
                    </button>
                    <button type="submit" id="submitAddressBtn"
                        class="px-6 py-3 rounded-xl bg-primary text-white font-semibold hover:shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ statictranslations('Save Address') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════ EXISTING ACCOUNT DETECTED MODAL (SOW #11) ══════════ --}}
    <div id="existingAccountModal" style="z-index:99999"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-gray-100 relative">
            <button type="button" id="closeExistingAccountModal" class="absolute top-5 end-5 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ statictranslations('Account Already Registered') }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed" id="existingAccountMessage">
                    {{ statictranslations('You are already registered. Please log in to your account.') }}
                </p>
            </div>

            {{-- Inline Login Form --}}
            <form id="inlineLoginForm" class="space-y-4" autocomplete="on">
                <div class="space-y-1">
                    <label for="inlineLoginEmail" class="block text-xs font-semibold text-gray-700">{{ statictranslations('Email Address') }}</label>
                    <input type="email" id="inlineLoginEmail" name="email" autocomplete="username" readonly
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-700 text-sm">
                </div>
                <div id="inlinePasswordGroup" class="space-y-2 mt-3">
                    <label for="inlineLoginPassword" class="block text-xs font-semibold text-gray-700">{{ statictranslations('Password') }} *</label>
                    <input type="password" id="inlineLoginPassword" name="inline_login_password" autocomplete="current-password" placeholder="••••••••"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm">
                    <p id="inlineLoginError" class="text-xs text-red-500 hidden"></p>
                </div>

                <div class="space-y-3 pt-2">
                    <button type="button" id="inlineLoginActionBtn"
                        class="w-full py-3.5 bg-primary text-white font-semibold text-sm rounded-xl hover:bg-primary/90 transition-all shadow-md">
                        {{ statictranslations('Log In to Account') }}
                    </button>
                    <button type="button" id="useDifferentEmailModalBtn"
                        class="w-full py-3 bg-gray-100 text-gray-700 font-semibold text-sm rounded-xl hover:bg-gray-200 transition-colors">
                        {{ statictranslations('Use Different Email') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/coupon.js') }}"></script>
    <script src="{{ asset('js/checkout-process.js') }}"></script>
@endpush
