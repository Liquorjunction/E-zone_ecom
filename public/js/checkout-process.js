document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addAddressModal');
    if(modal) document.body.appendChild(modal);
});

document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById("checkout-root");
    if (!root) return;

    const updateUrl = root.dataset.updateUrl;
    const removeUrl = root.dataset.removeUrl;
    const csrf =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

    let debounceTimers = {};

    function formatMoney(n) {
        const symbol = window.env?.CURRENCY_SYMBOL || "$";
        const num = typeof n === 'string' ? parseFloat(n.replace(/[,\s]/g, '')) : Number(n);
        const formatted = num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return symbol + ' ' + formatted;
    }

    function showStockLimitMessage(max) {
        if (!max) return;
        let message = '';
        if (max === 1) {
            message = trans('Only 1 item available in stock.');
        } else {
            message = trans('Only') + ' ' + max + ' ' + trans('items available in stock.');
        }

        if (window.Toast && typeof window.Toast.info === 'function') {
            window.Toast.info({ title: trans('Stock limit'), message: message });
            return;
        }

        let banner = document.getElementById('stockLimitBanner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'stockLimitBanner';
            banner.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4 bg-amber-500 text-white px-4 py-3 rounded-xl shadow-xl';
            document.body.appendChild(banner);
        }
        banner.textContent = message;
        banner.style.opacity = '1';
        banner.style.transform = 'translate(-50%, 0)';

        clearTimeout(banner._hideTimer);
        banner._hideTimer = setTimeout(() => {
            banner.style.opacity = '0';
            banner.style.transform = 'translate(-50%, -100%)';
            setTimeout(() => banner.remove(), 300);
        }, 2500);
    }

    function getMaxStockForItem(id) {
        const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
        let max = null;

        if (input) {
            const raw = input.getAttribute('max') || input.dataset.maxStock || input.dataset.max;
            const parsed = parseInt(raw || '', 10);
            if (!Number.isNaN(parsed) && parsed > 0) max = parsed;
        }

        if (!max) {
            const summaryRow = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"]`);
            const raw = summaryRow?.dataset?.maxStock;
            const parsed = parseInt(raw || '', 10);
            if (!Number.isNaN(parsed) && parsed > 0) max = parsed;
        }

        return max;
    }

    function clampQuantity(val, max) {
        let v = Math.max(1, Number(val || 1));
        if (max && v > max) v = max;
        return v;
    }

    root.addEventListener("click", function (e) {
        const target = e.target;

        const removeBtn = target.closest(".remove-item-btn, .remove-item-ajax");
        if (removeBtn) {
            e.preventDefault();
            const id = removeBtn.dataset.itemId;
            if (id) removeItem(id);
            return;
        }

        const increaseBtn = target.closest(".qty-increase");
        if (increaseBtn) {
            e.preventDefault();
            const id = increaseBtn.dataset.itemId;
            const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
            if (input) {
                const max = getMaxStockForItem(id);
                let newVal = Number(input.value || 0) + 1;
                if (max) newVal = Math.min(newVal, max);
                input.value = newVal;
                if (max && newVal === max) showStockLimitMessage(max);
                debounceUpdate(id, newVal);
            }
            return;
        }

        const decreaseBtn = target.closest(".qty-decrease");
        if (decreaseBtn) {
            e.preventDefault();
            const id = decreaseBtn.dataset.itemId;
            const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
            if (input) {
                let newVal = Math.max(1, Number(input.value || 1) - 1);
                input.value = newVal;
                debounceUpdate(id, newVal);
            }
            return;
        }

        const summaryIncreaseBtn = target.closest(".summary-qty-increase");
        if (summaryIncreaseBtn) {
            e.preventDefault();
            if (root.dataset.step === '3') return;

            const id = summaryIncreaseBtn.dataset.itemId;
            const quantityDisplay = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`);
            const mainInput = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);

            if (mainInput) {
                const max = getMaxStockForItem(id);
                let newVal = Number(mainInput.value || 0) + 1;
                if (max) newVal = Math.min(newVal, max);
                mainInput.value = newVal;
                if (quantityDisplay) quantityDisplay.textContent = newVal;
                if (max && newVal === max) showStockLimitMessage(max);
                debounceUpdate(id, newVal);
            } else if (quantityDisplay) {
                const max = getMaxStockForItem(id);
                let newVal = Number(quantityDisplay.textContent || 0) + 1;
                if (max) newVal = Math.min(newVal, max);
                quantityDisplay.textContent = newVal;
                if (max && newVal === max) showStockLimitMessage(max);
                debounceUpdate(id, newVal);
            }
            return;
        }

        const summaryDecreaseBtn = target.closest(".summary-qty-decrease");
        if (summaryDecreaseBtn) {
            e.preventDefault();
            if (root.dataset.step === '3') return;

            const id = summaryDecreaseBtn.dataset.itemId;
            const quantityDisplay = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`);
            const mainInput = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);

            if (mainInput) {
                let newVal = Math.max(1, Number(mainInput.value || 1) - 1);
                mainInput.value = newVal;
                if (quantityDisplay) quantityDisplay.textContent = newVal;
                debounceUpdate(id, newVal);
            } else if (quantityDisplay) {
                let newVal = Math.max(1, Number(quantityDisplay.textContent || 1) - 1);
                quantityDisplay.textContent = newVal;
                debounceUpdate(id, newVal);
            }
            return;
        }

        const summaryRemoveBtn = target.closest(".summary-remove-item-btn");
        if (summaryRemoveBtn) {
            e.preventDefault();
            const id = summaryRemoveBtn.dataset.itemId;
            if (id) removeItem(id);
            return;
        }
    });

    root.addEventListener("input", function (e) {
        if (e.target.classList.contains("cart-quantity-input")) {
            const id = e.target.dataset.itemId;
            const max = getMaxStockForItem(id);
            const val = clampQuantity(e.target.value, max);
            e.target.value = val;
            if (max && val === max) showStockLimitMessage(max);
            debounceUpdate(id, val);
        }
    });

    const addressSelect = document.getElementById("savedAddressSelect");
    if (addressSelect) {
        addressSelect.addEventListener("change", function () {
            const opt = this.options[this.selectedIndex];
            const fieldIds = ["firstName", "lastName", "address", "city", "state", "zip", "phone", "email"];

            if (!opt || !opt.dataset || !opt.dataset.addr) {
                fieldIds.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = "";
                });
                return;
            }

            try {
                const addr = JSON.parse(opt.dataset.addr);
                const mapping = {
                    firstName: addr.first_name,
                    lastName: addr.last_name,
                    address: addr.street || addr.address,
                    city: addr.city,
                    state: addr.state || addr.region,
                    zip: addr.postal_code || addr.zip,
                    phone: addr.phone,
                    email: addr.email,
                };

                for (const [id, val] of Object.entries(mapping)) {
                    const el = document.getElementById(id);
                    if (el && val) {
                        el.value = val;
                        el.style.transition = "background-color 0.3s ease";
                        el.style.backgroundColor = "rgba(34, 197, 94, 0.1)";
                        setTimeout(() => (el.style.backgroundColor = ""), 500);
                    }
                }
            } catch (e) {
                console.error("Error parsing address data", e);
            }
        });
    }

    function updateCardDetailsVisibility() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        const cardWrapper = document.getElementById("card-details-wrapper") || document.getElementById("cardDetails");

        if (cardWrapper) {
            cardWrapper.style.display = selected && selected.value === "card" ? "block" : "none";
            const inputs = cardWrapper.querySelectorAll("input, select");
            inputs.forEach((input) => {
                if (selected && selected.value === "card") {
                    input.setAttribute("required", "required");
                } else {
                    input.removeAttribute("required");
                }
            });
        }
    }

    document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener("change", updateCardDetailsVisibility);
    });
    updateCardDetailsVisibility();

    function debounceUpdate(id, quantity) {
        if (debounceTimers[id]) clearTimeout(debounceTimers[id]);
        debounceTimers[id] = setTimeout(() => { updateItem(id, quantity); }, 500);
    }

    function getCurrentShippingMethod() {
        const shippingMethodEl = document.querySelector('input[name="shipping_method"]:checked');
        return shippingMethodEl ? shippingMethodEl.value : 'standard';
    }

    async function refreshTotalsForShipping() {
        const firstCartInput = root.querySelector('.cart-quantity-input[data-item-id]');
        if (!firstCartInput) {
            await requestSummaryRefresh();
            return;
        }
        const itemId = firstCartInput.dataset.itemId;
        const quantity = Number(firstCartInput.value || 1);
        if (!itemId) return;
        await updateItem(itemId, quantity);
    }

    async function requestSummaryRefresh() {
        if (!updateUrl) return;
        try {
            const resp = await fetch(updateUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf, Accept: "application/json" },
                body: JSON.stringify({ shipping_method: getCurrentShippingMethod() }),
            });
            const data = await resp.json();
            if (resp.ok && data?.success && data?.summary) updateSummary(data.summary);
        } catch (e) {
            console.error("Failed to refresh summary for shipping", e);
        }
    }

    document.querySelectorAll('input[name="shipping_method"]').forEach((radio) => {
        radio.addEventListener("change", refreshTotalsForShipping);
    });

    async function updateItem(id, quantity) {
        if (!updateUrl) return;
        const shippingMethod = getCurrentShippingMethod();

        try {
            const resp = await fetch(updateUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf, Accept: "application/json" },
                body: JSON.stringify({ id, item_id: id, quantity, shipping_method: shippingMethod }),
            });

            let data = null;
            try { data = await resp.json(); } catch (e) { data = null; }

            if (!resp.ok) {
                const maxStock = data?.item?.max_stock || data?.max_stock;
                if (maxStock) {
                    const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
                    if (input) input.value = Math.min(Number(input.value || 1), maxStock);
                    const summaryQtyDisplay = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`);
                    if (summaryQtyDisplay) summaryQtyDisplay.textContent = Math.min(Number(summaryQtyDisplay.textContent || 1), maxStock);
                    showStockLimitMessage(maxStock);
                }
                throw new Error(data?.message || ("Server returned " + resp.status));
            }

            if (data.success) {
                if (data.item?.max_stock) {
                    const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
                    if (input) { input.setAttribute('max', data.item.max_stock); input.dataset.maxStock = data.item.max_stock; }
                    const summaryRow = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"]`);
                    if (summaryRow) summaryRow.dataset.maxStock = data.item.max_stock;
                }

                const cartRow = document.querySelector(`.cart-item-row[data-item-id="${id}"]`);
                const lineTotalEl = document.querySelector(`#item-line-total-${id}`);
                if (lineTotalEl && data.item?.discount) {
                    if (data.item.discount.has_discount) {
                        const discountedLineTotal = data.item.discount.line_total_after_discount ?? data.item.line_total ?? ((Number(data.item.final_price ?? data.item.unit_price ?? 0)) * Number(data.item.quantity ?? quantity));
                        lineTotalEl.textContent = formatMoney(discountedLineTotal);
                    } else {
                        lineTotalEl.textContent = formatMoney(data.item.line_total);
                    }
                } else if (lineTotalEl && data.item?.line_total) {
                    lineTotalEl.textContent = formatMoney(data.item.line_total);
                }

                if (cartRow && data.item?.discount) {
                    const discountBadge = cartRow.querySelector('.inline-flex.items-center.text-xs.font-semibold.text-green-700');
                    if (data.item.discount.has_discount && data.item.discount.discount_amount > 0) {
                        if (discountBadge) {
                            const discountText = discountBadge.querySelector('svg').nextSibling;
                            if (discountText) discountText.textContent = ` Save ${ window.env?.CURRENCY_SYMBOL || '$' } ${ (data.item.discount.discount_amount * data.item.quantity).toFixed(2) }`;
                        } else {
                            const priceContainer = cartRow.querySelector('.text-right');
                            if (priceContainer) {
                                const newBadge = document.createElement('div');
                                newBadge.className = 'inline-flex items-center text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-full mb-1';
                                newBadge.innerHTML = `<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>Save ${ window.env?.CURRENCY_SYMBOL || '$' } ${ (data.item.discount.discount_amount * data.item.quantity).toFixed(2) }`;
                                priceContainer.insertBefore(newBadge, priceContainer.firstChild);
                            }
                        }
                    } else if (discountBadge) {
                        discountBadge.remove();
                    }
                }

                const summaryQtyDisplay = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`);
                const cartInput = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
                if (summaryQtyDisplay && cartInput) summaryQtyDisplay.textContent = cartInput.value;

                const summaryItemPrice = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"] .summary-item-price`);
                if (summaryItemPrice && data.item?.discount) {
                    if (data.item.discount.has_discount) {
                        const discountedLineTotal = data.item.discount.line_total_after_discount ?? data.item.line_total ?? ((Number(data.item.final_price ?? data.item.unit_price ?? 0)) * Number(data.item.quantity ?? quantity));
                        summaryItemPrice.textContent = formatMoney(discountedLineTotal);
                    } else {
                        summaryItemPrice.textContent = formatMoney(data.item.line_total);
                    }
                }

                updateSummary(data.summary);

                if (data.summary && typeof data.summary.itemCount !== 'undefined') {
                    window.dispatchEvent(new CustomEvent('cart:changed', { detail: { summary: data.summary } }));
                }
            } else {
                console.error("Update failed:", data.message);
                if (typeof showUniversalPopup === 'function') {
                    showUniversalPopup(data.message || "Could not update item.", 'error', 3000);
                } else {
                    alert(data.message || "Could not update item.");
                }
            }
        } catch (e) {
            console.error("AJAX Error:", e);
            if (e?.message) {
                if (typeof showUniversalPopup === 'function') {
                    showUniversalPopup(e.message, 'error', 3000);
                } else {
                    alert(e.message);
                }
            }
        }
    }

    async function removeItem(id) {
        if (!removeUrl) return submitFallback(id);

        try {
            const resp = await fetch(removeUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf, Accept: "application/json" },
                body: JSON.stringify({ id, item_id: id, shipping_method: getCurrentShippingMethod() }),
            });

            if (!resp.ok) throw new Error("Server returned " + resp.status);
            const data = await resp.json();

            if (data.success) {
                const row = document.querySelector(`.cart-item-row[data-item-id="${id}"]`);
                if (row) row.remove();

                const summaryRow = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"]`);
                if (summaryRow) summaryRow.remove();

                if (document.querySelectorAll(".cart-item-row").length === 0) {
                    location.reload();
                    return;
                }

                updateSummary(data.summary);

                if (data.summary && typeof data.summary.itemCount !== 'undefined') {
                    window.dispatchEvent(new CustomEvent('cart:changed', { detail: { summary: data.summary } }));
                }

                if (window.Toast) window.Toast.success({ title: trans('Removed'), message: trans('Item removed from cart') });
            } else {
                submitFallback(id);
            }
        } catch (e) {
            console.error("Remove failed, trying fallback", e);
            submitFallback(id);
        }
    }

    function submitFallback(id) {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = removeUrl;
        form.style.display = "none";

        const token = document.createElement("input");
        token.type = "hidden"; token.name = "_token"; token.value = csrf;
        form.appendChild(token);

        const idInput = document.createElement("input");
        idInput.type = "hidden"; idInput.name = "id"; idInput.value = id;
        form.appendChild(idInput);

        document.body.appendChild(form);
        form.submit();
    }

    function updateSummary(summary) {
        if (!summary) return;

        const map = {
            subtotal:                     [".order-subtotal",                  "order-subtotal", "order-subtotal-step2"],
            item_discount:                [".order-discount",                  "order-discount", "order-discount-step2"],
            subtotal_after_item_discount: [".order-subtotal-after-discount",   "order-subtotal-after-discount", "order-subtotal-after-discount-step2"],
            coupon_discount:              [".order-coupon-discount",            "order-coupon-discount", "order-coupon-discount-step2"],
            tax:                          [".order-tax",                        "order-tax", "order-tax-step2"],
            total:                        [".order-total",                      "order-total", "order-total-step2", "summary-total"],
            itemCount:                    [".order-item-count",                 "order-item-count", "order-item-count-step2"],
            shipping:                     [".order-shipping",                   "order-shipping", "order-shipping-step2"],
        };

        for (const [key, selectors] of Object.entries(map)) {
            if (summary[key] === undefined) continue;

            selectors.forEach((sel) => {
                const els = sel.startsWith(".") ? document.querySelectorAll(sel) : [document.getElementById(sel)];
                els.forEach((el) => {
                    if (!el) return;
                    if (key === "shipping") {
                        el.textContent = summary[key] > 0 ? formatMoney(summary[key]) : "FREE";
                    } else if (key === "itemCount") {
                        el.textContent = summary[key];
                    } else if (key === "item_discount" || key === "coupon_discount") {
                        if (summary[key] > 0) {
                            el.textContent = "-" + formatMoney(summary[key]);
                            const row = el.closest('[id$="-discount-row"], .discount-row');
                            if (row) row.style.display = '';
                        } else {
                            const row = el.closest('[id$="-discount-row"], .discount-row');
                            if (row) row.style.display = 'none';
                        }
                    } else {
                        el.textContent = formatMoney(summary[key]);
                    }
                });
            });
        }

        if (typeof summary.itemCount !== 'undefined') {
            const itemTotalCount = document.getElementById('item-total-count');
            if (itemTotalCount) {
                itemTotalCount.textContent = summary.itemCount + ' ' + (summary.itemCount === 1 ? 'Quantity' : 'Quantity');
            }
        }

        updateOrderSummaryItems(summary);
    }

    function updateOrderSummaryItems(summary) {
        if (!summary || !summary.items) return;

        Object.entries(summary.items).forEach(([itemId, itemData]) => {
            const summaryRow = document.querySelector(`.summary-item-row[data-summary-item-id="${itemId}"]`);
            if (!summaryRow) return;

            const qtyDisplay = summaryRow.querySelector(".summary-quantity");
            if (qtyDisplay) qtyDisplay.textContent = itemData.quantity;

            const priceElement = summaryRow.querySelector(".summary-item-price");
            if (priceElement && (itemData.final_price !== undefined || itemData.unit_price !== undefined)) {
                const unitPrice = itemData.final_price ?? itemData.unit_price ?? 0;
                const lineTotal = unitPrice * itemData.quantity;
                priceElement.textContent = formatMoney(lineTotal);
            }

            const lineThroughEl = summaryRow.querySelector(".text-xs.text-gray-400.line-through");
            if (lineThroughEl) {
                if (itemData.unit_price && itemData.final_price && itemData.unit_price > itemData.final_price) {
                    const originalLineTotal = itemData.unit_price * itemData.quantity;
                    lineThroughEl.innerHTML = `${window.env?.CURRENCY_SYMBOL || '$'} ${Number(originalLineTotal).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}<br />`;
                    lineThroughEl.style.display = '';
                } else {
                    lineThroughEl.style.display = 'none';
                }
            }
        });
    }
});

