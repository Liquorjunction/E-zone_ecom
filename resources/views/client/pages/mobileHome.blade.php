@extends('client.layouts.app')

@section('content')
    {{-- Mobile Hero Slider --}}
        @include('client.components.slider')

    {{-- Mobile Quick Stats --}}
    <section class="bg-white border-b border-gray-100 py-4">
        <div class="px-4">
            @php
                $stats = isset($quickStats) ? $quickStats->take(4) : collect();
                $locale = app()->getLocale();
            @endphp

            @if ($stats->isNotEmpty())
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($stats as $stat)
                        @php
                            $icon = $stat->icon ? asset('storage/' . ltrim($stat->icon, '/')) : null;
                            $text1 = $stat->getTranslation('text1', $locale, false) ?? $stat->getTranslation('text1', 'en', false);
                            $text2 = $stat->getTranslation('text2', $locale, false) ?? $stat->getTranslation('text2', 'en', false);
                        @endphp
                        <div class="flex items-center gap-2 p-3 rounded-lg bg-gray-50">
                            <div class="shrink-0">
                                @if ($icon)
                                    <img src="{{ $icon }}" alt="{{ $text1 ?? 'Stat' }}" class="w-8 h-8 object-contain">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                        {{ $text1 ? substr($text1, 0, 1) : 'Q' }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide truncate">{{ $text1 ?? '' }}</p>
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $text2 ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Mobile Categories Carousel --}}
    <section class="py-6 bg-gray-50" x-data="{
        currentSlide: 0,
        itemsPerPage: 3,
        totalItems: {{ $categories->count() }},
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        next() { if (this.currentSlide < this.totalSlides - 1) this.currentSlide++ },
        prev() { if (this.currentSlide > 0) this.currentSlide-- }
    }">
        <div class="px-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Shop by Category') }}</h2>
                <div class="flex gap-1">
                    <button @click="prev()" :disabled="currentSlide === 0"
                        :class="currentSlide === 0 ? 'opacity-30' : ''"
                        class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="next()" :disabled="currentSlide === totalSlides - 1"
                        :class="currentSlide === totalSlides - 1 ? 'opacity-30' : ''"
                        class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="relative overflow-hidden -mx-2">
                <div class="flex transition-transform duration-300 ease-out px-2"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @php $categoryChunks = $categories->chunk(3); @endphp
                    @foreach ($categoryChunks as $chunk)
                        <div class="w-full shrink-0 px-2">
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($chunk as $category)
                                    <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                        class="flex flex-col items-center gap-2 p-3 bg-white rounded-xl border border-gray-100 active:scale-95 transition-transform">
                                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center">
                                            @if ($category->{'category-icon'})
                                                <img src="{{ asset('/storage/' . $category->{'category-icon'}) }}"
                                                    alt="{{ $category->{'category-name'} }}" class="w-6 h-6 object-contain">
                                            @else
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                                </svg>
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-semibold text-gray-900 text-center line-clamp-2 leading-tight">
                                            {{ $category->{'category-name'} }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Dots --}}
            <div class="flex justify-center gap-1.5 mt-4" x-show="totalSlides > 1">
                <template x-for="i in totalSlides" :key="i">
                    <button @click="currentSlide = i - 1"
                        :class="currentSlide === i - 1 ? 'bg-primary w-6' : 'bg-gray-300 w-1.5'"
                        class="h-1.5 rounded-full transition-all"></button>
                </template>
            </div>
        </div>
    </section>

    {{-- Mobile Featured Banners --}}
    <section class="py-6 bg-white" x-data="{
        currentSlide: 0,
        totalItems: {{ $cards->take(4)->count() }},
        autoplay: null,
        next() { this.currentSlide = (this.currentSlide + 1) % this.totalItems },
        init() {
            this.autoplay = setInterval(() => { this.next(); }, 4000);
        }
    }">
        <div class="px-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ statictranslations('Featured Promotions') }}</h2>

            <div class="relative overflow-hidden rounded-xl">
                <div class="flex transition-transform duration-500 ease-out"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @foreach ($cards->take(4) as $card)
                        <div class="w-full shrink-0">
                            <a href="{{ $card->link ?? '#' }}" class="block relative h-48 rounded-xl overflow-hidden">
                                <img src="{{ asset('storage/' . $card->image) }}" alt="{{ $card->title }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    @php
                                        $tags = !empty($card->tags) ? json_decode($card->tags, true) : [];
                                        $firstTag = is_array($tags) && count($tags) > 0 ? $tags[0] : 'FEATURED';
                                    @endphp
                                    <span class="inline-block bg-white/20 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-full mb-2">
                                        {{ $firstTag }}
                                    </span>
                                    <h3 class="text-base font-bold text-white mb-1 line-clamp-2">{{ $card->title }}</h3>
                                    <p class="text-xs text-white/90 line-clamp-1">{{ $card->description }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Dots --}}
            <div class="flex justify-center gap-1.5 mt-3">
                <template x-for="i in totalItems" :key="i">
                    <button @click="currentSlide = i - 1"
                        :class="currentSlide === i - 1 ? 'bg-primary w-6' : 'bg-gray-300 w-1.5'"
                        class="h-1.5 rounded-full transition-all"></button>
                </template>
            </div>
        </div>
    </section>

    {{-- Mobile New Arrivals --}}
    <section class="py-6 bg-gray-50" x-data="{
        currentSlide: 0,
        itemsPerPage: 2,
        totalItems: {{ isset($newArrivals) ? $newArrivals->count() : 0 }},
        autoplay: null,
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides },
        init() {
            this.autoplay = setInterval(() => { this.next(); }, 3500);
        }
    }">
        <div class="px-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('New Arrivals') }}</h2>
                    <p class="text-xs text-gray-600">{{ statictranslations('Latest products') }}</p>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-primary">
                    {{ statictranslations('View All') }} →
                </a>
            </div>

            <div class="relative overflow-hidden -mx-2">
                <div class="flex transition-transform duration-500 ease-out px-2"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @php $newArrivalsChunks = ($newArrivals ?? collect())->chunk(2); @endphp
                    @foreach ($newArrivalsChunks as $chunk)
                        <div class="w-full shrink-0 px-2">
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($chunk as $productVariant)
                                    @include('client.partials.product-card', ['product' => $productVariant])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Dots --}}
            <div class="flex justify-center gap-1.5 mt-4" x-show="totalSlides > 1">
                <template x-for="i in totalSlides" :key="i">
                    <button @click="currentSlide = i - 1"
                        :class="currentSlide === i - 1 ? 'bg-primary w-6' : 'bg-gray-300 w-1.5'"
                        class="h-1.5 rounded-full transition-all"></button>
                </template>
            </div>
        </div>
    </section>

    {{-- Mobile Bestsellers --}}
    @php $bestSellerCount = ($bestSellers ?? collect())->count(); @endphp
    @if ($bestSellerCount > 0)
        <section class="py-6 bg-white" x-data="{
            currentSlide: 0,
            itemsPerPage: 2,
            totalItems: {{ $bestSellerCount }},
            get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
            next() { if (this.currentSlide < this.totalSlides - 1) this.currentSlide++ }
        }">
            <div class="px-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Best Sellers') }}</h2>
                        <p class="text-xs text-gray-600">{{ statictranslations('Top rated') }}</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-primary">
                        {{ statictranslations('View All') }} →
                    </a>
                </div>

                <div class="relative overflow-hidden -mx-2">
                    <div class="flex transition-transform duration-500 ease-out px-2"
                        :style="`transform: translateX(-${currentSlide * 100}%)`">
                        @php $bestSellerChunks = ($bestSellers ?? collect())->chunk(2); @endphp
                        @foreach ($bestSellerChunks as $chunk)
                            <div class="w-full shrink-0 px-2">
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ($chunk as $productVariant)
                                        @include('client.partials.product-card', ['product' => $productVariant])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Dots --}}
                <div class="flex justify-center gap-1.5 mt-4" x-show="totalSlides > 1">
                    <template x-for="i in totalSlides" :key="i">
                        <button @click="currentSlide = i - 1"
                            :class="currentSlide === i - 1 ? 'bg-primary w-6' : 'bg-gray-300 w-1.5'"
                            class="h-1.5 rounded-full transition-all"></button>
                    </template>
                </div>
            </div>
        </section>
    @endif

    {{-- Mobile Brands --}}
    <section class="py-6 bg-gray-50">
        <div class="px-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">{{ statictranslations('Trusted Brands') }}</h2>

            @php
                $brandCount = ($brands ?? collect())->count();
                $duration = max(15, $brandCount * 2.5);
            @endphp

            <div class="brand-marquee-wrapper">
                <div class="brand-marquee-inner" style="animation-duration: {{ $duration }}s;">
                    <div class="brand-marquee">
                        @foreach ($brands ?? collect() as $brand)
                            @php
                                $imgPath = $brand->image ? asset('storage/' . $brand->image) : 'https://logo.clearbit.com/' . ($brand->slug ?? preg_replace('/\s+/', '', strtolower($brand->name)));
                            @endphp
                            <a href="{{ route('brand.products', $brand->slug ?? $brand->id) }}">
                                <img src="{{ $imgPath }}" alt="{{ $brand->name }}"
                                    class="h-6 w-auto grayscale opacity-60">
                            </a>
                        @endforeach
                    </div>
                    <div class="brand-marquee" aria-hidden="true">
                        @foreach ($brands ?? collect() as $brand)
                            @php
                                $imgPath = $brand->image ? asset('storage/' . $brand->image) : 'https://logo.clearbit.com/' . ($brand->slug ?? preg_replace('/\s+/', '', strtolower($brand->name)));
                            @endphp
                            <a href="{{ route('brand.products', $brand->slug ?? $brand->id) }}">
                                <img src="{{ $imgPath }}" alt="{{ $brand->name }}"
                                    class="h-6 w-auto grayscale opacity-60">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Mobile Why Choose --}}
    <section class="py-6 bg-white">
        <div class="px-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">{{ statictranslations('Why Shop With Us?') }}</h2>

            @php
                $whyShopWithUs = isset($whyShopWithUs) ? $whyShopWithUs : collect();
                $locale = app()->getLocale();
            @endphp

            @if ($whyShopWithUs->isNotEmpty())
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($whyShopWithUs as $item)
                        @php
                            $title = $item->getTranslation('title', $locale, false) ?? $item->getTranslation('title', 'en', false);
                            $description = $item->getTranslation('description', $locale, false) ?? $item->getTranslation('description', 'en', false);
                            $icon = $item->icon ? asset('storage/' . ltrim($item->icon, '/')) : null;
                        @endphp
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center mx-auto mb-3">
                                @if ($icon)
                                    <img src="{{ $icon }}" alt="{{ $title ?? 'Icon' }}" class="w-5 h-5 object-contain">
                                @else
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-xs font-bold text-gray-900 mb-1">{{ $title ?? 'Feature' }}</h3>
                            <p class="text-[10px] text-gray-600 leading-relaxed line-clamp-2">{!! $description ?? '' !!}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <style>
        .brand-marquee-wrapper {
            overflow: hidden;
            position: relative;
        }
        .brand-marquee-inner {
            display: flex;
            align-items: center;
            gap: 2rem;
            width: max-content;
            animation: marquee linear infinite;
        }
        .brand-marquee {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .brand-marquee a {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
@endsection
