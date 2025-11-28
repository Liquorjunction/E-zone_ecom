<header @keydown.escape.window="accountOpen = false; languageOpen = false"
    class="bg-white shadow-lg sticky top-0 z-50 border-b-2 border-primary container-query">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-1 sm:py-1">
        <div class="flex items-center justify-between">

            <div class="flex items-center space-x-2 sm:space-x-4">

                <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden lg:flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 transition-all duration-300 text-primary touch:p-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-300" :class="{ 'rotate-90': sidebarCollapsed }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="flex items-center group">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                        class="w-20 h-12 xs:w-24 xs:h-16 sm:w-28 sm:h-18 lg:w-32 lg:h-20 object-contain transition-transform duration-300 group-hover:scale-105">
                </a>

                <nav class="hidden lg:flex items-center space-x-6 xl:space-x-8">
                    <a href="{{ route('support') }}"
                        class="relative font-medium text-responsive-sm transition-colors duration-300 group {{ request()->routeIs('support') ? 'text-primary' : 'text-gray-700 hover:text-primary' }}">
                        Support
                        <span
                            class="absolute bottom-0 left-0 h-0.5 bg-primary transition-all duration-300 {{ request()->routeIs('support') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                </nav>
            </div>

            <div class="hidden md:flex flex-1 max-w-xs lg:max-w-md xl:max-w-lg mx-4 lg:mx-6">
                <div class="relative w-full group">
                    <input type="text" placeholder="Search for electronics, deals..."
                        class="w-full pl-10 lg:pl-12 pr-4 py-2 lg:py-2.5 text-responsive-sm border-2 border-gray-200 rounded-full focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
                    <svg class="w-4 h-4 lg:w-5 lg:h-5 absolute left-3 lg:left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3">

                <div class="hidden md:flex items-center space-x-2 lg:space-x-3">

                    <div class="relative" @mouseleave="languageOpen = false">
                        <button @click="languageOpen = !languageOpen"
                            class="flex items-center gap-1 lg:gap-2 p-1.5 lg:p-2 rounded-full hover:bg-gray-100 text-gray-700 transition-colors touch:p-3"
                            title="Language">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c.5 0 .99.05 1.465.14M12 21c-.5 0-.99.05-1.465.14M12 3a9.004 9.004 0 0 0-8.716 6.747M12 3a9.004 9.004 0 0 1 8.716 6.747M12 3c.5 0 .99-.05 1.465-.14M12 3c-.5 0-.99-.05-1.465-.14M12 6a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm0 0a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm0 0a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                            </svg>
                            <span class="uppercase font-bold text-xs lg:text-sm">{{ session('locale', 'en') }}</span>
                        </button>

                        <div x-show="languageOpen" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2" x-cloak
                            class="absolute right-0 top-full pt-2 w-48 z-50">

                            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 p-2">
                                <a href="{{ route('lang.switch', 'en') }}"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'en' ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 text-gray-600' }}">
                                    <span>English (EN)</span>
                                </a>
                                <a href="{{ route('lang.switch', 'fr') }}"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'fr' ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 text-gray-600' }}">
                                    <span>French (FR)</span>
                                </a>
                                <a href="{{ route('lang.switch', 'ar') }}"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'ar' ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 text-gray-600' }}">
                                    <span>Arabic (AR)</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('product-compare') }}"
                        class="flex items-center justify-center p-1.5 lg:p-2 rounded-full hover:bg-gray-100 text-gray-700 transition-colors touch:p-3"
                        title="Compare Products">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001a.75.75 0 0 1 .75.75c0 .414-.336.75-.75.75h-4.992a2.25 2.25 0 0 0-2.25 2.25v.001a2.25 2.25 0 0 0 2.25 2.25h4.992a.75.75 0 0 1 0 1.5h-4.992a3.75 3.75 0 0 1-3.75-3.75v-.001a3.75 3.75 0 0 1 3.75-3.75Zm-6.046 9.152H4.981v.001a.75.75 0 0 1-.75-.75c0-.414.336-.75.75-.75h4.992a2.25 2.25 0 0 0 2.25-2.25v-.001a2.25 2.25 0 0 0-2.25-2.25H4.981a.75.75 0 0 1 0-1.5h4.992a3.75 3.75 0 0 1 3.75 3.75v.001a3.75 3.75 0 0 1-3.75 3.75Z" />
                        </svg>
                    </a>

                    <a href="{{ route('dashboard.wishlist') }}"
                        class="flex items-center justify-center p-1.5 lg:p-2 rounded-full hover:bg-gray-100 text-gray-700 transition-colors touch:p-3"
                        title="My Wishlist">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </a>

                    <a href="{{ route('cart') }}"
                        class="relative flex items-center justify-center p-1.5 lg:p-2 rounded-full hover:bg-gray-100 text-gray-700 transition-colors touch:p-3"
                        title="My Cart">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-bold">0</span>
                    </a>
                </div>

                @auth
                    <div class="hidden md:block relative ml-3" @mouseleave="accountOpen = false">
                        <button @click="accountOpen = !accountOpen"
                            class="flex items-center p-2 rounded-full hover:bg-gray-100 transition-colors">
                            <img src="https://via.placeholder.com/32x32" alt="User Avatar" class="w-8 h-8 rounded-full">
                            <svg class="w-4 h-4 ml-1 text-gray-700" :class="{ 'rotate-180': accountOpen }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
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
                            class="absolute right-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-lg border-2 border-gray-100 p-4"
                            x-cloak>
                            <div class="pb-3 mb-3 border-b-2 border-gray-100">
                                <p class="font-semibold text-gray-900">John Doe</p>
                                <p class="text-sm text-gray-600">john.doe@example.com</p>
                            </div>
                            <nav class="space-y-1">
                                <a href="{{ route('dashboard.overview') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('dashboard.orders') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    <span>My Orders</span>
                                </a>
                                <a href="{{ route('dashboard.profile') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>My Profile</span>
                                </a>
                                <a href="#"
                                    class="flex items-center gap-3 px-3 py-2 text-red-500 hover:bg-red-50 hover:text-red-600 font-medium rounded-lg transition-colors mt-2 border-t-2 border-gray-100 pt-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Logout</span>
                                </a>
                            </nav>
                        </div>
                    </div>
                @else
                    <div class="hidden md:flex items-center space-x-2 lg:space-x-3">
                        <a href="{{ route('login') }}"
                            class="font-medium px-3 lg:px-4 py-1.5 lg:py-2 rounded-full transition-all duration-300 hover:bg-gray-100 text-primary text-responsive-sm">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                            class="text-white px-4 lg:px-6 py-2 lg:py-2.5 rounded-full font-medium shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl bg-primary text-responsive-sm">
                            Register
                        </a>
                    </div>
                @endauth

                <div class="md:hidden flex items-center space-x-1 sm:space-x-2">
                    <a href="{{ route('cart') }}"
                        class="relative flex items-center justify-center p-2 rounded-full hover:bg-gray-100 text-gray-700 transition-colors touch:p-3"
                        title="My Cart">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-bold">0</span>
                    </a>

                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-all duration-300 touch:p-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                {{-- Mobile Menu Dropdown --}}
                <div x-show="mobileMenuOpen"
                     x-cloak
                     @click.away="mobileMenuOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="md:hidden absolute top-full right-4 mt-2 w-64 bg-white rounded-2xl shadow-lg border-2 border-gray-100 p-4 z-50">

                    {{-- Language Switcher --}}
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ gtrans('Language') }}</h3>
                        <div class="space-y-1">
                            <a href="{{ route('lang.switch', 'en') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'en' ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 text-gray-600' }}">
                                <span>English (EN)</span>
                            </a>
                            <a href="{{ route('lang.switch', 'fr') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'fr' ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 text-gray-600' }}">
                                <span>French (FR)</span>
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ app()->getLocale() == 'ar' ? 'bg-primary/10 text-primary' : 'hover:bg-gray-100 text-gray-600' }}">
                                <span>Arabic (AR)</span>
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        @auth
                            {{-- Authenticated User Menu --}}
                            <div class="space-y-2">
                                <a href="{{ route('dashboard.overview') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    <span>{{ gtrans('Dashboard') }}</span>
                                </a>
                                <a href="{{ route('dashboard.orders') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    <span>{{ gtrans('My Orders') }}</span>
                                </a>
                                <a href="{{ route('dashboard.profile') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ gtrans('My Profile') }}</span>
                                </a>
                                <a href="{{ route('dashboard.wishlist') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span>{{ gtrans('My Wishlist') }}</span>
                                </a>
                                <a href="#"
                                    class="flex items-center gap-3 px-3 py-2 text-red-500 hover:bg-red-50 hover:text-red-600 font-medium rounded-lg transition-colors border-t border-gray-100 pt-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>{{ gtrans('Logout') }}</span>
                                </a>
                            </div>
                        @else
                            {{-- Guest User Menu --}}
                            <div class="space-y-2">
                                <a href="{{ route('login') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>{{ gtrans('Login') }}</span>
                                </a>
                                <a href="{{ route('register') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-primary hover:bg-primary/10 font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    <span>{{ gtrans('Register') }}</span>
                                </a>
                            </div>
                        @endauth

                        {{-- Additional Links --}}
                        <div class="border-t border-gray-100 pt-3 mt-3 space-y-2">
                            <a href="{{ route('support') }}"
                                class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ gtrans('Support') }}</span>
                            </a>
                            <a href="{{ route('product-compare') }}"
                                class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16.023 9.348h4.992v-.001a.75.75 0 0 1 .75.75c0 .414-.336.75-.75.75h-4.992a2.25 2.25 0 0 0-2.25 2.25v.001a2.25 2.25 0 0 0 2.25 2.25h4.992a.75.75 0 0 1 0 1.5h-4.992a3.75 3.75 0 0 1-3.75-3.75v-.001a3.75 3.75 0 0 1 3.75-3.75Zm-6.046 9.152H4.981v.001a.75.75 0 0 1-.75-.75c0-.414.336-.75.75-.75h4.992a2.25 2.25 0 0 0 2.25-2.25v-.001a2.25 2.25 0 0 0-2.25-2.25H4.981a.75.75 0 0 1 0-1.5h4.992a3.75 3.75 0 0 1 3.75 3.75v.001a3.75 3.75 0 0 1-3.75 3.75Z" />
                                </svg>
                                <span>{{ gtrans('Compare Products') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:hidden mt-3">
            <div class="relative">
                <input type="text" placeholder="Search products..."
                    class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-opacity-20 bg-white">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</header>
