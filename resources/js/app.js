import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

function getMetaCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function setMetaCsrfToken(token) {
    if (!token) return;
    let meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) {
        meta = document.createElement('meta');
        meta.setAttribute('name', 'csrf-token');
        document.head.appendChild(meta);
    }
    meta.setAttribute('content', token);
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

async function refreshCsrfToken(force = false) {
    const now = Date.now();
    if (!force && window.__lastCsrfRefreshAt && now - window.__lastCsrfRefreshAt < 60000) {
        return getMetaCsrfToken();
    }

    const response = await axios.get('/csrf-token', {
        headers: {
            'X-SKIP-TOAST': '1',
        },
    });

    const token = response && response.data ? response.data.token : null;
    if (token) {
        window.__lastCsrfRefreshAt = now;
        setMetaCsrfToken(token);
    }
    return token;
}

const initialCsrfToken = getMetaCsrfToken();
if (initialCsrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = initialCsrfToken;
}

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
}, async function (error) {
    try {
        const resp = error && error.response;
        const config = error && error.config ? error.config : {};
        const url = String(config && config.url ? config.url : '');

        if (resp && resp.status === 419 && !config.__csrfRetried && !url.includes('/csrf-token')) {
            config.__csrfRetried = true;
            const freshToken = await refreshCsrfToken(true);
            if (freshToken) {
                config.headers = config.headers || {};
                config.headers['X-CSRF-TOKEN'] = freshToken;
                return axios(config);
            }
        }
    } catch (e) {}

    try {
        const resp = error && error.response;
        const msg = resp && resp.data && (resp.data.message || resp.data.error) ? (resp.data.message || resp.data.error) : (error.message || 'Request failed');
        window.Toast && window.Toast.error({ title: trans('Error'), message: msg, duration: 6000 });
    } catch (e) {}
    return Promise.reject(error);
});

window.addEventListener('pageshow', function (event) {
    if (event && event.persisted) {
        refreshCsrfToken(true).catch(function () {});
    }
});

window.addEventListener('focus', function () {
    refreshCsrfToken(false).catch(function () {});
});

document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') {
        refreshCsrfToken(false).catch(function () {});
    }
});

document.addEventListener('submit', function (ev) {
    const form = ev.target && ev.target.closest ? ev.target.closest('form') : null;
    if (!form) return;
    const method = String(form.getAttribute('method') || 'GET').toUpperCase();
    if (method === 'GET') return;

    const token = getMetaCsrfToken();
    if (!token) return;

    let hiddenTokenInput = form.querySelector('input[name="_token"]');
    if (!hiddenTokenInput) {
        hiddenTokenInput = document.createElement('input');
        hiddenTokenInput.type = 'hidden';
        hiddenTokenInput.name = '_token';
        form.appendChild(hiddenTokenInput);
    }
    hiddenTokenInput.value = token;
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