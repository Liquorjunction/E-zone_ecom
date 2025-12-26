document.addEventListener('DOMContentLoaded', function() {
  const getCsrf = () => {
    const t = document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return t || '';
  };

  // Add loading state helper
  function setLoading(element, isLoading) {
    if (isLoading) {
      element.style.opacity = '0.6';
      element.style.pointerEvents = 'none';
    } else {
      element.style.opacity = '1';
      element.style.pointerEvents = 'auto';
    }
  }

  // Add smooth number animation
  function animateValue(element, start, end, duration = 500) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
      current += increment;
      if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
        current = end;
        clearInterval(timer);
      }
      element.textContent = '$' + current.toFixed(2);
    }, 16);
  }

  function updateCartItem(itemId, quantity) {
    const url = '/cart/update';
    const body = new URLSearchParams();
    body.append('_token', getCsrf());
    body.append('item_id', String(itemId));
    body.append('quantity', String(quantity));

    return fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
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
    const body = new URLSearchParams();
    body.append('_token', getCsrf());
    body.append('item_id', String(itemId));

    return fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    }).then(r => r.json());
  }

  // Debounce function for input changes
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Quantity button handlers with smooth animations
  document.querySelectorAll('.qty-decrease, .qty-increase').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const itemId = this.getAttribute('data-item-id');
      const input = document.querySelector('.cart-quantity-input[data-item-id="' + itemId + '"]');
      const row = document.querySelector('.cart-item-row[data-item-id="' + itemId + '"]');

      if (!input) return;

      let v = parseInt(input.value || '1', 10);
      if (this.classList.contains('qty-decrease')) {
        v = Math.max(1, v - 1);
      } else {
        v = v + 1;
      }

      input.value = v;

      // Add loading state
      if (row) setLoading(row, true);

      updateCartItem(itemId, v).then(resp => {
        if (!resp || !resp.success) {
          console.error('Update failed', resp);
          if (row) setLoading(row, false);
          return;
        }

        // Update line total with animation
        const lineEl = document.getElementById('item-line-total-' + itemId);
        if (lineEl && resp.item && resp.item.line_total !== undefined) {
          const currentValue = parseFloat(lineEl.textContent.replace('$', ''));
          const newValue = Number(resp.item.line_total);
          if (currentValue !== newValue) {
            lineEl.style.transform = 'scale(1.1)';
            setTimeout(() => {
              lineEl.style.transform = 'scale(1)';
            }, 200);
          }
          lineEl.textContent = '$' + newValue.toFixed(2);
        }

        // Update order summary with animations
        if (resp.summary) {
          updateSummary(resp.summary);
        }

        // Remove loading state
        if (row) {
          setLoading(row, false);
          // Add success pulse effect
          row.style.transition = 'all 0.3s ease';
          row.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
          setTimeout(() => {
            row.style.backgroundColor = '';
          }, 500);
        }
      }).catch(err => {
        console.error(err);
        if (row) setLoading(row, false);
      });
    });
  });

  // Quantity input manual change with debounce
  document.querySelectorAll('.cart-quantity-input').forEach(input => {
    const handleChange = debounce(function() {
      const itemId = this.getAttribute('data-item-id');
      const row = document.querySelector('.cart-item-row[data-item-id="' + itemId + '"]');
      let v = parseInt(this.value || '1', 10);

      if (isNaN(v) || v < 1) v = 1;
      this.value = v;

      if (row) setLoading(row, true);

      updateCartItem(itemId, v).then(resp => {
        if (!resp || !resp.success) {
          console.error('Update failed', resp);
          if (row) setLoading(row, false);
          return;
        }

        const lineEl = document.getElementById('item-line-total-' + itemId);
        if (lineEl && resp.item && resp.item.line_total !== undefined) {
          lineEl.style.transform = 'scale(1.1)';
          setTimeout(() => {
            lineEl.style.transform = 'scale(1)';
          }, 200);
          lineEl.textContent = '$' + Number(resp.item.line_total).toFixed(2);
        }

        if (resp.summary) {
          updateSummary(resp.summary);
        }

        if (row) {
          setLoading(row, false);
          row.style.transition = 'all 0.3s ease';
          row.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
          setTimeout(() => {
            row.style.backgroundColor = '';
          }, 500);
        }
      }).catch(err => {
        console.error(err);
        if (row) setLoading(row, false);
      });
    }, 500);

    input.addEventListener('change', handleChange);
    input.addEventListener('input', handleChange);
  });

  // Helper function to update summary with animations
  function updateSummary(summary) {
    const subtotalEl = document.getElementById('order-subtotal');
    const taxEl = document.getElementById('order-tax');
    const totalEl = document.getElementById('order-total');
    const itemCountEl = document.getElementById('order-item-count');

    if (subtotalEl) {
      subtotalEl.style.transition = 'transform 0.3s ease';
      subtotalEl.style.transform = 'scale(1.1)';
      setTimeout(() => {
        subtotalEl.style.transform = 'scale(1)';
        subtotalEl.textContent = '$' + Number(summary.subtotal).toFixed(2);
      }, 150);
    }

    if (taxEl) {
      taxEl.style.transition = 'transform 0.3s ease';
      taxEl.style.transform = 'scale(1.1)';
      setTimeout(() => {
        taxEl.style.transform = 'scale(1)';
        taxEl.textContent = '$' + Number(summary.tax).toFixed(2);
      }, 150);
    }

    if (totalEl) {
      totalEl.style.transition = 'transform 0.3s ease';
      totalEl.style.transform = 'scale(1.15)';
      setTimeout(() => {
        totalEl.style.transform = 'scale(1)';
        totalEl.textContent = '$' + Number(summary.total).toFixed(2);
      }, 150);
    }

    if (itemCountEl) {
      itemCountEl.textContent = summary.itemCount;
    }
  }

  // Remove forms via AJAX with smooth animations
  document.querySelectorAll('.cart-remove-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const itemId = this.querySelector('input[name="item_id"]')?.value;
      if (!itemId) return;

      const action = this.getAttribute('action');
      const row = document.querySelector('.cart-item-row[data-item-id="' + itemId + '"]');

      const proceedWithRemoval = () => {
        if (row) {
          row.style.transition = 'all 0.4s ease';
          row.style.opacity = '0.5';
          row.style.transform = 'translateX(-20px)';
        }
        removeCartItem(itemId, action).then(handleRemoveResponse.bind(null, row)).catch(handleRemoveError.bind(null, row));
      };

      // use custom modal if available
      showConfirmModal('Remove this item from your cart?', 'Remove', 'Confirm').then(confirmed => { if (confirmed) proceedWithRemoval(); });
      return;
    });
  });

  // Also support standalone remove buttons (used on checkout list)
  document.querySelectorAll('.remove-item-ajax, .remove-item-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const itemId = this.getAttribute('data-item-id') || this.dataset.itemId;
      if (!itemId) return;

      const row = document.querySelector('.cart-item-row[data-item-id="' + itemId + '"]');

      const proceed = () => {
        if (row) {
          row.style.transition = 'all 0.4s ease';
          row.style.opacity = '0.5';
          row.style.transform = 'translateX(-20px)';
        }
        removeCartItem(itemId).then(handleRemoveResponse.bind(null, row)).catch(handleRemoveError.bind(null, row));
      };

      // use custom modal confirmation
      showConfirmModal('Remove this item from your cart?', 'Remove', 'Confirm').then(confirmed => { if (confirmed) proceed(); });
      return;

      if (row) {
        row.style.transition = 'all 0.4s ease';
        row.style.opacity = '0.5';
        row.style.transform = 'translateX(-20px)';
      }

      removeCartItem(itemId).then(handleRemoveResponse.bind(null, row)).catch(handleRemoveError.bind(null, row));
    });
  });

  // centralized handlers to reuse in both flows
  function handleRemoveResponse(row, resp) {
    if (!resp || !resp.success) {
      console.error('Remove failed', resp);
      if (row) {
        row.style.opacity = '1';
        row.style.transform = 'translateX(0)';
      }
      if (window.Toast && typeof window.Toast.error === 'function') {
        window.Toast.error({ title: 'Error', message: (resp && resp.message) ? resp.message : 'Failed to remove item.' });
      }
      return;
    }

    if (row) {
      row.style.opacity = '0';
      row.style.transform = 'translateX(-100%)';
      row.style.maxHeight = row.offsetHeight + 'px';

      setTimeout(() => {
        row.style.maxHeight = '0';
        row.style.padding = '0';
        row.style.margin = '0';
        row.style.border = 'none';
      }, 400);

      setTimeout(() => {
        row.remove();
      }, 800);
    }

    if (resp.summary) updateSummary(resp.summary);

    if (window.Toast && typeof window.Toast.success === 'function') {
      window.Toast.success({ title: 'Success', message: (resp && resp.message) ? resp.message : 'Item removed from cart.' });
    }

    if (resp.summary && parseInt(resp.summary.itemCount, 10) === 0) {
      setTimeout(() => window.location.reload(), 1000);
    }
  }

  function handleRemoveError(row, err) {
    console.error(err);
    if (row) {
      row.style.opacity = '1';
      row.style.transform = 'translateX(0)';
    }
    if (window.Toast && typeof window.Toast.error === 'function') {
      window.Toast.error({ title: 'Error', message: 'Failed to remove item.' });
    }
  }

  // Add smooth scroll to top on step change
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('step')) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Form validation with better UX
  const checkoutForm = document.querySelector('form[action*="checkout.process"]');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(e) {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
      }
    });
  }

  // Add input focus effects
  document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="number"]').forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement?.classList.add('input-focused');
    });

    input.addEventListener('blur', function() {
      this.parentElement?.classList.remove('input-focused');
    });
  });

  // Autofill shipping address when selecting a saved address
  const savedSelect = document.getElementById('savedAddressSelect');
  if (savedSelect) {
    savedSelect.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      const data = opt.getAttribute('data-addr');
      if (!data) {
        // clear fields
        ['firstName','lastName','address','city','state','zip'].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.value = '';
        });
        return;
      }

      try {
        const addr = JSON.parse(data);
        const name = (addr.name || '').split(' ');
        const first = name.shift() || '';
        const last = name.join(' ') || '';

        if (document.getElementById('firstName')) document.getElementById('firstName').value = first;
        if (document.getElementById('lastName')) document.getElementById('lastName').value = last;
        if (document.getElementById('address')) document.getElementById('address').value = addr.street || '';
        if (document.getElementById('city')) document.getElementById('city').value = addr.city || '';
        if (document.getElementById('state')) document.getElementById('state').value = addr.state || '';
        if (document.getElementById('zip')) document.getElementById('zip').value = addr.postal_code || '';
      } catch (e) {
        console.error('Invalid address data', e);
      }
    });
  }

  // Add smooth transitions to all elements
  const style = document.createElement('style');
  style.textContent = `
    * {
      transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
      transition-timing-function: ease-in-out;
      transition-duration: 200ms;
    }

    input, button, select, textarea {
      transition-duration: 300ms;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .animate-spin {
      animation: spin 1s linear infinite;
    }
  `;
  document.head.appendChild(style);

  // Small in-page confirmation modal matching app style
  function showConfirmModal(message, confirmText = 'Remove', title = 'Confirm') {
    return new Promise((resolve) => {
      // create overlay
      const overlay = document.createElement('div');
      overlay.className = 'cp-confirm-overlay fixed inset-0 z-60 flex items-center justify-center p-4';
      overlay.style.background = 'rgba(15,23,42,0.6)';

      const modal = document.createElement('div');
      modal.className = 'cp-confirm-modal bg-white rounded-2xl shadow-2xl max-w-md w-full p-6';
      modal.style.boxShadow = '0 10px 30px rgba(2,6,23,0.2)';

      modal.innerHTML = `
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
          <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(180deg,#06b6d4,#0ea5a3);display:flex;align-items:center;justify-content:center;color:white;font-weight:700">!</div>
          <div>
            <div style="font-weight:700;color:#0f172a;margin-bottom:4px">${escapeHtml(title)}</div>
            <div style="color:#475569;font-size:14px">${escapeHtml(message)}</div>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:14px">
          <button class="cp-cancel-btn" style="padding:10px 16px;border-radius:12px;border:1px solid #e6edf3;background:#fff;color:#334155">Cancel</button>
          <button class="cp-confirm-btn" style="padding:10px 16px;border-radius:12px;background:linear-gradient(90deg,#0ea5a3,#06b6d4);color:#fff;font-weight:700">${escapeHtml(confirmText)}</button>
        </div>`;

      overlay.appendChild(modal);
      document.body.appendChild(overlay);

      // helpers to cleanup
      function cleanup() {
        if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
      }

      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) { cleanup(); resolve(false); }
      });

      modal.querySelector('.cp-cancel-btn').addEventListener('click', function () { cleanup(); resolve(false); });
      modal.querySelector('.cp-confirm-btn').addEventListener('click', function () { cleanup(); resolve(true); });
    });
  }

  function escapeHtml(unsafe){
    return String(unsafe)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
});
