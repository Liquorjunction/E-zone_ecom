{{-- Announcement bar --}}
@php
    $infotexts = $siteContent->infotext ?? [];
    if (is_string($infotexts)) {
        $infotexts = trim($infotexts) !== '' ? [$infotexts] : [];
    } elseif (is_array($infotexts)) {
        $infotexts = array_filter(array_map('trim', $infotexts));
    } else {
        $infotexts = [];
    }
    $infotexts = array_values(array_slice($infotexts, 0, 3));
@endphp

@if (count($infotexts))
    <div class="bg-linear-to-r from-primary to-primary-dark text-white overflow-hidden"
         aria-label="Announcements"
         aria-live="polite">
        <div class="py-2 flex items-center justify-center">
            <div class="relative w-full overflow-hidden" data-marquee-container>
                <div class="flex gap-12 whitespace-nowrap select-none" data-marquee-track>
                    @php $sets = 6; @endphp
                    @for ($s = 0; $s < $sets; $s++)
                        <div class="flex gap-12 shrink-0"
                             data-marquee-set="{{ $s === 0 ? 'original' : 'clone' }}"
                             @if($s > 0) aria-hidden="true" @endif>
                            @foreach ($infotexts as $text)
                                <div class="flex items-center gap-2 shrink-0">
                                    <svg class="w-3 h-3 text-yellow-300 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
@endif

<style>
    @keyframes marquee-scroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(var(--marquee-offset, -50%)); }
    }
    [data-marquee-track] { will-change: transform; }
    [data-marquee-track].is-ready {
        animation: marquee-scroll var(--marquee-duration, 20s) linear infinite;
    }
    [data-marquee-container]:hover [data-marquee-track] {
        animation-play-state: paused;
    }
    @media (prefers-reduced-motion: reduce) {
        [data-marquee-track].is-ready { animation: none; }
    }
</style>

<script>
(function () {
    const PX_PER_SECOND = 60;
    const _observers = new WeakMap();

    function initMarqueeContainer(container) {
        const track    = container.querySelector('[data-marquee-track]');
        const original = container.querySelector('[data-marquee-set="original"]');
        if (!track || !original) return;

        if (_observers.has(container)) {
            _observers.get(container).disconnect();
        }

        function measure() {
            const GAP_PX      = parseFloat(getComputedStyle(track).gap) || 48;
            const oneSetWidth = original.offsetWidth + GAP_PX;
            const duration    = (oneSetWidth / PX_PER_SECOND).toFixed(2);

            track.style.setProperty('--marquee-offset', `-${oneSetWidth}px`);
            track.style.setProperty('--marquee-duration', `${duration}s`);
            track.classList.add('is-ready');
        }

        const ro = new ResizeObserver(() => {
            track.classList.remove('is-ready');
            requestAnimationFrame(measure);
        });
        ro.observe(container);
        _observers.set(container, ro);

        measure();
    }

    window.Marquee = {
        init: function (root) {
            const scope = root instanceof Element ? root : document;
            const containers = scope.querySelectorAll('[data-marquee-container]');
            if (!containers.length) return;
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(() => containers.forEach(initMarqueeContainer));
            } else {
                containers.forEach(initMarqueeContainer);
            }
        }
    };

    // Auto-init on first page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.Marquee.init());
    } else {
        window.Marquee.init();
    }

    // Listen for a custom event to re-init after AJAX
    window.addEventListener('announcementbar:refresh', function(e) {
        // Optionally, e.detail can be a DOM element to scope the search
        window.Marquee.init(e.detail && e.detail.root ? e.detail.root : undefined);
    });

    // Helper for global AJAX: call this after AJAX that updates the bar
    window.refreshAnnouncementBar = function(root) {
        const event = new CustomEvent('announcementbar:refresh', { detail: root ? { root } : {} });
        window.dispatchEvent(event);
    };
}());
</script>
