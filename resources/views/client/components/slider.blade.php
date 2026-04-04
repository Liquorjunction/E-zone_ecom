<style>
    /* Slider improvements */
    .slider-progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: var(--color-primary);
        animation: slideProgress 5s linear forwards;
        z-index: 40;
        transform-origin: left;
    }

    @keyframes slideProgress {
        from { width: 0%; }
        to   { width: 100%; }
    }

    .slide-content-enter {
        animation: slideContentIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    @keyframes slideContentIn {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .slider-nav-btn {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.25s ease;
    }

    .slider-nav-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: scale(1.08);
    }
</style>

<section class="relative w-full h-72 sm:h-100 md:h-screen max-h-200 overflow-hidden bg-gray-900"
    x-data="{
        activeSlide: 0,
        slidesCount: {{ $sliders->count() }},
        progressKey: 0,
        autoplayInterval: null,
        touchStartX: null,
        touchStartY: null,
        startAutoplay() {
            this.autoplayInterval = setInterval(() => { this.next(); }, 5000);
        },
        stopAutoplay() { clearInterval(this.autoplayInterval); },
        next() {
            this.activeSlide = (this.activeSlide === this.slidesCount - 1) ? 0 : this.activeSlide + 1;
            this.progressKey++;
        },
        prev() {
            this.activeSlide = (this.activeSlide === 0) ? this.slidesCount - 1 : this.activeSlide - 1;
            this.progressKey++;
        },
        goTo(index) { this.activeSlide = index; this.progressKey++; },
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
        @foreach ($sliders as $index => $slider)
            <div
                x-show="activeSlide === {{ $index }}"
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 scale-[1.03]"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-[0.98]"
                class="absolute inset-0 w-full h-full"
                style="display: none;">

                {{-- Background Image --}}
                <img
                    src="{{ asset('/storage/' . $slider->image) }}"
                    alt="{{ $slider->title }}"
                    class="absolute inset-0 w-full h-full object-fill z-0">

                {{-- Gradient overlays: bottom-heavy + subtle left vignette --}}
                <div class="absolute inset-0 z-10 pointer-events-none"
                     style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.35) 45%, transparent 75%);"></div>
                <div class="absolute inset-0 z-10 pointer-events-none"
                     style="background: linear-gradient(to right, rgba(0,0,0,0.45) 0%, transparent 60%);"></div>

                {{-- Content --}}
                <div class="absolute bottom-0 left-0 w-full z-20 pb-14 sm:pb-20 md:pb-32 px-4">
                    <div class="container mx-auto">
                        <div class="max-w-2xl"
                             x-show="activeSlide === {{ $index }}"
                             x-transition:enter="transition duration-700 ease-out delay-150"
                             x-transition:enter-start="opacity-0 translate-y-6"
                             x-transition:enter-end="opacity-100 translate-y-0">

                            {{-- Title --}}
                            <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight drop-shadow-lg"
                                x-show="activeSlide === {{ $index }}"
                                x-transition:enter="transition delay-200 duration-700 ease-out"
                                x-transition:enter-start="opacity-0 translate-y-5"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                {{ $slider->title }}
                            </h1>

                            {{-- Eyebrow badge --}}
                            @if($slider->subtitle)
                                <div class="mb-3 sm:mb-4 inline-flex items-center gap-2">
                                    <span class="h-px w-8 bg-primary"></span>
                                    <span class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-primary">
                                        {{ $slider->subtitle }}
                                    </span>
                                </div>
                            @endif

                            {{-- Button --}}
                            @if ($slider->button_text)
                                <div class="mt-5 sm:mt-8"
                                     x-show="activeSlide === {{ $index }}"
                                     x-transition:enter="transition delay-400 duration-700 ease-out"
                                     x-transition:enter-start="opacity-0 translate-y-4"
                                     x-transition:enter-end="opacity-100 translate-y-0">
                                    <a href="{{ $slider->button_link ?? '#' }}"
                                       class="group inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white
                                              px-6 sm:px-8 py-2.5 sm:py-3.5 rounded-full font-semibold
                                              shadow-[0_4px_24px_rgba(0,0,0,0.25)] hover:shadow-[0_8px_32px_rgba(0,0,0,0.35)]
                                              transition-all duration-300 hover:-translate-y-0.5 text-sm sm:text-base">
                                        {{ $slider->button_text }}
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Progress bar (resets on slide change) --}}
    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-white/10 z-40">
        <div :key="progressKey" class="slider-progress-bar"></div>
    </div>

    {{-- Navigation Arrows --}}
    <button @click="prev()"
        class="slider-nav-btn absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 z-30
               hidden md:flex items-center justify-center w-11 h-11 rounded-full
               bg-white/15 text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button @click="next()"
        class="slider-nav-btn absolute right-4 sm:right-6 top-1/2 -translate-y-1/2 z-30
               hidden md:flex items-center justify-center w-11 h-11 rounded-full
               bg-white/15 text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    {{-- Pagination Dots --}}
    <div class="absolute bottom-5 right-6 z-30 flex items-center gap-2">
        @foreach ($sliders as $index => $slider)
            <button @click="goTo({{ $index }})"
                class="rounded-full transition-all duration-300 cursor-pointer"
                :class="activeSlide === {{ $index }}
                    ? 'w-7 h-2 bg-primary'
                    : 'w-2 h-2 bg-white/40 hover:bg-white/70'"
                aria-label="Go to slide {{ $index + 1 }}">
            </button>
        @endforeach
    </div>
</section>
