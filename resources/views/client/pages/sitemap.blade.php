@extends('client.layouts.app')

@section('title', statictranslations('Sitemap') . ' - ' . statictranslations('Ezone Electronics'))
@section('description', statictranslations('Complete sitemap of Ezone Electronics. Find all pages including products, account management, support, and more.'))

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">

            <x-breadcrumbs>
                <x-slot:title>{{ statictranslations('Sitemap') }}</x-slot:title>
                <x-slot:content>{{ statictranslations('A list of all the pages on our website') }}</x-slot:content>
            </x-breadcrumbs>

            <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 space-y-8">

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                        {{ statictranslations('Main Pages') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('home') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4a1 1 0 001-1v-1a1 1 0 10-2 0v1a1 1 0 001 1zm5 0a1 1 0 001-1v-1a1 1 0 10-2 0v1a1 1 0 001 1z" />
                            </svg>
                            <span>{{ statictranslations('Home') }}</span>
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>{{ statictranslations('All Products (Category)') }}</span>
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>{{ statictranslations('Product Detail Page') }}</span>
                        </a>
                        <a href="{{ route('product-compare') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ statictranslations('Compare Products') }}</span>
                        </a>
                        <a href="{{ route('search') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>{{ statictranslations('Search Results') }}</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                        {{ statictranslations('Product Collections') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('new-arrivals') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>{{ statictranslations('New Arrivals') }}</span>
                        </a>
                        <a href="{{ route('best-sellers') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>{{ statictranslations('Best Sellers') }}</span>
                        </a>
                        <a href="{{ route('deals') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>{{ statictranslations('Deals & Offers') }}</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                        {{ statictranslations('Checkout Process') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('cart') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ statictranslations('1. Shopping Cart') }}</span>
                        </a>
                        <a href="{{ route('checkout') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>{{ statictranslations('2. Checkout') }}</span>
                        </a>
                        <a href="{{ route('checkout-process') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>{{ statictranslations('3. Checkout Process') }}</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                        {{ statictranslations('My Account') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('login') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>{{ statictranslations('Login') }}</span>
                        </a>
                        <a href="{{ route('register') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span>{{ statictranslations('Register') }}</span>
                        </a>
                        <a href="{{ route('dashboard.overview') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>{{ statictranslations('Dashboard') }}</span>
                        </a>
                        <a href="{{ route('dashboard.orders') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>{{ statictranslations('My Orders') }}</span>
                        </a>
                        <a href="{{ route('order-detail', ['order' => 1]) }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>{{ statictranslations('Order Detail') }}</span>
                        </a>
                        <a href="{{ route('dashboard.profile') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ statictranslations('My Profile') }}</span>
                        </a>
                        <a href="{{ route('dashboard.addresses') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ statictranslations('Saved Addresses') }}</span>
                        </a>
                        <a href="{{ route('dashboard.payments') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>{{ statictranslations('Payment Methods') }}</span>
                        </a>
                        <a href="{{ route('dashboard.wishlist') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span>{{ statictranslations('My Wishlist') }}</span>
                        </a>
                        <a href="{{ route('dashboard.rewards') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ statictranslations('My Rewards') }}</span>
                        </a>
                        <a href="{{ route('dashboard.security') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>{{ statictranslations('Security') }}</span>
                        </a>
                        <a href="{{ route('order-tracking') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>{{ statictranslations('Order Tracking') }}</span>
                        </a>
                        <a href="{{ route('order-detail', ['order' => 1]) }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>{{ statictranslations('Order Detail') }}</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-1 h-7 rounded-full bg-primary mr-3"></span>
                        {{ statictranslations('Company & Help') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('about') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ statictranslations('About Us') }}</span>
                        </a>
                        <a href="{{ route('showrooms') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ statictranslations('Our Showrooms') }}</span>
                        </a>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ statictranslations('Contact Us') }}</span>
                        </a>
                        <a href="{{ route('support') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ statictranslations('Help & Support') }}</span>
                        </a>
                        <a href="{{ route('faq') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.79 4 4 0 1.847-1.133 3.37-2.835 3.844M12 8v.01M12 16v.01" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ statictranslations('FAQ') }}</span>
                        </a>
                        <a href="{{ route('shipping-returns') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m-3 3V9a4 4 0 014-4h4m0 0l-3 3m3-3l-3-3" />
                            </svg>
                            <span>{{ statictranslations('Shipping & Returns') }}</span>
                        </a>
                        <a href="{{ route('privacy-policy') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>{{ statictranslations('Privacy Policy') }}</span>
                        </a>
                        <a href="{{ route('terms') }}"
                            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 hover:text-primary font-medium rounded-xl transition-colors border-2 border-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>{{ statictranslations('Terms of Service') }}</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
