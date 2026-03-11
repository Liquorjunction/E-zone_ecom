@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
                            Checkout
                        </h1>
                        <p class="text-gray-600">Complete your purchase securely</p>
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
                            <span class="text-primary font-medium">Checkout</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8 bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                <div class="flex items-center justify-between relative">
                    <!-- Step 1 - Complete -->
                    <div class="flex flex-col items-center z-10 flex-1">
                        <div
                            class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="mt-2 text-sm font-semibold text-green-500">Shopping Cart</span>
                    </div>

                    <!-- Connector Line (Half Complete) -->
                    <div class="absolute top-6 left-0 right-0 h-0.5 bg-gray-200 z-0" style="margin: 0 10%;"></div>
                    <div class="absolute top-6 left-0 h-0.5 bg-primary z-0" style="width: 43%; margin-left: 10%;"></div>

                    <!-- Step 2 - Active -->
                    <div class="flex flex-col items-center z-10 flex-1">
                        <div
                            class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg shadow-lg">
                            2
                        </div>
                        <span class="mt-2 text-sm font-semibold text-primary">Checkout</span>
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

            <form>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left Column - Forms -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Contact Information -->
                        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
                                Contact Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email Address *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                            </svg>
                                        </div>
                                        <input type="email" id="email" name="email" required
                                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="you@example.com">
                                    </div>
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Phone Number *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <input type="tel" id="phone" name="phone" required
                                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="+1 (555) 000-0000">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
                                Shipping Address
                            </h2>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="firstName" class="block text-sm font-semibold text-gray-700 mb-2">
                                            First Name *
                                        </label>
                                        <input type="text" id="firstName" name="firstName" required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="John">
                                    </div>

                                    <div>
                                        <label for="lastName" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Last Name *
                                        </label>
                                        <input type="text" id="lastName" name="lastName" required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="Doe">
                                    </div>
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Street Address *
                                    </label>
                                    <input type="text" id="address" name="address" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                        placeholder="123 Main Street">
                                </div>

                                <div>
                                    <label for="apartment" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Apartment, Suite, etc. (Optional)
                                    </label>
                                    <input type="text" id="apartment" name="apartment"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                        placeholder="Apt 4B">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                            City *
                                        </label>
                                        <input type="text" id="city" name="city" required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="New York">
                                    </div>

                                    <div>
                                        <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">
                                            State *
                                        </label>
                                        <select id="state" name="state" required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
                                            <option value="">Select State</option>
                                            <option value="NY">New York</option>
                                            <option value="CA">California</option>
                                            <option value="TX">Texas</option>
                                            <option value="FL">Florida</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="zip" class="block text-sm font-semibold text-gray-700 mb-2">
                                            ZIP Code *
                                        </label>
                                        <input type="text" id="zip" name="zip" required
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="10001">
                                    </div>
                                </div>

                                <div>
                                    <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Country *
                                    </label>
                                    <select id="country" name="country" required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="UK">United Kingdom</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
                                Shipping Method
                            </h2>

                            <div class="space-y-3">
                                <label
                                    class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-primary group">
                                    <input type="radio" name="shipping" value="standard" checked
                                        class="mt-1 w-5 h-5 text-primary focus:ring-primary">
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <span
                                                class="font-semibold text-gray-900 group-hover:text-primary transition-colors">Standard
                                                Shipping</span>
                                            <span class="font-bold text-primary">FREE</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Delivery in 5-7 business days</p>
                                    </div>
                                </label>

                                <label
                                    class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-primary group">
                                    <input type="radio" name="shipping" value="express"
                                        class="mt-1 w-5 h-5 text-primary focus:ring-primary">
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <span
                                                class="font-semibold text-gray-900 group-hover:text-primary transition-colors">Express
                                                Shipping</span>
                                            <span class="font-bold text-gray-900">$15.00</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Delivery in 2-3 business days</p>
                                    </div>
                                </label>

                                <label
                                    class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-primary group">
                                    <input type="radio" name="shipping" value="overnight"
                                        class="mt-1 w-5 h-5 text-primary focus:ring-primary">
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <span
                                                class="font-semibold text-gray-900 group-hover:text-primary transition-colors">Overnight
                                                Shipping</span>
                                            <span class="font-bold text-gray-900">$30.00</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Next business day delivery</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="w-1 h-6 rounded-full bg-primary mr-3"></span>
                                Payment Method
                            </h2>

                            <div class="space-y-3 mb-6">
                                <label
                                    class="flex items-center p-4 border-2 border-primary rounded-xl cursor-pointer bg-primary/5">
                                    <input type="radio" name="payment_method" value="card" checked
                                        class="w-5 h-5 text-primary focus:ring-primary">
                                    <span class="ml-3 font-semibold text-gray-900 flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        Credit / Debit Card
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-primary group">
                                    <input type="radio" name="payment_method" value="paypal"
                                        class="w-5 h-5 text-primary focus:ring-primary">
                                    <span
                                        class="ml-3 font-semibold text-gray-900 group-hover:text-primary transition-colors flex items-center">
                                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z" />
                                        </svg>
                                        PayPal
                                    </span>
                                </label>

                                <label
                                    class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-primary group">
                                    <input type="radio" name="payment_method" value="cod"
                                        class="w-5 h-5 text-primary focus:ring-primary">
                                    <span
                                        class="ml-3 font-semibold text-gray-900 group-hover:text-primary transition-colors flex items-center">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Cash on Delivery
                                    </span>
                                </label>
                            </div>

                            <!-- Card Details Form -->
                            <div id="cardDetails" class="space-y-4">
                                <div>
                                    <label for="cardNumber" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Card Number *
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="cardNumber" name="cardNumber"
                                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="1234 5678 9012 3456">
                                    </div>
                                </div>

                                <div>
                                    <label for="cardName" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Name on Card *
                                    </label>
                                    <input type="text" id="cardName" name="cardName"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                        placeholder="John Doe">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="expiry" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Expiry Date *
                                        </label>
                                        <input type="text" id="expiry" name="expiry"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="MM/YY">
                                    </div>

                                    <div>
                                        <label for="cvv" class="block text-sm font-semibold text-gray-700 mb-2">
                                            CVV *
                                        </label>
                                        <input type="text" id="cvv" name="cvv" maxlength="3"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                            placeholder="123">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                                Order Summary
                            </h2>

                            <!-- Products List -->
                            <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-100">
                                <div class="flex gap-3">
                                    <img src="https://via.placeholder.com/80x80?text=Product+1" alt="Product"
                                        class="w-20 h-20 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-sm mb-1">Premium Wireless Headphones
                                        </h3>
                                        <p class="text-xs text-gray-500 mb-2">Qty: 1</p>
                                        <p class="font-bold text-primary">$199.99</p>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <img src="https://via.placeholder.com/80x80?text=Product+2" alt="Product"
                                        class="w-20 h-20 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-sm mb-1">Smart Watch Series 8</h3>
                                        <p class="text-xs text-gray-500 mb-2">Qty: 2</p>
                                        <p class="font-bold text-primary">$399.00</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
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

                            <!-- Place Order Button -->
                            <button type="submit"
                                class="w-full px-6 py-4 bg-primary text-white font-bold rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl mb-4">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Place Secure Order
                                </span>
                            </button>

                            <!-- Back to Cart -->
                            <a href="{{ route('cart') }}"
                                class="block w-full px-6 py-3 border-2 border-gray-200 text-gray-700 text-center font-semibold rounded-xl transition-all duration-300 hover:border-primary hover:text-primary">
                                Back to Cart
                            </a>

                            <!-- Security Badges -->
                            <div class="mt-6 pt-6 border-t-2 border-gray-100">
                                <p class="text-xs text-gray-500 text-center mb-4">We accept</p>
                                <div class="flex justify-center items-center space-x-3 mb-4">
                                    <svg class="h-8" viewBox="0 0 48 32" fill="none">
                                        <rect width="48" height="32" rx="4" fill="#1434CB" />
                                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
                                            fill="white" font-weight="bold" font-size="14">VISA</text>
                                    </svg>
                                    <svg class="h-8" viewBox="0 0 48 32" fill="none">
                                        <rect width="48" height="32" rx="4" fill="#EB001B" />
                                        <circle cx="18" cy="16" r="10" fill="#FF5F00" />
                                        <circle cx="30" cy="16" r="10" fill="#F79E1B" />
                                    </svg>
                                    <svg class="h-8" viewBox="0 0 48 32" fill="none">
                                        <rect width="48" height="32" rx="4" fill="#0072CE" />
                                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
                                            fill="white" font-weight="bold" font-size="10">AMEX</text>
                                    </svg>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        256-bit SSL Encryption
                                    </div>
                                    <div class="flex items-center justify-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        PCI DSS Compliant
                                    </div>
                                    <div class="flex items-center justify-center text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        30-Day Money Back Guarantee
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <!-- Trust Section -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Secure Payment</h3>
                    <p class="text-sm text-gray-600">Your payment information is encrypted and secure with
                        industry-standard protocols</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Free Shipping</h3>
                    <p class="text-sm text-gray-600">Enjoy free standard shipping on all orders over $50 with no hidden
                        fees</p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">24/7 Support</h3>
                    <p class="text-sm text-gray-600">Our customer support team is available around the clock to assist you
                    </p>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-linear-to-r from-primary/5 to-primary/10 rounded-2xl p-6 border-2 border-primary/20">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Need Help with Your Order?</h3>
                        <p class="text-sm text-gray-600">Our support team is ready to assist you with any questions</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="#"
                            class="flex items-center px-6 py-3 bg-white border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Live Chat
                        </a>
                        <a href="#"
                            class="flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Call Us
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle card details based on payment method selection
            document.addEventListener('DOMContentLoaded', function() {
                const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
                const cardDetails = document.getElementById('cardDetails');

                paymentRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'card') {
                            cardDetails.style.display = 'block';
                        } else {
                            cardDetails.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
