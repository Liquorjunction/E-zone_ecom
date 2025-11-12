@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>Cart</x-slot:title>
                <x-slot:content>Review your cart.</x-slot:content>
            </x-breadcrumbs>

            @if (true)
                {{-- Assuming cart has items --}}

                <!-- Progress Steps -->
                <div class="mb-8 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                    <div class="flex items-center justify-between relative">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center z-10 flex-1">
                            <div
                                class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg shadow-lg">
                                1
                            </div>
                            <span class="mt-2 text-sm font-semibold text-primary">Shopping Cart</span>
                        </div>

                        <!-- Connector Line -->
                        <div class="absolute top-6 left-0 right-0 h-0.5 bg-gray-200 z-0" style="margin: 0 10%;"></div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center z-10 flex-1">
                            <div
                                class="w-12 h-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-lg">
                                2
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-500">Checkout</span>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center z-10 flex-1">
                            <div
                                class="w-12 h-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-lg">
                                3
                            </div>
                            <span class="mt-2 text-sm font-medium text-gray-500">Order Complete</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">

                        <!-- Cart Item -->
                        <div
                            class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 transition-all duration-300 hover:shadow-lg">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Product Image -->
                                <div class="shrink-0">
                                    <img src="https://via.placeholder.com/150x150?text=Product" alt="Product"
                                        class="w-full sm:w-32 h-32 object-cover rounded-xl">
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-1">Sample Product</h3>
                                            <span
                                                class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Electronics</span>
                                        </div>
                                        <button class="text-gray-400 hover:text-red-500 transition-colors p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            In Stock
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                            </svg>
                                            Free Shipping
                                        </p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                        <!-- Quantity Controller -->
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-700 mr-4">Quantity:</span>
                                            <div
                                                class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                                                <button
                                                    class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <input type="number" value="1" min="1"
                                                    class="w-16 text-center border-0 focus:outline-none font-semibold text-gray-900">
                                                <button
                                                    class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-primary">$29.99</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Coupon Code -->
                        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                Have a Coupon Code?
                            </h3>
                            <div class="flex gap-3">
                                <input type="text" placeholder="Enter coupon code"
                                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300">
                                <button
                                    class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                    Apply
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                                Order Summary
                            </h2>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal (1 item)</span>
                                    <span class="font-semibold">$29.99</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Shipping</span>
                                    <span class="font-semibold text-green-600">FREE</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax (8%)</span>
                                    <span class="font-semibold">$2.40</span>
                                </div>

                                <div class="border-t-2 border-gray-100 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xl font-bold text-gray-900">Total</span>
                                        <span class="text-3xl font-bold text-primary">$32.39</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 text-right">Including all taxes</p>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <a href="{{ route('checkout') }}"
                                class="block w-full px-6 py-4 bg-primary text-white text-center font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl mb-3">
                                Proceed to Checkout
                            </a>

                            <!-- Continue Shopping Button -->
                            <a href="{{ route('products') }}"
                                class="block w-full px-6 py-4 border-2 border-gray-200 text-gray-700 text-center font-semibold rounded-xl transition-all duration-300 hover:border-primary hover:text-primary">
                                Continue Shopping
                            </a>

                            <!-- Trust Badges -->
                            <div class="mt-6 pt-6 border-t-2 border-gray-100 space-y-3">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Secure Checkout
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    Easy Returns
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Best Price Guarantee
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <!-- Empty Cart State -->
                <div class="flex items-center justify-center py-20">
                    <div class="text-center max-w-md">
                        <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Your Cart is Empty</h2>
                        <p class="text-gray-600 mb-8">Looks like you haven't added anything to your cart yet. Start
                            shopping to fill it up!</p>
                        <a href="{{ route('products') }}"
                            class="inline-flex items-center px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
