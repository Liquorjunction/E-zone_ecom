@extends('client.layouts.app')

@section('content')
{{-- Hero Slider Section --}}
@include('client.components.slider')

{{-- Quick Stats Bar --}}
<section class="bg-white border-b border-gray-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $stats = isset($quickStats) ? $quickStats->take(4) : collect();
            $locale = app()->getLocale();
        @endphp

        @if ($stats->isNotEmpty())
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 py-6 sm:py-8">
                @foreach ($stats as $stat)
                    @php
                        $icon = $stat->icon ? asset('storage/' . ltrim($stat->icon, '/')) : null;
                        $text1 = $stat->getTranslation('text1', $locale, false) ?? $stat->getTranslation('text1', 'en', false);
                        $text2 = $stat->getTranslation('text2', $locale, false) ?? $stat->getTranslation('text2', 'en', false);
                    @endphp
                    <div class="flex items-center justify-center gap-3 p-4 rounded-xl bg-gray-50 hover:bg-primary/5 transition-colors">
                        <div class="flex-shrink-0">
                            @if ($icon)
                                <img src="{{ $icon }}" alt="{{ $text1 ?? 'Stat' }}" class="w-10 h-10 object-contain">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                    {{ $text1 ? substr($text1, 0, 1) : 'Q' }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $text1 ?? '' }}</p>
                            <p class="text-sm sm:text-base font-bold text-gray-900">{{ $text2 ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- Shop by Category Carousel --}}
<section class="py-12 sm:py-16 lg:py-20 bg-gray-50" x-data="{
        currentSlide: 0,
        itemsPerPage: window.innerWidth >= 1280 ? 6 : window.innerWidth >= 1024 ? 5 : window.innerWidth >= 768 ? 4 : window.innerWidth >= 640 ? 3 : 2,
        totalItems: {{ $categories->count() }},
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
        get canGoPrev() { return this.currentSlide > 0 },
        next() { if(this.canGoNext) this.currentSlide++ },
        prev() { if(this.canGoPrev) this.currentSlide-- },
        init() {
            window.addEventListener('resize', () => {
                this.itemsPerPage = window.innerWidth >= 1280 ? 6 : window.innerWidth >= 1024 ? 5 : window.innerWidth >= 768 ? 4 : window.innerWidth >= 640 ? 3 : 2;
                if(this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
            });
        }
    }">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8 sm:mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                    {{ gtrans('Shop by Category') }}
                </h2>
                <p class="text-sm sm:text-base text-gray-600">
                    {{ gtrans('Browse our extensive collection') }}
                </p>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <button @click="prev()" :disabled="!canGoPrev"
                    :class="!canGoPrev ? 'opacity-40 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                    class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="next()" :disabled="!canGoNext"
                    :class="!canGoNext ? 'opacity-40 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                    class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out"
                :style="`transform: translateX(-${currentSlide * 100}%)`">
                @foreach ($categories->chunk(6) as $categoryChunk)
                <div class="w-full flex-shrink-0">
                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5">
                        @foreach ($categoryChunk as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                            class="group bg-white rounded-xl p-5 sm:p-6 text-center hover:shadow-lg transition-all duration-300 border border-transparent hover:border-primary/20">
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gray-50 flex items-center justify-center mb-4 group-hover:bg-primary/10 transition-colors">
                                    @if ($category->{'category-icon'})
                                    <img src="{{ asset('/storage/' . $category->{'category-icon'}) }}"
                                        alt="{{ $category->{'category-name'} }}"
                                        class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                                    @else
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 group-hover:text-primary transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    @endif
                                </div>
                                <h3
                                    class="text-sm font-semibold text-gray-900 group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $category->{'category-name'} }}
                                </h3>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dots Indicator --}}
        <div class="flex justify-center gap-2 mt-6" x-show="totalSlides > 1">
            <template x-for="i in totalSlides" :key="i">
                <button @click="currentSlide = i - 1"
                    :class="currentSlide === i - 1 ? 'bg-primary w-8' : 'bg-gray-300 w-2'"
                    class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>
    </div>
</section>

{{-- Featured Promotional Banners Carousel --}}
@php
    $chunks = $cards->take(4)->chunk(2);
