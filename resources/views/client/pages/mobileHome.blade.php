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
        touchStartX: null,
        touchStartY: null,
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        next() { if (this.currentSlide < this.totalSlides - 1) this.currentSlide++ },
        prev() { if (this.currentSlide > 0) this.currentSlide-- },
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
    @touchstart.passive="onTouchStart($event)"
    @touchend="onTouchEnd($event)">
        <div class="px-4">
            <div class="mb-4 text-center">
                <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Shop by Category') }}</h2>
                <p class="text-xs text-gray-600">{{ statictranslations('Browse our extensive collection') }}</p>
            </div>

            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-300 ease-out"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @php $categoryChunks = $categories->chunk(3); @endphp
                    @foreach ($categoryChunks as $chunk)
                        <div class="w-full shrink-0">
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
        touchStartX: null,
        touchStartY: null,
        next() { this.currentSlide = (this.currentSlide + 1) % this.totalItems },
        prev() {
            this.currentSlide = this.currentSlide === 0 ? this.totalItems - 1 : this.currentSlide - 1;
        },
        init() {
            this.autoplay = setInterval(() => { this.next(); }, 4000);
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
    @touchstart.passive="onTouchStart($event)"
    @touchend="onTouchEnd($event)">
        <div class="px-4">
            <div class="mb-4 text-center">
                <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Featured Promotions') }}</h2>
                <p class="text-xs text-gray-600">{{ statictranslations('Handpicked deals just for you') }}</p>
            </div>

            <div class="relative overflow-hidden rounded-xl">
                <div class="flex transition-transform duration-500 ease-out"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @foreach ($cards->take(4) as $card)
                        <div class="w-full shrink-0">
                            <a href="{{ $card->link ?? '#' }}" class="block relative h-48 rounded-xl overflow-hidden">
                                <img src="{{ asset('storage/' . $card->image) }}" alt="{{ $card->title }}"
                                    class="absolute inset-0 w-full h-full">
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

    {{-- Mobile Deals & Discounts --}}
    @php
        $mobileDeals = ($deals ?? collect());
        $mobileDealsChunks = $mobileDeals->chunk(2);
        $mobileDealsCount = $mobileDeals->count();
    @endphp
    @if ($mobileDealsCount > 0)
        <section class="py-6 bg-gray-50" x-data="{
            currentSlide: 0,
            itemsPerPage: 1,
            totalItems: {{ $mobileDealsCount }},
            get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
            next() { if (this.currentSlide < this.totalSlides - 1) this.currentSlide++ },
            prev() { if (this.currentSlide > 0) this.currentSlide-- },
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
            },
            touchStartX: null,
            touchStartY: null
        }"
        @touchstart.passive="onTouchStart($event)"
        @touchend="onTouchEnd($event)">
            <div class="px-4">
                <div class="mb-4 text-center">
                    <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Deals & Discounts') }}</h2>
                    <p class="text-xs text-gray-600">{{ statictranslations('Grab the best offers on top products') }}</p>
                </div>

                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out"
                        :style="`transform: translateX(-${currentSlide * 100}%)`">
                        @foreach ($mobileDealsChunks as $chunk)
                            <div class="w-full shrink-0">
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ($chunk as $productVariant)
                                        @include('client.partials.product-card-mobile', ['product' => $productVariant])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

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

    {{-- Mobile New Arrivals --}}
    <section class="py-6 bg-gray-50" x-data="{
        currentSlide: 0,
        itemsPerPage: 2,
        totalItems: {{ isset($newArrivals) ? $newArrivals->count() : 0 }},
        autoplay: null,
        touchStartX: null,
        touchStartY: null,
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides },
        prev() {
            this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
        },
        init() {
            this.autoplay = setInterval(() => { this.next(); }, 3500);
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
    @touchstart.passive="onTouchStart($event)"
    @touchend="onTouchEnd($event)">
        <div class="px-4">
            <div class="mb-4 text-center">
                <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('New Arrivals') }}</h2>
                <p class="text-xs text-gray-600">{{ statictranslations('The latest tech just landed') }}</p>
            </div>

            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-500 ease-out"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @php $newArrivalsChunks = ($newArrivals ?? collect())->chunk(2); @endphp
                    @foreach ($newArrivalsChunks as $chunk)
                        <div class="w-full shrink-0">
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($chunk as $productVariant)
                                    @include('client.partials.product-card-mobile', ['product' => $productVariant])
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
            touchStartX: null,
            touchStartY: null,
            get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
            next() { if (this.currentSlide < this.totalSlides - 1) this.currentSlide++ },
            prev() { if (this.currentSlide > 0) this.currentSlide-- },
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
        @touchstart.passive="onTouchStart($event)"
        @touchend="onTouchEnd($event)">
            <div class="px-4">
                <div class="mb-4 text-center">
                    <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Best Sellers') }}</h2>
                    <p class="text-xs text-gray-600">{{ statictranslations('Most loved by our customers') }}</p>
                </div>

                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out"
                        :style="`transform: translateX(-${currentSlide * 100}%)`">
                        @php $bestSellerChunks = ($bestSellers ?? collect())->chunk(2); @endphp
                        @foreach ($bestSellerChunks as $chunk)
                            <div class="w-full shrink-0">
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ($chunk as $productVariant)
                                        @include('client.partials.product-card-mobile', ['product' => $productVariant])
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
    @php
        $brandsCollection = ($brands ?? collect());
        $brandChunks = $brandsCollection->chunk(6);
        $brandCount = $brandsCollection->count();
    @endphp
    <section class="py-6 bg-gray-50" x-data="{
        currentSlide: 0,
        itemsPerPage: 6,
        totalItems: {{ $brandCount }},
        touchStartX: null,
        touchStartY: null,
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        next() { if (this.currentSlide < this.totalSlides - 1) this.currentSlide++ },
        prev() { if (this.currentSlide > 0) this.currentSlide-- },
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
    @touchstart.passive="onTouchStart($event)"
    @touchend="onTouchEnd($event)">
        <div class="px-4">
            <div class="mb-4 text-center">
                <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Trusted Brands') }}</h2>
                <p class="text-xs text-gray-600">{{ statictranslations("We partner with the world's leading technology brands") }}</p>
            </div>

            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-500 ease-out"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @foreach ($brandChunks as $chunk)
                        <div class="w-full shrink-0 flex justify-center">
                            <div class="grid grid-cols-3 gap-3 w-fit">
                                @foreach ($chunk as $brand)
                                    @php
                                        $imgPath = $brand->image ? asset('storage/' . $brand->image) : 'https://logo.clearbit.com/' . ($brand->slug ?? preg_replace('/\s+/', '', strtolower($brand->name)));
                                    @endphp
                                    <a href="{{ route('brand.products', $brand->slug ?? $brand->id) }}"
                                        class="bg-white rounded-xl p-3 flex items-center justify-center border border-gray-100">
                                        <img src="{{ $imgPath }}" alt="{{ $brand->name }}"
                                            class="h-6 w-auto grayscale opacity-70 object-contain">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-center gap-1.5 mt-4" x-show="totalSlides > 1">
                <template x-for="i in totalSlides" :key="i">
                    <button @click="currentSlide = i - 1"
                        :class="currentSlide === i - 1 ? 'bg-primary w-6' : 'bg-gray-300 w-1.5'"
                        class="h-1.5 rounded-full transition-all"></button>
                </template>
            </div>
        </div>
    </section>

    {{-- Mobile Why Choose --}}
    @php
        $whyShopWithUs = isset($whyShopWithUs) ? $whyShopWithUs : collect();
        $locale = app()->getLocale();
        $whyShopCount = $whyShopWithUs->count();
        $whyShopChunks = $whyShopWithUs->chunk(4);
    @endphp
    <section class="py-6 bg-white" x-data="{
        currentSlide: 0,
        itemsPerPage: 4,
        totalItems: {{ $whyShopCount }},
        touchStartX: null,
        touchStartY: null,
        get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
        next() { if (this.currentSlide < this.totalSlides - 1) this.currentSlide++ },
        prev() { if (this.currentSlide > 0) this.currentSlide-- },
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
    @touchstart.passive="onTouchStart($event)"
    @touchend="onTouchEnd($event)">
        <div class="px-4">
            <div class="mb-4 text-center">
                <h2 class="text-lg font-bold text-gray-900">{{ statictranslations('Why Shop With Us?') }}</h2>
                <p class="text-xs text-gray-600">{{ statictranslations('Experience excellence in every purchase') }}</p>
            </div>

            @if ($whyShopWithUs->isNotEmpty())
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out"
                        :style="`transform: translateX(-${currentSlide * 100}%)`">
                        @foreach ($whyShopChunks as $chunk)
                            <div class="w-full shrink-0">
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ($chunk as $item)
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
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-center gap-1.5 mt-4" x-show="totalSlides > 1">
                    <template x-for="i in totalSlides" :key="i">
                        <button @click="currentSlide = i - 1"
                            :class="currentSlide === i - 1 ? 'bg-primary w-6' : 'bg-gray-300 w-1.5'"
                            class="h-1.5 rounded-full transition-all"></button>
                    </template>
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