// ═══════════════════════════════════════════════════════════════
//  AJAX CHECKOUT SUBMISSION  (Step 2 → Place Secure Order)
// ═══════════════════════════════════════════════════════════════
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const checkoutForm = document.querySelector('form[action*="checkout"]');
        if (!checkoutForm) return;

        checkoutForm.addEventListener('submit', async function (e) {
            const submitter = e.submitter;
            // Only intercept the "Place Secure Order" button (value="3")
            if (!submitter || submitter.value !== '3') return;

            e.preventDefault();

            // Clear previous errors
            clearAllFieldErrors();

            // Client-side validation first
            const isValid = runCheckoutValidation(checkoutForm);
            if (!isValid) return;

            // Show loading spinner on button
            const originalBtnHTML = submitter.innerHTML;
            submitter.disabled = true;
            submitter.innerHTML = `
                <span class="inline-flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Placing Order...
                </span>`;

            const formData = new FormData(checkoutForm);
            formData.set('step', '3');

            try {
                const response = await fetch(checkoutForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const contentType = response.headers.get('content-type') || '';

                // Non-JSON = server did a full redirect (shouldn't happen with expectsJson, but just in case)
                if (!contentType.includes('application/json')) {
                    if (typeof window.goToStep === 'function') {
                        window.goToStep(3, { success: true });
                    } else {
                        window.location.href = response.url;
                    }
                    return;
                }

                const data = await response.json();

                // Success — go to step 3 without page reload
                if (response.ok && (data.success || data.redirect)) {
                    if (typeof window.goToStep === 'function') {
                        window.goToStep(3, {
                            success: true,
                            order_id: data.order_id,
                            order_number: data.order_number,
                            order_detail_url: data.order_detail_url,
                        });
                    } else {
                        // Fallback: follow redirect if goToStep not loaded
                        window.location.href = data.redirect || data.url || (window.location.pathname + '?step=3');
                    }
                    return;
                }

                // Laravel 422 validation errors — show inline, stay on step 2
                if (response.status === 422 && data.errors) {
                    handleServerValidationErrors(data.errors);
                    submitter.disabled = false;
                    submitter.innerHTML = originalBtnHTML;
                    return;
                }

                // Other server error — go to step 3 error state without reload
                if (typeof window.goToStep === 'function') {
                    window.goToStep(3, { success: false, message: data.message || trans('Something went wrong. Please try again.') });
                } else {
                    showCheckoutErrorBanner(data.message || trans('Something went wrong. Please try again.'));
                }
                submitter.disabled = false;
                submitter.innerHTML = originalBtnHTML;

            } catch (err) {
                console.error('Checkout AJAX error:', err);
                if (typeof window.goToStep === 'function') {
                    window.goToStep(3, { success: false, message: trans('A network error occurred. Please check your connection and try again.') });
                } else {
                    showCheckoutErrorBanner(trans('A network error occurred. Please check your connection and try again.'));
                }
                submitter.disabled = false;
                submitter.innerHTML = originalBtnHTML;
            }
        });
    });

    // ── Helpers ────────────────────────────────────────────────

    function showCheckoutErrorBanner(message) {
        let banner = document.getElementById('checkout-error-banner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'checkout-error-banner';
            banner.className = 'mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-center shadow-sm';
            const checkoutRoot = document.getElementById('checkout-root');
            if (checkoutRoot) checkoutRoot.insertBefore(banner, checkoutRoot.firstChild);
            else document.body.prepend(banner);
        }
        banner.innerHTML = `
            <div class="shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-bold text-red-800">Order Failed</p>
                <p class="text-sm text-red-700">${message}</p>
            </div>`;
        banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Maps Laravel field names → DOM element IDs
    const serverFieldMap = {
        saved_address_id:         'shippingAddressSelect',
        saved_billing_address_id: 'billingAddressSelect',
        billing_address_id:       'billingAddressSelect',
        email:          'email',
        phone:          'phone',
        firstName:      'firstName',
        lastName:       'lastName',
        address:        'address',
        city:           'city',
        zip:            'zip',
        country:        'country',
        payment_method: 'payment_method',
        cardNumber:     'cardNumber',
        expiry:         'expiry',
        cvv:            'cvv',
    };

    function handleServerValidationErrors(errors) {
        let firstField = null;

        Object.entries(errors).forEach(([key, messages]) => {
            const message = Array.isArray(messages) ? messages[0] : messages;
            const mappedId = serverFieldMap[key] || key;

            const field = document.getElementById(mappedId)
                || document.querySelector(`[name="${mappedId}"]`)
                || document.querySelector(`[name="${key}"]`);

            if (!field) return;
            setFieldError(field, message);
            if (!firstField) firstField = field;
        });

        if (firstField) {
            firstField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstField.focus({ preventScroll: true });
        }
    }

    function setFieldError(field, message) {
        field.classList.add('border-red-500', 'bg-red-50');
        field.classList.remove('border-gray-200', 'border-green-400');

        const wrapper = field.closest('div');
        let msgEl = wrapper ? wrapper.querySelector('.error-message') : null;
        if (!msgEl && wrapper) {
            msgEl = document.createElement('span');
            msgEl.className = 'error-message text-red-500 text-sm mt-1 font-medium block';
            wrapper.appendChild(msgEl);
        }
        if (msgEl) {
            msgEl.textContent = message;
            msgEl.classList.remove('hidden');
            msgEl.style.display = 'block';
        }
    }

    function clearAllFieldErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
            el.style.display = '';
        });
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'bg-red-50');
        });
        const banner = document.getElementById('checkout-error-banner');
        if (banner) banner.remove();
    }

    /**
     * Client-side validation before AJAX submit.
     * Checks all visible required fields and returns true if all pass.
     */
    function runCheckoutValidation(form) {
        let isValid = true;
        let firstInvalid = null;

        function fail(field, msg) {
            setFieldError(field, msg);
            if (!firstInvalid) firstInvalid = field;
            isValid = false;
        }

        // Email
        const email = form.querySelector('[name="email"]');
        if (email && email.offsetParent !== null) {
            if (!email.value.trim()) fail(email, 'Email is required');
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) fail(email, 'Please enter a valid email address');
        }

        // Phone
        const phone = form.querySelector('[name="phone"]');
        if (phone && phone.offsetParent !== null) {
            if (!phone.value.trim()) fail(phone, 'Phone number is required');
            else if (!/^[\d\s\+\-\(\)]{7,}$/.test(phone.value.trim())) fail(phone, 'Please enter a valid phone number');
        }

        // Shipping address
        const shippingSelect = form.querySelector('[name="saved_address_id"]');
        const manualFields = document.getElementById('manualAddressFields');
        const isManualVisible = manualFields && !manualFields.classList.contains('hidden');

        if (shippingSelect && shippingSelect.offsetParent !== null && !shippingSelect.value && !isManualVisible) {
            fail(shippingSelect, 'Please select a shipping address');
        }

        if (isManualVisible) {
            [
                { name: 'firstName', label: 'First name is required' },
                { name: 'lastName',  label: 'Last name is required' },
                { name: 'address',   label: 'Street address is required' },
                { name: 'city',      label: 'City is required' },
                { name: 'country',   label: 'Country is required' },
            ].forEach(({ name, label }) => {
                const f = form.querySelector(`[name="${name}"]`);
                if (f && !f.value.trim()) fail(f, label);
            });
        }

        // Card details (only when card payment is selected and section is visible)
        const paymentMethod = form.querySelector('input[name="payment_method"]:checked');
        if (paymentMethod && paymentMethod.value === 'card') {
            const cardDetails = document.getElementById('cardDetails');
            if (cardDetails && cardDetails.style.display !== 'none') {
                const cardNumber = form.querySelector('[name="cardNumber"]');
                const expiry    = form.querySelector('[name="expiry"]');
                const cvv       = form.querySelector('[name="cvv"]');

                if (cardNumber && !cardNumber.value.replace(/\s/g, '').match(/^\d{13,19}$/)) fail(cardNumber, 'Please enter a valid card number');
                if (expiry && !/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry.value.trim())) fail(expiry, 'Format should be MM/YY');
                if (cvv && !/^\d{3,4}$/.test(cvv.value.trim())) fail(cvv, 'CVV must be 3 or 4 digits');
            }
        }

        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus({ preventScroll: true });
        }

        return isValid;
    }
})();

