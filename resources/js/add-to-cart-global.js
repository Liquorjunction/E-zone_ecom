// This script defines a global addToCart function for product cards
// It submits an AJAX request to add the product/variant to the cart
import axios from 'axios';

function markButtonsInCart(variantId, productId) {
    const selectors = [];
    if (variantId) selectors.push('button.add-to-cart-btn[data-product-variant-id="' + variantId + '"]');
    if (productId) selectors.push('button.add-to-cart-btn[data-product-id="' + productId + '"]');
    if (!selectors.length) return;

    const seen = new Set();
    document.querySelectorAll(selectors.join(',')).forEach((btn) => {
        if (!btn || seen.has(btn)) return;
        seen.add(btn);
        btn.disabled = true;
        btn.setAttribute('aria-disabled', 'true');
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-primary/90', 'hover:shadow-xl', 'hover:shadow-primary/40', 'hover:scale-105');

        const textSpan = btn.querySelector('span');
        if (textSpan) {
            textSpan.textContent = 'In Cart';
        } else {
            // Icon-only button: swap to check icon
            btn.innerHTML = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';
        }
    });
}

window.__markInCartButtons = markButtonsInCart;

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
        try { window.Toast && window.Toast.error({ title: 'Error', message: 'This item is out of stock.' }); } catch (e) { alert('This item is out of stock.'); }
        return;
    }

    const url = '/cart/add';
    const formData = new FormData();
    if (productId && productId !== 'null') formData.append('product_id', productId);
    if (variantId && variantId !== 'null') formData.append('variant_id', variantId);
    formData.append('quantity', 1);

    // CSRF token
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;

    axios.post(url, formData, {
        headers: Object.assign({ 'Content-Type': 'multipart/form-data', 'X-SKIP-TOAST': '1' }, csrf ? { 'X-CSRF-TOKEN': csrf } : {})
    }).then(function (resp) {
        const data = resp && resp.data;
        const msg = (data && (data.message || (data.success ? data.success : null))) || 'Item added to cart';
        try { window.Toast && window.Toast.success({ title: 'Added', message: msg }); } catch (e) { alert(msg); }
        try { markButtonsInCart(variantId, productId); } catch (e) {}
        try { window.dispatchEvent(new CustomEvent('cart:changed', { detail: data })); } catch (e) {}
    }).catch(function (error) {
        const resp = error && error.response;
        let msg = 'Failed to add to cart';
        // If unauthenticated, redirect to login page
        if (resp && resp.status === 401) {
            window.location.href = '/login';
            return;
        }
        if (resp && resp.data && (resp.data.message || resp.data.error)) msg = resp.data.message || resp.data.error;
        try { window.Toast && window.Toast.error({ title: 'Error', message: msg }); } catch (e) { alert(msg); }
    });
};
