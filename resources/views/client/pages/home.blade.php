@extends('client.layouts.app')

@section('content')
    {{-- Slider Hero Section --}}
    @include('client.components.slider')

    <section class="py-16 sm:py-24 bg-white" x-data="{
        scrollAmount: 0,
        container: null,
        isAtStart: true,
        isAtEnd: false,
        init() {
            this.container = this.$refs.container;
            this.checkScroll();
        },
        scrollNext() {
            this.container.scrollBy({ left: this.container.offsetWidth, behavior: 'smooth' });
        },
        scrollPrev() {
            this.container.scrollBy({ left: -this.container.offsetWidth, behavior: 'smooth' });
        },
        checkScroll() {
            // Updates the button states based on scroll position
            this.isAtStart = this.container.scrollLeft <= 10;
            this.isAtEnd = this.container.scrollLeft + this.container.offsetWidth >= this.container.scrollWidth - 10;
        }
    }">

        <div class="container mx-auto px-4 relative group">
            <div class="text-center mb-8 sm:mb-12">
                <h2 class="text-responsive-2xl sm:text-responsive-3xl lg:text-responsive-4xl font-bold tracking-tight text-gray-900 mb-2 sm:mb-4">
                    Shop by Category
                </h2>
                <p class="text-responsive-base sm:text-responsive-lg text-gray-600 max-w-2xl mx-auto">
                    Explore our wide range of electronics and find exactly what you need
                </p>
            </div>

            {{-- Navigation Buttons (Visible on Desktop) --}}
            <div class="hidden lg:block">
                <button @click="scrollPrev()"
                    :class="{ 'opacity-0 pointer-events-none': isAtStart, 'opacity-100': !isAtStart }"
                    class="absolute top-1/2 -left-4 transform -translate-y-1/2 z-10 bg-white border border-gray-200 text-gray-800 w-12 h-12 rounded-full shadow-lg flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300"
                    aria-label="Previous slide">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button @click="scrollNext()" :class="{ 'opacity-0 pointer-events-none': isAtEnd, 'opacity-100': !isAtEnd }"
                    class="absolute top-1/2 -right-4 transform -translate-y-1/2 z-10 bg-white border border-gray-200 text-gray-800 w-12 h-12 rounded-full shadow-lg flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300"
                    aria-label="Next slide">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            {{-- Carousel Container --}}
            <div x-ref="container" @scroll.debounce.50ms="checkScroll()"
                class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-12 no-scrollbar scroll-smooth"
                style="scrollbar-width: none; -ms-overflow-style: none;" {{-- Hides scrollbar --}}>
                @foreach ($categories as $category)
                    <div class="min-w-full sm:min-w-[calc(50%-12px)] lg:min-w-[calc(25%-18px)] snap-start shrink-0">
                        <a href="javascript:void(0)" {{-- Assuming standard route --}}
                            class="group relative block overflow-hidden p-6 sm:p-8 bg-white border-2 border-gray-100 rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 h-full">

                            {{-- Decorative Circle (Hover Effect) --}}
                            <div
                                class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-300 -mr-16 -mt-16 bg-primary">
                            </div>

                            <div class="relative flex flex-col h-full">
                                {{-- Icon / Image Container --}}
                                <div
                                    class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gray-50 transition-all duration-300 mb-5 group-hover:scale-110 group-hover:bg-primary/10 overflow-hidden">
                                    @if ($category->{'category-icon'})
                                        {{-- If dynamic image exists --}}
                                        <img src="{{ asset('/storage/' . $category->{'category-icon'}) }}"
                                            alt="{{ $category->{'category-name'} }}"
                                            class="w-10 h-10 object-contain group-hover:brightness-110">
                                    @else
                                        {{-- Default Fallback Icon --}}
                                        <svg class="w-8 h-8 text-gray-700 transition-colors group-hover:text-primary"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <h3
                                    class="text-responsive-lg sm:text-responsive-xl font-bold text-gray-900 mb-1 sm:mb-2 transition-colors group-hover:text-primary line-clamp-1">
                                    {{ $category->{'category-name'} ?? $category->{'category-name'} }}
                                    {{-- Handling both possible field names --}}
                                </h3>

                                {{-- Link Arrow --}}
                                <span
                                    class="inline-flex items-center text-responsive-sm font-semibold transition-colors text-primary mt-auto">
                                    Explore
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24 bg-white" x-data="{
        scrollAmount: 0,
        container: null,
        isAtStart: true,
        isAtEnd: false,
        init() {
            this.container = this.$refs.container;
            this.checkScroll();
            // Re-check scroll on window resize to handle responsive changes
            window.addEventListener('resize', () => this.checkScroll());
        },
        scrollNext() {
            // Scrolls by the full container width (showing the next group of 2)
            this.container.scrollBy({ left: this.container.offsetWidth, behavior: 'smooth' });
        },
        scrollPrev() {
            this.container.scrollBy({ left: -this.container.offsetWidth, behavior: 'smooth' });
        },
        checkScroll() {
            // Updates button visibility
            // 10px buffer handles sub-pixel scrolling differences
            this.isAtStart = this.container.scrollLeft <= 10;
            this.isAtEnd = this.container.scrollLeft + this.container.offsetWidth >= this.container.scrollWidth - 10;
        }
    }">

        <div class="container mx-auto px-4 relative group">

            {{-- Navigation Buttons (Hidden on start/end) --}}
            <div class="hidden md:block">
                <button @click="scrollPrev()"
                    class="absolute top-1/2 -left-4 transform -translate-y-1/2 z-10 bg-white border border-gray-200 text-gray-800 w-12 h-12 rounded-full shadow-lg flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300"
                    :class="{ 'opacity-0 pointer-events-none': isAtStart, 'opacity-100': !isAtStart }"
                    aria-label="Previous">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <button @click="scrollNext()"
                    class="absolute top-1/2 -right-4 transform -translate-y-1/2 z-10 bg-white border border-gray-200 text-gray-800 w-12 h-12 rounded-full shadow-lg flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all duration-300"
                    :class="{ 'opacity-0 pointer-events-none': isAtEnd, 'opacity-100': !isAtEnd }" aria-label="Next">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            {{-- Carousel Container --}}
            <div x-ref="container" @scroll.debounce.50ms="checkScroll()"
                class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-8 no-scrollbar scroll-smooth"
                style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach ($cards as $card)
                    @php
                        // Alternate Styles
                        $bgClass = $loop->even ? 'bg-primary' : 'bg-gray-900';
                        $badgeClass = $loop->even ? 'bg-white text-primary' : 'bg-primary text-white';
                        $blurClass = $loop->even ? 'bg-white' : 'bg-primary';
                        $opacityClass = $loop->even ? 'opacity-20' : 'opacity-10';

                        // Safe Tag Decoding
                        $tags = !empty($card->tags) ? json_decode($card->tags, true) : [];
                        $firstTag = is_array($tags) && count($tags) > 0 ? $tags[0] : 'FEATURED';
                    @endphp

                    {{--
                    WIDTH LOGIC:
                    min-w-[100%] -> Mobile (1 item)
                    md:min-w-[calc(50%-12px)] -> Tablet/Desktop (2 items exactly)
                    The '-12px' accounts for half of the 'gap-6' (24px) so 2 items fit perfectly.
                --}}
                    <div class="w-full md:w-[calc(50%-12px)] snap-start shrink-0">
                        <a href="{{ $card->link ?? '#' }}"
                            class="group relative block {{ $bgClass }} rounded-3xl p-8 md:p-12 overflow-hidden shadow-xl h-full transform hover:-translate-y-1 transition-transform duration-300">

                            {{-- Background Image --}}
                            <img src="{{ asset('storage/' . $card->image) }}" alt="{{ $card->title }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                            {{-- Overlays --}}
                            <div class="absolute inset-0 w-full h-full pointer-events-none">
                                <div class="absolute inset-0 bg-black opacity-60"></div>
                                <div
                                    class="absolute bottom-0 right-0 w-64 h-64 rounded-full {{ $opacityClass }} blur-3xl {{ $blurClass }}">
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="relative z-10 flex flex-col h-full items-start">
                                <span
                                    class="inline-block {{ $badgeClass }} text-xs font-bold px-3 py-1 rounded-full mb-4 shadow-sm">
                                    {{ $firstTag }}
                                </span>

                                <h3 class="text-3xl md:text-4xl font-extrabold text-white mb-4 leading-tight">
                                    {{ $card->title }}
                                </h3>

                                <p class="text-lg text-white text-opacity-80 mb-8 line-clamp-2">
                                    {{ $card->description }}
                                </p>

                                @if ($card->link_text)
                                    <span
                                        class="mt-auto inline-flex items-center font-semibold text-white group-hover:underline decoration-2 underline-offset-4">
                                        {{ $card->link_text }}
                                        <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="deals" class="py-16 sm:py-24 bg-linear-to-br from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12">
                <div>
                    <h2 class="text-4xl font-bold tracking-tight text-gray-900 mb-2">
                        Hot Deals This Week
                    </h2>
                    <p class="text-lg text-gray-600">Limited time offers on premium electronics</p>
                </div>
                <a href="{{ route('products') }}"
                    class="inline-flex items-center font-semibold mt-4 md:mt-0 px-6 py-3 rounded-full transition-all duration-300 group hover:shadow-lg text-primary bg-primary/10">
                    View All Products
                    <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/hp.webp') }} alt="AeroMax Wireless Headphones"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-red-500 text-white text-xs font-bold px-2 sm:px-3 py-1 rounded-full">
                            -20%
                        </div>
                    </a>
                    <div class="p-4 sm:p-5">
                        <span class="text-xs font-semibold px-2 sm:px-3 py-1 rounded-full bg-primary/10 text-primary">Audio</span>
                        <a href="#">
                            <h3
                                class="mt-2 sm:mt-3 text-responsive-base sm:text-responsive-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                AeroMax Wireless Headphones
                            </h3>
                        </a>
                        <div class="mt-3 sm:mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-responsive-lg sm:text-responsive-xl font-bold text-primary">$199.99</p>
                                <p class="text-responsive-xs sm:text-responsive-sm text-gray-400 line-through">$249.99</p>
                            </div>
                            <button
                                class="p-2 sm:p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10 touch:p-4"
                                title="Add to Cart">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/laptop.avif') }} alt="NovaBook Pro 14" (M3)"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-red-500 text-white text-xs font-bold px-2 sm:px-3 py-1 rounded-full">
                            -15%
                        </div>
                    </a>
                    <div class="p-4 sm:p-5">
                        <span
                            class="text-xs font-semibold px-2 sm:px-3 py-1 rounded-full bg-primary/10 text-primary">Laptops</span>
                        <a href="#">
                            <h3
                                class="mt-2 sm:mt-3 text-responsive-base sm:text-responsive-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                NovaBook Pro 14" (M3)
                            </h3>
                        </a>
                        <div class="mt-3 sm:mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-responsive-lg sm:text-responsive-xl font-bold text-primary">$1299.00</p>
                                <p class="text-responsive-xs sm:text-responsive-sm text-gray-400 line-through">$1499.00</p>
                            </div>
                            <button
                                class="p-2 sm:p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10 touch:p-4"
                                title="Add to Cart">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/phone.jpg') }} alt="Quantum X1 5G (256GB)"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-red-500 text-white text-xs font-bold px-2 sm:px-3 py-1 rounded-full">
                            -10%
                        </div>
                    </a>
                    <div class="p-4 sm:p-5">
                        <span
                            class="text-xs font-semibold px-2 sm:px-3 py-1 rounded-full bg-primary/10 text-primary">Smartphones</span>
                        <a href="#">
                            <h3
                                class="mt-2 sm:mt-3 text-responsive-base sm:text-responsive-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                Quantum X1 5G (256GB)
                            </h3>
                        </a>
                        <div class="mt-3 sm:mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-responsive-lg sm:text-responsive-xl font-bold text-primary">$899.00</p>
                                <p class="text-responsive-xs sm:text-responsive-sm text-gray-400 line-through">$999.00</p>
                            </div>
                            <button
                                class="p-2 sm:p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10 touch:p-4"
                                title="Add to Cart">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/tv.jpg') }} alt="Vision-Pro 55" 4K QLED TV"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            -25%
                        </div>
                    </a>
                    <div class="p-5">
                        <span
                            class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Televisions</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                Vision-Pro 55" 4K QLED TV
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-primary">$649.50</p>
                                <p class="text-sm text-gray-400 line-through">$866.00</p>
                            </div>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="new-arrivals" class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 sm:mb-12">
                <div>
                    <h2 class="text-responsive-2xl sm:text-responsive-3xl lg:text-responsive-4xl font-bold tracking-tight text-gray-900 mb-1 sm:mb-2">
                        Check Out What's New
                    </h2>
                    <p class="text-responsive-base sm:text-responsive-lg text-gray-600">The very latest in personal and home technology</p>
                </div>
                <a href="{{ route('products') }}"
                    class="inline-flex items-center font-semibold mt-4 md:mt-0 px-4 sm:px-6 py-2 sm:py-3 rounded-full transition-all duration-300 group hover:shadow-lg text-primary bg-primary/10 touch:px-6 touch:py-4">
                    View All New Products
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/drone.webp') }} alt="SkyPro 4K HDR Drone"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            NEW
                        </div>
                    </a>
                    <div class="p-5">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Drones &
                            Cameras</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                SkyPro 4K HDR Drone
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$449.00</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/watch.jpeg') }} alt="E-Zone Watch Series 8"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            NEW
                        </div>
                    </a>
                    <div class="p-5">
                        <span
                            class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Wearables</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                E-Zone Watch Series 8
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$399.00</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/vr.avif') }} alt="Metaverse VR Headset"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            NEW
                        </div>
                    </a>
                    <div class="p-5">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Gaming &
                            VR</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                Metaverse VR Headset
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$549.00</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/sepaker.webp') }} alt="AudioHome Mini Speaker"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            NEW
                        </div>
                    </a>
                    <div class="p-5">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Smart
                            Home</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                AudioHome Mini Speaker
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$79.99</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="bestsellers" class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12">
                <div>
                    <h2 class="text-4xl font-bold tracking-tight text-gray-900 mb-2">
                        Bestsellers
                    </h2>
                    <p class="text-lg text-gray-600">Our most popular products loved by customers</p>
                </div>
                <a href="{{ route('products') }}"
                    class="inline-flex items-center font-semibold mt-4 md:mt-0 px-6 py-3 rounded-full transition-all duration-300 group hover:shadow-lg text-primary bg-primary/10">
                    View All Bestsellers
                    <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/mouse.png') }} alt="Gaming Mouse Pro"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            BESTSELLER
                        </div>
                    </a>
                    <div class="p-5">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Gaming</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                Gaming Mouse Pro
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$89.99</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/buds.webp') }} alt="Wireless Earbuds Max"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            BESTSELLER
                        </div>
                    </a>
                    <div class="p-5">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Audio</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                Wireless Earbuds Max
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$149.99</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/case.jpg') }} alt="Smartphone Case Premium"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            BESTSELLER
                        </div>
                    </a>
                    <div class="p-5">
                        <span
                            class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Accessories</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                Smartphone Case Premium
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$29.99</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <a href="#" class="block relative aspect-square bg-gray-100 overflow-hidden">
                        <img src={{ asset('images/powerbank.jpg') }} alt="Portable Charger 20000mAh"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-300">
                        <div
                            class="absolute top-3 left-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            BESTSELLER
                        </div>
                    </a>
                    <div class="p-5">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary">Power</span>
                        <a href="#">
                            <h3
                                class="mt-3 text-lg font-bold text-gray-900 transition-colors line-clamp-2 group-hover:text-primary">
                                Portable Charger 20000mAh
                            </h3>
                        </a>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-2xl font-bold text-primary">$49.99</p>
                            <button
                                class="p-3 rounded-full transition-all duration-300 hover:shadow-lg transform hover:scale-110 bg-primary/10"
                                title="Add to Cart">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="brands" class="py-16 sm:py-24 bg-linear-to-br from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold tracking-tight text-gray-900 mb-4">
                    Shop by Top Brands
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    We feature the biggest names in tech, all in one place.
                </p>
            </div>

            <div
                class="w-full inline-flex flex-nowrap overflow-hidden mask-[linear-gradient(to_right,transparent_0,black_128px,black_calc(100%-128px),transparent_100%)]">
                <ul
                    class="flex items-center justify-center md:justify-start [&_li]:mx-8 [&_img]:max-w-none animate-infinite-scroll">
                    <li><img src="https://logo.clearbit.com/apple.com" alt="Apple"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/samsung.com" alt="Samsung"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/sony.com" alt="Sony"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/google.com" alt="Google"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/microsoft.com" alt="Microsoft"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/dell.com" alt="Dell"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/lg.com" alt="LG"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/nvidia.com" alt="Nvidia"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                </ul>
                <ul class="flex items-center justify-center md:justify-start [&_li]:mx-8 [&_img]:max-w-none animate-infinite-scroll"
                    aria-hidden="true">
                    <li><img src="https://logo.clearbit.com/apple.com" alt="Apple"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/samsung.com" alt="Samsung"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/sony.com" alt="Sony"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/google.com" alt="Google"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/microsoft.com" alt="Microsoft"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/dell.com" alt="Dell"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/lg.com" alt="LG"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                    <li><img src="https://logo.clearbit.com/nvidia.com" alt="Nvidia"
                            class="h-10 w-auto grayscale opacity-75 hover:opacity-100 transition-opacity"></li>
                </ul>
            </div>

        </div>
    </section>


    <section class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold tracking-tight text-gray-900 mb-4">
                    Why Choose E-Zone?
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    We're committed to providing you with the best shopping experience
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group relative overflow-hidden">
                    <div
                        class="relative bg-linear-to-br from-white to-gray-50 border-2 border-gray-100 rounded-3xl p-8 text-center transition-all duration-300 hover:shadow-2xl hover:border-opacity-0">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-300 -mr-16 -mt-16 bg-primary">
                        </div>

                        <div class="relative">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6 transition-all duration-300 group-hover:scale-110 bg-primary/10">
                                <svg class="w-10 h-10 transition-colors text-primary" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Fast & Free Shipping</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Get your orders delivered to your doorstep in no time, with free shipping on all orders over
                                $50.
                            </p>

                            <div
                                class="mt-6 inline-flex items-center text-sm font-semibold transition-colors text-primary">
                                Learn more
                                <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden">
                    <div
                        class="relative bg-linear-to-br from-white to-gray-50 border-2 border-gray-100 rounded-3xl p-8 text-center transition-all duration-300 hover:shadow-2xl hover:border-opacity-0">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-300 -mr-16 -mt-16 bg-primary">
                        </div>

                        <div class="relative">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6 transition-all duration-300 group-hover:scale-110 bg-primary/10">
                                <svg class="w-10 h-10 transition-colors text-primary" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Secure Payments</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Your payments are safe and secure with our end-to-end encrypted payment gateway and SSL
                                protection.
                            </p>

                            <div
                                class="mt-6 inline-flex items-center text-sm font-semibold transition-colors text-primary">
                                Learn more
                                <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden">
                    <div
                        class="relative bg-linear-to-br from-white to-gray-50 border-2 border-gray-100 rounded-3xl p-8 text-center transition-all duration-300 hover:shadow-2xl hover:border-opacity-0">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-300 -mr-16 -mt-16 bg-primary">
                        </div>

                        <div class="relative">
                            <div
                                class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6 transition-all duration-300 group-hover:scale-110 bg-primary/10">
                                <svg class="w-10 h-10 transition-colors text-primary" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 mb-4">24/7 Customer Support</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Our support team is available around the clock to help you with any questions or issues you
                                may have.
                            </p>

                            <div
                                class="mt-6 inline-flex items-center text-sm font-semibold transition-colors text-primary">
                                Learn more
                                <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
