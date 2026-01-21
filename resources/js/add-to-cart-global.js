// This script defines a global addToCart function for product cards
// It submits an AJAX request to add the product/variant to the cart
import axios from 'axios';

window.addToCart = function(variantId, productId) {
    if (!productId && !variantId) return;
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
        try { window.dispatchEvent(new CustomEvent('cart:changed', { detail: data })); } catch (e) {}
    }).catch(function (error) {
        const resp = error && error.response;
        let msg = 'Failed to add to cart';
        if (resp && resp.data && (resp.data.message || resp.data.error)) msg = resp.data.message || resp.data.error;
        try { window.Toast && window.Toast.error({ title: 'Error', message: msg }); } catch (e) { alert(msg); }
    });
};
