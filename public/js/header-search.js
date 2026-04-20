// ─────────────────────────────────────────────────────────────
//  Shared helpers  (pure / stateless — safe to reuse across instances)
// ─────────────────────────────────────────────────────────────

/**
 * Highlight every occurrence of `q` inside `text` with a yellow <mark>.
 * Handles multilingual text objects (e.g. { en: "...", ar: "..." }).
 */
function highlightMatch(text, q) {
    if (!text) return '';
    if (typeof text === 'object') {
        const lang = document.documentElement.lang || 'en';
        text = text[lang] || Object.values(text)[0] || '';
    }
    // Escape special regex characters so user input cannot break the expression
    const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return String(text).replace(new RegExp(escaped, 'gi'), m => `<mark class="bg-yellow-200">${m}</mark>`);
}

/**
 * Build the HTML string for a complete search-result payload.
 * Sections: Categories · Subcategories · Brands · Products · Orders
 */
function renderSearchResults(data, q) {
    let html = '';

    if (data.categories && data.categories.length) {
        html += '<div class="px-4 pt-3 pb-1 text-xs font-bold text-blue-700 uppercase">' + trans('Categories') + '</div>';
        html += '<div class="px-4 pb-2 flex flex-wrap gap-2">';
        data.categories.forEach(c => {
            html += `<a href="${c.url}" class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold hover:bg-blue-200 transition">${highlightMatch(c.display_name || c['category-name'] || '', q)}</a>`;
        });
        html += '</div>';
    }

    if (data.sub_categories && data.sub_categories.length) {
        html += '<div class="px-4 pt-3 pb-1 text-xs font-bold text-green-700 uppercase">' + trans('Subcategories') + '</div>';
        html += '<div class="px-4 pb-2 flex flex-wrap gap-2">';
        data.sub_categories.forEach(s => {
            html += `<a href="${s.url}" class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold hover:bg-green-200 transition">${highlightMatch(s.display_name || s['sub-category-name'] || '', q)}</a>`;
        });
        html += '</div>';
    }

    if (data.brands && data.brands.length) {
        html += '<div class="px-4 pt-3 pb-1 text-xs font-bold text-yellow-700 uppercase">' + trans('Brands') + '</div>';
        html += '<div class="px-4 pb-2 flex flex-wrap gap-2">';
        data.brands.forEach(b => {
            html += `<a href="${b.url}" class="inline-block px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold hover:bg-yellow-200 transition">${highlightMatch(b.display_name || b.name, q)}</a>`;
        });
        html += '</div>';
    }

    if (data.products && data.products.length) {
        html += '<div class="px-4 pt-3 pb-1 text-xs font-bold text-primary uppercase">' + trans('Products') + '</div>';
        html += '<div class="max-h-96 overflow-y-auto px-4">';
        data.products.forEach(product => {
            html += `<a href="${product.url}" class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 rounded-xl transition">
                <img src="${product.image_url}" class="w-12 h-12 rounded-lg object-cover bg-gray-100" alt="">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-800 text-sm truncate">${highlightMatch(product.display_name || product.name, q)}</div>
                    <div class="text-xs text-gray-400">${product.sku || ''}</div>
                </div>
            </a>`;
        });
        html += '</div>';
    }

    if (data.orders && data.orders.length) {
        html += '<div class="px-4 pt-3 pb-1 text-xs font-bold text-gray-700 uppercase">' + trans('Orders') + '</div>';
        html += '<div class="px-4 pt-2">';
        data.orders.forEach(o => {
            html += `<a href="${o.url}" class="inline-block px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200 transition">Order #${highlightMatch(o.order_number, q)}</a>`;
        });
        html += '</div>';
    }

    return html || '<div class="p-4 text-sm text-gray-500">' + trans('No results found') + '</div>';
}

// ─────────────────────────────────────────────────────────────
//  Search instance factory
//  Call once per input/results pair — each gets its own state.
// ─────────────────────────────────────────────────────────────

/**
 * Wire up a fully independent search experience for the given elements.
 *
 * @param {HTMLInputElement}  inputEl     The text input to listen to
 * @param {HTMLElement}       resultsEl   The dropdown container to render into
 */