@endphp
<section class="py-8 sm:py-12 bg-white" x-data="{
        currentBanner: 0,
        totalBanners: {{ $chunks->count() }},
        autoplay: null,
        init() {
            this.startAutoplay();
        },
        startAutoplay() {
            this.autoplay = setInterval(() => {
                this.next();
            }, 5000);
        },
        stopAutoplay() {
            clearInterval(this.autoplay);
        },
        next() {
            this.currentBanner = (this.currentBanner + 1) % this.totalBanners;
        },
        prev() {
            this.currentBanner = this.currentBanner === 0 ? this.totalBanners - 1 : this.currentBanner - 1;
        }
    }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative">
            <div class="overflow-hidden">
                @foreach ($chunks as $chunk)
                <div x-show="currentBanner === {{ $loop->index }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-full"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-full"
                     class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($chunk as $card)
                    @php
                        // remove gradient background so images show directly
                        $bgClass = '';
                        $tags = !empty($card->tags) ? json_decode($card->tags, true) : [];
                        $firstTag = is_array($tags) && count($tags) > 0 ? $tags[0] : 'FEATURED';
                    @endphp
                    <a href="{{ $card->link ?? '#' }}"
                        class="group relative overflow-hidden rounded-2xl bg-transparent min-h-[280px] sm:min-h-[320px] flex items-center">
                        <img src="{{ asset('storage/' . $card->image) }}" alt="{{ $card->title }}"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">

                        <div class="relative z-10 p-8 sm:p-10 lg:p-12">
                            <span
                                class="inline-block bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full mb-4">
                                {{ $firstTag }}
                            </span>
                            <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight">
                                {{ $card->title }}
                            </h3>
                            <p class="text-white/90 text-sm sm:text-base mb-6 max-w-md line-clamp-2">
                                {{ $card->description }}
                            </p>
                            @if ($card->link_text)
                            <span
                                class="inline-flex items-center text-white font-semibold text-sm group-hover:gap-2 transition-all">
                                {{ $card->link_text }}
                                <svg class="w-5 h-5 ml-1 transition-transform group-hover:translate-x-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                @endforeach
            </div>

            {{-- Navigation Arrows --}}
            <button @click="prev()"
                class="hidden lg:flex absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 hover:bg-white rounded-full items-center justify-center shadow-lg transition-all z-10">
                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button @click="next()"
                class="hidden lg:flex absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 hover:bg-white rounded-full items-center justify-center shadow-lg transition-all z-10">
                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- Dots Indicator --}}
        <div class="flex justify-center gap-2 mt-6">
            <template x-for="i in totalBanners" :key="i">
                <button @click="currentBanner = i - 1"
                    :class="currentBanner === i - 1 ? 'bg-primary w-8' : 'bg-gray-300 w-2'"
                    class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>
    </div>
</section>

