@extends('client.layouts.app')

@php
    $brandName = $brand ? $brand->name : Str::title($brand_slug);

    // 1. Dynamic Hero Banners
    $brandBanners = [];
    if ($brand && $brand->activeBanners && $brand->activeBanners->count() > 0) {
        foreach ($brand->activeBanners as $b) {
            $brandBanners[] = [
                'image' => asset('storage/' . $b->image),
                'alt' => $b->getTranslation('alt_text', app()->getLocale(), false) ?: ($brand->name . ' Banner'),
            ];
        }
    } elseif ($brand && !empty($brand->banner)) {
        $brandBanners[] = [
            'image' => asset('storage/' . $brand->banner),
            'alt' => $brand->name . ' Banner',
        ];
    } else {
        $brandBanners[] = [
            'image' => asset('images/sharp-banner.jpg'),
            'alt' => $brandName . ' Banner',
        ];
    }

    // 2. Dynamic Tagline & Story Description
    $brandSubtitle = $brand ? $brand->getTranslation('tagline', app()->getLocale(), false) : '';
    if (empty($brandSubtitle)) {
        $brandSubtitle = 'In step with your future';
    }

    $brandIntroText = $brand ? $brand->getTranslation('description', app()->getLocale(), false) : '';
    if (empty($brandIntroText)) {
        $brandIntroText = 'Explore all products and innovative technologies from ' . $brandName . '.';
    }

    // 3. Dynamic Category Showcase Cards (Only display if configured in Admin)
    $featuredCategories = [];
    if ($brand && $brand->activeCategoryShowcases && $brand->activeCategoryShowcases->count() > 0) {
        foreach ($brand->activeCategoryShowcases as $sc) {
            $catTitle = $sc->getTranslation('title', app()->getLocale(), false);
            if (empty($catTitle) && $sc->category) {
                $catTitle = $sc->category->getTranslation('category-name', app()->getLocale(), false);
            }

            $buttonText = $sc->getTranslation('button_text', app()->getLocale(), false);
            if (empty($buttonText)) {
                $buttonText = statictranslations('View') . ' ' . statictranslations($catTitle ?: 'Products');
            } else {
                $buttonText = statictranslations($buttonText);
            }

            $buttonUrl = $sc->button_url;
            if (empty($buttonUrl)) {
                $buttonUrl = !empty($sc->category_id)
                    ? (url()->current() . '?category=' . $sc->category_id . '#products-catalog')
                    : '#products-catalog';
            }

            $featuredCategories[] = [
                'id' => $sc->slug ?: ('category-' . $sc->id),
                'title' => $catTitle ?: 'Category',
                'description' => $sc->getTranslation('description', app()->getLocale(), false) ?: '',
                'image' => $sc->image ? asset('storage/' . $sc->image) : asset('images/no_image.jpg'),
                'button_text' => $buttonText,
                'button_url' => $buttonUrl,
            ];
        }
    }
@endphp

