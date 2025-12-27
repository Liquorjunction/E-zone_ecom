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
        const msg = (data && (data.message || (data.success ? data.success : null))) || 'Item added to cart';
        try { window.Toast && window.Toast.success({ title: 'Added', message: msg }); } catch (e) { alert(msg); }

        // Optionally fire a custom event so other scripts (cart icon) can update
        try { window.dispatchEvent(new CustomEvent('cart:changed', { detail: data })); } catch (e) {}
    }).catch(function (error) {
        const resp = error && error.response;
        let msg = 'Failed to add to cart';
        if (resp && resp.data && (resp.data.message || resp.data.error)) msg = resp.data.message || resp.data.error;
        try { window.Toast && window.Toast.error({ title: 'Error', message: msg }); } catch (e) { alert(msg); }
    });
});
