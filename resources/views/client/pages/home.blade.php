@extends('client.layouts.app')

@section('title', statictranslations('Ezone Retails Morocco, Sharp & Roch Home Appliances in Casablanca'))
@section('description', statictranslations('Ezone Retails Morocco, Sharp & Roch Home Appliances in Casablanca'))

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
                                    bg-transparent border border-gray-100 hover:border-primary/25
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
                                <a href="{{ route('brand.products', $brand->slug ?? $brand->id) }}" class="shrink-0">
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
