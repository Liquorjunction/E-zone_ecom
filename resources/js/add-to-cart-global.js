// This script defines a global addToCart function for product cards
// It submits an AJAX request to add the product/variant to the cart
import axios from 'axios';

const ITEM_STATUS_URL = '/cart/item-status';

function setButtonInCartState(btn, inCart) {
    if (!btn) return;

    if (inCart) {
        btn.disabled = true;
        btn.setAttribute('aria-disabled', 'true');
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-primary/90', 'hover:shadow-xl', 'hover:shadow-primary/40', 'hover:scale-105');

        const textSpan = btn.querySelector('span');
        if (textSpan) {
            textSpan.textContent = trans('In Cart');
        } else {
            btn.innerHTML = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';
        }
    } else {
        btn.disabled = false;
        btn.removeAttribute('aria-disabled');
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
    }
}

function getCsrfToken() {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    return tokenMeta ? tokenMeta.getAttribute('content') : null;
}

function checkItemStatus(variantId, productId) {
    if (!productId && !variantId) {
        return Promise.resolve(false);
    }

    const csrf = getCsrfToken();
    const payload = {
        product_id: Number(productId || 0),
    };

    if (variantId && variantId !== 'null') {
        payload.variant_id = Number(variantId);
    }

    return axios.post(ITEM_STATUS_URL, payload, {
        headers: Object.assign({ 'X-SKIP-TOAST': '1' }, csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
    }).then((resp) => {
        const data = resp && resp.data;
        return !!(data && data.success && data.in_cart);
    }).catch(() => false);
}

function markButtonsInCart(variantId, productId) {
    const selectors = [];
    if (variantId) selectors.push('button.add-to-cart-btn[data-product-variant-id="' + variantId + '"]');
    else if (productId) selectors.push('button.add-to-cart-btn[data-product-id="' + productId + '"]');
    if (!selectors.length) return;

    const seen = new Set();
    document.querySelectorAll(selectors.join(',')).forEach((btn) => {
        if (!btn || seen.has(btn)) return;
        seen.add(btn);
        setButtonInCartState(btn, true);
    });
}

window.__markInCartButtons = markButtonsInCart;

function syncCardButtonsFromStatusRoute() {
    const buttons = Array.from(document.querySelectorAll('button.add-to-cart-btn[data-product-id]'));
    if (!buttons.length) return;

    const keyToButtons = new Map();

    buttons.forEach((btn) => {
        const productId = btn.getAttribute('data-product-id');
        const variantId = btn.getAttribute('data-product-variant-id');
        const key = String(productId || '') + '::' + String(variantId || '');

        if (!keyToButtons.has(key)) keyToButtons.set(key, []);
        keyToButtons.get(key).push({ btn, productId, variantId });
    });

    keyToButtons.forEach((entries) => {
        const sample = entries[0];
        checkItemStatus(sample.variantId, sample.productId).then((inCart) => {
            entries.forEach(({ btn }) => setButtonInCartState(btn, inCart));
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', syncCardButtonsFromStatusRoute);
} else {
    syncCardButtonsFromStatusRoute();
}

window.addEventListener('cart:changed', syncCardButtonsFromStatusRoute);

window.addToCart = function(variantId, productId) {
    if (!productId && !variantId) return;

    // Find the product card element
    let card = null;
    if (variantId) {
        card = document.querySelector('[data-product-variant-id="' + variantId + '"]');
    }
    if (!card && productId) {
        card = document.querySelector('[onclick*="addToCart(' + variantId + ', \'" + productId + "\')"]');
    }
    // Find stock value in the card
    let stock = null;
    if (card) {
        // Traverse up to card root
        let root = card.closest('.group');
        if (root) {
            // Use getElementsByClassName for robustness
            let stockElems = root.getElementsByClassName('text-xs');
            let stockElem = null;
            for (let i = 0; i < stockElems.length; i++) {
                if (stockElems[i].textContent.includes('In Stock') || stockElems[i].textContent.includes('Out of Stock')) {
                    stockElem = stockElems[i];
                    break;
                }
            }
            if (stockElem) {
                let stockText = stockElem.textContent;
                let match = stockText.match(/\((\d+)\)/);
                if (match) {
                    stock = parseInt(match[1], 10);
                } else if (stockText.includes('Out of Stock')) {
                    stock = 0;
                }
            }
        }
    }
    if (stock === 0) {
        try { window.Toast && window.Toast.error({ title: trans('Error'), message: trans('This item is out of stock.') }); } catch (e) { alert(trans('This item is out of stock.')); }
        return;
    }

    checkItemStatus(variantId, productId).then((alreadyInCart) => {
        if (alreadyInCart) {
            markButtonsInCart(variantId, productId);
            return;
        }

        const url = '/cart/add';
        const formData = new FormData();
        if (productId && productId !== 'null') formData.append('product_id', productId);
        if (variantId && variantId !== 'null') formData.append('variant_id', variantId);
        formData.append('quantity', 1);

        const csrf = getCsrfToken();

        axios.post(url, formData, {
            headers: Object.assign({ 'Content-Type': 'multipart/form-data', 'X-SKIP-TOAST': '1' }, csrf ? { 'X-CSRF-TOKEN': csrf } : {})
        }).then(function (resp) {
            const data = resp && resp.data;
            const msg = (data && (data.message || (data.success ? data.success : null))) || trans('Item added to cart');
            try { window.Toast && window.Toast.success({ title: trans('Added'), message: msg }); } catch (e) { alert(msg); }
            try { markButtonsInCart(variantId, productId); } catch (e) {}
            try { window.dispatchEvent(new CustomEvent('cart:changed', { detail: data })); } catch (e) {}
        }).catch(function (error) {
            const resp = error && error.response;
            let msg = trans('Failed to add to cart.');
            // If unauthenticated, redirect to login page
            if (resp && resp.status === 401) {
                window.location.href = '/login';
                return;
            }
            if (resp && resp.data && (resp.data.message || resp.data.error)) msg = resp.data.message || resp.data.error;
            try { window.Toast && window.Toast.error({ title: trans('Error'), message: msg }); } catch (e) { alert(msg); }
        });
    });
};
