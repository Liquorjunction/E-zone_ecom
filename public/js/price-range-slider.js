(function() {
    'use strict';

    const priceRanges = document.querySelectorAll('[data-price-range]');
    if (!priceRanges.length) return;

    const priceGap = 100; // Minimum distance between min and max

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
        const sliderTrack = container.querySelector('[data-range-track]');
        const rangeRoot = container.querySelector('[data-range-root]');

        if (!minPriceInput || !maxPriceInput || !minPriceRange || !maxPriceRange || !rangeRoot) return;

        const form = container.closest('form');
        const minLimit = parseInt(minPriceRange.min) || 0;
        const maxLimit = parseInt(minPriceRange.max) || 5000;
        const step = parseInt(minPriceRange.step) || 1;
        const defaultMin = Number.isFinite(parseFloat(container.dataset.defaultMin))
            ? parseFloat(container.dataset.defaultMin)
            : minLimit;
        const defaultMax = Number.isFinite(parseFloat(container.dataset.defaultMax))
            ? parseFloat(container.dataset.defaultMax)
            : maxLimit;
        let priceDirty = false;

        function clampToLimits(val) {
            return Math.min(Math.max(val, minLimit), maxLimit);
        }

        function clampToStep(val) {
            const snapped = Math.round(val / step) * step;
            if (Math.abs(snapped - maxLimit) <= step / 2) return maxLimit;
            if (Math.abs(snapped - minLimit) <= step / 2) return minLimit;
            return clampToLimits(snapped);
        }

        function updateVisuals() {
            if (!sliderTrack) return;
            const minVal = parseInt(minPriceRange.value) || minLimit;
            const maxVal = parseInt(maxPriceRange.value) || maxLimit;
            const left = ((minVal - minLimit) / (maxLimit - minLimit)) * 100;
            const right = 100 - ((maxVal - minLimit) / (maxLimit - minLimit)) * 100;
            sliderTrack.style.left = left + "%";
            sliderTrack.style.right = right + "%";
        }

        const notifyFilters = debounce(() => {
            if (!form) return;
            const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
            form.dispatchEvent(submitEvent);
        }, 150);

        function setInputNames(enabled) {
            if (enabled) {
                if (minPriceInput.dataset.name) minPriceInput.name = minPriceInput.dataset.name;
                if (maxPriceInput.dataset.name) maxPriceInput.name = maxPriceInput.dataset.name;
                return;
            }

            minPriceInput.removeAttribute('name');
            maxPriceInput.removeAttribute('name');
        }

        function updateDirtyState(minVal, maxVal) {
            const minMatches = Math.abs(minVal - defaultMin) <= step / 2;
            const maxMatches = Math.abs(maxVal - defaultMax) <= step / 2;
            const isDefault = minMatches && maxMatches;
            if (isDefault) {
                priceDirty = false;
                container.dataset.priceDirty = 'false';
                setInputNames(false);
                return;
            }

            priceDirty = true;
            container.dataset.priceDirty = 'true';
            setInputNames(true);
        }

        function setValues(minVal, maxVal, options = {}) {
            minPriceRange.value = minVal;
            maxPriceRange.value = maxVal;
            minPriceInput.value = minVal;
            maxPriceInput.value = maxVal;
            updateVisuals();
            updateDirtyState(minVal, maxVal);
            if (!options.silent) {
                notifyFilters();
            }
        }

        function handleMinInputChange() {
            let val = parseInt(minPriceInput.value);
            if (Number.isNaN(val)) val = minLimit;
            val = clampToLimits(val);
            const maxVal = parseInt(maxPriceInput.value) || maxLimit;

            if (maxVal - val < priceGap) val = maxVal - priceGap;
            if (val < minLimit) val = minLimit;

            setValues(val, maxVal);
        }

        function handleMaxInputChange() {
            let val = parseInt(maxPriceInput.value);
            if (Number.isNaN(val)) val = maxLimit;
            val = clampToLimits(val);
            const minVal = parseInt(minPriceInput.value) || minLimit;

            if (val - minVal < priceGap) val = minVal + priceGap;
            if (val > maxLimit) val = maxLimit;

            setValues(minVal, val);
        }

        function getValueFromPointer(clientX) {
            const rect = rangeRoot.getBoundingClientRect();
            const percent = Math.min(Math.max((clientX - rect.left) / rect.width, 0), 1);
            const raw = minLimit + percent * (maxLimit - minLimit);
            return clampToStep(clampToLimits(raw));
        }

        let activeThumb = null;

        function startDrag(e) {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            const pointerValue = getValueFromPointer(e.clientX);
            const minVal = parseInt(minPriceRange.value) || minLimit;
            const maxVal = parseInt(maxPriceRange.value) || maxLimit;
            const distToMin = Math.abs(pointerValue - minVal);
            const distToMax = Math.abs(pointerValue - maxVal);
            activeThumb = distToMin <= distToMax ? 'min' : 'max';
            updateActiveThumb(pointerValue);
            rangeRoot.setPointerCapture(e.pointerId);
        }

        function updateActiveThumb(pointerValue) {
            let minVal = parseInt(minPriceRange.value) || minLimit;
            let maxVal = parseInt(maxPriceRange.value) || maxLimit;

            if (activeThumb === 'min') {
                minVal = pointerValue;
                if (maxVal - minVal < priceGap) minVal = maxVal - priceGap;
                if (minVal < minLimit) minVal = minLimit;
            } else if (activeThumb === 'max') {
                maxVal = pointerValue;
                if (maxVal - minVal < priceGap) maxVal = minVal + priceGap;
                if (maxVal > maxLimit) maxVal = maxLimit;
            }
            setValues(minVal, maxVal);
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

        minPriceInput.addEventListener('change', handleMinInputChange);
        maxPriceInput.addEventListener('change', handleMaxInputChange);

        rangeRoot.addEventListener('pointerdown', startDrag);
        rangeRoot.addEventListener('pointermove', onDrag);
        rangeRoot.addEventListener('pointerup', endDrag);
        rangeRoot.addEventListener('pointercancel', endDrag);

        let minVal = parseInt(minPriceRange.value) || minLimit;
        let maxVal = parseInt(maxPriceRange.value) || maxLimit;
        if (maxVal - minVal < priceGap) {
            minVal = Math.max(minLimit, maxVal - priceGap);
        }
        if (minPriceInput.value === '') minPriceInput.value = minVal;
        if (maxPriceInput.value === '') maxPriceInput.value = maxVal;
        setValues(minVal, maxVal, { silent: true });

        const initialMin = parseFloat(minPriceInput.value);
        const initialMax = parseFloat(maxPriceInput.value);
        if ((Number.isFinite(initialMin) && initialMin !== defaultMin) ||
            (Number.isFinite(initialMax) && initialMax !== defaultMax)) {
            updateDirtyState(initialMin, initialMax);
        }
    }

    priceRanges.forEach(setupPriceRange);

    // Remove min_price/max_price from form submission if unchanged from default (works for both normal and AJAX submits)
    function handlePriceRangeFormSubmit(e) {
        var form = e.target;
        var priceRange = form.querySelector('[data-price-range]');
        if (!priceRange) return;
        var minInput = priceRange.querySelector('[data-input="min"]');
        var maxInput = priceRange.querySelector('[data-input="max"]');
        var defaultMin = parseFloat(priceRange.dataset.defaultMin);
        var defaultMax = parseFloat(priceRange.dataset.defaultMax);
        var minVal = parseFloat(minInput.value);
        var maxVal = parseFloat(maxInput.value);
        if (minInput && Math.abs(minVal - defaultMin) < 0.01) {
            minInput.name = '';
        }
        if (maxInput && Math.abs(maxVal - defaultMax) < 0.01) {
            maxInput.name = '';
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', handlePriceRangeFormSubmit, true);
        });
    });
})();