// ═══════════════════════════════════════════════════════════════
//  Modal and Address Handling
// ═══════════════════════════════════════════════════════════════
(function() {
    const modal = document.getElementById('addAddressModal');
    const openBillingBtn = document.getElementById('addBillingAddressBtn');
    const openShippingBtn = document.getElementById('addShippingAddressBtn');
    const openBillingBtnEmpty = document.getElementById('addBillingAddressBtnEmpty');
    const openShippingBtnEmpty = document.getElementById('addShippingAddressBtnEmpty');
    const closeBtn = document.getElementById('closeAddressModal');
    const cancelBtn = document.getElementById('cancelAddressModal');
    const form = document.getElementById('addAddressForm');
    const typeField = document.getElementById('modal_address_type');
    const cityField = document.getElementById('modal_city_id');
    const countryField = document.getElementById('modal_country');
    const submitBtn = document.getElementById('submitAddressBtn');

    if (!modal) return;

    const openModalWithType = function(type) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (form) form.reset();
        if (typeField) typeField.value = type;
        if (countryField && !countryField.value) countryField.value = 'Morocco';

        const content = document.getElementById('addAddressContent');
        if (content) {
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    };

    if (openBillingBtn)      openBillingBtn.addEventListener('click',      function(e) { e.preventDefault(); openModalWithType('billing'); });
    if (openShippingBtn)     openShippingBtn.addEventListener('click',     function(e) { e.preventDefault(); openModalWithType('shipping'); });
    if (openBillingBtnEmpty) openBillingBtnEmpty.addEventListener('click', function(e) { e.preventDefault(); openModalWithType('billing'); });
    if (openShippingBtnEmpty) openShippingBtnEmpty.addEventListener('click', function(e) { e.preventDefault(); openModalWithType('shipping'); });

    const closeModal = function() {
        const content = document.getElementById('addAddressContent');
        if (content) {
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            if (form) form.reset();
        }, 200);
    };

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const fieldsToValidate = form.querySelectorAll('[name="type"], [name="name"], [name="street"], [name="city_id"], [name="country"]');
            let isValid = true;

            fieldsToValidate.forEach(field => {
                if (!validateField(field)) isValid = false;
            });

            const postalField = form.querySelector('[name="postal_code"]');
            if (postalField && postalField.value.trim() && !validateField(postalField)) isValid = false;

            const phoneField = form.querySelector('[name="phone"]');
            if (phoneField && phoneField.value.trim() && !validateField(phoneField)) isValid = false;

            if (!isValid) {
                const firstInvalid = form.querySelector('.error-state');
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...</span>';
            }

            const formData = new FormData(form);
            const data = {
                _token: formData.get('_token'),
                type: formData.get('type'),
                address_label: formData.get('address_label'),
                name: formData.get('name'),
                street: formData.get('street'),
                city_id: formData.get('city_id'),
                postal_code: formData.get('postal_code'),
                country: formData.get('country'),
                phone: formData.get('phone'),
                is_default: formData.get('is_default') ? 1 : 0
            };

            fetch('/client/addresses', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': data._token, 'Accept': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success || result.data) {
                    closeModal();
                    alert('Address saved successfully! The page will reload to show your new address.');
                    window.location.reload();
                } else {
                    alert('Error saving address: ' + (result.message || 'Unknown error'));
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span class="inline-flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Save Address</span>';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showUniversalPopup === 'function') {
                    showUniversalPopup('Error saving address. Please try again.', 'error', 3000);
                } else {
                    alert('Error saving address. Please try again.');
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="inline-flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Save Address</span>';
                }
            });
        });
    }

    if (cityField && countryField) {
        cityField.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const country = option ? option.getAttribute('data-country') : '';
            if (country) countryField.value = country;
        });
    }
})();

