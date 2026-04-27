@extends('client.layouts.app')

@section('content')
    {{-- Hero Slider Section --}}
    @include('client.components.slider')

    {{-- ─── Quick Stats Bar ─────────────────────────────────────────────────── --}}
    <section class="bg-white border-b border-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $stats = isset($quickStats) ? $quickStats->take(4) : collect();
                $locale = app()->getLocale();
            @endphp

            @if ($stats->isNotEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-6 pb-4 sm:pt-8 sm:pb-6">
                    @foreach ($stats as $stat)
                        @php
                            $icon  = $stat->icon ? asset('storage/' . ltrim($stat->icon, '/')) : null;
                            $text1 = $stat->getTranslation('text1', $locale, false) ?? $stat->getTranslation('text1', 'en', false);
                            $text2 = $stat->getTranslation('text2', $locale, false) ?? $stat->getTranslation('text2', 'en', false);
                        @endphp
                        <div class="section-card stat-item flex items-center justify-center gap-3 p-4 rounded-2xl
                                    bg-secondary-bg border border-gray-100 hover:border-primary/25
                                    shadow-[0_2px_8px_rgba(0,0,0,0.05)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.10)]
                                    transition-all duration-300 cursor-default">
                            <div class="shrink-0 w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                @if ($icon)
                                    <img src="{{ $icon }}" alt="{{ $text1 ?? 'Stat' }}" class="w-6 h-6 object-contain">
                                @else
                                    <span class="text-primary font-bold text-sm">{{ $text1 ? strtoupper(substr($text1, 0, 1)) : 'Q' }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">{{ $text1 ?? '' }}</p>
                                <p class="text-sm sm:text-base font-bold text-gray-900">{{ $text2 ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ─── Shop by Category Carousel ──────────────────────────────────────── --}}
    @include('client.components.categorySection')

    {{-- ─── Featured Promotional Banners ──────────────────────────────────── --}}
    @php $bannerChunks = $cards->take(4)->chunk(2); @endphp
    @if ($cards->isNotEmpty())
        <section class="py-8 sm:py-12 lg:py-16 bg-white" x-data="{
            currentSlide: 0,
            itemsPerPage: window.innerWidth >= 1024 ? 2 : 1,
            totalItems: {{ $cards->take(4)->count() }},
            autoplay: null,
            get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
            get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
            get canGoPrev() { return this.currentSlide > 0 },
            next() { if (this.canGoNext) this.currentSlide++; else this.currentSlide = 0 },
            prev() { if (this.canGoPrev) this.currentSlide--; else this.currentSlide = this.totalSlides - 1 },
            init() {
                this.startAutoplay();
                window.addEventListener('resize', () => {
                    this.itemsPerPage = window.innerWidth >= 1024 ? 2 : 1;
                    if (this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
                });
            },
            startAutoplay() { this.autoplay = setInterval(() => { this.next(); }, 9000); },
            stopAutoplay()  { clearInterval(this.autoplay); }
        }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Section Header --}}
                <div class="text-center mb-10 sm:mb-14">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                        {{ statictranslations('Featured Promotions') }}
                    </h2>
                    <p class="mt-3 text-sm sm:text-base text-gray-400 max-w-md mx-auto leading-relaxed">
                        {{ statictranslations('Handpicked deals just for you') }}
                    </p>
                    <div class="section-header-divider">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                {{-- Slider --}}
                <div class="relative flex items-center gap-3 sm:gap-4">
                    <button @click="prev()" :disabled="!canGoPrev"
                        :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                        class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm z-0">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex-1 overflow-hidden">
                        <div class="flex transition-transform duration-500 ease-in-out"
                            :style="`transform: translateX(-${currentSlide * 100}%)`">
                            @foreach ($bannerChunks as $chunk)
                                @if ($chunk->count() > 0)
                                    <div class="w-full shrink-0">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 py-2">
                                            @foreach ($chunk as $card)
                                                @php
                                                    $tags     = !empty($card->tags) ? json_decode($card->tags, true) : [];
                                                    $firstTag = is_array($tags) && count($tags) > 0 ? $tags[0] : 'FEATURED';
                                                @endphp
                                                <a href="{{ $card->link ?? '#' }}"
                                                    class="promo-card group relative overflow-hidden rounded-2xl bg-gray-900 min-h-70 sm:min-h-80 flex items-center
                                                    hover:transition-shadow duration-300">
                                                    <img src="{{ asset('storage/' . $card->image) }}"
                                                        alt="{{ $card->title }}"
                                                        class="absolute inset-0 w-full h-full object-fill z-0">
                                                    <div class="relative z-10 p-8 sm:p-10 lg:p-12">
                                                        <span class="inline-block bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wide">
                                                            {{ $firstTag }}
                                                        </span>
                                                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight">
                                                            {{ $card->title }}
                                                        </h3>
                                                        <p class="text-white/80 text-sm sm:text-base mb-6 max-w-md line-clamp-2">
                                                            {{ $card->description }}
                                                        </p>
                                                        @if ($card->link_text)
                                                            <span class="promo-cta-arrow">
                                                                {{ $card->link_text }}
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                                </svg>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <button @click="next()" :disabled="!canGoNext"
                        :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                        class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm z-0">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                {{-- Dots --}}
                <div class="flex items-center justify-center gap-4 mt-8 z-0" x-show="totalSlides > 1">
                    <button @click="prev()" :disabled="!canGoPrev"
                        :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                        class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-1.5">
                        <template x-for="i in totalSlides" :key="i">
                            <button @click="currentSlide = i - 1"
                                :class="currentSlide === i - 1 ? 'bg-primary w-6 h-2 rounded-full' : 'bg-gray-300 hover:bg-gray-400 w-2 h-2 rounded-full'"
                                class="transition-all duration-300 ease-out"></button>
                        </template>
                    </div>
                    <button @click="next()" :disabled="!canGoNext"
                        :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                        class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    @endif

    {{-- ─── Deals & Discounts Carousel ─────────────────────────────────────── --}}
    @php
        $dealsChunks = ($deals ?? collect())->chunk(4);
        $dealsCount  = ($deals ?? collect())->count();
    @endphp
    @if ($dealsCount > 0)
        <section id="deals" class="py-8 sm:py-12 lg:py-16 bg-secondary-bg" x-data="{
            currentSlide: 0,
            itemsPerPage: window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2,
            totalItems: {{ $dealsCount }},
            autoplay: null,
            get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
            get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
            get canGoPrev() { return this.currentSlide > 0 },
            next() { if (this.canGoNext) this.currentSlide++; else this.currentSlide = 0 },
            prev() { if (this.canGoPrev) this.currentSlide--; else this.currentSlide = this.totalSlides - 1 },
            init() {
                this.startAutoplay && this.startAutoplay();
                window.addEventListener('resize', () => {
                    this.itemsPerPage = window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2;
                    if (this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
                });
            },
            startAutoplay() { this.autoplay = setInterval(() => { this.next(); }, 9000); },
            stopAutoplay()  { clearInterval(this.autoplay); }
        }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Section Header --}}
                <div class="text-center mb-10 sm:mb-14">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                        {{ statictranslations('Deals & Discounts') }}
                    </h2>
                    <p class="mt-3 text-sm sm:text-base text-gray-400 max-w-md mx-auto leading-relaxed">
                        {{ statictranslations('Grab the best offers on top products') }}
                    </p>
                    <div class="section-header-divider">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                {{-- Slider --}}
                <div class="relative flex items-center gap-3 sm:gap-4">
                    <button @click="prev()" :disabled="!canGoPrev"
                        :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                        class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex-1 carousel-track">
                        <div class="flex transition-transform duration-500 ease-in-out"
                            :style="`transform: translateX(-${currentSlide * 100}%)`">
                            @foreach ($dealsChunks as $chunk)
                                @if ($chunk->count() > 0)
                                    <div class="w-full shrink-0">
                                        {{-- Mobile: compact 2-col grid --}}
                                        <div class="grid grid-cols-2 gap-3 px-1 md:hidden">
                                            @foreach ($chunk as $productVariant)
                                                @include('client.partials.product-card-mobile', ['product' => $productVariant])
                                            @endforeach
                                        </div>
                                        {{-- Desktop: full grid --}}
                                        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 px-1">
                                            @foreach ($chunk as $productVariant)
                                                @include('client.partials.product-card', ['product' => $productVariant])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <button @click="next()" :disabled="!canGoNext"
                        :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                        class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                {{-- Dots --}}
                <div class="flex items-center justify-center gap-4 mt-8" x-show="totalSlides > 1">
                    <button @click="prev()" :disabled="!canGoPrev"
                        :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                        class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-1.5">
                        <template x-for="i in totalSlides" :key="i">
                            <button @click="currentSlide = i - 1"
                                :class="currentSlide === i - 1 ? 'bg-primary w-6 h-2 rounded-full' : 'bg-gray-300 hover:bg-gray-400 w-2 h-2 rounded-full'"
                                class="transition-all duration-300 ease-out"></button>
                        </template>
                    </div>
                    <button @click="next()" :disabled="!canGoNext"
                        :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                        class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                {{-- View All Deals Button Below Cards --}}
                <div class="flex justify-center mt-8">
                    <a href="{{ route('products.deals') }}"
                       class="btn-primary inline-flex items-center">
                        {{ statictranslations('View All Deals') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ─── New Arrivals Carousel ───────────────────────────────────────────── --}}
    @php $newArrivalsChunks = ($newArrivals ?? collect())->chunk(4); @endphp
    <section id="new-arrivals" class="py-8 sm:py-12 lg:py-16 bg-white" x-data="{
        currentSlide: 0,
        itemsPerPage: window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2,
        totalItems: {{ isset($newArrivals) ? $newArrivals->count() : 0 }},
        autoplay: null,
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
        get canGoPrev() { return this.currentSlide > 0 },
        next() { if (this.canGoNext) this.currentSlide++; else this.currentSlide = 0 },
        prev() { if (this.canGoPrev) this.currentSlide--; else this.currentSlide = this.totalSlides - 1 },
        init() {
            this.startAutoplay();
            window.addEventListener('resize', () => {
                this.itemsPerPage = window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2;
                if (this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
            });
        },
        startAutoplay() { this.autoplay = setInterval(() => { this.next(); }, 9000); },
        stopAutoplay()  { clearInterval(this.autoplay); }
    }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Section Header --}}
            <div class="text-center mb-10 sm:mb-14">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    {{ statictranslations('New Arrivals') }}
                </h2>
                <p class="mt-3 text-sm sm:text-base text-gray-400 max-w-md mx-auto leading-relaxed">
                    {{ statictranslations('The latest tech just landed') }}
                </p>
                <div class="section-header-divider">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Slider --}}
            <div class="relative flex items-center gap-3 sm:gap-4">
                <button @click="prev()" :disabled="!canGoPrev"
                    :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                    class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="flex-1 carousel-track">
                    <div class="flex transition-transform duration-500 ease-in-out"
                        :style="`transform: translateX(-${currentSlide * 100}%)`">
                        @foreach ($newArrivalsChunks as $chunk)
                            @if ($chunk->count() > 0)
                                <div class="w-full shrink-0">
                                    {{-- Mobile: compact 2-col grid --}}
                                    <div class="grid grid-cols-2 gap-3 px-1 md:hidden">
                                        @foreach ($chunk as $productVariant)
                                            @include('client.partials.product-card-mobile', ['product' => $productVariant])
                                        @endforeach
                                    </div>
                                    {{-- Desktop: full grid --}}
                                    <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 px-1">
                                        @foreach ($chunk as $productVariant)
                                            @include('client.partials.product-card', ['product' => $productVariant])
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <button @click="next()" :disabled="!canGoNext"
                    :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                    class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            {{-- Dots --}}
            <div class="flex items-center justify-center gap-4 mt-8" x-show="totalSlides > 1">
                <button @click="prev()" :disabled="!canGoPrev"
                    :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                    class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="flex items-center gap-1.5">
                    <template x-for="i in totalSlides" :key="i">
                        <button @click="currentSlide = i - 1"
                            :class="currentSlide === i - 1 ? 'bg-primary w-6 h-2 rounded-full' : 'bg-gray-300 hover:bg-gray-400 w-2 h-2 rounded-full'"
                            class="transition-all duration-300 ease-out"></button>
                    </template>
                </div>
                <button @click="next()" :disabled="!canGoNext"
                    :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                    class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    {{-- ─── Best Sellers Carousel ───────────────────────────────────────────── --}}
    @php $bestSellerCount = ($bestSellers ?? collect())->count(); @endphp
    @if ($bestSellerCount > 0)
        <section id="bestsellers" class="py-8 sm:py-12 lg:py-16 bg-secondary-bg" x-data="{
            currentSlide: 0,
            itemsPerPage: window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2,
            totalItems: {{ $bestSellerCount }},
            get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
            get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
            get canGoPrev() { return this.currentSlide > 0 },
            next() { if (this.canGoNext) this.currentSlide++ },
            prev() { if (this.canGoPrev) this.currentSlide-- },
            init() {
                window.addEventListener('resize', () => {
                    this.itemsPerPage = window.innerWidth >= 1024 ? 4 : window.innerWidth >= 640 ? 2 : 2;
                    if (this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
                });
            }
        }">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Section Header --}}
                <div class="text-center mb-10 sm:mb-14">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                        {{ statictranslations('Best Sellers') }}
                    </h2>
                    <p class="mt-3 text-sm sm:text-base text-gray-400 max-w-md mx-auto leading-relaxed">
                        {{ statictranslations('Most loved by our customers') }}
                    </p>
                    <div class="section-header-divider">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                {{-- Slider --}}
                <div class="relative flex items-center gap-3 sm:gap-4">
                    <button @click="prev()" :disabled="!canGoPrev"
                        :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                        class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex-1 carousel-track">
                        <div class="flex transition-transform duration-500 ease-in-out"
                            :style="`transform: translateX(-${currentSlide * 100}%)`">
                            @php $bestSellerChunks = ($bestSellers ?? collect())->chunk(4); @endphp
                            @foreach ($bestSellerChunks as $chunk)
                                @if ($chunk->count() > 0)
                                    <div class="w-full shrink-0">
                                        {{-- Mobile: compact 2-col grid --}}
                                        <div class="grid grid-cols-2 gap-3 px-1 md:hidden">
                                            @foreach ($chunk as $productVariant)
                                                @include('client.partials.product-card-mobile', ['product' => $productVariant])
                                            @endforeach
                                        </div>
                                        {{-- Desktop: full grid --}}
                                        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 px-1">
                                            @foreach ($chunk as $productVariant)
                                                @include('client.partials.product-card', ['product' => $productVariant])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <button @click="next()" :disabled="!canGoNext"
                        :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                        class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                {{-- Dots --}}
                <div class="flex items-center justify-center gap-4 mt-8" x-show="totalSlides > 1">
                    <button @click="prev()" :disabled="!canGoPrev"
                        :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                        class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex items-center gap-1.5">
                        <template x-for="i in totalSlides" :key="i">
                            <button @click="currentSlide = i - 1"
                                :class="currentSlide === i - 1 ? 'bg-primary w-6 h-2 rounded-full' : 'bg-gray-300 hover:bg-gray-400 w-2 h-2 rounded-full'"
                                class="transition-all duration-300 ease-out"></button>
                        </template>
                    </div>
                    <button @click="next()" :disabled="!canGoNext"
                        :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                        class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    @endif

    {{-- ─── Trusted Brands Marquee ──────────────────────────────────────────── --}}
    <section id="brands" class="py-8 sm:py-12 lg:py-16 bg-white border-y border-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10 sm:mb-14">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    {{ statictranslations('Trusted Brands') }}
                </h2>
                <p class="mt-3 text-sm sm:text-base text-gray-400 max-w-md mx-auto leading-relaxed">
                    {{ statictranslations("We partner with the world's leading technology brands") }}
                </p>
                <div class="section-header-divider">
                    <span></span><span></span><span></span>
                </div>
            </div>

            @php
                $brandCount = ($brands ?? collect())->count();
                $duration   = max(12, $brandCount * 2);
            @endphp

            <div class="brand-marquee-wrapper">
                <div class="brand-marquee-inner" style="animation-duration: {{ $duration }}s;">
                    @foreach ([1, 2] as $_)
                        <div class="brand-marquee" @if($_ === 2) aria-hidden="true" @endif>
                            @foreach ($brands ?? collect() as $brand)
                                @php
                                    $imgPath = $brand->image
                                        ? asset('storage/' . $brand->image)
                                        : 'https://logo.clearbit.com/' . ($brand->slug ?? preg_replace('/\s+/', '', strtolower($brand->name)));
                                @endphp
                                <a href="javascript:void(0)" class="shrink-0">
                                    <img src="{{ $imgPath }}" alt="{{ $brand->name }}"
                                        class="h-8 lg:h-10 w-auto grayscale opacity-50 hover:opacity-100 hover:grayscale-0 transition-all duration-300 cursor-pointer">
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Why Shop With Us ────────────────────────────────────────────────── --}}
    <section class="py-8 sm:py-12 lg:py-16 bg-secondary-bg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10 sm:mb-14">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    {{ statictranslations('Why Shop With Us?') }}
                </h2>
                <p class="mt-3 text-sm sm:text-base text-gray-400 max-w-md mx-auto leading-relaxed">
                    {{ statictranslations('Experience excellence in every purchase') }}
                </p>
                <div class="section-header-divider">
                    <span></span><span></span><span></span>
                </div>
            </div>

            @php
                $whyShopWithUs = isset($whyShopWithUs) ? $whyShopWithUs : collect();
                $locale        = app()->getLocale();
            @endphp

            @if ($whyShopWithUs->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 lg:gap-8 py-2">
                    @foreach ($whyShopWithUs as $item)
                        @php
                            $title       = $item->getTranslation('title', $locale, false) ?? $item->getTranslation('title', 'en', false);
                            $description = $item->getTranslation('description', $locale, false) ?? $item->getTranslation('description', 'en', false);
                            $icon        = $item->icon ? asset('storage/' . ltrim($item->icon, '/')) : null;
                        @endphp
                        <div class="section-card bg-white rounded-2xl p-6 lg:p-8 text-center
                                    shadow-[0_2px_8px_rgba(0,0,0,0.05)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.10)]
                                    border border-gray-100 hover:border-primary/20
                                    transition-all duration-300 cursor-default">
                            <div class="w-14 h-14 lg:w-16 lg:h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-colors duration-300 group-hover:bg-primary/15">
                                @if ($icon)
                                    <img src="{{ $icon }}" alt="{{ $title ?? 'Icon' }}" class="w-7 h-7 lg:w-8 lg:h-8 object-contain">
                                @else
                                    <svg class="w-7 h-7 lg:w-8 lg:h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-3">{{ $title ?? 'Feature' }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{!! $description ?? '' !!}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-400 py-8">
                    {{ statictranslations('No features available at the moment.') }}
                </div>
            @endif
        </div>
    </section>
@endsection

{{-- WhatsApp Chat Floating Button --}}
@php
    $whatsappNumber = preg_replace('/[^0-9]/', '', get_site_setting('phone', ''));
@endphp
<a href="https://wa.me/{{ $whatsappNumber }}?text=Hi" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
   class="fixed z-50 bottom-6 right-6 md:bottom-8 md:right-8 bg-green-500 hover:bg-green-600 rounded-full shadow-lg p-3 flex items-center justify-center transition-colors duration-300"
   style="box-shadow: 0 4px 16px rgba(0,0,0,0.18);">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="white">
        <path d="M16 3C9.373 3 4 8.373 4 15c0 2.637.86 5.13 2.484 7.2L4 29l7.016-2.293A12.93 12.93 0 0 0 16 27c6.627 0 12-5.373 12-12S22.627 3 16 3zm0 22.5c-2.07 0-4.09-.57-5.83-1.65l-.415-.25-4.17 1.36 1.37-4.06-.27-.42A9.98 9.98 0 0 1 6 15c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10zm5.13-7.47c-.28-.14-1.65-.81-1.9-.9-.25-.09-.43-.14-.61.14-.18.28-.7.9-.86 1.08-.16.18-.32.2-.6.07-.28-.14-1.18-.44-2.25-1.4-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.32.42-.48.14-.16.18-.28.28-.46.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.02-.22-.53-.45-.46-.61-.47-.16-.01-.34-.01-.52-.01-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.3 0 1.36.99 2.68 1.13 2.87.14.18 1.95 2.98 4.73 4.06.66.28 1.18.45 1.58.58.66.21 1.26.18 1.74.11.53-.08 1.65-.67 1.88-1.32.23-.65.23-1.2.16-1.32-.07-.12-.25-.18-.53-.32z"/>
    </svg>
</a>
