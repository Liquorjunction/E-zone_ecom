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
    buttons.forEach(b => {
        const svg = b.querySelector('svg');
        if (added) {
            b.classList.add('is-wishlisted');
            b.classList.add('opacity-100');
            b.setAttribute('data-init-wishlisted', '1');
            b.setAttribute('aria-pressed', 'true');
            b.classList.remove('opacity-0');
            if (svg) svg.classList.add('text-red-500');
        } else {
            b.classList.remove('is-wishlisted');
            b.classList.remove('opacity-100');
            b.setAttribute('data-init-wishlisted', '0');
            b.setAttribute('aria-pressed', 'false');
            if (svg) svg.classList.remove('text-red-500');
        }
    });
    try {
        window.dispatchEvent(new CustomEvent('wishlist:changed', { detail: { id, added } }));
    } catch (e) {}
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
            if (res.ok) {
                console.debug('Wishlist: removed', id, json);
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
                    const countEl = document.getElementById('wishlist-count');
                    if (countEl) {
                        let count = parseInt(countEl.textContent, 10) || 0;
                        count = Math.max(0, count - 1);
                        countEl.textContent = count;
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
                    }
                } catch (e) {}
                const msg = json.message || 'Removed from wishlist';
                try { window.Toast && window.Toast.success({ title: 'Wishlist', message: msg }); } catch (e) {}
            } else {
                const msg = json.message || 'Failed to remove';
                try { window.Toast && window.Toast.error({ title: 'Wishlist', message: msg }); } catch (e) {}
            }
        } else {
            const res = await postJson('/client/wishlist/add', { product_variant_id: id });
            const json = await res.json().catch(()=>({}));
            if (res.ok) {
                console.debug('Wishlist: added', id, json);
                updateButtonState(btn, true);
                const msg = json.message || 'Added to wishlist';
                try { window.Toast && window.Toast.success({ title: 'Wishlist', message: msg }); } catch (e) {}
            } else {
                const msg = json.message || 'Failed to add to wishlist';
                try { window.Toast && window.Toast.error({ title: 'Wishlist', message: msg }); } catch (e) {}
            }
        }
    } catch (err) {
        console.error('Wishlist error', err);
        try { window.Toast && window.Toast.error({ title: 'Wishlist', message: 'Request failed' }); } catch (e) {}
    }
});

document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.wishlist-btn[data-product-variant-id][data-init-wishlisted]').forEach(b => {
        if (b.dataset.initWishlisted === '1' || b.dataset.initWishlisted === 'true') updateButtonState(b, true);
    });
});

export {};
