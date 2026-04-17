import axios from 'axios';

// Intercept forms with class `ajax-add-to-cart` and submit via AJAX
document.addEventListener('submit', function (ev) {
    const form = ev.target;
    if (!form.classList || !form.classList.contains('ajax-add-to-cart')) return;
    ev.preventDefault();

    const url = form.getAttribute('action') || '/cart/add';
    const formData = new FormData(form);

    // include product/variant data attributes if present
    const pid = form.dataset.productId;
    const vid = form.dataset.variantId;
    if (pid && !formData.has('product_id')) formData.append('product_id', pid);
    if (vid && !formData.has('variant_id') && vid !== 'null') formData.append('variant_id', vid);

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;

    axios.post(url, formData, {
        headers: Object.assign({ 'Content-Type': 'multipart/form-data', 'X-SKIP-TOAST': '1' }, csrf ? { 'X-CSRF-TOKEN': csrf } : {})
    }).then(function (resp) {
        const data = resp && resp.data;
        const msg = (data && (data.message || (data.success ? data.success : null))) || trans('Item added to cart');
        try { window.Toast && window.Toast.success({ title: trans('Added'), message: msg }); } catch (e) {
            try { showUniversalPopup(msg, 'success', 2000); } catch(e2) { alert(msg); }
        }

        try {
            if (typeof window.__markInCartButtons === 'function') {
                window.__markInCartButtons(vid, pid);
            }
        } catch (e) {}

        // Optionally fire a custom event so other scripts (cart icon) can update
        try { window.dispatchEvent(new CustomEvent('cart:changed', { detail: data })); } catch (e) {}
    }).catch(function (error) {
        const resp = error && error.response;
        let msg = trans('Failed to add to cart.');
        if (resp && resp.data && (resp.data.message || resp.data.error)) msg = resp.data.message || resp.data.error;
        try { window.Toast && window.Toast.error({ title: trans('Error'), message: msg }); } catch (e) {
            try { showUniversalPopup(msg, 'error', 3000); } catch(e2) { alert(msg); }
        }
    });
});
