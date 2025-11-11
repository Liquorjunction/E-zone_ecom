@extends('client.layouts.app')

@section('content')
@php
  // FOR DESIGNING ONLY: Uncomment the line you want to test.
  // Your real controller will set this variable.
  // $step = 1; // Cart
  // $step = 2; // Checkout Form
  // $step = 3; // Success
  $step = 1;
@endphp

<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            @if($step == 1)
              Shopping Cart
            @elseif($step == 2)
              Checkout
            @else
              Order Confirmed
            @endif
          </h1>
          <p class="text-gray-600">
            @if($step == 1)
              Review your items before checkout
            @elseif($step == 2)
              Complete your purchase securely
            @else
              Thank you for your purchase!
            @endif
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
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
            <span class="text-primary font-medium">
              @if($step == 1)
                Cart
              @elseif($step == 2)
                Checkout
              @else
                Order Confirmed
              @endif
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-8 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
      <div class="flex items-center justify-between relative">

        <div class="flex flex-col items-center z-10 flex-1">
          <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg shadow-lg
            @if($step == 1) bg-primary text-white @endif
            @if($step > 1) bg-green-500 text-white @endif
          ">
            @if($step > 1)
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            @else
              1
            @endif
          </div>
          <span class="mt-2 text-sm font-semibold
            @if($step == 1) text-primary @endif
            @if($step > 1) text-green-500 @endif
          ">Shopping Cart</span>
        </div>

        <div class="absolute top-6 left-0 right-0 h-0.5 bg-gray-200 z-0" style="margin: 0 10%;"></div>
        <div class="absolute top-6 left-0 h-0.5 z-0
          @if($step == 1) w-0 @endif
          @if($step == 2) w-1/2 bg-primary @endif
          @if($step == 3) w-full bg-green-500 @endif
        " style="margin-left: 10%;"></div>

        <div class="flex flex-col items-center z-10 flex-1">
          <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
            @if($step == 2) bg-primary text-white shadow-lg @endif
            @if($step < 2) bg-gray-200 text-gray-600 @endif
            @if($step > 2) bg-green-500 text-white shadow-lg @endif
          ">
            @if($step > 2)
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            @else
              2
            @endif
          </div>
          <span class="mt-2 text-sm font-medium
            @if($step == 2) text-primary font-semibold @endif
            @if($step < 2) text-gray-500 @endif
            @if($step > 2) text-green-500 font-semibold @endif
          ">Checkout</span>
        </div>

        <div class="flex flex-col items-center z-10 flex-1">
          <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
            @if($step == 3) bg-green-500 text-white shadow-lg @endif
            @if($step < 3) bg-gray-200 text-gray-600 @endif
          ">
            @if($step == 3)
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            @else
              3
            @endif
          </div>
          <span class="mt-2 text-sm font-semibold
            @if($step == 3) text-green-500 @endif
            @if($step < 3) text-gray-500 @endif
          ">Order Complete</span>
        </div>
      </div>
    </div>

    <form method="POST" action="{{-- route('checkout.process') --}}">
      @csrf

      @if($step == 1)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <div class="lg:col-span-2 space-y-4">

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg">
              <div class="flex flex-col sm:flex-row gap-4">
                <div class="shrink-0">
                  <img src="https://via.placeholder.com/150x150?text=Product" alt="Product" class="w-full sm:w-32 h-32 object-cover rounded-xl">
                </div>

                <div class="flex-1">
                  <div class="flex justify-between items-start mb-2">
                    <div>
                      <h3 class="text-lg font-bold text-gray-900 mb-1">Sample Product</h3>
                      <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Electronics</span>
                    </div>
                    <button class="text-gray-400 hover:text-red-500 transition-colors p-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                  </div>
                  <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <p class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>In Stock</p>
                  </div>
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center">
                      <span class="text-sm font-medium text-gray-700 mr-4">Quantity:</span>
                      <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                        <button type="button" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg></button>
                        <input type="number" value="1" min="1" class="w-16 text-center border-0 focus:outline-none font-semibold text-gray-900">
                        <button type="button" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button>
                      </div>
                    </div>
                    <div class="text-right"><p class="text-2xl font-bold text-primary">$29.99</p></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
              <h3 class="font-bold text-gray-900 mb-4 flex items-center"><svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>Have a Coupon Code?</h3>
              <div class="flex gap-3">
                <input type="text" placeholder="Enter coupon code" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl ...">
                <button type="button" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl ...">Apply</button>
              </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="w-1 h-7 rounded-full bg-primary mr-3"></span>Order Summary</h2>
              <div class="space-y-4 mb-6">
                <div class="flex justify-between text-gray-600"><span>Subtotal (1 item)</span><span class="font-semibold">$29.99</span></div>
                <div class="flex justify-between text-gray-600"><span>Shipping</span><span class="font-semibold text-green-600">FREE</span></div>
                <div class="flex justify-between text-gray-600"><span>Tax (8%)</span><span class="font-semibold">$2.40</span></div>
                <div class="border-t-2 border-gray-100 pt-4">
                  <div class="flex justify-between items-center"><span class="text-xl font-bold text-gray-900">Total</span><span class="text-3xl font-bold text-primary">$32.39</span></div>
                </div>
              </div>
              <button type="submit" name="step" value="2" class="block w-full px-6 py-4 bg-primary text-white text-center font-bold rounded-xl ...">
                Proceed to Checkout
              </button>
            </div>
          </div>
        </div>

      @elseif($step == 2)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6 ...">Contact Information</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="email" class="block ...">Email Address *</label>
                  <input type="email" id="email" name="email" required class="w-full ...">
                </div>
                <div>
                  <label for="phone" class="block ...">Phone Number *</label>
                  <input type="tel" id="phone" name="phone" required class="w-full ...">
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6 ...">Shipping Address</h2>
              <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="firstName" class="block ...">First Name *</label>
                    <input type="text" id="firstName" name="firstName" required class="w-full ...">
                  </div>
                  <div>
                    <label for="lastName" class="block ...">Last Name *</label>
                    <input type="text" id="lastName" name="lastName" required class="w-full ...">
                  </div>
                </div>
                <div>
                  <label for="address" class="block ...">Street Address *</label>
                  <input type="text" id="address" name="address" required class="w-full ...">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label for="city" class="block ...">City *</label>
                    <input type="text" id="city" name="city" required class="w-full ...">
                  </div>
                  <div>
                    <label for="state" class="block ...">State *</label>
                    <input type="text" id="state" name="state" required class="w-full ...">
                  </div>
                  <div>
                    <label for="zip" class="block ...">ZIP Code *</label>
                    <input type="text" id="zip" name="zip" required class="w-full ...">
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6 ...">Shipping Method</h2>
              <div class="space-y-3">
                <label class="flex ..."><input type="radio" name="shipping" value="standard" checked ...>Standard Shipping</label>
                <label class="flex ..."><input type="radio" name="shipping" value="express" ...>Express Shipping</label>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-6 ...">Payment Method</h2>
              <label class="flex ..."><input type="radio" name="payment" value="card" checked ...>Credit / Debit Card</label>
              <div id="cardDetails" class="space-y-4 mt-4">
                 <div>
                  <label for="cardNumber" class="block ...">Card Number *</label>
                  <input type="text" id="cardNumber" name="cardNumber" class="w-full ...">
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="expiry" class="block ...">Expiry Date *</label>
                    <input type="text" id="expiry" name="expiry" class="w-full ..." placeholder="MM/YY">
                  </div>
                  <div>
                    <label for="cvv" class="block ...">CVV *</label>
                    <input type="text" id="cvv" name="cvv" maxlength="3" class="w-full ...">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 ...">Order Summary</h2>
              <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100">
                <div class="flex gap-3"><img src="https://via.placeholder.com/80x80" alt="Product" class="w-20 h-20 rounded-lg"> ...</div>
              </div>
              <div class="space-y-3 mb-6">
                <div class="flex justify-between ..."><span>Subtotal</span><span>$29.99</span></div>
                <div class="flex justify-between ..."><span>Shipping</span><span>FREE</span></div>
                <div class="flex justify-between ..."><span>Tax (8%)</span><span>$2.40</span></div>
                <div class="border-t-2 ... pt-3"><div class="flex justify-between ..."><span class="text-xl ...">Total</span><span class="text-3xl ...">$32.39</span></div></div>
              </div>
              <button type="submit" name="step" value="3" class="w-full px-6 py-4 bg-primary text-white font-bold rounded-xl ...">
                Place Secure Order
              </button>
            </div>
          </div>
        </div>

      @elseif($step == 3)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-8 text-center">
              <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-50 flex items-center justify-center">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </div>
              <h2 class="text-3xl font-bold text-gray-900 mb-4">Order Successfully Placed!</h2>
              <p class="text-lg text-gray-600 mb-6">Your order #<span class="font-bold text-primary">EZ-2024-001</span> has been confirmed.</p>

              <div class="bg-gray-50 rounded-xl p-6 text-left">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Order Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div><span class="font-semibold ...">Order Number:</span> ...</div>
                  <div><span class="font-semibold ...">Order Date:</span> ...</div>
                  <div><span class="font-semibold ...">Payment Method:</span> ...</div>
                  <div><span class="font-semibold ...">Shipping Method:</span> ...</div>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
              <div class="flex flex-col sm:flex-row gap-4">
                <a href="#" class="flex-1 px-6 py-4 bg-primary text-white ...">Continue Shopping</a>
                <a href="#" class="flex-1 px-6 py-4 border-2 border-primary text-primary ...">View Order Details</a>
              </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">
              <h2 class="text-2xl font-bold text-gray-900 mb-6 ...">Order Summary</h2>
              <div class="space-y-4 mb-6 pb-6 border-b-2 ...">
                <div class="flex gap-3"><img src="https://via.placeholder.com/80x80" alt="Product" class="w-20 h-20 rounded-lg"> ...</div>
              </div>
              <div class="space-y-3 mb-6">
                <div class="flex justify-between ..."><span>Subtotal</span><span>$29.99</span></div>
                <div class="flex justify-between ..."><span>Shipping</span><span>FREE</span></div>
                <div class="flex justify-between ..."><span>Tax (8%)</span><span>$2.40</span></div>
                <div class="border-t-2 ... pt-3"><div class="flex justify-between ..."><span class="text-xl ...">Total</span><span class="text-3xl ...">$32.39</span></div></div>
              </div>
            </div>
          </div>
        </div>
      @endif

    </form>
  </div>
</div>
@endsection
