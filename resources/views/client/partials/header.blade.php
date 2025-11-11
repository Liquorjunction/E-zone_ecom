<header class="bg-white shadow-lg sticky top-0 z-50 border-b-2 border-primary">
    <div class="container lg:max-w-screen px-4 py-3">
        <div class="flex items-center justify-between">

            <div class="flex items-center space-x-4">
                <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden lg:flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 transition-all duration-300 text-primary">
                    <svg class="w-6 h-6 transition-transform duration-300" :class="{ 'rotate-90': sidebarCollapsed }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="flex items-center group">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                        class="w-24 h-16 object-contain transition-transform duration-300 group-hover:scale-105">
                </a>

                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="{{ route('home') }}"
                        class="relative font-medium transition-colors duration-300 group hover:opacity-80 text-primary">
                        Home
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('products') }}"
                        class="relative font-medium transition-colors duration-300 group hover:opacity-80 text-primary">
                        Products
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('cart') }}"
                        class="relative font-medium transition-colors duration-300 group flex items-center hover:opacity-80 text-primary">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </PATH>
                        </svg>
                        Cart
                        <span
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">0</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </nav>
            </div>

            <div class="hidden md:flex flex-1 max-w-md mx-6">
                <div class="relative w-full group">
                    <input type="text" placeholder="Search for electronics, deals..."
                        class="w-full pl-12 pr-4 py-2.5 border-2 border-gray-200 rounded-full focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300 bg-white">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('login') }}"
                    class="font-medium px-4 py-2 rounded-full transition-all duration-300 hover:bg-gray-100 text-primary">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="text-white px-6 py-2.5 rounded-full font-medium shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl bg-primary">
                    Register
                </a>
            </div>

            <div class="md:hidden">
                <button class="text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-all duration-300"
                    onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
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

    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200">
        <nav class="container mx-auto px-4 py-4 space-y-2">
            <a href="{{ route('home') }}" class="block py-2 px-4 rounded-lg transition-colors hover:bg-gray-100 text-primary">
                Home
            </a>
            <a href="{{ route('products') }}" class="block py-2 px-4 rounded-lg transition-colors hover:bg-gray-100 text-primary">
                Products
            </a>
            <a href="{{ route('cart') }}" class="block py-2 px-4 rounded-lg transition-colors hover:bg-gray-100 text-primary">
                Cart
            </a>
            <div class="border-t border-gray-200 my-2 pt-2">
                <a href="{{ route('login') }}" class="block py-2 px-4 rounded-lg transition-colors hover:bg-gray-100 text-primary">
                    Login
                </a>
                <a href="{{ route('register') }}" class="block py-2 px-4 text-white rounded-lg text-center mt-2 bg-primary">
                    Register
                </a>
            </div>
        </nav>
    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }
</script>