@section('title', statictranslations($brandName . ' Products in Morocco — Ezone'))
@section('description', statictranslations('Explore a focused collection of ' . $brandName . ' home appliances and electronics at Ezone Retails Morocco, with products selected to make brand-specific browsing simple and efficient. You can review available items, compare options, and move through the catalog with the same shopping tools used across the site, including clear product cards, pagination, and responsive layout support. This page is useful when you want to see everything we currently carry from ' . $brandName . ' in one place, whether you are searching for a replacement item, comparing models, or discovering new additions. Availability, pricing, and product assortment may change over time to reflect current stock and featured selections.'))

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen pb-12">

        {{-- 1. Hero Image-Only Slider (Inspired by home page slider UI, without text or buttons) --}}
        <section class="relative w-full mb-8 overflow-hidden bg-gray-950 aspect-[16/7] sm:aspect-[16/6] lg:aspect-[1600/562] min-h-[200px] sm:min-h-[280px]"
            x-data="{
                activeSlide: 0,
                slidesCount: {{ count($brandBanners) }},
                progressKey: 0,
                autoplayInterval: null,
                touchStartX: null,
                touchStartY: null,
                startAutoplay() {
                    if (this.slidesCount > 1) {
                        this.autoplayInterval = setInterval(() => { this.next(); }, 5000);
                    }
                },
                stopAutoplay() {
                    if (this.autoplayInterval) {
                        clearInterval(this.autoplayInterval);
                    }
                },
                next() {
                    this.activeSlide = (this.activeSlide === this.slidesCount - 1) ? 0 : this.activeSlide + 1;
                    this.progressKey++;
                },
                prev() {
                    this.activeSlide = (this.activeSlide === 0) ? this.slidesCount - 1 : this.activeSlide - 1;
                    this.progressKey++;
                },
                goTo(index) {
                    this.activeSlide = index;
                    this.progressKey++;
                },
                onTouchStart(event) {
                    const touch = event.touches && event.touches[0];
                    if (!touch) return;
                    this.touchStartX = touch.clientX;
                    this.touchStartY = touch.clientY;
                },
                onTouchEnd(event) {
                    const touch = event.changedTouches && event.changedTouches[0];
                    if (!touch || this.touchStartX === null || this.touchStartY === null) return;
                    const deltaX = touch.clientX - this.touchStartX;
                    const deltaY = touch.clientY - this.touchStartY;
                    this.touchStartX = null;
                    this.touchStartY = null;
                    if (Math.abs(deltaX) < 40 || Math.abs(deltaX) <= Math.abs(deltaY)) return;
                    if (deltaX < 0) this.next();
                    else this.prev();
                }
            }"
            x-init="startAutoplay()"
            @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()"
            @touchstart.passive="onTouchStart($event)"
            @touchend="onTouchEnd($event)">

            {{-- Slides Wrapper --}}
            <div class="relative w-full h-full">
                @foreach ($brandBanners as $index => $banner)
                    <div
                        x-show="activeSlide === {{ $index }}"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 scale-[1.02]"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-500"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-[0.98]"
                        class="absolute inset-0 w-full h-full"
                        style="{{ $index === 0 ? '' : 'display: none;' }}">
                        <img
                            src="{{ $banner['image'] }}"
                            alt="{{ $banner['alt'] ?? ($brand ? $brand->name : $brand_slug) . ' Banner ' . ($index + 1) }}"
                            class="w-full h-full object-cover"
                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                    </div>
                @endforeach
            </div>

            @if (count($brandBanners) > 1)
                {{-- Navigation Arrows (frosted glass like home slider) --}}
                <button @click="prev()"
                    class="slider-nav-btn absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 z-30
                           hidden sm:flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 rounded-full
                           bg-white/20 hover:bg-white/35 text-white backdrop-blur-md border border-white/25 shadow-lg transition-all duration-200 cursor-pointer"
                    aria-label="Previous Slide">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="next()"
                    class="slider-nav-btn absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 z-30
                           hidden sm:flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 rounded-full
                           bg-white/20 hover:bg-white/35 text-white backdrop-blur-md border border-white/25 shadow-lg transition-all duration-200 cursor-pointer"
                    aria-label="Next Slide">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Pagination Dots (Pills) at Bottom Right --}}
                <div class="absolute bottom-4 sm:bottom-6 right-4 sm:right-8 z-30 flex items-center gap-2">
                    @foreach ($brandBanners as $index => $banner)
                        <button @click="goTo({{ $index }})"
                            class="rounded-full transition-all duration-300 cursor-pointer shadow-xs"
                            :class="activeSlide === {{ $index }}
                                ? 'w-7 sm:w-8 h-2 sm:h-2.5 bg-primary'
                                : 'w-2 sm:w-2.5 h-2 sm:h-2.5 bg-white/50 hover:bg-white/80'"
                            aria-label="Go to slide {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>

                {{-- Animated Progress Bar at Bottom Edge --}}
                <div class="absolute bottom-0 left-0 w-full h-1 bg-white/10 z-40">
                    <div :key="progressKey" class="brand-slider-progress"></div>
                </div>
            @endif
        </section>

        <div class="container mx-auto px-4 max-w-7xl">

            {{-- 2. Breadcrumbs Navigation --}}
            <nav class="flex text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    {{ Breadcrumbs::render() }}
                </ol>
            </nav>

            @if ($brand)
                {{-- 3. Brand Intro Card --}}
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6 sm:gap-8 bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-200/80 p-6 sm:p-8 lg:p-10 mb-8">
                    @if ($brand->image)
                        <img src="{{ asset('storage/' . $brand->image) }}" alt="{{ $brand->name }}"
                            class="w-32 h-32 sm:w-36 sm:h-36 object-contain rounded-2xl border border-gray-200/80 shadow-xs bg-white p-3 shrink-0"
                            style="object-fit: contain;">
                    @else
                        <div
                            class="w-32 h-32 sm:w-36 sm:h-36 flex items-center justify-center bg-gray-100 rounded-2xl text-4xl text-gray-400 font-bold border border-gray-200 shrink-0">
                            <span>{{ strtoupper(substr($brand->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-3">
                            {{-- {{ strtoupper($brand->name) }} --}}
                            <span class="block text-primary text-xl sm:text-2xl lg:text-3xl font-bold mt-1 tracking-normal">
                                {{ statictranslations($brandSubtitle) }}
                            </span>
                        </h1>

                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed mb-6">
                            {{ statictranslations($brandIntroText) }}
                        </p>

                        <div>
                            <a href="#products-catalog"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary hover:bg-primary-dark text-white font-semibold text-xs sm:text-sm shadow-xs hover:shadow transition-all group/btn">
                                <span>{{ statictranslations('Shop All') }} {{ $brand->name }}</span>
                                <svg class="w-4 h-4 transform group-hover/btn:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                @if (!empty($featuredCategories) && count($featuredCategories) > 0)
                    {{-- 4. Categories Section Heading --}}
                    <div class="mb-8">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ statictranslations('Categories') }}
                        </h2>
                    </div>

                    {{-- 5. Alternating Left / Right Category Cards --}}
                    {{-- Odd cards (1,3,5,7): image LEFT, text RIGHT. Even cards (2,4,6,8): text LEFT, image RIGHT. --}}
                    {{-- On screens below lg, the image is always on top. --}}
                    <div class="space-y-8 sm:space-y-10 mb-16">
                        @foreach ($featuredCategories as $category)
                            @php
                                $imageOnRight = $loop->even;
                            @endphp

                            <div id="{{ $category['id'] }}"
                                 class="scroll-mt-40 bg-white rounded-2xl sm:rounded-3xl border border-gray-200/80 shadow-xs hover:shadow-md transition-all duration-300 overflow-hidden group">

                                <div class="grid grid-cols-1 lg:grid-cols-2 lg:h-[400px]">

                                    {{-- Image / Visual Side --}}
                                    <div class="{{ $imageOnRight ? 'lg:col-start-2' : 'lg:col-start-1' }} lg:row-start-1 h-64 sm:h-80 lg:h-full relative bg-gray-100 overflow-hidden">
                                        <img src="{{ $category['image'] }}"
                                             alt="{{ $category['title'] }}"
                                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </div>

                                    {{-- Content Side: Only Heading, Description, and Button --}}
                                    <div class="{{ $imageOnRight ? 'lg:col-start-1' : 'lg:col-start-2' }} lg:row-start-1 p-6 sm:p-10 lg:p-12 flex flex-col justify-center bg-white">
                                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight mb-3 sm:mb-4">
                                            {{ statictranslations($category['title']) }}
                                        </h3>

                                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed mb-6 sm:mb-8">
                                            {{ statictranslations($category['description']) }}
                                        </p>

                                        <div>
                                            <a href="{{ $category['button_url'] }}"
                                               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary hover:bg-primary-dark text-white font-semibold text-xs sm:text-sm shadow-xs hover:shadow transition-all group/btn">
                                                <span>{{ $category['button_text'] }}</span>
                                                <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- 6. All Products Catalog Section --}}
                <div id="products-catalog" class="scroll-mt-40 mb-8 pt-4">
                    @if (!empty($selected_category_id))
                        @php
                            $currentFilteredCat = \App\Models\Categories::find($selected_category_id);
                        @endphp
                        @if ($currentFilteredCat)
                            <div class="mb-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-semibold">
                                <span>{{ statictranslations('Filtered by:') }} {{ $currentFilteredCat->getTranslation('category-name', app()->getLocale(), false) }}</span>
                                <a href="{{ route('brand.products', $brand->slug) }}#products-catalog" class="hover:text-primary-dark font-bold ml-1 text-sm leading-none" title="{{ statictranslations('Clear filter') }}">&times;</a>
                            </div>
                        @endif
                    @endif

                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ $brand->name }} — {{ statictranslations('All Products') }}
                        </h2>
                        @if ($products && $products->total())
                            <span class="text-sm font-medium text-gray-500">
                                {{ $products->total() }} {{ statictranslations('products available') }}
                            </span>
                        @endif
                    </div>

                    @if ($products && $products->count())
                        <div class="grid grid-cols-2 gap-3 sm:hidden">
                            @foreach ($products as $product)
                                @include('client.partials.product-card-mobile', ['product' => $product])
                            @endforeach
                        </div>
                        <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach ($products as $product)
                                @include('client.partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-500 text-lg">
                            {{ statictranslations('No products found for this brand.') }}
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-16 text-center">
                    <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ statictranslations('Brand Not Found') }}</h1>
                    <p class="text-gray-600 text-lg">{{ statictranslations('The brand') }} "{{ Str::title($brand_slug) }}"
                        {{ statictranslations('could not be found.') }}</p>
                    <a href="{{ route('brands') }}"
                        class="mt-6 inline-block bg-primary text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-primary-dark transition">{{ statictranslations('Back to All Brands') }}</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Slider Styles & Smooth Scrolling Style --}}
    <style>
        html {
            scroll-behavior: smooth;
        }

        .brand-slider-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 100%;
            background: var(--color-primary, #e60012);
            animation: brandSlideProgress 5s linear forwards;
            z-index: 40;
            transform-origin: left;
        }

        @keyframes brandSlideProgress {
            from { width: 0%; }
            to   { width: 100%; }
        }

        .slider-nav-btn {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: all 0.25s ease;
        }

        .slider-nav-btn:hover {
            transform: scale(1.08);
        }
    </style>
@endsection
