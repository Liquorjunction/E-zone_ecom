function getCsrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : null;
}

async function postJson(url, data = {}) {
    const token = getCsrfToken();

    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data),
        credentials: 'same-origin',
    });

    const json = await res.json().catch(() => ({}));
    return { res, json };
}

const inFlightVariantIds = new Set();

function iconMarkup(sizeClass, compared) {
    const strokeClass = compared ? 'text-blue-700' : 'text-blue-500';
    const barClass = compared ? 'text-blue-500' : 'text-blue-400';

    return `<svg class="${sizeClass} ${strokeClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="7" width="7" height="13" rx="2" fill="currentColor" class="text-blue-200"/><rect x="14" y="3" width="7" height="17" rx="2" fill="currentColor" class="${barClass}"/><rect x="3" y="7" width="7" height="13" rx="2" stroke="currentColor"/><rect x="14" y="3" width="7" height="17" rx="2" stroke="currentColor"/></svg>`;
}

function setButtonVisual(btn, compared) {
    if (!btn) return;

    btn.classList.toggle('is-compared', compared);
    btn.setAttribute('aria-pressed', compared ? 'true' : 'false');

    const hideOnCompared = btn.dataset.hideOnCompared === '1';
    if (hideOnCompared) {
        btn.classList.toggle('hidden', compared);
    }

    if (btn.classList.contains('group-hover:opacity-100')) {
        btn.classList.toggle('opacity-100', compared);
        btn.classList.toggle('opacity-0', !compared);
    }

    if (btn.classList.contains('rounded-xl')) {
        btn.classList.toggle('bg-blue-50', compared);
        btn.classList.toggle('text-blue-700', compared);
        btn.classList.toggle('bg-gray-100', !compared);
        btn.classList.toggle('text-blue-500', !compared);
    }

    const currentSvg = btn.querySelector('svg');
    const sizeClass = currentSvg?.classList.contains('w-4') ? 'w-4 h-4' : 'w-5 h-5';

    btn.innerHTML = iconMarkup(sizeClass, compared);
}

function syncButtonState(ids) {
    const comparedIds = new Set((ids || []).map((id) => String(id)));
    document.querySelectorAll('.compare-btn[data-product-variant-id], .compare-btn[data-variant-id]').forEach((btn) => {
        const id = String(btn.dataset.productVariantId || btn.dataset.variantId || '');
        if (!id) return;
        setButtonVisual(btn, comparedIds.has(id));
    });

    try {
        window.dispatchEvent(
            new CustomEvent('compare:changed', {
                detail: {
                    ids: Array.from(comparedIds),
                    count: comparedIds.size,
                },
            }),
        );
    } catch (_) {}
}

async function bootCompareState() {
    try {
        const { res, json } = await postJson('/compare/get', {});
        if (!res.ok) return;
        syncButtonState(json.ids || []);
    } catch (_) {}
}

document.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('.compare-btn');
    if (!btn) return;

    ev.preventDefault();
    ev.stopPropagation();

    const variantId = btn.dataset.productVariantId || btn.dataset.variantId;
    if (!variantId) return;

    if (inFlightVariantIds.has(variantId)) return;

    const compared = btn.classList.contains('is-compared');
    const endpoint = compared ? '/compare/remove' : '/compare/add';

    try {
        inFlightVariantIds.add(variantId);
        btn.disabled = true;

        const { res, json } = await postJson(endpoint, { product_variant_id: variantId });

        if (res.ok) {
            const ids = Array.isArray(json.ids) ? json.ids : [];
            syncButtonState(ids);
            if (window.Toast && json.message) {
                window.Toast.success({ title: 'Compare', message: json.message });
            }
            return;
        }

        if (window.Toast) {
            window.Toast.error({
                title: 'Compare',
                message: json.message || trans('Failed to update compare list.'),
            });
        }
    } catch (_) {
        if (window.Toast) {
            window.Toast.error({ title: 'Compare', message: trans('Request failed.') });
        }
    } finally {
        inFlightVariantIds.delete(variantId);
        btn.disabled = false;
    }
});

document.addEventListener('DOMContentLoaded', bootCompareState);

export {};
