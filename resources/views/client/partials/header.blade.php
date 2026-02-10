<header
    class="bg-white shadow-md sticky top-0 z-50 container-query after:content-[''] after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:bg-primary after:z-40 lg:after:block">

    <div class="bg-linear-to-r from-primary to-primary-dark text-white py-2.5 overflow-hidden">
        @if($siteContent->infotext ?? false)
            <div class="relative flex items-center justify-center space-x-2">
                <!-- Animated scrolling text container -->
                <div class="flex items-center gap-8 animate-scroll whitespace-nowrap">
                    @for($i = 0; $i < 3; $i++)
                        <div class="flex items-center space-x-2 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="text-xs sm:text-sm font-semibold">{{ $siteContent->infotext }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>

    <style>
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-33.333%);
            }
        }

        .animate-scroll {
            animation: scroll 20s linear infinite;
        }

        .animate-scroll:hover {
            animation-play-state: paused;
        }
    </style>

    <div class="container max-w-screen mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-center justify-between gap-4 z-50">

            <div class="flex items-center space-x-3 sm:space-x-4">
                <a href="{{ route('home') }}" class="flex items-center group">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                        class="w-24 h-14 xs:w-28 xs:h-16 sm:w-32 sm:h-18 lg:w-36 lg:h-20 object-contain transition-transform duration-300 group-hover:scale-105">
                </a>
            </div>

            <div class="hidden md:flex flex-1 max-w-xl mx-4">
                <div class="relative w-full group">
                    <input type="text" placeholder="Search for electronics, deals..."
                        class="w-full pl-12 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-gray-50 transition-all duration-300">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-primary transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="flex items-center space-x-1 sm:space-x-2">

                <div class="hidden md:flex items-center space-x-1">

                    <div class="relative"
                        @click.away="languageOpen = false; if(languageCloseTimeout){ clearTimeout(languageCloseTimeout); languageCloseTimeout = null }"
                        @mouseenter="languageOpen = true; if(languageCloseTimeout){ clearTimeout(languageCloseTimeout); languageCloseTimeout = null }"
                        @mouseleave="languageCloseTimeout = setTimeout(function(){ languageOpen = false }, 150)">
                        <button @click="languageOpen = !languageOpen"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-700 transition-colors"
                            title="Language">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                            <span class="uppercase font-semibold text-xs">{{ session('locale', 'en') }}</span>
                            <svg class="w-3 h-3" :class="{ 'rotate-180': languageOpen }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div x-show="languageOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2" x-cloak
                            class="absolute right-0 top-full mt-2 w-48 z-50">
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 py-2">
                                <a href="{{ route('lang.switch', 'en') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 transition-colors {{ app()->getLocale() == 'en' ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-gray-50 text-gray-700' }}">
                                    <span>English (EN)</span>
                                </a>
                                <a href="{{ route('lang.switch', 'fr') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 transition-colors {{ app()->getLocale() == 'fr' ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-gray-50 text-gray-700' }}">
                                    <span>French (FR)</span>
                                </a>
                                <a href="{{ route('lang.switch', 'ar') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 transition-colors {{ app()->getLocale() == 'ar' ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-gray-50 text-gray-700' }}">
                                    <span>Arabic (AR)</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('product-compare') }}"
                        class="p-2.5 rounded-lg hover:bg-gray-100 text-gray-700 transition-colors group"
                        title="Compare Products">
                        <svg class="w-5 h-5 group-hover:text-primary transition-colors" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="7" width="7" height="13" rx="2" />
                            <rect x="14" y="3" width="7" height="17" rx="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 15h4" />
                        </svg>
                    </a>

                    <a href="{{ route('dashboard.wishlist') }}"
                        class="relative p-2.5 rounded-lg hover:bg-gray-100 text-gray-700 transition-colors group"
                        title="My Wishlist">
                        <svg class="w-5 h-5 group-hover:text-red-500 transition-colors" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z" />
                        </svg>
                        <span
                            class="wishlist-counter absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg">{{ $wishlistCount ?? 0 }}</span>
                    </a>


                    <a href="{{ route('cart') }}"
                        class="relative p-2.5 rounded-lg hover:bg-gray-100 text-gray-700 transition-colors group"
                        title="My Cart">
                        <svg class="w-5 h-5 group-hover:text-primary transition-colors" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span
                            class="cart-counter absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold shadow-lg">{{ $cartItemCount ?? 0 }}</span>
                    </a>
                </div>

                @auth
                    <div class="hidden md:block relative"
                        @click.away="accountOpen = false; if(accountCloseTimeout){ clearTimeout(accountCloseTimeout); accountCloseTimeout = null }"
                        @mouseenter="accountOpen = true; if(accountCloseTimeout){ clearTimeout(accountCloseTimeout); accountCloseTimeout = null }"
                        @mouseleave="accountCloseTimeout = setTimeout(function(){ accountOpen = false }, 150)">
                        <button @click="accountOpen = !accountOpen"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            @if (Auth::user()->avatar)
                                <img src="{{ Str::startsWith(Auth::user()->avatar, ['http://', 'https://']) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}"
                                    alt="User Avatar" class="w-8 h-8 rounded-full ring-2 ring-gray-200">
                            @else
                                <img src="{{ asset('images/avatar-placeholder.png') }}" alt="User Avatar"
                                    class="w-8 h-8 rounded-full ring-2 ring-gray-200">
                            @endif
                            <svg class="w-4 h-4 text-gray-700 transition-transform" :class="{ 'rotate-180': accountOpen }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <div x-show="accountOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-3 z-50"
                            x-cloak>
                            <div class="px-4 pb-3 mb-3 border-b border-gray-100">
                                <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                            </div>
                            <nav class="px-2 space-y-1">
                                <a href="{{ route('dashboard.overview') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    <span>{{ gtrans('Dashboard') }}</span>
                                </a>
                                <a href="{{ route('dashboard.orders') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>{{ gtrans('My Orders') }}</span>
                                </a>
                                <a href="{{ route('dashboard.profile') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ gtrans('My Profile') }}</span>
                                </a>
                                <a href="{{ route('dashboard.wishlist') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span>{{ gtrans('My Wishlist') }}</span>
                                </a>
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center gap-3 px-3 py-2.5 text-red-500 hover:bg-red-50 font-medium rounded-lg transition-colors w-full text-left">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>{{ gtrans('Logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </nav>
                        </div>
                    </div>
                @else
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-300 bg-primary text-white hover:bg-primary-dark shadow-md hover:shadow-lg transform hover:scale-105">
                            {{ gtrans('Login') }}
                        </a>
                    </div>
                @endauth

                <div class="md:hidden flex items-center space-x-2">
                    <a href="{{ route('cart') }}"
                        class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-700 transition-colors"
                        title="My Cart">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span
                            class="cart-counter absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">{{ $cartItemCount ?? 0 }}</span>
                    </a>

                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <div x-show="mobileMenuOpen" x-cloak @click.away="mobileMenuOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="md:hidden absolute top-full right-4 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-200 py-4 z-50">

                    <div class="px-4 mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ gtrans('Language') }}</h3>
                        <div class="space-y-1">
                            <a href="{{ route('lang.switch', 'en') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'en' ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-gray-50 text-gray-600' }}">
                                <span>English (EN)</span>
                            </a>
                            <a href="{{ route('lang.switch', 'fr') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'fr' ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-gray-50 text-gray-600' }}">
                                <span>French (FR)</span>
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'ar' ? 'bg-primary/10 text-primary font-medium' : 'hover:bg-gray-50 text-gray-600' }}">
                                <span>Arabic (AR)</span>
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4 px-2">
                        @auth
                            <div class="space-y-1">
                                <a href="{{ route('dashboard.overview') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    <span>{{ gtrans('Dashboard') }}</span>
                                </a>
                                <a href="{{ route('dashboard.orders') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>{{ gtrans('My Orders') }}</span>
                                </a>
                                <a href="{{ route('dashboard.profile') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ gtrans('My Profile') }}</span>
                                </a>
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center gap-3 px-3 py-2.5 text-red-500 hover:bg-red-50 font-medium rounded-lg transition-colors w-full text-left">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>{{ gtrans('Logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="space-y-1">
                                <a href="{{ route('login') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-primary font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>{{ gtrans('Login') }}</span>
                                </a>
                                <a href="{{ route('register') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-white bg-primary hover:bg-primary-dark font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    <span>{{ gtrans('Register') }}</span>
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <div class="md:hidden mt-3">
            <div class="relative">
                <input type="text" placeholder="Search products..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-gray-50 text-sm">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        <!-- bottom stripe removed - rendered via CSS pseudo-element -->
    </div>
    <script>
        (function() {
            // Cart counter update
            function updateCartCounter(count) {
                var els = document.querySelectorAll('.cart-counter');
                els.forEach(function(el) {
                    el.textContent = (count === null || count === undefined) ? 0 : count;
                });
            }
            window.addEventListener('cart:changed', function(e) {
                var data = e && e.detail ? e.detail : null;
                var count = null;
                try {
                    if (data) {
                        if (data.summary && typeof data.summary.itemCount !== 'undefined') count = data.summary
                            .itemCount;
                        else if (typeof data.itemCount !== 'undefined') count = data.itemCount;
                        else if (typeof data === 'number') count = data;
                    }
                } catch (err) {
                    count = null;
                }
                if (count !== null) updateCartCounter(count);
            });

            // Wishlist counter update
            function updateWishlistCounter(count) {
                var els = document.querySelectorAll('.wishlist-counter');
                els.forEach(function(el) {
                    el.textContent = (count === null || count === undefined) ? 0 : count;
                });
            }
            window.addEventListener('wishlist:changed', function(e) {
                var data = e && e.detail ? e.detail : null;
                var count = null;
                try {
                    if (data) {
                        if (data.summary && typeof data.summary.itemCount !== 'undefined') count = data.summary
                            .itemCount;
                        else if (typeof data.itemCount !== 'undefined') count = data.itemCount;
                        else if (typeof data === 'number') count = data;
                    }
                } catch (err) {
                    count = null;
                }
                if (count !== null) updateWishlistCounter(count);
            });
        })();
    </script>
</header>
