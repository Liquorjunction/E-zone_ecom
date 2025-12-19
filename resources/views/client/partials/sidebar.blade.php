{{-- Data injected via App\View\Composers\SidebarComposer --}}
<aside class="hidden lg:flex lg:flex-col fixed inset-y-0 left-0 bg-white shadow-xl border-r-2 border-primary z-30 transition-all duration-300 container-query"
    style="top: var(--header-height); max-height: calc(100vh - var(--header-height));"
       :class="{ 'w-16': sidebarCollapsed, 'w-72': !sidebarCollapsed }"
       @mouseenter="if(!pinned) sidebarCollapsed = false"
       @mouseleave="if(!pinned) sidebarCollapsed = true">

    <div x-show="!sidebarCollapsed" x-cloak class="shrink-0 bg-white z-10 py-5 px-5 border-b-2 border-gray-100 transition-all duration-300 flex items-center justify-between">

        <h2 class="text-xl font-bold text-gray-900 flex items-center transition-all duration-300">
            <span class="w-1.5 h-7 rounded-full mr-3 bg-primary transition-all duration-300"></span>
            <span class="transition-all duration-300">
                {{ gtrans('Shop by Category') }}
            </span>
        </h2>

        <div class="ml-2 flex items-center">
            <button @click.prevent="pinned = !pinned"
                    :title="pinned ? 'Unpin sidebar' : 'Pin sidebar'"
                    :class="pinned ? 'bg-primary text-white hover:bg-primary-700' : 'bg-white text-gray-600 hover:bg-gray-100'"
                    class="flex items-center justify-center p-2 rounded-md border border-transparent shadow-sm transition-all duration-200 focus:outline-none">

                <!-- Unpin (mirrored) - shown when not pinned -->
                <svg x-show="!pinned" x-cloak class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
                    <g transform="translate(16 0) scale(-1 1)">
                        <path d="m10.25 10.25 4 4m-12.5-7.5 5-5s1 2 2 3 4.5 2 4.5 2l-6.5 6.5s-1-3.5-2-4.5-3-2-3-2z"/>
                    </g>
                </svg>

                <!-- Pin (provided) - shown when pinned -->
                <svg x-show="pinned" x-cloak class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
                    <path d="m10.25 10.25 4 4m-12.5-7.5 5-5s1 2 2 3 4.5 2 4.5 2l-6.5 6.5s-1-3.5-2-4.5-3-2-3-2z"/>
                </svg>

            </button>
        </div>

    </div>

    <nav class="flex-1 overflow-y-auto mt-2 pb-6 transition-all duration-300 custom-scrollbar"
         :class="{ 'px-2': sidebarCollapsed, 'px-3': !sidebarCollapsed }">
        <ul class="space-y-1">
            @foreach ($sidebarCategories as $category)
                <li x-data="{ open: false }" class="group">
                    <div class="relative">
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                           class="flex items-center py-3.5 text-sm font-medium text-gray-700 rounded-xl transition-all duration-300 hover:bg-gray-50 group-hover:shadow-md"
                           :class="{ 'px-2 justify-center': sidebarCollapsed, 'px-4': !sidebarCollapsed }">

                            <span class="shrink-0 text-primary transition-colors w-6 h-6 flex items-center justify-center">
                                @if($category->{'category-icon'} && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->{'category-icon'}))
                                    <img src="{{ asset('storage/' . $category->{'category-icon'}) }}"
                                         alt="icon" class="w-5 h-5 object-contain">
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                @endif
                            </span>

                            <span x-show="!sidebarCollapsed"
                                  class="ml-3 flex-1 text-left group-hover:font-semibold group-hover:text-primary transition-all">
                                {{ $category->{'category-name'} }}
                            </span>

                            @if($category->subcategories->count() > 0)
                                <button x-show="!sidebarCollapsed"
                                        @click.prevent="open = !open"
                                        type="button"
                                        class="ml-2 p-1 rounded-lg transition-all duration-300 hover:bg-white">
                                    <svg class="h-4 w-4 text-gray-400 transition-transform duration-300 group-hover:text-primary"
                                         :class="{ 'rotate-90': open }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </a>
                        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>

                    @if($category->subcategories->count() > 0)
                        <div x-show="!sidebarCollapsed && open"
                             x-collapse
                             class="ml-9 mt-1.5 space-y-0.5 pl-4 border-l-2 border-gray-100">
                            @foreach ($category->subcategories as $subcategory)
                                <a href="{{ route('products.index', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}"
                                   class="block px-3 py-2.5 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50 hover:pl-4 hover:font-medium hover:text-primary">
                                    <span class="flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-2.5 group-hover:bg-primary transition-colors"></span>
                                        {{ $subcategory->{'sub-category-name'} }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>

    <div x-show="!sidebarCollapsed" class="shrink-0 bg-white p-4 border-t-2 border-gray-100 shadow-lg mt-auto">
        <a href="{{ route('products.index') }}"
           class="flex items-center justify-center w-full px-5 py-3 text-sm font-semibold text-white bg-primary rounded-xl transition-all duration-300 hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            {{ gtrans('View All Products') }}
        </a>
    </div>
</aside>

<button @click="mobileSidebarOpen = true"
        class="lg:hidden fixed bottom-6 left-6 z-40 p-4 text-white bg-primary rounded-full shadow-2xl transition-all duration-300 hover:shadow-3xl transform hover:scale-110">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

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

<aside x-show="mobileSidebarOpen"
       x-cloak
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="lg:hidden fixed inset-y-0 left-0 w-80 bg-white shadow-2xl overflow-y-auto z-50 flex flex-col">

    <div class="shrink-0 sticky top-0 bg-white z-10 py-5 px-5 border-b-2 border-primary flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900 flex items-center">
            <span class="w-1.5 h-7 rounded-full mr-3 bg-primary"></span>
            {{ gtrans('Categories') }}
        </h2>
        <button @click="mobileSidebarOpen = false"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto mt-2 px-3 pb-6">
        <ul class="space-y-1">
            @foreach ($sidebarCategories as $category)
                <li x-data="{ open: false }" class="group">
                    <div class="relative">
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                           class="flex items-center px-4 py-3.5 text-sm font-medium text-gray-700 rounded-xl transition-all duration-300 hover:bg-gray-50">
                            <span class="shrink-0 text-primary w-6 h-6 flex items-center justify-center">
                                @if($category->{'category-icon'} && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->{'category-icon'}))
                                    <img src="{{ asset('storage/' . $category->{'category-icon'}) }}"
                                         alt="icon" class="w-5 h-5 object-contain">
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                @endif
                            </span>

                            <span class="ml-3 flex-1">{{ $category->{'category-name'} }}</span>

                            @if($category->subcategories->count() > 0)
                                <button @click.prevent="open = !open"
                                        type="button"
                                        class="ml-2 p-1 rounded-lg">
                                    <svg class="h-4 w-4 text-gray-400 transition-transform duration-300"
                                         :class="{ 'rotate-90': open }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </a>
                    </div>

                    @if($category->subcategories->count() > 0)
                        <div x-show="open"
                             x-collapse
                             class="ml-9 mt-1.5 space-y-0.5 pl-4 border-l-2 border-gray-100">
                            @foreach ($category->subcategories as $subcategory)
                                <a href="{{ route('products.index', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}"
                                   class="block px-3 py-2.5 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50">
                                    <span class="flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 mr-2.5"></span>
                                        {{ $subcategory->{'sub-category-name'} }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
</aside>
