<section class="relative w-full h-80 sm:h-96 md:h-screen overflow-hidden" x-data="{
    activeSlide: 0,
    slidesCount: {{ $sliders->count() }},
    autoplayInterval: null,
    startAutoplay() {
        this.autoplayInterval = setInterval(() => { this.next() }, 5000);
    },
    stopAutoplay() {
        clearInterval(this.autoplayInterval);
    },
    next() {
        this.activeSlide = (this.activeSlide === this.slidesCount - 1) ? 0 : this.activeSlide + 1;
    },
    prev() {
        this.activeSlide = (this.activeSlide === 0) ? this.slidesCount - 1 : this.activeSlide - 1;
    },
    goTo(index) {
        this.activeSlide = index;
    }
}" x-init="startAutoplay()"
    @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
    {{-- Slides Wrapper --}}
    <div class="relative w-full h-full">
        @foreach ($sliders as $index => $slider)
            <div x-show="activeSlide === {{ $index }}" x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="absolute inset-0 w-full h-full"
                style="display: none;" {{-- Prevents flicker before Alpine loads --}}>
                {{-- Background Image --}}
                <img src="{{ asset('/storage/' . $slider->image) }}" alt="{{ $slider->title }}"
                    class="absolute inset-0 w-full h-full object-fill z-0">

                {{-- Dark Overlay --}}
                <div
                    class="absolute inset-0 bg-linear-to-t from-black/80 via-black/40 to-transparent z-10 pointer-events-none">
                </div>

                {{-- Content --}}
                <div class="absolute bottom-0 left-0 w-full z-20 pb-12 sm:pb-16 md:pb-32 px-4">
                    <div class="container mx-auto">
                        <div class="max-w-3xl text-left">
                            {{-- Title with slight delay animation --}}
                            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-6xl font-extrabold text-white leading-tight drop-shadow-md"
                                x-show="activeSlide === {{ $index }}"
                                x-transition:enter="transition delay-200 duration-700 ease-out"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                {{ $slider->title }}
                            </h1>

                            {{-- Subtitle with more delay --}}
                            <p class="mt-2 sm:mt-3 md:mt-4 text-sm sm:text-base md:text-lg text-gray-200 max-w-2xl drop-shadow-sm"
                                x-show="activeSlide === {{ $index }}"
                                x-transition:enter="transition delay-300 duration-700 ease-out"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                {{ $slider->subtitle }}
                            </p>

                            {{-- Button --}}
                            @if ($slider->button_text)
                                <div class="mt-4 sm:mt-6 md:mt-8" x-show="activeSlide === {{ $index }}"
                                    x-transition:enter="transition delay-500 duration-700 ease-out"
                                    x-transition:enter-start="opacity-0 translate-y-4"
                                    x-transition:enter-end="opacity-100 translate-y-0">
                                    <a href="{{ $slider->button_link ?? '#' }}"
                                        class="inline-block text-white px-6 sm:px-8 py-2 sm:py-3 rounded-full font-medium shadow-lg transition-transform transform hover:-translate-y-1 bg-primary border border-transparent hover:bg-primary/90 text-sm sm:text-base">
                                        {{ $slider->button_text }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Navigation Arrows (Hidden on mobile) --}}
    <button @click="prev()"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-30 hidden md:flex items-center justify-center w-12 h-12 rounded-full bg-white/20 backdrop-blur-md hover:bg-white/40 text-white transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button @click="next()"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-30 hidden md:flex items-center justify-center w-12 h-12 rounded-full bg-white/20 backdrop-blur-md hover:bg-white/40 text-white transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>

    {{-- Pagination Dots --}}
    <div class="absolute bottom-8 left-0 w-full z-30 flex justify-center space-x-3">
        @foreach ($sliders as $index => $slider)
            <button @click="goTo({{ $index }})" class="h-2 rounded-full transition-all duration-300"
                :class="activeSlide === {{ $index }} ? 'w-8 bg-primary' : 'w-2 bg-white/50 hover:bg-white'"
                aria-label="Go to slide {{ $index + 1 }}">
            </button>
        @endforeach
    </div>
</section>
