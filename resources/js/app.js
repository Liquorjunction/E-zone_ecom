import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import persist from '@alpinejs/persist';

Alpine.plugin(collapse);
Alpine.plugin(persist);
window.Alpine = Alpine;

Alpine.start();

import './translations';
import './toast';
import './wishlist';
import './compare';
import './pages/security';
import './pagination';
import './ajax-cart';
import './add-to-cart-global';
import './universal-popup';
import './newsletter';
import './prose';
import './timer';

axios.interceptors.response.use(function (response) {
    try {
        if (response && response.config && response.config.headers && (response.config.headers['X-SKIP-TOAST'] || response.config.headers['x-skip-toast'])) {
            return response;
        }
    } catch (e) {}
    try {
        const data = response && response.data;
        if (data && data.toast) {
            window.Toast && window.Toast.show(data.toast);
        } else if (data && data.message) {
            window.Toast && window.Toast.info({ title: '', message: data.message, duration: 3500 });
        }
    } catch (e) {}
    return response;
}, function (error) {
    try {
        const resp = error && error.response;
        const msg = resp && resp.data && (resp.data.message || resp.data.error) ? (resp.data.message || resp.data.error) : (error.message || 'Request failed');
        window.Toast && window.Toast.error({ title: trans('Error'), message: msg, duration: 6000 });
    } catch (e) {}
    return Promise.reject(error);
});

document.addEventListener('click', function (ev) {
    const btn = ev.target.closest && ev.target.closest('[data-toast]');
    if (!btn) return;
    if (btn.closest && btn.closest('[data-toast-ignore], .no-toast')) return;
    const type = btn.getAttribute('data-toast') || 'info';
    const title = btn.getAttribute('data-toast-title') || '';
    const message = btn.getAttribute('data-toast-message') || '';
    const actionText = btn.getAttribute('data-toast-action') || null;
    try {
        window.Toast && window.Toast.show({ type, title, message, actionText });
    } catch (e) {}
});