function initSearch(inputEl, resultsEl) {
    // ── Per-instance private state ──
    let searchTimeout = null;
    let page          = 1;
    let loading       = false;
    let lastQuery     = '';
    let hasMore       = true;
    const clearBtn    = document.querySelector(`[data-search-clear="${inputEl.id}"]`);

    function syncClearButton() {
        if (!clearBtn) return;
        clearBtn.classList.toggle('hidden', !inputEl.value.trim());
    }

    function resetSearchState() {
        clearTimeout(searchTimeout);
        page = 1;
        lastQuery = '';
        hasMore = true;
        loading = false;
        resultsEl.innerHTML = '';
        resultsEl.classList.add('hidden');
        syncClearButton();
    }

    if (clearBtn) {
        clearBtn.addEventListener('mousedown', function (event) {
            event.preventDefault();
        });

        clearBtn.addEventListener('click', function () {
            inputEl.value = '';
            inputEl.focus();
            resetSearchState();
        });
    }

    // ── Loader helpers (scoped to THIS resultsEl) ──
    function showLoader() {
        resultsEl.innerHTML += `
            <div class="flex justify-center items-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-t-4 border-primary border-b-4 border-gray-200"></div>
            </div>`;
    }
    function hideLoader() {
        const spinner = resultsEl.querySelector('.animate-spin');
        if (spinner) spinner.parentElement.remove();
    }

    // ── Core fetch (scoped to THIS resultsEl + state) ──
    function fetchResults(q, append = false) {
        if (loading) return;
        loading = true;
        showLoader();

        fetch(`/ajax/search?q=${encodeURIComponent(q)}&page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            hideLoader();
            loading = false;

            if (data.status === 'success' && data.result.has_results) {
                if (append) {
                    resultsEl.innerHTML += renderSearchResults(data.result.data, q);
                } else {
                    resultsEl.innerHTML = renderSearchResults(data.result.data, q);
                }
                resultsEl.classList.remove('hidden');
                hasMore = data.result.has_more || false;
            } else {
                if (!append) {
                    resultsEl.innerHTML = '<div class="p-4 text-sm text-gray-500">' + trans('No results found') + '</div>';
                    resultsEl.classList.remove('hidden');
                }
                hasMore = false;
            }
        })
        .catch(() => {
            // Network / parse errors — hide loader and reset so the user can retry
            hideLoader();
            loading = false;
        });
    }

    // ── Input: debounced search trigger ──
    inputEl.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(searchTimeout);
        syncClearButton();

        if (q.length < 2) {
            resetSearchState();
            return;
        }

        page      = 1;
        lastQuery = q;
        hasMore   = true;

        searchTimeout = setTimeout(() => fetchResults(q), 300);
    });

    // ── Blur: hide results (small delay so link clicks still register) ──
    inputEl.addEventListener('blur', function () {
        setTimeout(() => {
            if (!resultsEl.matches(':hover')) {
                resultsEl.classList.add('hidden');
            }
        }, 200);
    });

    // ── Click fallback: navigate explicitly so desktop clicks cannot be swallowed ──
    resultsEl.addEventListener('click', function (event) {
        const link = event.target.closest('a[href]');
        if (!link) return;

        event.preventDefault();
        window.location.href = link.href;
    });

    // ── Focus: re-show results if content already exists ──
    inputEl.addEventListener('focus', function () {
        syncClearButton();
        if (resultsEl.innerHTML.trim()) resultsEl.classList.remove('hidden');
    });

    // ── Scroll: infinite-load next page ──
    resultsEl.addEventListener('scroll', function () {
        if (!hasMore || loading) return;
        if (resultsEl.scrollTop + resultsEl.clientHeight >= resultsEl.scrollHeight - 60) {
            page++;
            fetchResults(lastQuery, true);
        }
    });

    syncClearButton();
}

// ─────────────────────────────────────────────────────────────
//  Bootstrap — wire up Desktop and Mobile independently
// ─────────────────────────────────────────────────────────────

(function () {
    // Desktop search (hidden on mobile via Tailwind `hidden md:flex`)
    const desktopInput   = document.getElementById('universal-search-input');
    const desktopResults = document.getElementById('universal-search-results');
    if (desktopInput && desktopResults) {
        initSearch(desktopInput, desktopResults);
    }

    // Mobile search (lives inside the mobile dropdown panel)
    const mobileInput   = document.getElementById('universal-search-input-mobile');
    const mobileResults = document.getElementById('universal-search-results-mobile');
    if (mobileInput && mobileResults) {
        initSearch(mobileInput, mobileResults);
    }
})();

// ─────────────────────────────────────────────────────────────
//  Counter listeners — Cart · Wishlist · Compare
//  (unchanged from original — no search dependency)
// ─────────────────────────────────────────────────────────────

(function () {
    function updateCartCounter(count) {
        document.querySelectorAll('.cart-counter').forEach(function (el) {
            el.textContent = (count === null || count === undefined) ? 0 : count;
        });
    }
    window.addEventListener('cart:changed', function (e) {
        var data = e && e.detail ? e.detail : null, count = null;
        try {
            if (data) {
                if (data.summary && typeof data.summary.itemCount !== 'undefined') count = data.summary.itemCount;
                else if (typeof data.itemCount !== 'undefined') count = data.itemCount;
                else if (typeof data === 'number') count = data;
            }
        } catch (err) {}
        if (count !== null) updateCartCounter(count);
    });

    function updateWishlistCounter(count) {
        document.querySelectorAll('.wishlist-counter').forEach(function (el) {
            var n = (count === null || count === undefined) ? 0 : count;
            el.textContent = n;
            el.classList.toggle('hidden', n == 0);
        });
    }
    window.addEventListener('wishlist:changed', function (e) {
        var data = e && e.detail ? e.detail : null, count = null;
        try {
            if (data) {
                if (data.summary && typeof data.summary.itemCount !== 'undefined') count = data.summary.itemCount;
                else if (typeof data.itemCount !== 'undefined') count = data.itemCount;
                else if (typeof data === 'number') count = data;
            }
        } catch (err) {}
        if (count !== null) updateWishlistCounter(count);
    });

    function updateCompareCounter(count) {
        document.querySelectorAll('.compare-counter').forEach(function (el) {
            var n = (count === null || count === undefined) ? 0 : count;
            el.textContent = n;
            el.classList.toggle('hidden', n == 0);
        });
    }
    window.addEventListener('compare:changed', function (e) {
        var data = e && e.detail ? e.detail : null, count = null;
        try {
            if (data) {
                if (typeof data.count !== 'undefined') count = data.count;
                else if (Array.isArray(data.ids)) count = data.ids.length;
                else if (typeof data === 'number') count = data;
            }
        } catch (err) {}
        if (count !== null) updateCompareCounter(count);
    });
})();
