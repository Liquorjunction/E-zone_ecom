@php
    $categoryChunks = $categories->chunk(5);
@endphp

<style>
    /* ── Nav buttons ──────────────────────────────────────────────── */
    .nav-btn {
        position: relative;
        overflow: hidden;
    }

    .nav-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 9999px;
        background: var(--color-primary);
        transform: scale(0);
        transition: transform 0.25s cubic-bezier(.4, 0, .2, 1);
        z-index: 0;
    }

    .nav-btn:not(:disabled):hover::after {
        transform: scale(1);
    }

    .nav-btn svg {
        position: relative;
        z-index: 1;
        transition: color 0.2s;
    }

    .nav-btn:not(:disabled):hover svg {
        color: white;
    }

    /* ── Category cards (inside carousel — cannot use translate-y) ── */
    /* overflow:hidden is kept to clip the image scale effect.        */
    /* Hover feedback comes from: border glow, shadow, icon scale,    */
    /* and a ::after inset-border overlay.                            */
    .category-card {
        position: relative;
        overflow: hidden;
        /* needed to clip group-hover:scale-110 on image */
        isolation: isolate;
    }

    /* Animated primary-color inset border + outer glow on hover */
    .category-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        border: 2px solid oklch(0.69 0.14 232.2 / 0);
        box-shadow: inset 0 0 0 0 oklch(0.69 0.14 232.2 / 0);
        transition: border-color 0.3s ease, box-shadow 0.35s ease;
        pointer-events: none;
        z-index: 10;
    }

    .category-card:hover::after {
        border-color: oklch(0.69 0.14 232.2 / 0.45);
        box-shadow: inset 0 0 24px oklch(0.69 0.14 232.2 / 0.06),
            0 12px 32px oklch(0.69 0.14 232.2 / 0.15);
    }

    /* Subtle background tint that slides in from bottom */
    .category-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 50% 120%, oklch(0.69 0.14 232.2 / 0.07) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.35s ease;
        pointer-events: none;
        z-index: 0;
    }

    .category-card:hover::before {
        opacity: 1;
    }
</style>

<section class="py-6 sm:py-10 lg:py-12 bg-secondary-bg" x-data="{
    currentSlide: 0,
    itemsPerPage: window.innerWidth >= 1280 ? 5 : window.innerWidth >= 1024 ? 4 : window.innerWidth >= 768 ? 3 : window.innerWidth >= 640 ? 3 : 2,
    totalItems: {{ $categories->count() }},
    get totalSlides() { return Math.ceil(this.totalItems / this.itemsPerPage) },
    get canGoNext() { return this.currentSlide < this.totalSlides - 1 },
    get canGoPrev() { return this.currentSlide > 0 },
    next() { if (this.canGoNext) this.currentSlide++ },
    prev() { if (this.canGoPrev) this.currentSlide-- },
    init() {
        window.addEventListener('resize', () => {
            this.itemsPerPage = window.innerWidth >= 1280 ? 5 : window.innerWidth >= 1024 ? 4 : window.innerWidth >= 768 ? 3 : window.innerWidth >= 640 ? 3 : 2;
            if (this.currentSlide >= this.totalSlides) this.currentSlide = Math.max(0, this.totalSlides - 1);
        });
    }
}">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-10 sm:mb-14">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                {{ statictranslations('Shop by Category') }}
            </h2>
            <p class="mt-3 text-sm sm:text-base text-gray-400 max-w-md mx-auto leading-relaxed">
                {{ statictranslations('Browse our extensive collection') }}
            </p>
            <div class="mt-5 flex items-center justify-center gap-1.5">
                <span class="block w-8 h-0.75 rounded-full bg-primary"></span>
                <span class="block w-3 h-0.75 rounded-full bg-primary/40"></span>
                <span class="block w-1.5 h-0.75 rounded-full bg-primary/20"></span>
            </div>
        </div>

        {{-- Slider with flanking nav buttons --}}
        <div class="relative flex items-center gap-3 sm:gap-4">

            {{-- Left Nav Button --}}
            <button @click="prev()" :disabled="!canGoPrev" :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm transition-shadow hover:shadow-md">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Slides --}}
            <div class="flex-1 overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out"
                    :style="`transform: translateX(-${currentSlide * 100}%)`">
                    @foreach ($categoryChunks as $categoryChunk)
                        @if ($categoryChunk->count() > 0)
                            <div class="w-full shrink-0">
                                <div
                                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                                    @foreach ($categoryChunk as $category)
                                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                            class="category-card group bg-white rounded-2xl p-6 sm:p-6 text-center
                                                   shadow-[0_2px_8px_rgba(0,0,0,0.06)]
                                                   border border-gray-100
                                                   transition-all duration-300">
                                            <div class="flex flex-col items-center gap-4">
                                                {{-- Icon --}}
                                                <div
                                                    class="category-icon-wrap rounded-2xl
                                                            flex items-center justify-center
                                                            transition-all duration-300">
                                                    @if ($category->{'category-icon'})
                                                        <img src="{{ asset('/storage/' . $category->{'category-icon'}) }}"
                                                            alt="{{ $category->{'category-name'} }}"
                                                            class="w-24 h-24 sm:w-28 sm:h-28 object-contain
                                                                   transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">
                                                    @else
                                                        <svg class="w-24 h-24 sm:w-28 sm:h-28 text-gray-300 group-hover:text-primary transition-colors duration-300"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                                                        </svg>
                                                    @endif
                                                </div>

                                                {{-- Label --}}
                                                <h3
                                                    class="text-sm sm:text-base font-semibold text-gray-700
                                                            group-hover:text-primary transition-colors duration-300
                                                            line-clamp-2 leading-snug w-full">
                                                    {{ $category->{'category-name'} }}
                                                </h3>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Right Nav Button --}}
            <button @click="next()" :disabled="!canGoNext" :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                class="nav-btn hidden md:flex shrink-0 w-11 h-11 rounded-full border-2 border-gray-200 bg-white items-center justify-center shadow-sm transition-shadow hover:shadow-md">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

        </div>

        {{-- Dots + Mobile Nav --}}
        <div class="flex items-center justify-center gap-4 mt-8" x-show="totalSlides > 1">

            {{-- Mobile prev --}}
            <button @click="prev()" :disabled="!canGoPrev" :class="!canGoPrev ? 'opacity-30 cursor-not-allowed' : ''"
                class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Dots --}}
            <div class="flex items-center gap-1.5">
                <template x-for="i in totalSlides" :key="i">
                    <button @click="currentSlide = i - 1"
                        :class="currentSlide === i - 1 ?
                            'bg-primary w-6 h-2 rounded-full' :
                            'bg-gray-300 hover:bg-gray-400 w-2 h-2 rounded-full'"
                        class="transition-all duration-300 ease-out">
                    </button>
                </template>
            </div>

            {{-- Mobile next --}}
            <button @click="next()" :disabled="!canGoNext" :class="!canGoNext ? 'opacity-30 cursor-not-allowed' : ''"
                class="md:hidden nav-btn w-9 h-9 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

        </div>

    </div>
</section>