{{-- Hot Deals Carousel --}}
<section id="deals" class="py-12 sm:py-16 lg:py-20 bg-gray-50" x-data="{
        currentSlide: 0,
        itemsPerPage: window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2,
        totalItems: 8,
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
        get canGoPrev() { return this.currentSlide > 0 },
        next() { if(this.canGoNext) this.currentSlide++ },
        prev() { if(this.canGoPrev) this.currentSlide-- },
        init() {
            window.addEventListener('resize', () => {
                this.itemsPerPage = window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2;
                if(this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
            });
        }
    }">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8 sm:mb-10">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span
                        class="inline-flex items-center gap-1 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                                clip-rule="evenodd" />
                        </svg>
                        HOT
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">
                        {{ gtrans('Deals of the Week') }}
                    </h2>
                </div>
                <p class="text-sm sm:text-base text-gray-600">
                    {{ gtrans('Limited time offers - Don\'t miss out!') }}
                </p>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <button @click="prev()" :disabled="!canGoPrev"
                    :class="!canGoPrev ? 'opacity-40 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                    class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="next()" :disabled="!canGoNext"
                    :class="!canGoNext ? 'opacity-40 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                    class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out"
                :style="`transform: translateX(-${currentSlide * 100}%)`">
                {{-- First Slide --}}
                <div class="w-full flex-shrink-0">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                        @include('client.components.product-card', [
                        'image' => 'images/hp.webp',
                        'badge' => '-20%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Audio',
                        'title' => 'AeroMax Wireless Headphones',
                        'price' => '$199.99',
                        'oldPrice' => '$249.99'
                        ])
                        @include('client.components.product-card', [
                        'image' => 'images/laptop.avif',
                        'badge' => '-15%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Laptops',
                        'title' => 'NovaBook Pro 14" (M3)',
                        'price' => '$1299.00',
                        'oldPrice' => '$1499.00'
                        ])
                        @include('client.components.product-card', [
                        'image' => 'images/phone.jpg',
                        'badge' => '-10%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Smartphones',
                        'title' => 'Quantum X1 5G (256GB)',
                        'price' => '$899.00',
                        'oldPrice' => '$999.00'
                        ])
                        @include('client.components.product-card', [
                        'image' => 'images/tv.jpg',
                        'badge' => '-25%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Televisions',
                        'title' => 'Vision-Pro 55" 4K QLED TV',
                        'price' => '$649.50',
                        'oldPrice' => '$866.00'
                        ])
                    </div>
                </div>

                {{-- Second Slide --}}
                <div class="w-full flex-shrink-0">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                        @include('client.components.product-card', [
                        'image' => 'images/mouse.png',
                        'badge' => '-30%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Gaming',
                        'title' => 'Gaming Mouse Pro RGB',
                        'price' => '$69.99',
                        'oldPrice' => '$99.99'
                        ])
                        @include('client.components.product-card', [
                        'image' => 'images/buds.webp',
                        'badge' => '-18%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Audio',
                        'title' => 'Wireless Earbuds Max',
                        'price' => '$149.99',
                        'oldPrice' => '$182.99'
                        ])
                        @include('client.components.product-card', [
                        'image' => 'images/watch.jpeg',
                        'badge' => '-22%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Wearables',
                        'title' => 'Smartwatch Series 8',
                        'price' => '$389.00',
                        'oldPrice' => '$499.00'
                        ])
                        @include('client.components.product-card', [
                        'image' => 'images/powerbank.jpg',
                        'badge' => '-35%',
                        'badgeColor' => 'bg-red-500',
                        'category' => 'Power',
                        'title' => 'Portable Charger 20000mAh',
                        'price' => '$49.99',
                        'oldPrice' => '$76.99'
                        ])
                    </div>
                </div>
            </div>
        </div>

        {{-- Dots Indicator --}}
        <div class="flex justify-center gap-2 mt-8" x-show="totalSlides > 1">
            <template x-for="i in totalSlides" :key="i">
                <button @click="currentSlide = i - 1"
                    :class="currentSlide === i - 1 ? 'bg-primary w-8' : 'bg-gray-300 w-2'"
                    class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>

        {{-- View All Button Mobile --}}
        <div class="mt-8 text-center lg:hidden">
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-dark transition-colors">
                {{ gtrans('View All Deals') }}
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- New Arrivals Carousel --}}
<section id="new-arrivals" class="py-12 sm:py-16 lg:py-20 bg-white" x-data="{
    currentSlide: 0,
    itemsPerPage: window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2,
    totalItems: {{ isset($newArrivals) ? $newArrivals->count() : 0 }},
    autoplay: null,
    get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
    get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
    get canGoPrev() { return this.currentSlide > 0 },
    next() { if(this.canGoNext) this.currentSlide++; else this.currentSlide = 0 },
    prev() { if(this.canGoPrev) this.currentSlide--; else this.currentSlide = this.totalSlides - 1 },
    init() {
        this.startAutoplay();
        window.addEventListener('resize', () => {
            this.itemsPerPage = window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2;
            if(this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
        });
    },
    startAutoplay() {
        this.autoplay = setInterval(() => { this.next(); }, 4000);
    },
    stopAutoplay() {
        clearInterval(this.autoplay);
    }
    }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8 sm:mb-10">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">
                        {{ gtrans('New Arrivals') }}
                    </h2>
                </div>
                <p class="text-sm sm:text-base text-gray-600">
                    {{ gtrans('The latest tech just landed') }}
                </p>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <button @click="prev()" :class="!canGoPrev ? 'opacity-40' : 'hover:bg-primary hover:text-white'"
                        class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="next()" :class="!canGoNext ? 'opacity-40' : 'hover:bg-primary hover:text-white'"
                        class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out"
                 :style="`transform: translateX(-${currentSlide * 100}%)`">
                @foreach(($newArrivals ?? collect())->chunk(4) as $chunk)
                <div class="w-full flex-shrink-0">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                        @foreach($chunk as $productVariant)
                            @php
                                $isVariant = class_basename($productVariant) === 'ProductVariant';
                                $master = $isVariant ? $productVariant->product : $productVariant;

                                $variantImage = $isVariant ? optional($productVariant->images()->ordered()->first())->image_path : null;
                                $productImage = optional($master->images()->forProduct($master->id)->ordered()->first())->image_path ?? null;

                                if ($variantImage) {
                                    $img = 'storage/' . $variantImage;
                                } elseif ($productImage) {
                                    $img = 'storage/' . $productImage;
                                } elseif (!empty($master->cover_image)) {
                                    $img = 'storage/' . $master->cover_image;
                                } else {
                                    $img = 'images/no-image.png';
                                }

                                $locale = app()->getLocale() ?? 'en';
                                $baseName = $master?->getTranslation('name', $locale) ?? '';
                                if (is_array($baseName)) { $baseName = implode(' ', $baseName); }
                                $variantName = $isVariant ? ($productVariant->getTranslation('variant_name', $locale) ?? '') : '';
                                if (is_array($variantName)) { $variantName = implode(' ', $variantName); }
                                $title = trim($baseName . ($variantName ? ' - ' . $variantName : '')) ?: 'Product';

                                $category = $master->category?->name ?? 'General';
                                if (is_array($category)) { $category = implode(' ', $category); }

                                $price = $isVariant ? ('$' . number_format($productVariant->price ?? 0, 2)) : ('$' . number_format($master->real_price ?? $master->price ?? 0, 2));
                            @endphp

                            @include('client.components.product-card', [
                                'image' => $img,
                                'badge' => 'NEW',
                                'badgeColor' => 'bg-green-500',
                                'category' => $category,
                                'title' => $title,
                                'price' => $price,
                                'url' => route('products.detail', $master->slug ?? $master->id),
                                'product_id' => $master->id,
                                'variant_id' => $isVariant ? $productVariant->id : null,
                            ])
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dots Indicator --}}
        <div class="flex justify-center gap-2 mt-8" x-show="totalSlides > 1">
            <template x-for="i in totalSlides" :key="i">
                <button @click="currentSlide = i - 1"
                        :class="currentSlide === i - 1 ? 'bg-primary w-8' : 'bg-gray-300 w-2'"
                        class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>
    </div>
