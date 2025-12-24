// Simple wishlist behavior: send AJAX add/remove requests and toggle UI
function getCsrfToken() {
  const m = document.querySelector('meta[name="csrf-token"]');
  return m ? m.getAttribute('content') : null;
}

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
  const svg = button.querySelector('svg');
  if (added) {
    button.classList.add('is-wishlisted');
    if (svg) svg.classList.add('text-red-500');
  } else {
    button.classList.remove('is-wishlisted');
    if (svg) svg.classList.remove('text-red-500');
  }
}

document.addEventListener('click', async (ev) => {
  const btn = ev.target.closest('.wishlist-btn');
  if (!btn) return;
  // Prevent anchor navigation when button is inside a link
  ev.preventDefault();
  ev.stopPropagation();

  // ensure it's a button type to avoid form/anchor default behavior
  try { btn.type = 'button'; } catch (e) {}

  const id = btn.dataset.productVariantId;
  if (!id) return;

  const isWishlisted = btn.classList.contains('is-wishlisted');
  try {
    if (isWishlisted) {
      // remove
      const res = await postJson('/client/wishlist/remove', { product_variant_id: id });
      if (res.ok) updateButtonState(btn, false);
    } else {
      const res = await postJson('/client/wishlist/add', { product_variant_id: id });
      if (res.ok) updateButtonState(btn, true);
    }
  } catch (err) {
    console.error('Wishlist error', err);
  }
});

// Auto-initialize visible buttons (optional: could fetch current wishlist state)
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.wishlist-btn[data-product-variant-id][data-init-wishlisted]').forEach(b => {
    if (b.dataset.initWishlisted === '1' || b.dataset.initWishlisted === 'true') updateButtonState(b, true);
  });
});

export {};
