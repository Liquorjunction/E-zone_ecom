@extends('client.layouts.app')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-white min-h-screen">
        <div class="container mx-auto px-4 py-4 lg:py-8">

            <x-breadcrumbs>
                <x-slot:title>@yield('dashboard-title', 'My Account')</x-slot:title>
                <x-slot:content>Welcome Back {{Auth::user()->name }}</x-slot:content>
            </x-breadcrumbs>

            <div class="flex flex-col lg:flex-row lg:gap-6">

                {{-- Mobile Bottom Navigation --}}
                <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-100 shadow-lg z-50">
                    <div class="grid grid-cols-5 gap-1 p-2">
                        <a href="{{ route('dashboard.overview') }}"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-colors
                            {{ request()->routeIs('dashboard.overview') ? 'text-primary bg-primary/10' : 'text-gray-600' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span class="text-xs mt-1 font-medium">Home</span>
                        </a>

                        <a href="{{ route('dashboard.orders') }}"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-colors
                            {{ request()->routeIs('dashboard.orders') ? 'text-primary bg-primary/10' : 'text-gray-600' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="text-xs mt-1 font-medium">Orders</span>
                        </a>

                        <a href="{{ route('dashboard.wishlist') }}"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-colors
                            {{ request()->routeIs('dashboard.wishlist') ? 'text-primary bg-primary/10' : 'text-gray-600' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="text-xs mt-1 font-medium">Wishlist</span>
                        </a>

                        <a href="{{ route('dashboard.profile') }}"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-colors
                            {{ request()->routeIs('dashboard.profile') ? 'text-primary bg-primary/10' : 'text-gray-600' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-xs mt-1 font-medium">Profile</span>
                        </a>

                        <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                            class="flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-colors text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span class="text-xs mt-1 font-medium">More</span>
                        </button>
                    </div>
                </nav>

                {{-- Mobile Slide-out Menu --}}
                <div id="mobile-menu" class="hidden lg:hidden fixed inset-0 bg-black bg-opacity-50 z-50" onclick="this.classList.add('hidden')">
                    <div class="absolute right-0 top-0 bottom-0 w-80 bg-white shadow-xl" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-between p-6 border-b-2 border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900">Menu</h3>
                            <button onclick="document.getElementById('mobile-menu').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-4 border-b-2 border-gray-100">
                            <div class="flex items-center gap-3">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Str::startsWith(Auth::user()->avatar, ['http://', 'https://']) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}" alt="User Avatar" class="w-12 h-12 rounded-full">
                                @else
                                    <img src="{{ asset('images/avatar-placeholder.png') }}" alt="User Avatar" class="w-12 h-12 rounded-full">
                                @endif
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ Auth::user()->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <nav class="p-4 space-y-2 overflow-y-auto" style="max-height: calc(100vh - 200px)">
                            <a href="{{ route('dashboard.addresses') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.addresses') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Saved Addresses</span>
                            </a>

                            <a href="{{ route('dashboard.payments') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.payments') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span>Payment Methods</span>
                            </a>

                            {{-- <a href="{{ route('dashboard.rewards') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.rewards') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>My Rewards</span>
                            </a> --}}

                            <a href="{{ route('dashboard.security') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.security') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Security</span>
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </a>
                        </nav>
                    </div>
                </div>

                {{-- Desktop Sidebar --}}
                <aside class="hidden lg:block lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 sticky top-24">

                        <div class="flex items-center gap-4 mb-6 pb-6 border-b-2 border-gray-100">
                            @if(Auth::user()->avatar)
                                <img src="{{ Str::startsWith(Auth::user()->avatar, ['http://', 'https://']) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}" alt="User Avatar" class="w-16 h-16 rounded-full">
                            @else
                                <img src="{{ asset('images/avatar-placeholder.png') }}" alt="User Avatar" class="w-16 h-16 rounded-full">
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <nav class="space-y-2">
                            <a href="{{ route('dashboard.overview') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.overview') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('dashboard.orders') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.orders') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <span>My Orders</span>
                            </a>

                            <a href="{{ route('dashboard.profile') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.profile') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>My Profile</span>
                            </a>

                            <a href="{{ route('dashboard.addresses') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.addresses') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Saved Addresses</span>
                            </a>

                            <a href="{{ route('dashboard.payments') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.payments') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span>Payment Methods</span>
                            </a>

                            <a href="{{ route('dashboard.wishlist') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.wishlist') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span>Wishlist</span>
                            </a>

                            <a href="{{ route('dashboard.rewards') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.rewards') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>My Rewards</span>
                            </a>

                            <a href="{{ route('dashboard.security') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors
                                {{ request()->routeIs('dashboard.security') ? 'bg-primary/10 text-primary font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Security</span>
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </a>
                        </nav>
                    </div>
                </aside>

                <main class="flex-1 space-y-4 lg:space-y-6 pb-20 lg:pb-8">
                    @yield('dashboard-content')
                </main>

            </div>
        </div>
    </div>
@endsection
