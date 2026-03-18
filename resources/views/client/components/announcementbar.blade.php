    @php
        $infotexts = $siteContent->infotext ?? [];
        if (is_string($infotexts)) {
            $infotexts = trim($infotexts) !== '' ? [$infotexts] : [];
        } elseif (is_array($infotexts)) {
            $infotexts = array_filter(array_map('trim', $infotexts));
        } else {
            $infotexts = [];
        }
        $infotexts = array_values(array_slice($infotexts, 0, 3)); // re-index after filter
    @endphp

    @if (count($infotexts))
        <div class="bg-linear-to-r from-primary to-primary-dark text-white overflow-hidden" aria-label="Announcements"
            aria-live="polite">
            <div class="py-2 flex items-center justify-center">
                <div class="relative w-full overflow-hidden" data-marquee-container>

                    <div class="flex gap-12 whitespace-nowrap select-none" data-marquee-track>

                        @php $sets = 6; @endphp
                        @for ($s = 0; $s < $sets; $s++)
                            <div class="flex gap-12 shrink-0" data-marquee-set="{{ $s === 0 ? 'original' : 'clone' }}"
                                @if ($s > 0) aria-hidden="true" @endif>
                                @foreach ($infotexts as $text)
                                    <div class="flex items-center gap-2 shrink-0">
                                        <svg class="w-3 h-3 text-yellow-300 shrink-0" fill="currentColor"
                                            viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span class="text-xs font-medium tracking-wide">{{ $text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endfor

                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes marquee-scroll {
                0% {
                    transform: translateX(0);
                }

                100% {
                    transform: translateX(var(--marquee-offset, -50%));
                }
            }

            [data-marquee-track] {
                will-change: transform;
            }

            [data-marquee-track].is-ready {
                animation: marquee-scroll var(--marquee-duration, 20s) linear infinite;
            }

            /* Pause on hover */
            [data-marquee-container]:hover [data-marquee-track] {
                animation-play-state: paused;
            }

            /* Respect reduced-motion */
            @media (prefers-reduced-motion: reduce) {
                [data-marquee-track].is-ready {
                    animation: none;
                }
            }
        </style>

        <script>
            (function() {
                const PX_PER_SECOND = 60;

                function initMarquee(container) {
                    const track = container.querySelector('[data-marquee-track]');
                    const original = container.querySelector('[data-marquee-set="original"]');
                    if (!track || !original) return;

                    function measure() {
                        // Gap between sets: track uses gap-12 = 3rem = 48px (Tailwind default)
                        const GAP_PX = parseFloat(getComputedStyle(track).gap) || 48;

                        // The loop offset = width of one full set + one gap
                        // This is EXACT regardless of 1, 2 or 3 items inside
                        const oneSetWidth = original.offsetWidth + GAP_PX;

                        const duration = (oneSetWidth / PX_PER_SECOND).toFixed(2);

                        track.style.setProperty('--marquee-offset', `-${oneSetWidth}px`);
                        track.style.setProperty('--marquee-duration', `${duration}s`);
                        track.classList.add('is-ready');
                    }

                    // Re-measure on resize so speed stays consistent on window resize
                    const ro = new ResizeObserver(function() {
                        track.classList.remove('is-ready');
                        // Tiny delay lets layout settle before re-measuring
                        requestAnimationFrame(measure);
                    });
                    ro.observe(container);

                    measure();
                }

                function init() {
                    document.querySelectorAll('[data-marquee-container]').forEach(initMarquee);
                }

                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(init);
                } else {
                    window.addEventListener('load', init);
                }
            })();
        </script>
    @endif
