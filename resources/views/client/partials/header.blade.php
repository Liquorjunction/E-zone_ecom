<header class="bg-white z-50 sticky top-0" style="box-shadow: 0 1px 0 #e5e7eb, 0 4px 24px rgba(0,0,0,0.06);">

    {{-- ══ Announcement bar — only renders when there is actual non-empty text ══ --}}
    @php $infotext = trim($siteContent->infotext ?? ''); @endphp
    @if($infotext)
    <div class="bg-linear-to-r from-primary to-primary-dark text-white overflow-hidden">
        <div class="py-2 flex items-center justify-center">
            <div class="flex items-center gap-12 animate-scroll whitespace-nowrap select-none">
                @for($i = 0; $i < 3; $i++)
                    <div class="flex items-center gap-2 shrink-0">
                        <svg class="w-3 h-3 text-yellow-300 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-xs font-medium tracking-wide">{{ $infotext }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes scroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-33.333%); }
        }
        .animate-scroll { animation: scroll 24s linear infinite; }
        .animate-scroll:hover { animation-play-state: paused; }
    </style>

    {{-- ══ Main bar ══ --}}
    <div class="px-4 sm:px-6 lg:px-8 py-2.5">
        <div class="flex items-center gap-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="shrink-0 flex items-center group">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}"
                     alt="E-Zone Logo"
                     class="h-11 sm:h-13 lg:h-14 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
            </a>

            {{-- Search --}}
            <div class="hidden md:flex flex-1 max-w-2xl mx-auto">
                <div class="relative w-full group/search">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 transition-colors duration-200 group-focus-within/search:text-primary pointer-events-none"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           placeholder="Search electronics, brands, deals..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-2xl
                                  focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 focus:bg-white
                                  transition-all duration-200 placeholder:text-gray-400 text-gray-700">
                </div>
            </div>

            {{-- ── Right cluster ── --}}
            <div class="flex items-center gap-0.5 ml-auto">

                {{-- Language (desktop) --}}
                <div class="hidden md:block relative"
                     @click.away="languageOpen = false; if(languageCloseTimeout){ clearTimeout(languageCloseTimeout); languageCloseTimeout = null }"
                     @mouseenter="languageOpen = true; if(languageCloseTimeout){ clearTimeout(languageCloseTimeout); languageCloseTimeout = null }"
                     @mouseleave="languageCloseTimeout = setTimeout(function(){ languageOpen = false }, 150)">

                    <button @click="languageOpen = !languageOpen"
                            class="flex items-center gap-1.5 h-9 px-3 rounded-xl text-gray-500 text-xs font-bold
                                   hover:bg-gray-100 hover:text-gray-800 transition-all duration-150 tracking-wide">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                        <span class="uppercase">{{ session('locale', 'en') }}</span>
                        <svg class="w-2.5 h-2.5 text-gray-400 transition-transform duration-150" :class="{ 'rotate-180': languageOpen }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="languageOpen" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 top-full mt-1.5 w-40 z-50">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 overflow-hidden">
                            @foreach(['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية'] as $code => $label)
                            <a href="{{ route('lang.switch', $code) }}"
                               class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm transition-colors duration-100
                                      {{ app()->getLocale() == $code ? 'bg-primary/8 text-primary font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="w-6 h-6 rounded-lg {{ app()->getLocale() == $code ? 'bg-primary/15 text-primary' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center text-[10px] font-bold shrink-0 uppercase">{{ $code }}</span>
                                {{ $label }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Thin separator --}}
                <div class="hidden md:block w-px h-5 bg-gray-200 mx-1.5"></div>

                {{-- Compare --}}
                <a href="{{ route('product-compare') }}"
                   title="{{ gtrans('Compare') }}"
                   class="hidden md:flex items-center justify-center w-9 h-9 rounded-xl text-gray-500
                          hover:bg-gray-100 hover:text-gray-800 transition-all duration-150 relative group/tip">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="7" width="7" height="13" rx="2"/>
                        <rect x="14" y="3" width="7" height="17" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 15h4"/>
                    </svg>
                    <span class="compare-counter absolute -top-0.5 -right-0.5 min-w-4.25 h-4.25 px-0.5
                                 bg-blue-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold
                                 leading-none shadow-sm {{ ($compareCount ?? 0) == 0 ? 'hidden' : '' }}">{{ $compareCount ?? 0 }}</span>
                    <span class="absolute top-full mt-1.5 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] font-medium rounded-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-50">{{ gtrans('Compare') }}</span>
                </a>

                {{-- Wishlist --}}
                <a href="{{ route('dashboard.wishlist') }}"
                   title="{{ gtrans('Wishlist') }}"
                   class="hidden md:flex relative items-center justify-center w-9 h-9 rounded-xl text-gray-500
                          hover:bg-red-50 hover:text-red-500 transition-all duration-150 group/tip">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="wishlist-counter absolute -top-0.5 -right-0.5 min-w-4.25 h-4.25 px-0.5
                                 bg-red-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold
                                 leading-none shadow-sm {{ ($wishlistCount ?? 0) == 0 ? 'hidden' : '' }}">{{ $wishlistCount ?? 0 }}</span>
                    <span class="absolute top-full mt-1.5 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] font-medium rounded-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-50">{{ gtrans('Wishlist') }}</span>
                </a>

                {{-- Cart --}}
                <a href="{{ route('cart') }}"
                   title="{{ gtrans('Cart') }}"
                   class="relative flex items-center justify-center w-9 h-9 rounded-xl text-gray-500
                          hover:bg-primary/8 hover:text-primary transition-all duration-150 group/tip">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="cart-counter absolute -top-0.5 -right-0.5 min-w-4.25 h-4.25 px-0.5
                                 bg-primary text-white text-[9px] rounded-full flex items-center justify-center font-bold
                                 leading-none shadow-sm shadow-primary/30">{{ $cartItemCount ?? 0 }}</span>
                    <span class="absolute top-full mt-1.5 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] font-medium rounded-lg whitespace-nowrap opacity-0 group-hover/tip:opacity-100 transition-opacity duration-150 pointer-events-none z-50">{{ gtrans('Cart') }}</span>
                </a>

                {{-- Thin separator --}}
                <div class="hidden md:block w-px h-5 bg-gray-200 mx-1.5"></div>

                {{-- Account / Login ── desktop --}}
                @auth
                    <div class="hidden md:block relative"
                         @click.away="accountOpen = false; if(accountCloseTimeout){ clearTimeout(accountCloseTimeout); accountCloseTimeout = null }"
                         @mouseenter="accountOpen = true; if(accountCloseTimeout){ clearTimeout(accountCloseTimeout); accountCloseTimeout = null }"
                         @mouseleave="accountCloseTimeout = setTimeout(function(){ accountOpen = false }, 150)">

                        <button @click="accountOpen = !accountOpen"
                                class="flex items-center gap-2 pl-1.5 pr-2.5 py-1.5 rounded-xl
                                       hover:bg-gray-100 transition-all duration-150 group">
                            {{-- Avatar --}}
                            @if(Auth::user()->avatar)
                                <img src="{{ Str::startsWith(Auth::user()->avatar, ['http://', 'https://']) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}"
                                     alt="Avatar"
                                     class="w-7 h-7 rounded-lg object-cover ring-2 ring-primary/20 shrink-0">
                            @else
                                <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center ring-2 ring-primary/15 shrink-0">
                                    <span class="text-xs font-bold text-primary leading-none">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div class="hidden lg:block text-left leading-tight">
                                <p class="text-xs font-semibold text-gray-800 max-w-18 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-gray-400">Account</p>
                            </div>
                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-150 shrink-0"
                                 :class="{ 'rotate-180': accountOpen }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="accountOpen" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 top-full mt-1.5 w-60 z-50">
                            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

                                {{-- Profile strip --}}
                                <div class="flex items-center gap-3 px-4 py-3.5 bg-linear-to-br from-primary/5 to-primary/10 border-b border-gray-100/80">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Str::startsWith(Auth::user()->avatar, ['http://', 'https://']) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}"
                                             alt="Avatar" class="w-9 h-9 rounded-xl object-cover ring-2 ring-white shadow-sm shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-primary/15 flex items-center justify-center ring-2 ring-white shadow-sm shrink-0">
                                            <span class="font-bold text-primary">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>

                                {{-- Links --}}
                                <nav class="p-1.5 space-y-0.5">
                                    @foreach([
                                        ['route' => 'dashboard.overview', 'label' => 'Dashboard',   'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                                        ['route' => 'dashboard.orders',   'label' => 'My Orders',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                        ['route' => 'dashboard.profile',  'label' => 'My Profile',   'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                        ['route' => 'dashboard.wishlist', 'label' => 'My Wishlist',  'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                    ] as $item)
                                    <a href="{{ route($item['route']) }}"
                                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700
                                              hover:bg-gray-50 hover:text-primary transition-all duration-100">
                                        <span class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                            </svg>
                                        </span>
                                        {{ gtrans($item['label']) }}
                                    </a>
                                    @endforeach
                                </nav>

                                {{-- Logout --}}
                                <div class="px-1.5 pb-1.5 pt-0">
                                    <div class="border-t border-gray-100 pt-1.5">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium
                                                           text-red-500 hover:bg-red-50 transition-all duration-100">
                                                <span class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                    </svg>
                                                </span>
                                                {{ gtrans('Logout') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- Guest: Login only --}}
                @else
                    <a href="{{ route('login') }}"
                       class="hidden md:flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white
                              bg-primary shadow-md shadow-primary/25
                              hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-px
                              transition-all duration-150 active:translate-y-0 active:shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{ gtrans('Login') }}
                    </a>
                @endauth

                {{-- Mobile hamburger --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden flex items-center justify-center w-9 h-9 rounded-xl text-gray-600
                               hover:bg-gray-100 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen"  stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>{{-- end right cluster --}}
        </div>{{-- end flex row --}}
    </div>{{-- end main bar --}}

    {{-- Bottom primary accent line --}}
    <div class="h-px bg-linear-to-r from-primary/80 via-primary/30 to-transparent"></div>

    {{-- Mobile dropdown --}}
    <div x-show="mobileMenuOpen" x-cloak
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="md:hidden absolute top-full left-0 right-0 z-50 px-4 pb-4 pt-2 bg-white border-t border-gray-100"
         style="box-shadow: 0 16px 40px rgba(0,0,0,0.10);">

        {{-- Search --}}
        <div class="relative mb-3">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Search products..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-2xl
                          focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 focus:bg-white
                          transition-all duration-200">
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-2 gap-2 mb-3">
            <a href="{{ route('dashboard.wishlist') }}"
               class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-gray-50 text-sm font-medium text-gray-700 hover:bg-primary/5 hover:text-primary transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ gtrans('Wishlist') }}
            </a>
            <a href="{{ route('product-compare') }}"
               class="relative flex items-center gap-2 px-3 py-2.5 rounded-xl bg-gray-50 text-sm font-medium text-gray-700 hover:bg-primary/5 hover:text-primary transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="7" width="7" height="13" rx="2"/>
                    <rect x="14" y="3" width="7" height="17" rx="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 15h4"/>
                </svg>
                {{ gtrans('Compare') }}
                <span class="compare-counter ml-auto min-w-4.25 h-4.25 px-0.5
                             bg-blue-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold
                             leading-none shadow-sm {{ ($compareCount ?? 0) == 0 ? 'hidden' : '' }}">{{ $compareCount ?? 0 }}</span>
            </a>
        </div>

        {{-- Language --}}
        <div class="mb-3">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 px-0.5">{{ gtrans('Language') }}</p>
            <div class="grid grid-cols-3 gap-1.5">
                @foreach(['en' => 'EN', 'fr' => 'FR', 'ar' => 'AR'] as $code => $label)
                <a href="{{ route('lang.switch', $code) }}"
                   class="flex items-center justify-center py-2 rounded-xl text-xs font-bold transition-all duration-150
                          {{ app()->getLocale() == $code ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-gray-100 text-gray-600 hover:bg-primary/8 hover:text-primary' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Auth --}}
        @auth
            <div class="border-t border-gray-100 pt-3">
                <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-primary/5 mb-2">
                    @if(Auth::user()->avatar)
                        <img src="{{ Str::startsWith(Auth::user()->avatar, ['http://', 'https://']) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}"
                             alt="Avatar" class="w-8 h-8 rounded-xl object-cover ring-2 ring-primary/20 shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-xl bg-primary/15 flex items-center justify-center ring-2 ring-primary/20 shrink-0">
                            <span class="text-sm font-bold text-primary">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <a href="{{ route('dashboard.overview') }}"
                       class="flex items-center justify-center py-2.5 rounded-xl text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-primary/8 hover:text-primary transition-colors">
                        {{ gtrans('Dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center py-2.5 rounded-xl text-xs font-semibold bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                            {{ gtrans('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="border-t border-gray-100 pt-3">
                <a href="{{ route('login') }}"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-semibold
                          text-white bg-primary shadow-md shadow-primary/25 hover:bg-primary-dark transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    {{ gtrans('Login') }}
                </a>
            </div>
        @endauth
    </div>

    <script>
        (function () {
            function updateCartCounter(count) {
                document.querySelectorAll('.cart-counter').forEach(function (el) {
                    el.textContent = (count === null || count === undefined) ? 0 : count;
                });
            }
            window.addEventListener('cart:changed', function (e) {
                var data = e && e.detail ? e.detail : null, count = null;
                try {
                    if (data) {
                        if (data.summary && typeof data.summary.itemCount !== 'undefined') count = data.summary.itemCount;
                        else if (typeof data.itemCount !== 'undefined') count = data.itemCount;
                        else if (typeof data === 'number') count = data;
                    }
                } catch (err) {}
                if (count !== null) updateCartCounter(count);
            });
            function updateWishlistCounter(count) {
                document.querySelectorAll('.wishlist-counter').forEach(function (el) {
                    var n = (count === null || count === undefined) ? 0 : count;
                    el.textContent = n;
                    el.classList.toggle('hidden', n == 0);
                });
            }
            window.addEventListener('wishlist:changed', function (e) {
                var data = e && e.detail ? e.detail : null, count = null;
                try {
                    if (data) {
                        if (data.summary && typeof data.summary.itemCount !== 'undefined') count = data.summary.itemCount;
                        else if (typeof data.itemCount !== 'undefined') count = data.itemCount;
                        else if (typeof data === 'number') count = data;
                    }
                } catch (err) {}
                if (count !== null) updateWishlistCounter(count);
            });
            function updateCompareCounter(count) {
                document.querySelectorAll('.compare-counter').forEach(function (el) {
                    var n = (count === null || count === undefined) ? 0 : count;
                    el.textContent = n;
                    el.classList.toggle('hidden', n == 0);
                });
            }
            window.addEventListener('compare:changed', function (e) {
                var data = e && e.detail ? e.detail : null, count = null;
                try {
                    if (data) {
                        if (typeof data.count !== 'undefined') count = data.count;
                        else if (Array.isArray(data.ids)) count = data.ids.length;
                        else if (typeof data === 'number') count = data;
                    }
                } catch (err) {}
                if (count !== null) updateCompareCounter(count);
            });
        })();
    </script>

</header>
