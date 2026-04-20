(function () {
    'use strict';

    const priceRanges = document.querySelectorAll('[data-price-range]');
    if (!priceRanges.length) return;

    const priceGap = 100; // Minimum gap between min and max handles

    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    function setupPriceRange(container) {
        const minPriceInput = container.querySelector('[data-input="min"]');
        const maxPriceInput = container.querySelector('[data-input="max"]');
        const minPriceRange = container.querySelector('[data-range="min"]');
        const maxPriceRange = container.querySelector('[data-range="max"]');
        const sliderTrack   = container.querySelector('[data-range-track]');
        const rangeRoot     = container.querySelector('[data-range-root]');

        if (!minPriceInput || !maxPriceInput || !minPriceRange || !maxPriceRange || !rangeRoot) return;

        const form     = container.closest('form');
        const minLimit = parseFloat(minPriceRange.min) || 0;
        const maxLimit = parseFloat(minPriceRange.max) || 5000;
        const step     = parseFloat(minPriceRange.step) || 1;

        const defaultMin = Number.isFinite(parseFloat(container.dataset.defaultMin))
            ? parseFloat(container.dataset.defaultMin)
            : minLimit;
        const defaultMax = Number.isFinite(parseFloat(container.dataset.defaultMax))
            ? parseFloat(container.dataset.defaultMax)
            : maxLimit;

        // ─── FIX: strip name attributes up-front so page load never submits
        //         untouched price values (also guards against duplicate-form sync issues)
        minPriceInput.removeAttribute('name');
        maxPriceInput.removeAttribute('name');

        // ─────────────────────────────
        // UTILITY
        // ─────────────────────────────

        function clamp(val, lo, hi) {
            return Math.min(Math.max(val, lo), hi);
        }

        // ─── FIX: snap relative to minLimit, NOT to zero.
        //         When minLimit=119 and step=50, snapping to multiples of 50
        //         (e.g. 8050) and then writing that into an <input type="range"
        //         min="119" step="50">, the browser re-snaps the stored value to
        //         119 + N*50 = 8069, causing the phantom +19 drift.
        //         We always snap relative to the range's own origin (minLimit).
        function clampToStep(val) {
            const stepsFromMin = Math.round((val - minLimit) / step);
            const snapped = minLimit + stepsFromMin * step;
            // honour the exact limits so they're always reachable
            if (snapped >= maxLimit - step / 2) return maxLimit;
            if (snapped <= minLimit + step / 2) return minLimit;
            return clamp(snapped, minLimit, maxLimit);
        }

        function updateVisuals() {
            if (!sliderTrack) return;
            const minV = parseFloat(minPriceRange.value) || minLimit;
            const maxV = parseFloat(maxPriceRange.value) || maxLimit;
            const left  = ((minV - minLimit) / (maxLimit - minLimit)) * 100;
            const right = 100 - ((maxV - minLimit) / (maxLimit - minLimit)) * 100;
            sliderTrack.style.left  = left + '%';
            sliderTrack.style.right = right + '%';
        }

        // ─────────────────────────────
        // DIRTY / NAME STATE
        // ─────────────────────────────

        function setInputNames(enabled) {
            if (enabled) {
                if (minPriceInput.dataset.name) minPriceInput.name = minPriceInput.dataset.name;
                if (maxPriceInput.dataset.name) maxPriceInput.name = maxPriceInput.dataset.name;
            } else {
                minPriceInput.removeAttribute('name');
                maxPriceInput.removeAttribute('name');
            }
        }

        function updateDirtyState(minVal, maxVal) {
            const atDefault =
                Math.abs(minVal - defaultMin) <= step / 2 &&
                Math.abs(maxVal - defaultMax) <= step / 2;

            if (atDefault) {
                container.dataset.priceDirty = 'false';
                setInputNames(false);
            } else {
                container.dataset.priceDirty = 'true';
                setInputNames(true);
            }
        }

        // ─────────────────────────────
        // NOTIFY FILTER FORM
        // ─────────────────────────────

        const notifyFilters = debounce(() => {
            if (!form) return;
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        }, 150);

        // ─────────────────────────────
        // SET VALUES
        // ─────────────────────────────

        function setValues(minVal, maxVal, options = {}) {
            // ─── FIX: snap BOTH values to the step grid (relative to minLimit)
            //         BEFORE writing them to the DOM. Without this, the browser's
            //         own step-snapping for <input type="range"> silently mutates
            //         the stored value on the next read, causing drift like +19
            //         when minLimit is not itself a multiple of step (e.g. 119 with step=50).
            minVal = clampToStep(clamp(minVal, minLimit, maxLimit));
            maxVal = clampToStep(clamp(maxVal, minLimit, maxLimit));

            // Enforce minimum gap between thumbs
            if (maxVal - minVal < priceGap) {
                if (maxVal >= maxLimit) {
                    minVal = clampToStep(Math.max(minLimit, maxVal - priceGap));
                } else {
                    maxVal = clampToStep(Math.min(maxLimit, minVal + priceGap));
                }
            }

            minPriceRange.value = minVal;
            maxPriceRange.value = maxVal;
            minPriceInput.value = minVal;
            maxPriceInput.value = maxVal;

            updateVisuals();
            updateDirtyState(minVal, maxVal);

            if (!options.silent) notifyFilters();
        }

        // ─────────────────────────────
        // INPUT HANDLERS
        // ─────────────────────────────

        function handleMinInputChange() {
            let val    = parseFloat(minPriceInput.value);
            if (isNaN(val)) val = minLimit;
            const maxVal = parseFloat(maxPriceRange.value) || maxLimit;
            setValues(val, maxVal);
        }

        function handleMaxInputChange() {
            let val    = parseFloat(maxPriceInput.value);
            if (isNaN(val)) val = maxLimit;
            const minVal = parseFloat(minPriceRange.value) || minLimit;
            setValues(minVal, val);
        }

        minPriceInput.addEventListener('change', handleMinInputChange);
        maxPriceInput.addEventListener('change', handleMaxInputChange);

        // ─── FIX: also handle `input` (live typing) so the slider track moves
        //         as the user types, not only on blur
        minPriceInput.addEventListener('input', debounce(handleMinInputChange, 400));
        maxPriceInput.addEventListener('input', debounce(handleMaxInputChange, 400));

        // ─────────────────────────────
        // POINTER / DRAG HANDLERS
        // ─────────────────────────────

        function getValueFromPointer(clientX) {
            const rect    = rangeRoot.getBoundingClientRect();
            const percent = clamp((clientX - rect.left) / rect.width, 0, 1);
            const raw     = minLimit + percent * (maxLimit - minLimit);
            return clampToStep(raw);
        }

        let activeThumb = null;

        function startDrag(e) {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            const pointerValue = getValueFromPointer(e.clientX);
            const minVal       = parseFloat(minPriceRange.value) || minLimit;
            const maxVal       = parseFloat(maxPriceRange.value) || maxLimit;
            const distMin      = Math.abs(pointerValue - minVal);
            const distMax      = Math.abs(pointerValue - maxVal);

            // ─── FIX: when equidistant, prefer 'max' thumb so small gaps don't
            //         lock the min thumb in place
            activeThumb = distMin < distMax ? 'min' : 'max';
            updateActiveThumb(pointerValue);
            rangeRoot.setPointerCapture(e.pointerId);
        }

        function updateActiveThumb(pointerValue) {
            let minVal = parseFloat(minPriceRange.value) || minLimit;
            let maxVal = parseFloat(maxPriceRange.value) || maxLimit;

            if (activeThumb === 'min') {
                setValues(pointerValue, maxVal);
            } else if (activeThumb === 'max') {
                setValues(minVal, pointerValue);
            }
        }

        function onDrag(e) {
            if (!activeThumb) return;
            updateActiveThumb(getValueFromPointer(e.clientX));
        }

        function endDrag(e) {
            if (!activeThumb) return;
            activeThumb = null;
            rangeRoot.releasePointerCapture(e.pointerId);
        }

        rangeRoot.addEventListener('pointerdown',   startDrag);
        rangeRoot.addEventListener('pointermove',   onDrag);
        rangeRoot.addEventListener('pointerup',     endDrag);
        rangeRoot.addEventListener('pointercancel', endDrag);

        // ─────────────────────────────
        // INITIALISE FROM URL / DEFAULTS
        // ─────────────────────────────

        const urlParams     = new URLSearchParams(window.location.search);
        const urlMin        = urlParams.has('min_price') ? parseFloat(urlParams.get('min_price')) : null;
        const urlMax        = urlParams.has('max_price') ? parseFloat(urlParams.get('max_price')) : null;
        const hasPriceFilter = urlMin !== null || urlMax !== null;

        const initMin = hasPriceFilter && urlMin !== null ? urlMin : defaultMin;
        const initMax = hasPriceFilter && urlMax !== null ? urlMax : defaultMax;

        // Use silent: true so we don't fire AJAX on page load
        setValues(initMin, initMax, { silent: true });

        if (!hasPriceFilter) {
            // Ensure names are stripped when there is no active price filter
            container.dataset.priceDirty = 'false';
            setInputNames(false);
        }
    }

    priceRanges.forEach(setupPriceRange);

    // ─── Guard: strip price inputs before ANY form submit if they are at default.
    //     This is the last-resort safety net for non-AJAX (full-page) submits.
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const priceRange = form.querySelector('[data-price-range]');
            if (!priceRange) return;

            const minInput = priceRange.querySelector('[data-input="min"]');
            const maxInput = priceRange.querySelector('[data-input="max"]');
            const defMin   = parseFloat(priceRange.dataset.defaultMin);
            const defMax   = parseFloat(priceRange.dataset.defaultMax);

            if (minInput && Math.abs(parseFloat(minInput.value) - defMin) < 0.01) {
                minInput.removeAttribute('name');
            }
            if (maxInput && Math.abs(parseFloat(maxInput.value) - defMax) < 0.01) {
                maxInput.removeAttribute('name');
            }
        }, true); // capture phase so it runs before the form data is collected
    });
})();