// ═══════════════════════════════════════════════════════════════
//  Change Address Button + Payment Method Styling
// ═══════════════════════════════════════════════════════════════
(function() {
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.changeAddressBtn');
        if (!button) return;

        e.preventDefault();
        const card = button.closest('#shippingAddressPreview, #billingAddressPreview');
        if (!card) return;

        card.classList.add('hidden');
        const listId = card.id === 'shippingAddressPreview' ? 'shippingAddressList' : 'billingAddressList';
        const list = document.getElementById(listId);
        if (list) list.classList.remove('hidden');
    });

    const paymentMethodLabels = document.querySelectorAll('div.space-y-4.mb-6 > label');

    function updatePaymentMethodStyles() {
        paymentMethodLabels.forEach(label => {
            const radio = label.querySelector('input[name="payment_method"]');
            if (!radio) return;
            if (radio.checked) {
                label.classList.add('border-primary', 'bg-primary/5');
                label.classList.remove('border-gray-200');
            } else {
                label.classList.remove('border-primary', 'bg-primary/5');
                label.classList.add('border-gray-200');
            }
        });
    }

    paymentMethodLabels.forEach(label => {
        const radio = label.querySelector('input[name="payment_method"]');
        if (radio) radio.addEventListener('change', updatePaymentMethodStyles);
    });

    updatePaymentMethodStyles();
})();

