<!-- Sidebar -->

@php
    // Categories with icons
    $categories = [
        ['name' => 'Computers & Laptops', 'slug' => 'computers-laptops', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>'],
        ['name' => 'Smartphones & Tablets', 'slug' => 'smartphones-tablets', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3m-3-3.75h.008v.008H10.5v-.008z" /></svg>'],
        ['name' => 'TV & Home Theater', 'slug' => 'tv-home-theater', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3H19.5A2.25 2.25 0 0021.75 15V6.75A2.25 2.25 0 0019.5 4.5H4.5A2.25 2.25 0 002.25 6.75V15a2.25 2.25 0 002.25 2.25z" /></svg>'],
        ['name' => 'Audio & Gaming', 'slug' => 'audio-gaming', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" /></svg>'],
        ['name' => 'Cameras & Photography', 'slug' => 'cameras-photography', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>'],
        ['name' => 'Wearables & Fitness', 'slug' => 'wearables-fitness', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
        ['name' => 'Accessories', 'slug' => 'accessories', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" /></svg>'],
    ];

    // Subcategories for each category
    $subcategoriesMap = [
        'computers-laptops' => [
            ['name' => 'Desktop Computers', 'slug' => 'desktop-computers'],
            ['name' => 'Laptops', 'slug' => 'laptops'],
            ['name' => 'Monitors', 'slug' => 'monitors'],
            ['name' => 'Components', 'slug' => 'components'],
            ['name' => 'Peripherals', 'slug' => 'peripherals'],
        ],
        'smartphones-tablets' => [
            ['name' => 'Smartphones', 'slug' => 'smartphones'],
            ['name' => 'Tablets', 'slug' => 'tablets'],
            ['name' => 'Cases & Covers', 'slug' => 'cases-covers'],
            ['name' => 'Screen Protectors', 'slug' => 'screen-protectors'],
            ['name' => 'Chargers', 'slug' => 'chargers'],
        ],
        'tv-home-theater' => [
            ['name' => 'Smart TVs', 'slug' => 'smart-tvs'],
            ['name' => 'Soundbars', 'slug' => 'soundbars'],
            ['name' => 'Streaming Devices', 'slug' => 'streaming-devices'],
            ['name' => 'Home Theater Systems', 'slug' => 'home-theater'],
            ['name' => 'Projectors', 'slug' => 'projectors'],
        ],
        'audio-gaming' => [
            ['name' => 'Gaming Consoles', 'slug' => 'gaming-consoles'],
            ['name' => 'Headphones', 'slug' => 'headphones'],
            ['name' => 'Speakers', 'slug' => 'speakers'],
            ['name' => 'Gaming Accessories', 'slug' => 'gaming-accessories'],
            ['name' => 'Controllers', 'slug' => 'controllers'],
        ],
        'cameras-photography' => [
            ['name' => 'DSLR Cameras', 'slug' => 'dslr-cameras'],
            ['name' => 'Mirrorless Cameras', 'slug' => 'mirrorless-cameras'],
            ['name' => 'Action Cameras', 'slug' => 'action-cameras'],
            ['name' => 'Lenses', 'slug' => 'lenses'],
            ['name' => 'Tripods & Mounts', 'slug' => 'tripods-mounts'],
        ],
        'wearables-fitness' => [
            ['name' => 'Smartwatches', 'slug' => 'smartwatches'],
            ['name' => 'Fitness Trackers', 'slug' => 'fitness-trackers'],
            ['name' => 'Smart Glasses', 'slug' => 'smart-glasses'],
            ['name' => 'Health Monitors', 'slug' => 'health-monitors'],
            ['name' => 'Watch Bands', 'slug' => 'watch-bands'],
        ],
        'accessories' => [
            ['name' => 'Cables & Adapters', 'slug' => 'cables-adapters'],
            ['name' => 'Power Banks', 'slug' => 'power-banks'],
            ['name' => 'Memory Cards', 'slug' => 'memory-cards'],
            ['name' => 'Storage Devices', 'slug' => 'storage-devices'],
            ['name' => 'Bags & Cases', 'slug' => 'bags-cases'],
        ],
    ];
@endphp

<!-- Desktop Sidebar -->
<aside class="hidden lg:block fixed inset-y-0 left-0 bg-white shadow-xl overflow-y-auto border-r-2 border-primary z-40 transition-all duration-300"
       style="top: 88px;"
       :class="{ 'w-16': sidebarCollapsed, 'w-72': !sidebarCollapsed }">
    <!-- Header -->
    <div class="sticky top-0 bg-white z-10 py-5 px-5 border-b-2 border-gray-100 transition-all duration-300"
         :class="{ 'px-3 hidden': sidebarCollapsed }">
        <h2 class="text-xl font-bold text-gray-900 flex items-center transition-all duration-300"
            :class="{ 'justify-center': sidebarCollapsed }">
            <span class="w-1.5 h-7 rounded-full mr-3 bg-primary transition-all duration-300"
                  :class="{ 'mr-0': sidebarCollapsed }"></span>
            <span x-show="!sidebarCollapsed" class="transition-all duration-300">Shop by Category</span>
        </h2>
    </div>

    <!-- Categories Navigation -->
    <nav class="mt-2 pb-6 transition-all duration-300"
         :class="{ 'px-2': sidebarCollapsed, 'px-3': !sidebarCollapsed }">
        <ul class="space-y-1">
            @foreach ($categories as $category)
                <li x-data="{ open: false }" class="group">
                    <!-- Main Category -->
                    <div class="relative">
                        <a href="javascript:void(0)"
                           class="flex items-center py-3.5 text-sm font-medium text-gray-700 rounded-xl transition-all duration-300 hover:bg-background group-hover:shadow-md"
                           :class="{ 'px-2 justify-center': sidebarCollapsed, 'px-4': !sidebarCollapsed }">
                            <span class="shrink-0 text-primary transition-colors">
                                {!! $category['icon'] !!}
                            </span>
                            <span x-show="!sidebarCollapsed"
                                  class="ml-3 flex-1 text-left group-hover:font-semibold group-hover:text-primary transition-all">
                                {{ $category['name'] }}
                            </span>
                            <button x-show="!sidebarCollapsed"
                                    @click="open = !open"
                                    type="button"
                                    class="ml-2 p-1 rounded-lg transition-all duration-300 hover:bg-white">
                                <svg class="h-4 w-4 text-gray-400 transition-transform duration-300 group-hover:text-primary"
                                     :class="{ 'rotate-90': open }"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </a>
                        <!-- Active Indicator -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>

                    <!-- Subcategories -->
                    <div x-show="!sidebarCollapsed && open"
                         x-collapse
                         class="ml-9 mt-1.5 space-y-0.5 pl-4 border-l-2 border-gray-100">
                        @foreach ($subcategoriesMap[$category['slug']] ?? [] as $subcategory)
                            <a href="{{ route('products', ['category' => $category['slug'], 'subcategory' => $subcategory['slug']]) }}"
                               class="block px-3 py-2.5 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50 hover:pl-4 hover:font-medium hover:text-primary">
                                <span class="flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-2.5 group-hover:bg-primary transition-colors"></span>
                                    {{ $subcategory['name'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </li>
            @endforeach
        </ul>
    </nav>

    <!-- View All Button -->
    <div x-show="!sidebarCollapsed" class="sticky bottom-0 bg-white p-4 border-t-2 border-gray-100 shadow-lg">
        <a href="{{ route('products') }}"
           class="flex items-center justify-center w-full px-5 py-3 text-sm font-semibold text-white bg-primary rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            View All Products
        </a>
    </div>
</aside>

<!-- Mobile Sidebar Toggle Button -->
<button @click="mobileSidebarOpen = true"
        class="lg:hidden fixed bottom-6 left-6 z-50 p-4 text-white bg-primary rounded-full shadow-2xl transition-all duration-300 hover:shadow-3xl transform hover:scale-110">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<!-- Mobile Sidebar Overlay -->
<div x-show="mobileSidebarOpen"
     x-cloak
     @click="mobileSidebarOpen = false"
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

<!-- Mobile Sidebar -->
<aside x-show="mobileSidebarOpen"
       x-cloak
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="lg:hidden fixed inset-y-0 left-0 w-80 bg-white shadow-2xl overflow-y-auto z-50">

    <!-- Mobile Header -->
    <div class="sticky top-0 bg-white z-10 py-5 px-5 border-b-2 border-primary flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900 flex items-center">
            <span class="w-1.5 h-7 rounded-full mr-3 bg-primary"></span>
            Categories
        </h2>
        <button @click="mobileSidebarOpen = false"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Mobile Categories -->
    <nav class="mt-2 px-3 pb-6">
        <ul class="space-y-1">
            @foreach ($categories as $category)
                <li x-data="{ open: false }" class="group">
                    <div class="relative">
                        <a href="{{ route('products', ['category' => $category['slug']]) }}"
                           class="flex items-center px-4 py-3.5 text-sm font-medium text-gray-700 rounded-xl transition-all duration-300 hover:bg-gray-50">
                            <span class="shrink-0 text-primary">
                                {!! $category['icon'] !!}
                            </span>
                            <span class="ml-3 flex-1">{{ $category['name'] }}</span>
                            <button @click.prevent="open = !open"
                                    type="button"
                                    class="ml-2 p-1 rounded-lg">
                                <svg class="h-4 w-4 text-gray-400 transition-transform duration-300"
                                     :class="{ 'rotate-90': open }"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </a>
                    </div>

                    <div x-show="open"
                         x-collapse
                         class="ml-9 mt-1.5 space-y-0.5 pl-4 border-l-2 border-gray-100">
                        @foreach ($subcategoriesMap[$category['slug']] ?? [] as $subcategory)
                            <a href="{{ route('products', ['category' => $category['slug'], 'subcategory' => $subcategory['slug']]) }}"
                               class="block px-3 py-2.5 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50">
                                <span class="flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-2.5"></span>
                                    {{ $subcategory['name'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </li>
            @endforeach
        </ul>
    </nav>


</aside>

<!-- Alpine.js initialization script (add to your layout if not already present) -->
@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sidebar', () => ({
            mobileSidebarOpen: false,
            sidebarCollapsed: true
        }))
    })
</script>
@endpush
