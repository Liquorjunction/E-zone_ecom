@extends('client.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout-process.css') }}">
@endpush

@section('content')
@php
  $step = $step ?? (int) request()->get('step', 1);
  $cart = $cart ?? null;
@endphp

<div class="bg-gradient-to-br from-gray-50 via-blue-50/30 to-white min-h-screen py-12">
  <div id="checkout-root" class="container mx-auto px-4 max-w-7xl" data-remove-url="{{ route('cart.remove') }}" data-update-url="{{ route('cart.update') }}">

    <div class="mb-10">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
          <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-3 tracking-tight">
            @if($step == 1)
              Shopping Cart
            @elseif($step == 2)
              Checkout
            @else
              Order Confirmed
            @endif
          </h1>
          <p class="text-gray-600 text-lg">
            @if($step == 1)
              Review your items before proceeding
            @elseif($step == 2)
              Complete your purchase securely
            @else
              Thank you for your order!
            @endif
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm">
            <a href="#" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            @if($step > 1)
              <a href="#" class="hover:text-primary transition-colors">Cart</a>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            @endif
            @if($step == 3)
              <a href="#" class="hover:text-primary transition-colors">Checkout</a>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            @endif
            <span class="text-primary font-semibold">
              @if($step == 1)
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
          @if($step == 1) w-0 @endif
          @if($step == 2) bg-gradient-to-r from-primary to-primary/80 @endif
          @if($step == 3) bg-gradient-to-r from-green-500 to-green-400 @endif
        " style="@if($step == 2) width: calc(50% - 8%); margin-left: 8%; @elseif($step == 3) width: calc(100% - 16%); margin-left: 8%; @endif"></div>

        <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500">
          <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
            @if($step == 1) bg-gradient-to-br from-primary to-primary/80 text-white ring-4 ring-primary/20 @endif
            @if($step > 1) bg-gradient-to-br from-green-500 to-green-400 text-white @endif
          ">
            @if($step > 1)
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            @else
              1
            @endif
          </div>
          <span class="mt-3 text-sm font-semibold transition-colors duration-300
            @if($step == 1) text-primary @endif
            @if($step > 1) text-green-500 @endif
            @if($step < 1) text-gray-500 @endif
          ">Shopping Cart</span>
        </div>

        <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500">
          <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
            @if($step == 2) bg-gradient-to-br from-primary to-primary/80 text-white ring-4 ring-primary/20 @endif
            @if($step < 2) bg-gray-200 text-gray-600 @endif
            @if($step > 2) bg-gradient-to-br from-green-500 to-green-400 text-white @endif
          ">
            @if($step > 2)
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            @else
              2
            @endif
          </div>
          <span class="mt-3 text-sm font-semibold transition-colors duration-300
            @if($step == 2) text-primary @endif
            @if($step < 2) text-gray-500 @endif
            @if($step > 2) text-green-500 @endif
          ">Checkout</span>
        </div>

        <div class="flex flex-col items-center z-10 flex-1 transition-all duration-500">
          <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-lg shadow-xl transition-all duration-500 transform hover:scale-110
            @if($step == 3) bg-gradient-to-br from-green-500 to-green-400 text-white ring-4 ring-green-500/20 @endif
            @if($step < 3) bg-gray-200 text-gray-600 @endif
          ">
            @if($step == 3)
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            @else
              3
            @endif
          </div>
          <span class="mt-3 text-sm font-semibold transition-colors duration-300
            @if($step == 3) text-green-500 @endif
            @if($step < 3) text-gray-500 @endif
          ">Order Complete</span>
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('checkout.process') }}">
      @csrf

      @if($step == 1)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl">
              @if($cart && $cart->items->count() > 0)
                <div class="space-y-6">
                  @foreach($cart->items as $item)
                    <div class="cart-item-row flex flex-col sm:flex-row gap-6 p-6 bg-gradient-to-r from-gray-50 to-white rounded-2xl border border-gray-100 transition-all duration-300 hover:shadow-md" data-item-id="{{ $item->id }}">
                      <div class="shrink-0">
                        <img src="{{ optional($item->product)->cover_image ? asset('storage/' . optional($item->product)->cover_image) : 'https://via.placeholder.com/150x150?text=Product' }}" alt="{{ optional($item->product)->name ?? 'Product' }}" class="w-full sm:w-36 h-36 object-cover rounded-2xl shadow-md">
                      </div>
                      <div class="flex-1">
                        <div class="flex justify-between items-start mb-3">
                          <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ optional($item->variant)->variant_name ?? optional($item->product)->name ?? 'Product' }}</h3>
                            <span class="inline-block text-xs font-semibold px-3 py-1.5 rounded-full bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700">{{ optional($item->product)->brand->name ?? '' }}</span>
                          </div>

                            {{-- The Remove Button (AJAX) --}}
                            <button
                                type="button"
                                class="remove-item-btn flex items-center text-red-500 hover:text-red-700 transition-colors duration-200"
                                data-item-id="{{ $item->id }}"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                                Remove
                            </button>

                        </div>
                        <div class="space-y-2 text-sm text-gray-600 mb-5">
                          <p class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            <span class="font-medium">In Stock</span>
                          </p>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                          <div class="flex items-center">
                            <span class="text-sm font-semibold text-gray-700 mr-4">Quantity:</span>
                            <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
                              <button type="button" class="qty-decrease px-5 py-3 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold transition-all duration-300" data-item-id="{{ $item->id }}">−</button>
                              <input type="number" value="{{ $item->quantity }}" min="1" class="cart-quantity-input w-16 text-center border-0 focus:outline-none font-bold text-gray-900 bg-white" data-item-id="{{ $item->id }}">
                              <button type="button" class="qty-increase px-5 py-3 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold transition-all duration-300" data-item-id="{{ $item->id }}">+</button>
                            </div>
                          </div>
                          <div class="text-right">
                            <div class="text-sm text-gray-500 mb-1">Unit: ${{ number_format($item->unit_price, 2) }}</div>

                            @if($item->hasDiscount())
                              @php
                                $discountInfo = $item->getDiscountInfo();
                                $lineTotal = $item->getLineTotal();
                                $lineDiscount = $item->getLineDiscount();
                                $lineFinal = $item->getLineFinal();
                              @endphp

                              {{-- Original line total with strikethrough --}}
                              <div class="text-lg text-gray-400 line-through mb-1">${{ number_format($lineTotal, 2) }}</div>

                              {{-- Discount badge --}}
                              @if($discountInfo['discount_name'])
                                <div class="inline-flex items-center text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-full mb-1">
                                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                  </svg>
                                  Save ${{ number_format($lineDiscount, 2) }}
                                </div>
                              @endif

                              {{-- Final price after discount --}}
                              <p class="text-3xl font-bold text-red-600 item-line-total" id="item-line-total-{{ $item->id }}">${{ number_format($lineFinal, 2) }}</p>
                            @else
                              {{-- No discount - show regular price --}}
                              <p class="text-3xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent item-line-total" id="item-line-total-{{ $item->id }}">${{ number_format($item->quantity * $item->unit_price, 2) }}</p>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-center py-16">
                  <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                  <p class="text-gray-500 text-lg">Your cart is empty.</p>
                </div>
              @endif
            </div>


          </div>

          <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
              <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-primary to-primary/60 mr-3"></span>
                Order Summary
              </h2>
              @php
                $subtotal = $totals['subtotal'] ?? 0;
                $totalDiscount = $totals['total_discount'] ?? 0;
                $subtotalAfterDiscount = $totals['total'] ?? 0;
                $tax = round($subtotalAfterDiscount * 0.08, 2);
                $shipping = 0.00;
                $finalTotal = $subtotalAfterDiscount + $tax + $shipping;
                $itemCount = $cart ? $cart->items->sum('quantity') : 0;
              @endphp

              <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                @if($cart && $cart->items->count() > 0)
                  @foreach($cart->items as $item)
                    <div class="flex gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200 summary-item-row" data-summary-item-id="{{ $item->id }}">
                      <img src="{{ optional($item->product)->cover_image ? asset('storage/' . optional($item->product)->cover_image) : 'https://via.placeholder.com/80x80' }}" alt="{{ optional($item->product)->name ?? 'Product' }}" class="w-20 h-20 rounded-lg object-cover shadow-sm">
                      <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 text-sm mb-1 truncate">{{ optional($item->variant)->variant_name ?? optional($item->product)->name ?? 'Product' }}</h4>
                        <p class="text-xs text-gray-600">Qty: {{ $item->quantity }}</p>
                        <p class="text-sm font-bold text-primary mt-1">${{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                      </div>
                    </div>
                  @endforeach
                @endif
              </div>

              <div class="space-y-5 mb-8">
                <div class="flex justify-between text-gray-600 text-lg">
                  <span>Subtotal (<span id="order-item-count">{{ $itemCount }}</span> item{{ $itemCount !== 1 ? 's' : '' }})</span>
                  <span id="order-subtotal" class="font-bold">${{ number_format($subtotal, 2) }}</span>
                </div>

                @if($totalDiscount > 0)
                  <div class="flex justify-between text-green-600 text-lg bg-green-50 px-4 py-3 rounded-xl">
                    <span class="flex items-center">
                      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                      Discount Savings
                    </span>
                    <span id="order-discount" class="font-bold">-${{ number_format($totalDiscount, 2) }}</span>
                  </div>

                  <div class="flex justify-between text-gray-900 text-lg font-semibold border-t border-b border-gray-200 py-3">
                    <span>Subtotal After Discount</span>
                    <span id="order-subtotal-after-discount">${{ number_format($subtotalAfterDiscount, 2) }}</span>
                  </div>
                @endif

                <div class="flex justify-between text-gray-600 text-lg">
                  <span>Shipping</span>
                  <span id="order-shipping" class="font-bold text-green-600">{{ $shipping > 0 ? '$'.number_format($shipping,2) : 'FREE' }}</span>
                </div>
                <div class="flex justify-between text-gray-600 text-lg">
                  <span>Tax (8%)</span>
                  <span id="order-tax" class="font-bold">${{ number_format($tax, 2) }}</span>
                </div>
                <div class="border-t-2 border-gray-100 pt-5">
                  <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-900">Total</span>
                    <span id="order-total" class="text-4xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent">${{ number_format($finalTotal, 2) }}</span>
                  </div>

                  @if($totalDiscount > 0)
                    <div class="mt-3 text-center">
                      <span class="inline-flex items-center text-sm font-semibold text-green-700 bg-green-100 px-4 py-2 rounded-full">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        You save ${{ number_format($totalDiscount, 2) }} today!
                      </span>
                    </div>
                  @endif
                </div>
              </div>
              <a href="{{ route('checkout', ['step' => 2]) }}" class="block w-full px-6 py-5 bg-gradient-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5">
                Proceed to Checkout
                <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
              </a>
            </div>

            <div class="mt-6 bg-gradient-to-r from-primary/5 to-blue-50 rounded-3xl shadow-lg border border-primary/20 p-6">
              <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                Have a Coupon Code?
              </h3>
              <div class="flex gap-3">
                <input type="text" name="coupon_code" placeholder="Enter coupon code" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm">
                <button type="button" class="px-6 py-3 bg-gradient-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">Apply</button>
              </div>
            </div>
          </div>
        </div>

      @elseif($step == 2)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-primary to-primary/60 mr-3"></span>
                Contact Information
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                  <input type="email" id="email" name="email" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                </div>
                <div>
                  <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                  <input type="tel" id="phone" name="phone" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                </div>
              </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-primary to-primary/60 mr-3"></span>
                Shipping Address
              </h2>
              <div class="space-y-5">
                @if(Auth::check())
                  <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Saved Addresses <span class="text-xs text-gray-500">({{ $addresses->count() }} saved)</span></label>
                    <select id="savedAddressSelect" name="saved_address_id" class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white">
                      <option value="">Use a new address</option>
                      @forelse($addresses as $addr)
                        <option value="{{ $addr->id }}" data-addr='@json($addr)'>{{ $addr->address_label ?? ($addr->street . ', ' . $addr->city . ' ' . $addr->postal_code) }}</option>
                      @empty
                        <option value="" disabled>No saved addresses</option>
                      @endforelse
                    </select>
                  </div>
                @endif

                <script>
                  (function(){
                    const sel = document.getElementById('savedAddressSelect');
                    if (!sel) return;
                    sel.addEventListener('change', function(){
                      const opt = sel.options[sel.selectedIndex];
                      if (!opt || !opt.dataset || !opt.dataset.addr) return;
                      try {
                        const addr = JSON.parse(opt.dataset.addr);
                        // populate form fields
                        document.getElementById('firstName').value = addr.name ? (addr.name.split(' ')[0] || addr.name) : '';
                        document.getElementById('lastName').value = addr.name ? (addr.name.split(' ').slice(1).join(' ') || '') : '';
                        document.getElementById('address').value = addr.street || '';
                        document.getElementById('city').value = addr.city || '';
                        document.getElementById('state').value = addr.state || '';
                        document.getElementById('zip').value = addr.postal_code || addr.postcode || addr.zip || '';
                      } catch (e) { /* ignore parse errors */ }
                    });
                  })();
                </script>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label for="firstName" class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                    <input type="text" id="firstName" name="firstName" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                  </div>
                  <div>
                    <label for="lastName" class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                    <input type="text" id="lastName" name="lastName" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                  </div>
                </div>
                <div>
                  <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Street Address *</label>
                  <input type="text" id="address" name="address" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div>
                    <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City *</label>
                    <input type="text" id="city" name="city" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                  </div>
                  <div>
                    <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State *</label>
                    <input type="text" id="state" name="state" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                  </div>
                  <div>
                    <label for="zip" class="block text-sm font-semibold text-gray-700 mb-2">ZIP Code *</label>
                    <input type="text" id="zip" name="zip" required class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                  </div>
                </div>
                @auth
                  <div class="mt-3">
                    <label class="inline-flex items-center text-sm">
                      <input type="checkbox" name="save_address" value="1" class="form-checkbox h-4 w-4 text-primary mr-2">
                      Save this address to my account
                    </label>
                  </div>
                @endauth
              </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-primary to-primary/60 mr-3"></span>
                Shipping Method
              </h2>
              <div class="space-y-4">
                <label class="flex items-center p-5 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-primary hover:bg-primary/5">
                  <input type="radio" name="shipping" value="standard" checked class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                  <span class="ml-4 flex-1">
                    <span class="block font-semibold text-gray-900">Standard Shipping</span>
                    <span class="block text-sm text-gray-600">5-7 business days</span>
                  </span>
                  <span class="font-bold text-green-600">FREE</span>
                </label>
                <label class="flex items-center p-5 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-primary hover:bg-primary/5">
                  <input type="radio" name="shipping" value="express" class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                  <span class="ml-4 flex-1">
                    <span class="block font-semibold text-gray-900">Express Shipping</span>
                    <span class="block text-sm text-gray-600">2-3 business days</span>
                  </span>
                  <span class="font-bold text-gray-900">$12.99</span>
                </label>
              </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-primary to-primary/60 mr-3"></span>
                Payment Method
              </h2>
              <div class="space-y-4 mb-6">
                <label class="flex items-center p-4 border-2 border-primary rounded-xl cursor-pointer bg-primary/5">
                  <input type="radio" name="payment_method" value="card" checked class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                  <span class="ml-4 font-semibold text-gray-900">Credit / Debit Card</span>
                </label>

                <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                  <input type="radio" name="payment_method" value="paypal" class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                  <span class="ml-4 font-semibold text-gray-900">PayPal</span>
                </label>

                <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer">
                  <input type="radio" name="payment_method" value="cod" class="w-5 h-5 text-primary focus:ring-4 focus:ring-primary/20">
                  <span class="ml-4 font-semibold text-gray-900">Cash on Delivery</span>
                </label>
              </div>

              <div id="cardDetails" class="space-y-5">
                <div>
                  <label for="cardNumber" class="block text-sm font-semibold text-gray-700 mb-2">Card Number *</label>
                  <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                </div>
                <div class="grid grid-cols-2 gap-5">
                  <div>
                    <label for="expiry" class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date *</label>
                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY" class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                  </div>
                  <div>
                    <label for="cvv" class="block text-sm font-semibold text-gray-700 mb-2">CVV *</label>
                    <input type="text" id="cvv" name="cvv" maxlength="3" placeholder="123" class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-primary to-primary/60 mr-3"></span>
                Order Summary
              </h2>

              @php
                $subtotal = $cart ? $cart->items->sum(function($i){ return ($i->quantity * $i->unit_price); }) : 0;
                $tax = round($subtotal * 0.08, 2);
                $shipping = 0.00; // You should likely grab this from JS or session if possible, defaulting to 0
                $total = $subtotal + $tax + $shipping;
              @endphp

              <div class="space-y-4 mb-6">
                <div class="flex justify-between text-gray-600 text-base">
                  <span>Subtotal</span>
                  <span class="font-bold">${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600 text-base">
                  <span>Shipping</span>
                  <span class="font-bold text-green-600">{{ $shipping > 0 ? '$'.number_format($shipping,2) : 'FREE' }}</span>
                </div>
                <div class="flex justify-between text-gray-600 text-base">
                  <span>Tax (8%)</span>
                  <span class="font-bold">${{ number_format($tax, 2) }}</span>
                </div>
                <div class="border-t-2 border-gray-100 pt-4">
                  <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-900">Total</span>
                    <span id="summary-total" class="text-4xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent">${{ number_format($total, 2) }}</span>
                  </div>
                </div>
              </div>

              <button type="submit" name="step" value="3" class="w-full px-6 py-5 bg-gradient-to-r from-primary to-primary/80 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5">
                Place Secure Order
                <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
              </button>
              <p class="text-xs text-center text-gray-500 mt-4 flex items-center justify-center">
                <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                Your payment information is secure
              </p>
            </div>

            <div class="mt-6 bg-gradient-to-r from-primary/5 to-blue-50 rounded-3xl shadow-lg border border-primary/20 p-6">
              <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                Have a Coupon Code?
              </h3>
              <div class="flex gap-3">
                <input type="text" name="coupon_code" placeholder="Enter coupon code" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm">
                <button type="button" class="px-6 py-3 bg-gradient-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">Apply</button>
              </div>
            </div>
          </div>
        </div>

      @elseif($step == 3)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 space-y-6">

            <div class="bg-gradient-to-br from-green-50 to-white rounded-3xl shadow-xl border-2 border-green-200 p-12 text-center">
              <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-green-500 to-green-400 flex items-center justify-center shadow-2xl animate-bounce-slow">
                <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
              </div>
              <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Order Successfully Placed!</h2>
              <p class="text-xl text-gray-600 mb-2">Your order has been confirmed.</p>
              <p class="text-lg text-gray-600">Order #<span class="font-bold text-primary">{{ isset($order) ? ($order->order_number ?? '#' . $order->id) : '#EZ-2024-001' }}</span></p>

              <div class="bg-white rounded-2xl shadow-lg p-8 text-left mt-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                  <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                  Order Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base">
                  <div class="flex items-start">
                    <span class="font-semibold text-gray-700 mr-2">Order Number:</span>
                    <span class="text-gray-900">{{ isset($order) ? ($order->order_number ?? '#' . $order->id) : '#EZ-2024-001' }}</span>
                  </div>
                  <div class="flex items-start">
                    <span class="font-semibold text-gray-700 mr-2">Order Date:</span>
                    <span class="text-gray-900">{{ isset($order) ? $order->created_at->format('M d, Y') : date('M d, Y') }}</span>
                  </div>
                  <div class="flex items-start">
                    <span class="font-semibold text-gray-700 mr-2">Payment Method:</span>
                    <span class="text-gray-900">{{ isset($order) ? ($order->payment_method ?? 'Credit Card') : 'Credit Card' }}</span>
                  </div>
                  <div class="flex items-start">
                    <span class="font-semibold text-gray-700 mr-2">Shipping Method:</span>
                    <span class="text-gray-900">{{ isset($order) ? ($order->shipping_method ?? 'Standard Shipping') : 'Standard Shipping' }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mt-6 text-left">
                <div class="flex items-start">
                  <svg class="w-6 h-6 text-blue-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <div>
                    <h4 class="font-bold text-blue-900 mb-1">What's Next?</h4>
                    <p class="text-sm text-blue-800">We've sent a confirmation email to your inbox. You can track your order status in your account dashboard.</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
              <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('home') }}" class="flex-1 px-8 py-5 bg-gradient-to-r from-primary to-primary/80 text-white text-center font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                  Continue Shopping
                  <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </a>
                <a href="{{ isset($order) ? route('order-detail', $order->id) : '#' }}" class="flex-1 px-8 py-5 border-2 border-primary text-primary text-center font-bold rounded-2xl hover:bg-primary hover:text-white transition-all duration-300 transform hover:scale-105">
                  View Order Details
                  <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </a>
              </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-24">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-green-500 to-green-400 mr-3"></span>
                Order Summary
              </h2>

              @php
                $itemsForSummary = isset($order) && $order->items->isNotEmpty() ? $order->items : ($cart ? $cart->items : collect());
                $subtotal = $itemsForSummary->sum(function($i){ return ($i->quantity * ($i->unit_price ?? ($i->price ?? 0))); });
                $tax = round($subtotal * 0.08, 2);
                $shipping = 0.00;
                $total = isset($order) && $order->total ? $order->total : ($subtotal + $tax + $shipping);
              @endphp

              <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100 max-h-64 overflow-y-auto">
                @foreach($itemsForSummary as $itm)
                  <div class="flex gap-4 p-4 bg-gray-50 rounded-xl">
                    <img src="{{ optional($itm->product)->cover_image ? asset('storage/' . optional($itm->product)->cover_image) : 'https://via.placeholder.com/80x80' }}" alt="{{ optional($itm->product)->name ?? 'Product' }}" class="w-20 h-20 rounded-lg object-cover shadow-sm">
                    <div class="flex-1 min-w-0">
                      <h4 class="font-semibold text-gray-900 text-sm mb-1 truncate">{{ optional($itm->variant)->variant_name ?? optional($itm->product)->name ?? 'Product' }}</h4>
                      <p class="text-xs text-gray-600">Qty: {{ $itm->quantity }}</p>
                      <p class="text-sm font-bold text-primary mt-1">${{ number_format(($itm->unit_price ?? ($itm->price ?? 0)) * $itm->quantity, 2) }}</p>
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="space-y-4 mb-6">
                <div class="flex justify-between text-gray-600 text-base">
                  <span>Subtotal</span>
                  <span class="font-bold">${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600 text-base">
                  <span>Shipping</span>
                  <span class="font-bold text-green-600">{{ $shipping > 0 ? '$'.number_format($shipping,2) : 'FREE' }}</span>
                </div>
                <div class="flex justify-between text-gray-600 text-base">
                  <span>Tax (8%)</span>
                  <span class="font-bold">${{ number_format($tax, 2) }}</span>
                </div>
                <div class="border-t-2 border-gray-100 pt-4">
                  <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-900">Total</span>
                    <span class="text-4xl font-bold bg-gradient-to-r from-green-500 to-green-400 bg-clip-text text-transparent">${{ number_format($total, 2) }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                <div class="flex items-center text-green-700">
                  <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                  <span class="text-sm font-semibold">Payment Successful</span>
                </div>
              </div>
            </div>

            <div class="mt-6 bg-gradient-to-r from-primary/5 to-blue-50 rounded-3xl shadow-lg border border-primary/20 p-6">
              <h3 class="font-bold text-gray-900 text-lg mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                Have a Coupon Code?
              </h3>
              <div class="flex gap-3">
                <input type="text" name="coupon_code" placeholder="Enter coupon code" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm">
                <button type="button" class="px-6 py-3 bg-gradient-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">Apply</button>
              </div>
            </div>
          </div>
        </div>
      @endif

    </form>
  </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/checkout-process.js') }}"></script>
@endpush