</section>

{{-- Bestsellers Carousel --}}
@php
    $bestSellerCount = ($bestSellers ?? collect())->count();
@endphp

@if ($bestSellerCount > 0)
<section id="bestsellers" class="py-12 sm:py-16 lg:py-20 bg-gray-50" x-data="{
    currentSlide: 0,
    itemsPerPage: window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2,
    totalItems: {{ $bestSellerCount }},
    get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
    get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
    get canGoPrev() { return this.currentSlide > 0 },
    next() { if(this.canGoNext) this.currentSlide++ },
    prev() { if(this.canGoPrev) this.currentSlide-- },
    init() {
        window.addEventListener('resize', () => {
            this.itemsPerPage = window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2;
            if(this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
        });
    }
}">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8 sm:mb-10">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        TOP
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">
                        {{ gtrans('Best Sellers') }}
                    </h2>
                </div>
                <p class="text-sm sm:text-base text-gray-600">
                    {{ gtrans('Most loved by our customers') }}
                </p>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <button @click="prev()" :disabled="!canGoPrev" :class="!canGoPrev ? 'opacity-40 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                        class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="next()" :disabled="!canGoNext" :class="!canGoNext ? 'opacity-40 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                        class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out"
                 :style="`transform: translateX(-${currentSlide * 100}%)`">
                @foreach(($bestSellers ?? collect())->chunk(4) as $chunk)
                <div class="w-full shrink-0">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                        @foreach($chunk as $productVariant)
                            @php
                                $isVariant = class_basename($productVariant) === 'ProductVariant';
                                $master = $isVariant ? $productVariant->product : $productVariant;

                                $variantImage = $isVariant ? optional($productVariant->images()->ordered()->first())->image_path : null;
                                $productImage = optional($master->images()->forProduct($master->id)->ordered()->first())->image_path ?? null;

                                if ($variantImage) {
                                    $img = 'storage/' . $variantImage;
                                } elseif ($productImage) {
                                    $img = 'storage/' . $productImage;
                                } elseif (!empty($master->cover_image)) {
                                    $img = 'storage/' . $master->cover_image;
                                } else {
                                    $img = 'images/mouse.png';
                                }

                                $locale = app()->getLocale() ?? 'en';
                                $baseName = $master?->getTranslation('name', $locale) ?? '';
                                if (is_array($baseName)) { $baseName = implode(' ', $baseName); }
                                $variantName = $isVariant ? ($productVariant->getTranslation('variant_name', $locale) ?? '') : '';
                                if (is_array($variantName)) { $variantName = implode(' ', $variantName); }
                                $title = trim($baseName . ($variantName ? ' - ' . $variantName : '')) ?: 'Product';

                                $category = $master->category?->name ?? 'General';
                                if (is_array($category)) { $category = implode(' ', $category); }

                                $price = $isVariant ? ('$' . number_format($productVariant->price ?? 0, 2)) : ('$' . number_format($master->real_price ?? $master->price ?? 0, 2));
                            @endphp

                            @include('client.components.product-card', [
                                'image' => $img,
                                'badge' => 'TOP',
                                'badgeColor' => 'bg-orange-500',
                                'category' => $category,
                                'title' => $title,
                                'price' => $price,
                                'url' => route('products.detail', $master->slug ?? $master->id),
                                'product_id' => $master->id,
                                'variant_id' => $isVariant ? $productVariant->id : null,
                            ])
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dots Indicator --}}
        <div class="flex justify-center gap-2 mt-8" x-show="totalSlides > 1">
            <template x-for="i in totalSlides" :key="i">
                <button @click="currentSlide = i - 1"
                        :class="currentSlide === i - 1 ? 'bg-primary w-8' : 'bg-gray-300 w-2'"
                        class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>
    </div>
