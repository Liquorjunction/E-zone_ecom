document.addEventListener('DOMContentLoaded', function() {
  const getCsrf = () => {
    const t = document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return t || '';
  };

  function updateCartItem(itemId, quantity) {
    const url = '/cart/update';
    const body = new URLSearchParams();
    body.append('_token', getCsrf());
    body.append('item_id', String(itemId));
    body.append('quantity', String(quantity));

    return fetch(url, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    }).then(r => r.json());
  }

  function removeCartItem(itemId, formAction, formData) {
    const url = formAction || '/cart/remove';
    // formData is FormData or key/value mapping
    const body = new URLSearchParams();
    body.append('_token', getCsrf());
    body.append('item_id', String(itemId));

    return fetch(url, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    }).then(r => r.json());
  }

  // Quantity button handlers
  document.querySelectorAll('.qty-decrease, .qty-increase').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const itemId = this.getAttribute('data-item-id');
      const input = document.querySelector('.cart-quantity-input[data-item-id="' + itemId + '"]');
      if (!input) return;
      let v = parseInt(input.value || '1', 10);
      if (this.classList.contains('qty-decrease')) v = Math.max(1, v - 1);
      else v = v + 1;
      input.value = v;
      updateCartItem(itemId, v).then(resp => {
        if (!resp || !resp.success) return console.error('Update failed', resp);
        // update line total
        const lineEl = document.getElementById('item-line-total-' + itemId);
        if (lineEl && resp.item && resp.item.line_total !== undefined) {
          lineEl.textContent = '$' + Number(resp.item.line_total).toFixed(2);
        }
        // update order summary
        if (resp.summary) {
          document.getElementById('order-subtotal').textContent = '$' + Number(resp.summary.subtotal).toFixed(2);
          document.getElementById('order-tax').textContent = '$' + Number(resp.summary.tax).toFixed(2);
          document.getElementById('order-total').textContent = '$' + Number(resp.summary.total).toFixed(2);
          document.getElementById('order-item-count').textContent = resp.summary.itemCount;
        }
      }).catch(err => console.error(err));
    });
  });

  // Quantity input manual change
  document.querySelectorAll('.cart-quantity-input').forEach(input => {
    input.addEventListener('change', function() {
      const itemId = this.getAttribute('data-item-id');
      let v = parseInt(this.value || '1', 10);
      if (isNaN(v) || v < 1) v = 1;
      this.value = v;
      updateCartItem(itemId, v).then(resp => {
        if (!resp || !resp.success) return console.error('Update failed', resp);
        const lineEl = document.getElementById('item-line-total-' + itemId);
        if (lineEl && resp.item && resp.item.line_total !== undefined) {
          lineEl.textContent = '$' + Number(resp.item.line_total).toFixed(2);
        }
        if (resp.summary) {
          document.getElementById('order-subtotal').textContent = '$' + Number(resp.summary.subtotal).toFixed(2);
          document.getElementById('order-tax').textContent = '$' + Number(resp.summary.tax).toFixed(2);
          document.getElementById('order-total').textContent = '$' + Number(resp.summary.total).toFixed(2);
          document.getElementById('order-item-count').textContent = resp.summary.itemCount;
        }
      }).catch(err => console.error(err));
    });
  });

  // Remove forms via AJAX
  document.querySelectorAll('.cart-remove-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const itemId = this.querySelector('input[name="item_id"]')?.value;
      if (!itemId) return;
      const action = this.getAttribute('action');
      removeCartItem(itemId, action).then(resp => {
        if (!resp || !resp.success) return console.error('Remove failed', resp);
        // remove DOM row
        const row = document.querySelector('.cart-item-row[data-item-id="' + itemId + '"]');
        if (row) row.remove();
        // update summary
        if (resp.summary) {
          document.getElementById('order-subtotal').textContent = '$' + Number(resp.summary.subtotal).toFixed(2);
          document.getElementById('order-tax').textContent = '$' + Number(resp.summary.tax).toFixed(2);
          document.getElementById('order-total').textContent = '$' + Number(resp.summary.total).toFixed(2);
          document.getElementById('order-item-count').textContent = resp.summary.itemCount;
        }
        // if cart empty, reload to show empty state
        if (resp.summary && parseInt(resp.summary.itemCount, 10) === 0) {
          window.location.reload();
        }
      }).catch(err => console.error(err));
    });
  });

});
