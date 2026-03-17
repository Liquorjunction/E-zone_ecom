{{-- Data injected via App\View\Composers\SidebarComposer --}}

{{-- ═══════════════════════════════════════════════════════
     DESKTOP SIDEBAR
     ═══════════════════════════════════════════════════════ --}}
<aside x-cloak
    class="hidden lg:flex lg:flex-col fixed inset-y-0 left-0 bg-white z-30 transition-[width] duration-300 ease-out w-18 border-r-2 border-primary/30"
    style="top: var(--header-height); max-height: calc(100vh - var(--header-height)); will-change: width; box-shadow: 2px 0 20px rgba(0,0,0,0.07);"
    :class="{ 'w-18': sidebarCollapsed, 'w-68': !sidebarCollapsed }"
    @mouseenter.debounce.50ms="if(!pinned) sidebarCollapsed = false"
    @mouseleave.debounce.100ms="if(!pinned) sidebarCollapsed = true">

    {{-- ── Gradient accent line on right (always visible) ── --}}
    <div class="absolute right-0 top-0 bottom-0 w-0.5 bg-linear-to-b from-primary via-primary/50 to-transparent pointer-events-none z-20"></div>

    {{-- ── Header ── --}}
    <div class="shrink-0 z-10 border-b border-gray-100 flex items-center overflow-hidden"
         style="min-height: 60px;">

        {{-- Collapsed: centered logo-style icon --}}
        <div x-show="sidebarCollapsed"
             class="w-full flex items-center justify-center py-4">
            <span class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </span>
        </div>

        {{-- Expanded: title + pin button --}}
        <div x-show="!sidebarCollapsed"
             x-transition:enter="transition ease-out duration-200 delay-75"
             x-transition:enter-start="opacity-0 -translate-x-2"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="w-full flex items-center justify-between px-4 py-4">

            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 leading-tight tracking-wide">{{ statictranslations('Categories') }}</h2>
                    <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider leading-tight">{{ statictranslations('Shop by') }}</p>
                </div>
            </div>

                <button @click.prevent="pinned = !pinned"
                    :title="pinned ? '{{ statictranslations('Unpin sidebar') }}' : '{{ statictranslations('Pin sidebar') }}'"
                    :class="pinned
                        ? 'bg-primary text-white shadow-lg shadow-primary/30'
                        : 'bg-gray-100 text-gray-400 hover:bg-primary/10 hover:text-primary'"
                    class="shrink-0 flex items-center justify-center w-8 h-8 rounded-xl transition-all duration-200 focus:outline-none">
                <svg x-show="!pinned" class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                     stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                    <g transform="translate(16 0) scale(-1 1)">
                        <path d="m10.25 10.25 4 4m-12.5-7.5 5-5s1 2 2 3 4.5 2 4.5 2l-6.5 6.5s-1-3.5-2-4.5-3-2-3-2z"/>
                    </g>
                </svg>
                <svg x-show="pinned" class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                     stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                    <path d="m10.25 10.25 4 4m-12.5-7.5 5-5s1 2 2 3 4.5 2 4.5 2l-6.5 6.5s-1-3.5-2-4.5-3-2-3-2z"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Nav ── --}}
    <nav class="flex-1 overflow-y-auto py-2 custom-scrollbar"
         :class="{ 'px-2': sidebarCollapsed, 'px-2.5': !sidebarCollapsed }">
        <ul class="space-y-0.5">
            @foreach ($sidebarCategories as $category)
                <li x-data="{ open: false }" class="group/item">

                    {{-- Row wrapper --}}
                    <div class="relative flex items-center rounded-2xl overflow-hidden transition-all duration-200
                                hover:bg-primary/5 hover:shadow-sm"
                         :class="{ 'justify-center': sidebarCollapsed }">

                        {{-- Hover left accent bar --}}
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-0.75 rounded-r-full bg-primary
                                     transition-all duration-250 ease-out opacity-0 h-0
                                     group-hover/item:opacity-100 group-hover/item:h-8"></span>

                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                           data-category-link="{{ $category->slug }}"
                           class="category-sidebar-link flex items-center flex-1 min-w-0 py-2.5 transition-all duration-150 group-hover/item:text-primary"
                           :class="{ 'px-2 justify-center': sidebarCollapsed, 'pl-3 pr-1': !sidebarCollapsed }">

                            {{-- Icon container --}}
                            <span class="shrink-0 w-9 h-9 flex items-center justify-center rounded-xl
                                         bg-gray-100 group-hover/item:bg-primary/10
                                         transition-all duration-200 group-hover/item:scale-105"
                                  :class="{ 'mx-auto': sidebarCollapsed }">
                                @if($category->{'category-icon'} && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->{'category-icon'}))
                                    <img src="{{ asset('storage/' . $category->{'category-icon'}) }}"
                                         alt="icon" class="w-5 h-5 object-contain">
                                @else
                                    <svg class="w-5 h-5 text-gray-400 group-hover/item:text-primary transition-colors duration-150"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                @endif
                            </span>

                            {{-- Label --}}
                            <span x-show="!sidebarCollapsed"
                                  x-transition:enter="transition ease-out duration-200 delay-50"
                                  x-transition:enter-start="opacity-0 translate-x-2"
                                  x-transition:enter-end="opacity-100 translate-x-0"
                                  class="ml-3 flex-1 truncate text-sm font-medium text-gray-700
                                         group-hover/item:text-primary leading-snug transition-colors duration-150">
                                {{ $category->{'category-name'} }}
                            </span>
                        </a>

                        {{-- Chevron --}}
                        @if($category->subcategories->count() > 0)
                            <button x-show="!sidebarCollapsed"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    @click.prevent="open = !open"
                                    type="button"
                                    class="shrink-0 mr-2.5 w-6 h-6 flex items-center justify-center rounded-lg
                                           text-gray-400 group-hover/item:text-primary
                                           hover:bg-primary/10 transition-all duration-150">
                                <svg class="w-3.5 h-3.5 transition-transform duration-250"
                                     :class="{ 'rotate-90': open }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Collapsed tooltip --}}
                        <div x-show="sidebarCollapsed"
                             class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-gray-900 text-white text-xs font-medium
                                    rounded-lg whitespace-nowrap opacity-0 group-hover/item:opacity-100 transition-opacity duration-150 z-50
                                    shadow-lg before:content-[''] before:absolute before:right-full before:top-1/2 before:-translate-y-1/2
                                    before:border-4 before:border-transparent before:border-r-gray-900">
                            {{ $category->{'category-name'} }}
                        </div>
                    </div>

                    {{-- Subcategories --}}
                    @if($category->subcategories->count() > 0)
                        <div x-show="!sidebarCollapsed && open"
                             x-collapse.duration.200ms
                             class="mt-1 ml-13 space-y-0.5 pb-1 pl-3 border-l-2 border-primary/20">
                            @foreach ($category->subcategories as $subcategory)
                                @if (function_exists('countActiveVariants') ? countActiveVariants($subcategory->products) > 0 : (isset($countActiveVariants) ? $countActiveVariants($subcategory->products) > 0 : ($subcategory->products && $subcategory->products->where('status', 'active')->count() > 0)))
                                    <a href="{{ route('products.index', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}"
                                       data-subcategory-slug="{{ $subcategory->slug }}"
                                       data-category-slug="{{ $category->slug }}"
                                       class="subcategory-sidebar-link group/sub flex items-center gap-2.5 px-3 py-2
                                              text-xs text-gray-500 rounded-xl transition-all duration-150
                                              hover:bg-primary/5 hover:text-primary hover:pl-4 hover:font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover/sub:bg-primary shrink-0 transition-colors duration-150"></span>
                                        <span class="truncate">{{ $subcategory->{'sub-category-name'} }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </li>
            @endforeach
        </ul>
    </nav>

    {{-- ── Footer CTA ── --}}
    <div class="shrink-0 border-t border-gray-100 overflow-hidden"
         :class="{ 'p-2': sidebarCollapsed, 'p-3': !sidebarCollapsed }">

        {{-- Collapsed: icon-only --}}
        <div x-show="sidebarCollapsed" class="flex justify-center">
            <a href="{{ route('products.index') }}"
               title="{{ statictranslations('View All Products') }}"
               class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary text-white
                      shadow-md shadow-primary/30 transition-all duration-200 hover:bg-primary-dark hover:scale-105 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </a>
        </div>

        {{-- Expanded: full button --}}
        <div x-show="!sidebarCollapsed"
             x-transition:enter="transition ease-out duration-200 delay-75"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <a href="{{ route('products.index') }}"
               class="flex items-center justify-center gap-2.5 w-full px-4 py-2.5 text-sm font-semibold
                      text-white bg-primary rounded-2xl shadow-md shadow-primary/25
                      transition-all duration-200 hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/35
                      hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                {{ statictranslations('View All Products') }}
            </a>
        </div>
    </div>

</aside>


{{-- ═══════════════════════════════════════════════════════
     MOBILE FAB
     ═══════════════════════════════════════════════════════ --}}
<button @click="mobileSidebarOpen = true"
        class="sidebar-mobile-open-btn lg:hidden p-4 text-white bg-primary rounded-full
               shadow-2xl shadow-primary/40 transition-all duration-200
               hover:scale-110 hover:shadow-primary/50 active:scale-95">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>


{{-- ═══════════════════════════════════════════════════════
     MOBILE BACKDROP
     ═══════════════════════════════════════════════════════ --}}
<div x-show="mobileSidebarOpen"
     x-cloak
     @click="mobileSidebarOpen = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40"></div>


{{-- ═══════════════════════════════════════════════════════
     MOBILE SIDEBAR DRAWER
     ═══════════════════════════════════════════════════════ --}}
<aside x-show="mobileSidebarOpen"
       x-cloak
       x-transition:enter="transition ease-out duration-250 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="lg:hidden fixed inset-y-0 left-0 w-80 bg-white z-50 flex flex-col border-r-2 border-primary/30"
       style="box-shadow: 6px 0 36px rgba(0,0,0,0.14);">

    {{-- Gradient accent line on right --}}
    <div class="absolute right-0 top-0 bottom-0 w-0.5 bg-linear-to-b from-primary via-primary/50 to-transparent pointer-events-none z-20"></div>

    {{-- Header --}}
    <div class="shrink-0 sticky top-0 bg-white z-10 px-4 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-gray-900 tracking-wide leading-tight">{{ statictranslations('Categories') }}</h2>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider leading-tight">{{ statictranslations('Shop by') }}</p>
            </div>
        </div>
        <button @click="mobileSidebarOpen = false"
                class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100
                       hover:bg-primary/10 hover:text-primary text-gray-400 transition-all duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-2 px-2.5 custom-scrollbar">
        <ul class="space-y-0.5">
            @foreach ($sidebarCategories as $category)
                <li x-data="{ open: false }" class="group/item">

                    <div class="relative flex items-center rounded-2xl overflow-hidden transition-all duration-200 hover:bg-primary/5 hover:shadow-sm">

                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] rounded-r-full bg-primary
                                     transition-all duration-250 ease-out opacity-0 h-0
                                     group-hover/item:opacity-100 group-hover/item:h-8"></span>

                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                           data-category-link="{{ $category->slug }}"
                           class="category-sidebar-link flex items-center flex-1 min-w-0 pl-3 pr-1 py-2.5 transition-all duration-150 group-hover/item:text-primary">

                            <span class="shrink-0 w-9 h-9 flex items-center justify-center rounded-xl
                                         bg-gray-100 group-hover/item:bg-primary/10
                                         transition-all duration-200 group-hover/item:scale-105">
                                @if($category->{'category-icon'} && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->{'category-icon'}))
                                    <img src="{{ asset('storage/' . $category->{'category-icon'}) }}"
                                         alt="icon" class="w-5 h-5 object-contain">
                                @else
                                    <svg class="w-5 h-5 text-gray-400 group-hover/item:text-primary transition-colors duration-150"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                @endif
                            </span>

                            <span class="ml-3 flex-1 truncate text-sm font-medium text-gray-700
                                         group-hover/item:text-primary transition-colors duration-150">
                                {{ $category->{'category-name'} }}
                            </span>
                        </a>

                        @if($category->subcategories->count() > 0)
                            <button @click.prevent="open = !open"
                                    type="button"
                                    class="shrink-0 mr-2.5 w-6 h-6 flex items-center justify-center rounded-lg
                                           text-gray-400 group-hover/item:text-primary hover:bg-primary/10 transition-all duration-150">
                                <svg class="w-3.5 h-3.5 transition-transform duration-250"
                                     :class="{ 'rotate-90': open }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    @if($category->subcategories->count() > 0)
                        <div x-show="open"
                             x-collapse.duration.200ms
                             class="mt-1 ml-[3.25rem] space-y-0.5 pb-1 pl-3 border-l-2 border-primary/20">
                            @foreach ($category->subcategories as $subcategory)
                                @if (function_exists('countActiveVariants') ? countActiveVariants($subcategory->products) > 0 : (isset($countActiveVariants) ? $countActiveVariants($subcategory->products) > 0 : ($subcategory->products && $subcategory->products->where('status', 'active')->count() > 0)))
                                    <a href="{{ route('products.index', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}"
                                       data-subcategory-slug="{{ $subcategory->slug }}"
                                       data-category-slug="{{ $category->slug }}"
                                       class="subcategory-sidebar-link group/sub flex items-center gap-2.5 px-3 py-2
                                              text-xs text-gray-500 rounded-xl transition-all duration-150
                                              hover:bg-primary/5 hover:text-primary hover:pl-4 hover:font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover/sub:bg-primary shrink-0 transition-colors duration-150"></span>
                                        <span class="truncate">{{ $subcategory->{'sub-category-name'} }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </li>
            @endforeach
        </ul>
    </nav>

    {{-- Footer CTA --}}
    <div class="shrink-0 p-3 border-t border-gray-100">
        <a href="{{ route('products.index') }}"
           class="flex items-center justify-center gap-2.5 w-full px-4 py-2.5 text-sm font-semibold
                  text-white bg-primary rounded-2xl shadow-md shadow-primary/25
                  transition-all duration-200 hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/35
                  hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            {{ statictranslations('View All Products') }}
        </a>
    </div>

</aside>
