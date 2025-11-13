@extends('client.layouts.app')

@section('content')
<div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
  <div class="container mx-auto px-4">

    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
            Order Details
          </h1>
          <p class="text-gray-600">Complete information about your order</p>
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('dashboard.overview') }}" class="hover:text-primary transition-colors">Profile</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('dashboard.orders') }}" class="hover:text-primary transition-colors">My Orders</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-primary font-medium">Order Details</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Order Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 mb-4 border-b-2 border-gray-100">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                Order #ORD-123456789
              </h2>
              <p class="text-sm text-gray-600">Placed on <span class="font-semibold">November 10, 2025</span> at <span class="font-semibold">10:30 AM</span></p>
            </div>
            <div class="text-left md:text-right">
              <span class="inline-block px-4 py-2 bg-green-100 text-green-700 rounded-full font-semibold text-sm mb-2">
                Out for Delivery
              </span>
              <p class="text-sm text-gray-600">Expected: <span class="font-semibold text-gray-900">Nov 15, 2025</span></p>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="flex flex-wrap gap-3">
            <button class="flex items-center px-4 py-2 bg-primary text-white rounded-lg font-medium hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Track Shipment
            </button>
            <button class="flex items-center px-4 py-2 border-2 border-gray-200 text-gray-700 rounded-lg font-medium hover:border-primary hover:text-primary transition-all duration-300">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Print Invoice
            </button>
            <button class="flex items-center px-4 py-2 border-2 border-gray-200 text-gray-700 rounded-lg font-medium hover:border-primary hover:text-primary transition-all duration-300">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Need Help?
            </button>
          </div>
        </div>

        <!-- Order Progress Timeline -->
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
            Order Timeline
          </h3>

          <div class="relative">
            <!-- Progress Line -->
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            <div class="absolute left-6 top-0 w-0.5 bg-primary" style="height: 66.67%;"></div>

            <!-- Timeline Items -->
            <div class="space-y-8 relative">

              <!-- Order Placed -->
              <div class="flex items-start relative">
                <div class="shrink-0 w-12 h-12 rounded-full bg-primary flex items-center justify-center z-10 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div class="ml-6 flex-1">
                  <div class="bg-primary/5 rounded-xl p-4 border-2 border-primary/20">
                    <h4 class="font-bold text-gray-900 mb-1">Order Placed</h4>
                    <p class="text-sm text-gray-600 mb-2">Your order has been received and confirmed</p>
                    <p class="text-xs text-gray-500">November 10, 2025 - 10:30 AM</p>
                  </div>
                </div>
              </div>

              <!-- Order Processing -->
              <div class="flex items-start relative">
                <div class="shrink-0 w-12 h-12 rounded-full bg-primary flex items-center justify-center z-10 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div class="ml-6 flex-1">
                  <div class="bg-primary/5 rounded-xl p-4 border-2 border-primary/20">
                    <h4 class="font-bold text-gray-900 mb-1">Order Processing</h4>
                    <p class="text-sm text-gray-600 mb-2">Your order is being prepared for shipment</p>
                    <p class="text-xs text-gray-500">November 11, 2025 - 2:15 PM</p>
                  </div>
                </div>
              </div>

              <!-- Order Shipped -->
              <div class="flex items-start relative">
                <div class="shrink-0 w-12 h-12 rounded-full bg-primary flex items-center justify-center z-10 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div class="ml-6 flex-1">
                  <div class="bg-primary/5 rounded-xl p-4 border-2 border-primary/20">
                    <h4 class="font-bold text-gray-900 mb-1">Order Shipped</h4>
                    <p class="text-sm text-gray-600 mb-2">Package is on its way to you</p>
                    <p class="text-xs text-gray-500 mb-3">November 12, 2025 - 9:00 AM</p>
                    <div class="bg-white rounded-lg p-3 border border-gray-200">
                      <p class="text-xs font-semibold text-gray-700 mb-1">Carrier: FedEx Express</p>
                      <p class="text-xs text-gray-600">Tracking: <span class="font-mono text-primary">FDX1234567890</span></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Out for Delivery -->
              <div class="flex items-start relative">
                <div class="shrink-0 w-12 h-12 rounded-full bg-primary flex items-center justify-center z-10 shadow-lg animate-pulse">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                  </svg>
                </div>
                <div class="ml-6 flex-1">
                  <div class="bg-green-50 rounded-xl p-4 border-2 border-green-200">
                    <div class="flex items-center mb-1">
                      <h4 class="font-bold text-gray-900">Out for Delivery</h4>
                      <span class="ml-2 px-2 py-1 bg-green-500 text-white text-xs rounded-full font-semibold">Current</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Your package is on the delivery vehicle</p>
                    <p class="text-xs text-gray-500">November 13, 2025 - 8:45 AM</p>
                  </div>
                </div>
              </div>

              <!-- Delivered (Pending) -->
              <div class="flex items-start relative opacity-50">
                <div class="shrink-0 w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center z-10">
                  <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                  </svg>
                </div>
                <div class="ml-6 flex-1">
                  <div class="bg-gray-50 rounded-xl p-4 border-2 border-gray-200">
                    <h4 class="font-bold text-gray-700 mb-1">Delivered</h4>
                    <p class="text-sm text-gray-500">Awaiting delivery completion</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
            Order Items (2)
          </h3>

          <div class="space-y-4">
            <!-- Item 1 -->
            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl border-2 border-gray-100">
              <img src="https://via.placeholder.com/100x100?text=Product+1" alt="Product" class="w-24 h-24 object-cover rounded-lg shrink-0">
              <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">Premium Wireless Headphones</h4>
                <p class="text-sm text-gray-600 mb-2">Color: Black | Bluetooth 5.0</p>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Qty: 1</span>
                  <span class="text-xl font-bold text-primary">$199.99</span>
                </div>
              </div>
            </div>

            <!-- Item 2 -->
            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl border-2 border-gray-100">
              <img src="https://via.placeholder.com/100x100?text=Product+2" alt="Product" class="w-24 h-24 object-cover rounded-lg shrink-0">
              <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">Smart Watch Series 8</h4>
                <p class="text-sm text-gray-600 mb-2">Color: Silver | 44mm</p>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Qty: 1</span>
                  <span class="text-xl font-bold text-primary">$399.00</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping & Billing Address -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Shipping Address -->
          <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
            <h4 class="font-bold text-gray-900 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Shipping Address
            </h4>
            <div class="space-y-1 text-sm text-gray-700">
              <p class="font-semibold">John Doe</p>
              <p>123 Main Street, Apt 4B</p>
              <p>New York, NY 10001</p>
              <p>United States</p>
              <p class="pt-2 text-gray-600">Phone: +1 (555) 123-4567</p>
            </div>
          </div>

          <!-- Billing Address -->
          <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
            <h4 class="font-bold text-gray-900 mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
              Billing Address
            </h4>
            <div class="space-y-1 text-sm text-gray-700">
              <p class="font-semibold">John Doe</p>
              <p>123 Main Street, Apt 4B</p>
              <p>New York, NY 10001</p>
              <p>United States</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <div class="space-y-6 sticky top-24">

          <!-- Order Summary -->
          <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
              <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
              Order Summary
            </h3>

            <div class="space-y-3 text-sm mb-6">
              <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold text-gray-900">$598.99</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Shipping</span>
                <span class="font-semibold text-green-600">FREE</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Tax (8%)</span>
                <span class="font-semibold text-gray-900">$47.92</span>
              </div>
              <div class="border-t-2 border-gray-100 pt-3 mt-3">
                <div class="flex justify-between items-center">
                  <span class="text-lg font-bold text-gray-900">Total</span>
                  <span class="text-2xl font-bold text-primary">$646.91</span>
                </div>
              </div>
            </div>

            <div class="space-y-2 text-xs text-gray-600">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Payment Verified</span>
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Visa ending in 4242</span>
              </div>
            </div>
          </div>

          <!-- Need Help -->
          <div class="bg-linear-to-br from-primary/5 to-primary/10 rounded-2xl p-6 border-2 border-primary/20">
            <h4 class="font-bold text-gray-900 mb-2">Need Help?</h4>
            <p class="text-sm text-gray-600 mb-4">Our customer support team is here to assist you</p>
            <div class="space-y-2">
              <button class="w-full flex items-center justify-center px-4 py-3 bg-primary text-white rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Live Chat
              </button>
              <button class="w-full flex items-center justify-center px-4 py-3 bg-white border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary hover:text-white transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Email Us
              </button>
            </div>
          </div>

          <!-- Actions -->
          <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
            <h4 class="font-bold text-gray-900 mb-4">Quick Actions</h4>
            <div class="space-y-3">
              <button class="w-full px-4 py-3 bg-gray-50 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-all duration-300 text-left flex items-center">
                <svg class="w-5 h-5 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Return Items
              </button>
              <button class="w-full px-4 py-3 bg-gray-50 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-all duration-300 text-left flex items-center">
                <svg class="w-5 h-5 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Write Review
              </button>
              <button class="w-full px-4 py-3 bg-gray-50 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-all duration-300 text-left flex items-center">
                <svg class="w-5 h-5 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Buy Again
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- Back Button -->
    <div class="mt-8">
      <a href="{{ route('dashboard.orders') }}" class="inline-flex items-center px-6 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-primary hover:text-primary transition-all duration-300">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to My Orders
      </a>
    </div>

  </div>
</div>
@endsection
