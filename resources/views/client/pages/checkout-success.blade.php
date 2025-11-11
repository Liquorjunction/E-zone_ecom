@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            Order Confirmed
          </h1>
          <p class="text-gray-600">Thank you for your purchase! Your order has been successfully placed.</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('cart') }}" class="hover:text-primary transition-colors">Cart</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('checkout') }}" class="hover:text-primary transition-colors">Checkout</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">Order Confirmed</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-8 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
      <div class="flex items-center justify-between relative">
        <div class="flex flex-col items-center z-10 flex-1">
          <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span class="mt-2 text-sm font-semibold text-green-500">Shopping Cart</span>
        </div>

        <div class="absolute top-6 left-0 right-0 h-0.5 bg-green-500 z-0"></div>

        <div class="flex flex-col items-center z-10 flex-1">
          <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-lg shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span class="mt-2 text-sm font-semibold text-green-500">Checkout</span>
        </div>

        <div class="flex flex-col items-center z-10 flex-1">
          <div class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-lg shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <span class="mt-2 text-sm font-semibold text-green-500">Order Complete</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <div class="lg:col-span-2 space-y-6">

        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-8 text-center">
          <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-50 flex items-center justify-center">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Order Successfully Placed!</h2>
          <p class="text-lg text-gray-600 mb-6">Your order #<span class="font-bold text-primary">EZ-2024-001</span> has been confirmed and is being processed.</p>

          <div class="bg-gray-50 rounded-xl p-6 text-left">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Order Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-semibold text-gray-700">Order Number:</span>
                <span class="text-gray-900">EZ-2024-001</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Order Date:</span>
                <span class="text-gray-900">{{ date('M d, Y') }}</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Payment Method:</span>
                <span class="text-gray-900">Credit Card (**** **** **** 3456)</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Shipping Method:</span>
                <span class="text-gray-900">Standard Shipping (FREE)</span>
              </div>
              <div class="md:col-span-2">
                <span class="font-semibold text-gray-700">Shipping Address:</span>
                <span class="text-gray-900 block mt-1">John Doe<br>123 Main Street, Apt 4B<br>New York, NY 10001<br>United States</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
            What's Next?
          </h3>

          <div class="space-y-4">
            <div class="flex items-start space-x-4">
              <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
              <div>
                <h4 class="font-semibold text-gray-900">Order Processing</h4>
                <p class="text-sm text-gray-600">We'll start processing your order within 24 hours. You'll receive an email confirmation with tracking information.</p>
              </div>
            </div>

            <div class="flex items-start space-x-4">
              <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shrink-0">2</div>
              <div>
                <h4 class="font-semibold text-gray-900">Shipping</h4>
                <p class="text-sm text-gray-600">Your order will be shipped within 2-3 business days. You'll receive tracking information via email and SMS.</p>
              </div>
            </div>

            <div class="flex items-start space-x-4">
              <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shrink-0">3</div>
              <div>
                <h4 class="font-semibold text-gray-900">Delivery</h4>
                <p class="text-sm text-gray-600">Standard shipping typically takes 5-7 business days. Express shipping arrives in 2-3 business days.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('home') }}" class="flex-1 px-6 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl text-center">
              <span class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Continue Shopping
              </span>
            </a>
            <a href="#" class="flex-1 px-6 py-4 border-2 border-primary text-primary font-bold rounded-xl transition-all duration-300 hover:bg-primary hover:text-white text-center">
              <span class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Order Details
              </span>
            </a>
          </div>
        </div>

      </div>

      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">
          <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
            Order Summary
          </h2>

          <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100">
            <div class="flex gap-3">
              <img src="https://via.placeholder.com/80x80?text=Product+1" alt="Product" class="w-20 h-20 object-cover rounded-lg">
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 text-sm mb-1">Premium Wireless Headphones</h3>
                <p class="text-xs text-gray-500 mb-2">Qty: 1</p>
                <p class="font-bold text-primary">$199.99</p>
              </div>
            </div>

            <div class="flex gap-3">
              <img src="https://via.placeholder.com/80x80?text=Product+2" alt="Product" class="w-20 h-20 object-cover rounded-lg">
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 text-sm mb-1">Smart Watch Series 8</h3>
                <p class="text-xs text-gray-500 mb-2">Qty: 2</p>
                <p class="font-bold text-primary">$399.00</p>
              </div>
            </div>
          </div>

          <div class="space-y-3 mb-6">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal</span>
              <span class="font-semibold">$798.99</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Shipping</span>
              <span class="font-semibold text-green-600">FREE</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Tax (8%)</span>
              <span class="font-semibold">$63.92</span>
            </div>

            <div class="border-t-2 border-gray-100 pt-3">
              <div class="flex justify-between items-center">
                <span class="text-xl font-bold text-gray-900">Total</span>
                <span class="text-3xl font-bold text-primary">$862.91</span>
              </div>
            </div>
          </div>

          <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="font-semibold text-green-800">Order Confirmed</p>
                <p class="text-sm text-green-600">Processing will begin shortly</p>
              </div>
            </div>
          </div>

          <div class="pt-6 border-t-2 border-gray-100">
            <p class="text-xs text-gray-500 text-center mb-4">Your order is secure</p>
            <div class="flex justify-center items-center space-x-3 mb-4">
              <svg class="h-8" viewBox="0 0 48 32" fill="none">
                <rect width="48" height="32" rx="4" fill="#1434CB"/>
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-weight="bold" font-size="14">VISA</text>
              </svg>
              <svg class="h-8" viewBox="0 0 48 32" fill="none">
                <rect width="48" height="32" rx="4" fill="#EB001B"/>
                <circle cx="18" cy="16" r="10" fill="#FF5F00"/>
                <circle cx="30" cy="16" r="10" fill="#F79E1B"/>
              </svg>
              <svg class="h-8" viewBox="0 0 48 32" fill="none">
                <rect width="48" height="32" rx="4" fill="#0072CE"/>
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-weight="bold" font-size="10">AMEX</text>
              </svg>
            </div>

            <div class="space-y-2">
              <div class="flex items-center justify-center text-xs text-gray-600">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                256-bit SSL Encryption
              </div>
              <div class="flex items-center justify-center text-xs text-gray-600">
                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                PCI DSS Compliant
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-50 flex items-center justify-center">
          <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Secure Payment</h3>
        <p class="text-sm text-gray-600">Your payment information is encrypted and secure with industry-standard protocols</p>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-50 flex items-center justify-center">
          <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
          </svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Free Shipping</h3>
        <p class="text-sm text-gray-600">Enjoy free standard shipping on all orders over $50 with no hidden fees</p>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
          <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">24/7 Support</h3>
        <p class="text-sm text-gray-600">Our customer support team is available around the clock to assist you</p>
      </div>
    </div>

    <div class="mt-8 bg-linear-to-r from-primary/5 to-primary/10 rounded-2xl p-6 border-2 border-primary/20">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
          <h3 class="text-lg font-bold text-gray-900 mb-1">Need Help with Your Order?</h3>
          <p class="text-sm text-gray-600">Our support team is ready to assist you with any questions</p>
        </div>
        <div class="flex gap-3">
          <a href="#" class="flex items-center px-6 py-3 bg-white border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Live Chat
          </a>
          <a href="#" class="flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            Call Us
          </a>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