</section>
@endif

{{-- Brands Carousel (CSS marquee for smooth continuous scroll) --}}
<section id="brands" class="py-12 sm:py-16 bg-white border-y border-gray-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                {{ gtrans('Trusted Brands') }}
            </h2>
            <p class="text-sm sm:text-base text-gray-600">
                {{ gtrans('We partner with the world\'s leading technology brands') }}
            </p>
        </div>

        @php $brandCount = ($brands ?? collect())->count(); $duration = max(12, $brandCount * 2); @endphp
        <style>
            .brand-marquee { display: flex; gap: 3rem; align-items: center; }
            .brand-marquee img { display: block; }
            .brand-marquee-wrapper { overflow: hidden; }
            .brand-marquee-inner { display: flex; gap: 3rem; animation: marquee linear infinite; }
            .brand-marquee-inner:hover { animation-play-state: paused; }
            @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        </style>

        <div class="brand-marquee-wrapper">
            <div class="brand-marquee-inner" style="animation-duration: {{ $duration }}s;">
                <div class="brand-marquee">
                    @foreach(($brands ?? collect()) as $brand)
                        @php
                            $imgPath = $brand->image ? asset('storage/' . $brand->image) : 'https://logo.clearbit.com/' . ($brand->slug ?? preg_replace('/\s+/', '', strtolower($brand->name)));
                        @endphp
                        <a href="{{ route('brand-products', $brand->slug ?? $brand->id) }}" class="flex-shrink-0">
                            <img src="{{ $imgPath }}" alt="{{ $brand->name }}" class="h-8 lg:h-10 w-auto grayscale opacity-60 hover:opacity-100 hover:grayscale-0 transition-all cursor-pointer">
                        </a>
                    @endforeach
                </div>

                {{-- Duplicate for seamless loop --}}
                <div class="brand-marquee" aria-hidden="true">
                    @foreach(($brands ?? collect()) as $brand)
                        @php
                            $imgPath = $brand->image ? asset('storage/' . $brand->image) : 'https://logo.clearbit.com/' . ($brand->slug ?? preg_replace('/\s+/', '', strtolower($brand->name)));
                        @endphp
                        <a href="{{ route('brand-products', $brand->slug ?? $brand->id) }}" class="flex-shrink-0">
                            <img src="{{ $imgPath }}" alt="{{ $brand->name }}" class="h-8 lg:h-10 w-auto grayscale opacity-60 hover:opacity-100 hover:grayscale-0 transition-all cursor-pointer">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Why Choose Section --}}
<section class="py-12 sm:py-16 lg:py-20 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">
                {{ gtrans('Why Shop With Us?') }}
            </h2>
            <p class="text-sm sm:text-base text-gray-600 max-w-2xl mx-auto">
                {{ gtrans('Experience excellence in every purchase') }}
            </p>
        </div>

        @php
            $whyShopWithUs = isset($whyShopWithUs) ? $whyShopWithUs : collect();
            $locale = app()->getLocale();
        @endphp

        @if ($whyShopWithUs->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                @foreach ($whyShopWithUs as $item)
                    @php
                        $title = $item->getTranslation('title', $locale, false) ?? $item->getTranslation('title', 'en', false);
                        $description = $item->getTranslation('description', $locale, false) ?? $item->getTranslation('description', 'en', false);
                        $icon = $item->icon ? asset('storage/' . ltrim($item->icon, '/')) : null;
                    @endphp
                    <div class="bg-white rounded-2xl p-6 lg:p-8 text-center hover:shadow-lg transition-all border border-gray-100">
                        <div class="w-14 h-14 lg:w-16 lg:h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            @if ($icon)
                                <img src="{{ $icon }}" alt="{{ $title ?? 'Icon' }}" class="w-7 h-7 lg:w-8 lg:h-8 object-contain">
                            @else
                                <svg class="w-7 h-7 lg:w-8 lg:h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-3">
                            {{ $title ?? 'Feature' }}
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $description ?? '' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500">
                {{ gtrans('No features available at the moment.') }}
            </div>
        @endif
    </div>
</section>
@endsection