// ═══════════════════════════════════════════════════════════════
//  Server Error Banner
// ═══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
    checkAndShowValidationErrors();
});

function checkAndShowValidationErrors() {
    // Populated by Blade template if needed
}

function showServerErrorBanner(title, errors) {
    const banner = document.createElement('div');
    banner.id = 'serverErrorBanner';
    banner.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-2xl w-full mx-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl animate-slide-down';

    let errorList = '';
    if (errors && errors.length > 1) {
        errorList = '<ul class="mt-2 space-y-1 text-sm">';
        errors.forEach(error => {
            errorList += `<li class="flex items-start gap-2"><span>•</span><span>${error}</span></li>`;
        });
        errorList += '</ul>';
    }

    banner.innerHTML = `
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div class="flex-1">
                <p class="font-semibold">${title}</p>
                ${errorList}
            </div>
            <button onclick="this.closest('#serverErrorBanner').remove()" class="text-white hover:text-red-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>`;

    document.body.appendChild(banner);

    setTimeout(() => {
        if (banner.parentElement) {
            banner.style.opacity = '0';
            banner.style.transform = 'translate(-50%, -100%)';
            setTimeout(() => banner.remove(), 300);
        }
    }, 10000);
}

// ═══════════════════════════════════════════════════════════════
//  GLOBAL Validation Rules + Functions
//  Must be global so the address modal IIFE above can call validateField()
// ═══════════════════════════════════════════════════════════════
(function() {
    // Ensure trans is available before defining validationRules
    if (typeof window.trans !== 'function') {
        window.trans = function(key) {
            if (window.translations && window.translations[key]) {
                return window.translations[key];
            }
            return key;
        };
    }
    window.validationRules = {
        email:    { required: trans('Email is required'),               pattern: { value: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: trans('Please enter a valid email address') } },
        phone:    { required: trans('Phone number is required'),        pattern: { value: /^[\d\s\+\-\(\)]{7,}$/, message: trans('Please enter a valid phone number') } },
        cardNumber: { required: trans('Card number is required'),       pattern: { value: /^\d{13,19}$/, message: trans('Card number must be between 13 and 19 digits') } },
        expiry:   { required: trans('Expiry date is required'),         pattern: { value: /^(0[1-9]|1[0-2])\/\d{2}$/, message: trans('Format should be MM/YY') } },
        cvv:      { required: trans('CVV is required'),                 pattern: { value: /^\d{3,4}$/, message: trans('CVV must be 3 or 4 digits') } },
        name:     { required: trans('Full name is required'),           pattern: { value: /^[a-zA-Z\s]+$/, message: trans('Full name should only contain letters') } },
        street:   { required: trans('Street address is required'),      minLength: { value: 5, message: trans('Street address must be at least 5 characters') } },
        city_id:     { required: trans('Please select a city') },
        postal_code: { minLength: { value: 3, message: trans('Postal code must be at least 3 characters') } },
        country:     { required: trans('Country is required') },
        type:        { required: trans('Please select an address type') },
    };
})();

