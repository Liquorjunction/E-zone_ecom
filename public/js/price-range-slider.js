(function () {
    'use strict';

    // ─────────────────────────────────────────────────────────────────────────
    // PRODUCTION PRICE RANGE SLIDER
    //
    // Key design decisions:
    //  • NO <input type="range"> — the browser's native step-snapping is the
    //    root cause of all value drift. We use plain <div> thumbs driven by
    //    pointer events, so stored values are always exact integers.
    //  • FLOOR is always 0 (absolute floor).
    //  • CEIL comes from the DB via data-ceil / data-default-max.
    //  • step = 1 — any integer is valid. No rounding to 50 / 100.
    //  • Number inputs are free-form; clamping only happens on blur / Enter.
    //  • "dirty" flag gates whether price params appear in the URL.
    // ─────────────────────────────────────────────────────────────────────────

    document.querySelectorAll('[data-price-range]').forEach(initSlider);

    function initSlider(container) {

        // ── Required DOM elements ─────────────────────────────────────────────
        const track     = container.querySelector('[data-range-track]');
        const trackWrap = container.querySelector('[data-range-root]');
        const thumbMin  = container.querySelector('[data-thumb="min"]');
        const thumbMax  = container.querySelector('[data-thumb="max"]');
        const inputMin  = container.querySelector('[data-input="min"]');
        const inputMax  = container.querySelector('[data-input="max"]');

        if (!track || !trackWrap || !thumbMin || !thumbMax || !inputMin || !inputMax) {
            console.warn('[PriceSlider] Missing DOM elements in [data-price-range]', container);
            return;
        }

        const form = container.closest('form');

        // ── Bounds from Blade data-attributes ────────────────────────────────
        const FLOOR = Math.floor(parseFloat(container.dataset.floor ?? container.dataset.defaultMin) || 0);
        const CEIL  = Math.ceil(parseFloat(container.dataset.ceil  ?? container.dataset.defaultMax) || 5000);

        // ── Live state ────────────────────────────────────────────────────────
        let curMin = FLOOR;
        let curMax = CEIL;

        // Strip name attrs so an untouched slider never adds price to the URL
        inputMin.removeAttribute('name');
        inputMax.removeAttribute('name');
        container.dataset.priceDirty = 'false';

        // ─────────────────────────────────────────────────────────────────────
        // PURE HELPERS
        // ─────────────────────────────────────────────────────────────────────

        function toPercent(val) {
            return ((val - FLOOR) / (CEIL - FLOOR)) * 100;
        }

        function pixelToValue(clientX) {
            const rect  = trackWrap.getBoundingClientRect();
            const ratio = Math.min(Math.max((clientX - rect.left) / rect.width, 0), 1);
            return Math.round(FLOOR + ratio * (CEIL - FLOOR));
        }

        function paint() {
            const lPct = toPercent(curMin);
            const rPct = toPercent(curMax);
            track.style.left       = lPct + '%';
            track.style.right      = (100 - rPct) + '%';
            thumbMin.style.left    = lPct + '%';
            thumbMax.style.left    = rPct + '%';
        }

        function syncInputs() {
            inputMin.value = curMin;
            inputMax.value = curMax;
        }

        function setDirty(dirty) {
            container.dataset.priceDirty = dirty ? 'true' : 'false';
            if (dirty) {
                if (inputMin.dataset.name) inputMin.name = inputMin.dataset.name;
                if (inputMax.dataset.name) inputMax.name = inputMax.dataset.name;
            } else {
                inputMin.removeAttribute('name');
                inputMax.removeAttribute('name');
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // COMMIT  — single point where state is finalised + submit fires
        // ─────────────────────────────────────────────────────────────────────

        let submitTimer = null;

        function commit(rawMin, rawMax, opts) {
            opts = opts || {};

            // Clamp to absolute bounds, ensure integers
            let nMin = Math.round(Math.min(Math.max(rawMin, FLOOR), CEIL));
            let nMax = Math.round(Math.min(Math.max(rawMax, FLOOR), CEIL));

            // If inverted (user typed min > max) silently swap
            if (nMin > nMax) { var tmp = nMin; nMin = nMax; nMax = tmp; }

            // If identical, push apart by 1 so the filter is meaningful
            if (nMin === nMax) {
                if (nMax < CEIL) { nMax += 1; } else { nMin -= 1; }
            }

            curMin = nMin;
            curMax = nMax;

            paint();
            syncInputs();

            var atDefault = curMin <= FLOOR && curMax >= CEIL;
            setDirty(!atDefault);

            if (!opts.silent) {
                clearTimeout(submitTimer);
                submitTimer = setTimeout(function () {
                    if (form) {
                        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                    }
                }, opts.delay !== undefined ? opts.delay : 300);
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // THUMB DRAG
        // ─────────────────────────────────────────────────────────────────────

        function makeDraggable(thumb, side) {
            thumb.addEventListener('pointerdown', function (e) {
                if (e.button && e.button !== 0) return;
                e.preventDefault();
                thumb.setPointerCapture(e.pointerId);

                function onMove(ev) {
                    var v = pixelToValue(ev.clientX);
                    if (side === 'min') {
                        curMin = Math.max(Math.min(v, curMax - 1), FLOOR);
                    } else {
                        curMax = Math.min(Math.max(v, curMin + 1), CEIL);
                    }
                    paint();
                    syncInputs();
                }

                function onUp() {
                    thumb.removeEventListener('pointermove',   onMove);
                    thumb.removeEventListener('pointerup',     onUp);
                    thumb.removeEventListener('pointercancel', onUp);
                    // Commit on drag release — triggers filter
                    commit(curMin, curMax, { delay: 0 });
                }

                thumb.addEventListener('pointermove',   onMove);
                thumb.addEventListener('pointerup',     onUp);
                thumb.addEventListener('pointercancel', onUp);
            });
        }

        makeDraggable(thumbMin, 'min');
        makeDraggable(thumbMax, 'max');

        // ─────────────────────────────────────────────────────────────────────
        // TRACK CLICK  (jump nearest thumb)
        // ─────────────────────────────────────────────────────────────────────

        trackWrap.addEventListener('click', function (e) {
            if (e.target === thumbMin || e.target === thumbMax) return;
            var clicked = pixelToValue(e.clientX);
            var dMin    = Math.abs(clicked - curMin);
            var dMax    = Math.abs(clicked - curMax);
            if (dMin < dMax) {
                commit(clicked, curMax, { delay: 0 });
            } else {
                commit(curMin, clicked, { delay: 0 });
            }
        });

        // ─────────────────────────────────────────────────────────────────────
        // NUMBER INPUTS  — validate & commit on blur or Enter
        // ─────────────────────────────────────────────────────────────────────

        function applyInputs() {
            var rawMin = parseFloat(inputMin.value);
            var rawMax = parseFloat(inputMax.value);
            var nMin   = isNaN(rawMin) ? FLOOR : rawMin;
            var nMax   = isNaN(rawMax) ? CEIL  : rawMax;
            commit(nMin, nMax, { delay: 0 });
        }

        inputMin.addEventListener('blur',    applyInputs);
        inputMax.addEventListener('blur',    applyInputs);
        inputMin.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyInputs(); } });
        inputMax.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyInputs(); } });

        // ─────────────────────────────────────────────────────────────────────
        // INITIALISE FROM URL
        // ─────────────────────────────────────────────────────────────────────

        var params = new URLSearchParams(window.location.search);
        var urlMin = params.has('min_price') ? parseFloat(params.get('min_price')) : null;
        var urlMax = params.has('max_price') ? parseFloat(params.get('max_price')) : null;

        if (urlMin !== null || urlMax !== null) {
            commit(
                urlMin !== null ? urlMin : FLOOR,
                urlMax !== null ? urlMax : CEIL,
                { silent: true }
            );
        } else {
            curMin = FLOOR;
            curMax = CEIL;
            paint();
            syncInputs();
            setDirty(false);
        }

        // ─────────────────────────────────────────────────────────────────────
        // PUBLIC RESET (called by products.js "Clear All")
        // ─────────────────────────────────────────────────────────────────────

        // Public API — called by products.js
        container._sliderReset = function () {
            commit(FLOOR, CEIL, { silent: true });
            setDirty(false);
        };

        // Called when syncing values from another form's inputs into this slider
        // (e.g. sidebar → modal sync). Reads inputMin/inputMax and repaints.
        container._sliderCommitFromInputs = function (opts) {
            var rawMin = parseFloat(inputMin.value);
            var rawMax = parseFloat(inputMax.value);
            commit(isNaN(rawMin) ? FLOOR : rawMin, isNaN(rawMax) ? CEIL : rawMax, opts || { silent: true });
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRE-SUBMIT GUARD  — strip price params if slider is at defaults
    // ─────────────────────────────────────────────────────────────────────────

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var pr = form.querySelector('[data-price-range]');
            if (!pr) return;
            var minI  = pr.querySelector('[data-input="min"]');
            var maxI  = pr.querySelector('[data-input="max"]');
            var floor = parseFloat(pr.dataset.floor ?? pr.dataset.defaultMin) || 0;
            var ceil  = parseFloat(pr.dataset.ceil  ?? pr.dataset.defaultMax) || 5000;
            if (minI && parseFloat(minI.value) <= floor) minI.removeAttribute('name');
            if (maxI && parseFloat(maxI.value) >= ceil)  maxI.removeAttribute('name');
        }, true);
    });

})();
