function getCsrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : null;
}

document.addEventListener('click', function (ev) {
    try {
        const btn = ev.target.closest && ev.target.closest('.wishlist-btn');
        if (!btn) return;
        ev.preventDefault();
    } catch (e) {}
}, true);

async function postJson(url, data) {
    const token = getCsrfToken();
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data),
        credentials: 'same-origin'
    });
    return res;
}

function updateButtonState(button, added) {
    const id = String(button.dataset.productVariantId || button.dataset.variantId || button.dataset.productId || button.dataset.wid || '');
    if (!id) return;
    const selectors = [
        `.wishlist-btn[data-product-variant-id="${id}"]`,
        `.wishlist-btn[data-variant-id="${id}"]`,
        `.wishlist-btn[data-product-id="${id}"]`,
        `.wishlist-btn[data-wid="${id}"]`
    ];
    const buttons = document.querySelectorAll(selectors.join(','));
    const svgFilled = '<svg class="w-5 h-5 text-red-500 drop-shadow-sm transition-all duration-200" fill="currentColor" viewBox="0 0 24 24"><path d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z"/></svg>';
    const svgOutline = '<svg class="w-5 h-5 text-gray-400 transition-all duration-200 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12.1 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54l-1.35 1.31z"/></svg>';
    buttons.forEach(b => {
        if (added) {
            b.classList.add('is-wishlisted');
            b.classList.add('opacity-100');
            b.setAttribute('data-init-wishlisted', '1');
            b.setAttribute('aria-pressed', 'true');
            b.classList.remove('opacity-0');
            b.innerHTML = svgFilled;
        } else {
            b.classList.remove('is-wishlisted');
            b.classList.remove('opacity-100');
            b.setAttribute('data-init-wishlisted', '0');
            b.setAttribute('aria-pressed', 'false');
            b.innerHTML = svgOutline;
        }
    });
    // Optionally, pass count if available
    if (typeof window._wishlistCount !== 'undefined') {
        try {
            window.dispatchEvent(new CustomEvent('wishlist:changed', { detail: { id, added, itemCount: window._wishlistCount } }));
        } catch (e) {}
    } else {
        try {
            window.dispatchEvent(new CustomEvent('wishlist:changed', { detail: { id, added } }));
        } catch (e) {}
    }
}

document.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('.wishlist-btn');
    if (!btn) return;
    ev.preventDefault();
    ev.stopPropagation();
    try { ev.stopImmediatePropagation(); } catch (e) {}
    try { btn.type = 'button'; } catch (e) {}
    const id = btn.dataset.productVariantId;
    if (!id) return;
    const isWishlisted = btn.classList.contains('is-wishlisted');
    try {
        if (isWishlisted) {
            const res = await postJson('/client/wishlist/remove', { product_variant_id: id });
            const json = await res.json().catch(()=>({}));
            let count = (typeof json.count !== 'undefined') ? json.count : (typeof json.itemCount !== 'undefined' ? json.itemCount : undefined);
            if (typeof count === 'undefined') {
                // fallback: count unique productVariantId from wishlisted buttons
                const ids = new Set();
                document.querySelectorAll('.wishlist-btn.is-wishlisted').forEach((b) => {
                    const vid = b.dataset.productVariantId;
                    if (vid) ids.add(vid);
                });
                // removing one item
                count = Math.max(0, ids.size - 1);
            }
            window._wishlistCount = count;
            if (res.ok) {
                updateButtonState(btn, false);
                try {
                    const wrapperSelectors = [
                        `[data-wishlist-item="${id}"]`,
                        `[data-product-variant-id="${id}"]`,
                        `[data-variant-id="${id}"]`,
                        `[data-product-id="${id}"]`,
                        `[data-wid="${id}"]`
                    ].join(',');
                    const wrapper = document.querySelector(wrapperSelectors);
                    if (wrapper) wrapper.remove();
                    // Remove manual count update here, rely on event
                    if (count === 0) {
                        try {
                            const grid = document.getElementById('wishlist-grid');
                            const tpl = document.getElementById('wishlist-empty-template');
                            if (grid && tpl) {
                                const header = grid.previousElementSibling;
                                const parent = header ? header.parentNode : grid.parentNode;
                                parent.style.transition = 'opacity 220ms ease';
                                parent.style.opacity = '0';
                                setTimeout(() => {
                                    try {
                                        let node = header || grid;
                                        while (node) {
                                            const next = node.nextElementSibling;
                                            node.remove();
                                            if (node.classList && node.classList.contains('mt-6')) break;
                                            node = next;
                                        }
                                    } catch (e) {}
                                    try { parent.insertBefore(tpl.content.cloneNode(true), parent.firstChild); } catch (e) {}
                                    parent.style.opacity = '1';
                                }, 240);
                            } else {
                                window.location.reload();
                            }
                        } catch (e) { window.location.reload(); }
                    }
                } catch (e) {}
                const msg = json.message || trans('Removed from wishlist');
                try { window.Toast && window.Toast.success({ title: trans('Wishlist'), message: msg }); } catch (e) {}
            } else {
                const msg = json.message || trans('Failed to remove');
                try { window.Toast && window.Toast.error({ title: trans('Wishlist'), message: msg }); } catch (e) {}
            }
        } else {
            const res = await postJson('/client/wishlist/add', { product_variant_id: id });
            const json = await res.json().catch(()=>({}));
            let count = (typeof json.count !== 'undefined') ? json.count : (typeof json.itemCount !== 'undefined' ? json.itemCount : undefined);
            if (typeof count === 'undefined') {
                // fallback: count unique productVariantId from wishlisted buttons
                const ids = new Set();
                document.querySelectorAll('.wishlist-btn.is-wishlisted').forEach((b) => {
                    const vid = b.dataset.productVariantId;
                    if (vid) ids.add(vid);
                });
                // adding one item
                count = ids.size + 1;
            }
            window._wishlistCount = count;
            if (res.ok) {
                updateButtonState(btn, true);
                const msg = json.message || trans('Added to wishlist');
                try { window.Toast && window.Toast.success({ title: trans('Wishlist'), message: msg }); } catch (e) {}
            } else {
                const msg = json.message || trans('Failed to add to wishlist');
                try { window.Toast && window.Toast.error({ title: trans('Wishlist'), message: msg }); } catch (e) {}
            }
        }
    } catch (err) {
        console.error('Wishlist error', err);
        try { window.Toast && window.Toast.error({ title: trans('Wishlist'), message: trans('Request failed') }); } catch (e) {}
    }
});

document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.wishlist-btn[data-product-variant-id][data-init-wishlisted]').forEach(b => {
        if (b.dataset.initWishlisted === '1' || b.dataset.initWishlisted === 'true') updateButtonState(b, true);
    });
});

export {};