function validateField(field) {
    const fieldName = field.name || field.id;
    const rules = validationRules[fieldName];
    if (!rules) return true;

    if (rules.required && !field.value.trim()) {
        showFieldError(field, rules.required);
        return false;
    }
    if (rules.minLength && field.value.trim() && field.value.trim().length < rules.minLength.value) {
        showFieldError(field, rules.minLength.message);
        return false;
    }
    if (rules.pattern && field.value.trim()) {
        if (!rules.pattern.value.test(field.value.replace(/\s/g, ''))) {
            showFieldError(field, rules.pattern.message);
            return false;
        }
    }

    clearFieldError(field);
    return true;
}

function showFieldError(field, message) {
    const errorMsg = field.parentElement ? field.parentElement.querySelector('.error-message') : null;
    if (errorMsg) {
        errorMsg.textContent = message;
        errorMsg.classList.remove('hidden');
        errorMsg.style.display = 'block';
    }
    field.classList.add('error-state', 'border-red-500', 'bg-red-50');
    field.classList.remove('success-state', 'border-gray-200');
}

function clearFieldError(field) {
    const errorMsg = field.parentElement ? field.parentElement.querySelector('.error-message') : null;
    if (errorMsg) {
        errorMsg.textContent = '';
        errorMsg.classList.add('hidden');
        errorMsg.style.display = '';
    }
    field.classList.remove('error-state', 'border-red-500', 'bg-red-50');
    field.classList.add('success-state');
}

// ═══════════════════════════════════════════════════════════════
//  Real-time Validation + Card Input Formatting
// ═══════════════════════════════════════════════════════════════
(function() {
    document.addEventListener('input', function(e) {
        const field = e.target;
        if (validationRules[field.name || field.id]) validateField(field);
    });

    document.addEventListener('blur', function(e) {
        const field = e.target;
        if (validationRules[field.name || field.id]) validateField(field);
    }, true);

    // Format card number with spaces every 4 digits
    const cardNumberField = document.getElementById('cardNumber');
    if (cardNumberField) {
        cardNumberField.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            e.target.value = value.match(/.{1,4}/g)?.join(' ') || value;
        });
    }

    // Format expiry MM/YY automatically
    const expiryField = document.getElementById('expiry');
    if (expiryField) {
        expiryField.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) value = value.slice(0, 2) + '/' + value.slice(2, 4);
            e.target.value = value;
        });
    }
})();
