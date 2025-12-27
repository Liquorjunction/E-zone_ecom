import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// --- Alpine Init ---
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

// Global toast notifications
import './toast';
// Wishlist interactions
import './wishlist';
// Security page behaviors
import './pages/security';

// AJAX add-to-cart for homepage/product cards
import './ajax-cart';

// Automatically show toast for axios responses and errors
axios.interceptors.response.use(function (response) {
	// Skip global toasts for requests that set the X-SKIP-TOAST header
	try {
		if (response && response.config && response.config.headers && (response.config.headers['X-SKIP-TOAST'] || response.config.headers['x-skip-toast'])) {
			return response;
		}
	} catch (e) {}
	try {
		const data = response && response.data;
		if (data && data.toast) {
			// expect { type, title, message, duration, actionText }
			window.Toast && window.Toast.show(data.toast);
		} else if (data && data.message) {
			window.Toast && window.Toast.info({ title: '', message: data.message, duration: 3500 });
		}
	} catch (e) {
		// ignore
	}
	return response;
}, function (error) {
	try {
		const resp = error && error.response;
		const msg = resp && resp.data && (resp.data.message || resp.data.error) ? (resp.data.message || resp.data.error) : (error.message || 'Request failed');
		window.Toast && window.Toast.error({ title: 'Error', message: msg, duration: 6000 });
	} catch (e) {
		// ignore
	}
	return Promise.reject(error);
});

// Click-delegate: elements can include data-toast, data-toast-title, data-toast-message, data-toast-action
document.addEventListener('click', function (ev) {
    const btn = ev.target.closest && ev.target.closest('[data-toast]');
    if (!btn) return;
    // opt-out when element or ancestor has data-toast-ignore or class 'no-toast'
    if (btn.closest && btn.closest('[data-toast-ignore], .no-toast')) return;
    const type = btn.getAttribute('data-toast') || 'info';
    const title = btn.getAttribute('data-toast-title') || '';
    const message = btn.getAttribute('data-toast-message') || '';
    const actionText = btn.getAttribute('data-toast-action') || null;
    try {
        window.Toast && window.Toast.show({ type, title, message, actionText });
    } catch (e) {}
});
