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
        const message = max === 1
            ? 'Only 1 item available in stock.'
            : `Only ${max} items available in stock.`;

        if (window.Toast && typeof window.Toast.info === 'function') {
            window.Toast.info({
                title: 'Stock limit',
                message: message,
            });
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
        const input = root.querySelector(
            `.cart-quantity-input[data-item-id="${id}"]`
        );
        let max = null;

        if (input) {
            const raw = input.getAttribute('max') || input.dataset.maxStock || input.dataset.max;
            const parsed = parseInt(raw || '', 10);
            if (!Number.isNaN(parsed) && parsed > 0) {
                max = parsed;
            }
        }

        if (!max) {
            const summaryRow = document.querySelector(
                `.summary-item-row[data-summary-item-id="${id}"]`
            );
            const raw = summaryRow?.dataset?.maxStock;
            const parsed = parseInt(raw || '', 10);
            if (!Number.isNaN(parsed) && parsed > 0) {
                max = parsed;
            }
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
            const input = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );
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
            const input = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );
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

            const isStep3 = document.querySelector('.text-xl.font-extrabold.text-gray-900.mb-4')?.textContent.includes('Order Successfully Placed');
            if (isStep3) {
                return;
            }

            const id = summaryIncreaseBtn.dataset.itemId;
            const quantityDisplay = document.querySelector(
                `.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`
            );
            const mainInput = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );

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
                let currentQty = Number(quantityDisplay.textContent || 0);
                let newVal = currentQty + 1;
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

            const isStep3 = document.querySelector('.text-xl.font-extrabold.text-gray-900.mb-4')?.textContent.includes('Order Successfully Placed');
            if (isStep3) {
                return;
            }

            const id = summaryDecreaseBtn.dataset.itemId;
            const quantityDisplay = document.querySelector(
                `.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`
            );
            const mainInput = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );

            if (mainInput) {
                let newVal = Math.max(1, Number(mainInput.value || 1) - 1);
                mainInput.value = newVal;
                if (quantityDisplay) quantityDisplay.textContent = newVal;
                debounceUpdate(id, newVal);
            } else if (quantityDisplay) {
                let currentQty = Number(quantityDisplay.textContent || 1);
                let newVal = Math.max(1, currentQty - 1);
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

            const fieldIds = [
                "firstName",
                "lastName",
                "address",
                "city",
                "state",
                "zip",
                "phone",
                "email",
            ];

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
        const selected = document.querySelector(
            'input[name="payment_method"]:checked'
        );
        const cardWrapper =
            document.getElementById("card-details-wrapper") ||
            document.getElementById("cardDetails");

        if (cardWrapper) {
            cardWrapper.style.display =
                selected && selected.value === "card" ? "block" : "none";

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

    document
        .querySelectorAll('input[name="payment_method"]')
        .forEach((radio) => {
            radio.addEventListener("change", updateCardDetailsVisibility);
        });
    updateCardDetailsVisibility();

    function debounceUpdate(id, quantity) {
        if (debounceTimers[id]) clearTimeout(debounceTimers[id]);

        debounceTimers[id] = setTimeout(() => {
            updateItem(id, quantity);
        }, 500);
    }

    async function updateItem(id, quantity) {
        if (!updateUrl) return;

        try {
            const resp = await fetch(updateUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    id: id,
                    item_id: id,
                    quantity: quantity,
                }),
            });

            let data = null;
            try {
                data = await resp.json();
            } catch (e) {
                data = null;
            }

            if (!resp.ok) {
                const maxStock = data?.item?.max_stock || data?.max_stock;
                if (maxStock) {
                    const input = root.querySelector(
                        `.cart-quantity-input[data-item-id="${id}"]`
                    );
                    if (input) {
                        input.value = Math.min(Number(input.value || 1), maxStock);
                    }
                    const summaryQtyDisplay = document.querySelector(
                        `.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`
                    );
                    if (summaryQtyDisplay) {
                        const currentVal = Number(summaryQtyDisplay.textContent || 1);
                        summaryQtyDisplay.textContent = Math.min(currentVal, maxStock);
                    }
                    showStockLimitMessage(maxStock);
                }
                throw new Error(data?.message || ("Server returned " + resp.status));
            }

            if (data.success) {
                if (data.item?.max_stock) {
                    const input = root.querySelector(
                        `.cart-quantity-input[data-item-id="${id}"]`
                    );
                    if (input) {
                        input.setAttribute('max', data.item.max_stock);
                        input.dataset.maxStock = data.item.max_stock;
                    }
                    const summaryRow = document.querySelector(
                        `.summary-item-row[data-summary-item-id="${id}"]`
                    );
                    if (summaryRow) {
                        summaryRow.dataset.maxStock = data.item.max_stock;
                    }
                }

                const cartRow = document.querySelector(`.cart-item-row[data-item-id="${id}"]`);

                const lineTotalEl = document.querySelector(
                    `#item-line-total-${id}`
                );
                if (lineTotalEl && data.item?.discount) {
                    if (data.item.discount.has_discount) {
                        lineTotalEl.textContent = formatMoney(
                            data.item.discount.line_total_after_discount
                        );
                    } else {
                        lineTotalEl.textContent = formatMoney(
                            data.item.line_total
                        );
                    }
                } else if (lineTotalEl && data.item?.line_total) {
                    lineTotalEl.textContent = formatMoney(data.item.line_total);
                }

                if (cartRow && data.item?.discount) {
                    const discountBadge = cartRow.querySelector('.inline-flex.items-center.text-xs.font-semibold.text-green-700');
                    if (data.item.discount.has_discount && data.item.discount.discount_amount > 0) {
                        if (discountBadge) {
                            const discountText = discountBadge.querySelector('svg').nextSibling;
                            if (discountText) {
                                discountText.textContent = ` Save ${ window.env?.CURRENCY_SYMBOL || '$' } ${ (data.item.discount.discount_amount * data.item.quantity).toFixed(2) }`;
                            }
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

                const summaryQtyDisplay = document.querySelector(
                    `.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`
                );
                const cartInput = root.querySelector(
                    `.cart-quantity-input[data-item-id="${id}"]`
                );
                if (summaryQtyDisplay && cartInput) {
                    summaryQtyDisplay.textContent = cartInput.value;
                }

                const summaryItemPrice = document.querySelector(
                    `.summary-item-row[data-summary-item-id="${id}"] .summary-item-price`
                );
                if (summaryItemPrice && data.item?.discount) {
                    if (data.item.discount.has_discount) {
                        summaryItemPrice.textContent = formatMoney(
                            data.item.discount.line_total_after_discount
                        );
                    } else {
                        summaryItemPrice.textContent = formatMoney(
                            data.item.line_total
                        );
                    }
                }

                updateSummary(data.summary);

                // Dispatch event to update header cart counter
                if (data.summary && typeof data.summary.itemCount !== 'undefined') {
                    window.dispatchEvent(new CustomEvent('cart:changed', {
                        detail: {
                            summary: data.summary
                        }
                    }));
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
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    id: id,
                    item_id: id,
                }),
            });

            if (!resp.ok) throw new Error("Server returned " + resp.status);

            const data = await resp.json();

            if (data.success) {
                const row = document.querySelector(
                    `.cart-item-row[data-item-id="${id}"]`
                );
                if (row) {
                    row.remove();
                }

                const summaryRow = document.querySelector(
                    `.summary-item-row[data-summary-item-id="${id}"]`
                );
                if (summaryRow) {
                    summaryRow.remove();
                }

                if (document.querySelectorAll(".cart-item-row").length === 0) {
                    location.reload();
                    return;
                }

                updateSummary(data.summary);

                // Dispatch event to update header cart counter
                if (data.summary && typeof data.summary.itemCount !== 'undefined') {
                    window.dispatchEvent(new CustomEvent('cart:changed', {
                        detail: {
                            summary: data.summary
                        }
                    }));
                }

                if (window.Toast)
                    window.Toast.success({
                        title: "Removed",
                        message: "Item removed from cart",
                    });
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
        token.type = "hidden";
        token.name = "_token";
        token.value = csrf;
        form.appendChild(token);

        const idInput = document.createElement("input");
        idInput.type = "hidden";
        idInput.name = "id";
        idInput.value = id;
        form.appendChild(idInput);

        document.body.appendChild(form);
        form.submit();
    }

    function updateSummary(summary) {
        if (!summary) return;

        const map = {
            subtotal: [".order-subtotal", "order-subtotal"],
            total_discount: [".order-discount", "order-discount"],
            subtotal_after_discount: [
                ".order-subtotal-after-discount",
                "order-subtotal-after-discount",
            ],
            tax: [".order-tax", "order-tax"],
            total: [".order-total", "order-total", "summary-total"],
            itemCount: [".order-item-count", "order-item-count"],
            shipping: [".order-shipping", "order-shipping"],
        };

        for (const [key, selectors] of Object.entries(map)) {
            if (summary[key] === undefined) continue;

            selectors.forEach((sel) => {
                const els = sel.startsWith(".")
                    ? document.querySelectorAll(sel)
                    : [document.getElementById(sel)];

                els.forEach((el) => {
                    if (!el) return;
                    if (key === "shipping") {
                        el.textContent =
                            summary[key] > 0
                                ? formatMoney(summary[key])
                                : "FREE";
                    } else if (key === "itemCount") {
                        el.textContent = summary[key];
                    } else if (key === "total_discount") {
                        el.textContent = "-" + formatMoney(summary[key]);
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
        if (!summary || !summary.items) {
            console.warn(
                "updateOrderSummaryItems: No summary or items data received",
                summary
            );
            return;
        }

        console.log("Updating order summary items:", summary.items);

        Object.entries(summary.items).forEach(([itemId, itemData]) => {
            const summaryRow = document.querySelector(
                `.summary-item-row[data-summary-item-id="${itemId}"]`
            );
            if (!summaryRow) {
                console.warn(`Summary row not found for item ${itemId}`);
                return;
            }

            const qtyDisplay = summaryRow.querySelector(".summary-quantity");
            if (qtyDisplay) {
                qtyDisplay.textContent = itemData.quantity;
                console.log(
                    `Updated quantity for item ${itemId}: ${itemData.quantity}`
                );
            }

            const priceElement = summaryRow.querySelector(
                ".summary-item-price"
            );
            if (
                priceElement &&
                (itemData.final_price !== undefined ||
                    itemData.unit_price !== undefined)
            ) {
                const unitPrice =
                    itemData.final_price ?? itemData.unit_price ?? 0;
                const lineTotal = unitPrice * itemData.quantity;
                priceElement.textContent = formatMoney(lineTotal);
                console.log(
                    `Updated price for item ${itemId}: ${formatMoney(
                        lineTotal
                    )} (unit: ${formatMoney(unitPrice)}, qty: ${
                        itemData.quantity
                    })`
                );
            } else {
                console.warn(
                    `Price element or price data not found for item ${itemId}`,
                    itemData
                );
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

// Modal and Address Handling (from inline script)
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
    const submitBtn = document.getElementById('submitAddressBtn');

    if (!modal) return;

    // Function to open modal with type
    const openModalWithType = function(type) {
        if (typeField) typeField.value = type;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        if (form) form.reset();

        // Animate content in
        const content = document.getElementById('addAddressContent');
        if (content) {
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    };

    // Open modal for billing address
    if (openBillingBtn) {
        openBillingBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModalWithType('billing');
        });
    }

    // Open modal for shipping address
    if (openShippingBtn) {
        openShippingBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModalWithType('shipping');
        });
    }

    // Open modal for billing address (empty state button)
    if (openBillingBtnEmpty) {
        openBillingBtnEmpty.addEventListener('click', function(e) {
            e.preventDefault();
            openModalWithType('billing');
        });
    }

    // Open modal for shipping address (empty state button)
    if (openShippingBtnEmpty) {
        openShippingBtnEmpty.addEventListener('click', function(e) {
            e.preventDefault();
            openModalWithType('shipping');
        });
    }

    // Close modal
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

    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable submit button to prevent double submission
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...</span>';
            }

            const formData = new FormData(form);
            const name = (formData.get('first_name') + ' ' + formData.get(
                'last_name')).trim();

            // Prepare data for submission
            const data = {
                _token: formData.get('_token'),
                type: formData.get('type'),
                address_label: formData.get('address_label'),
                name: name,
                street: formData.get('street'),
                city: formData.get('city'),
                state: formData.get('state'),
                postal_code: formData.get('postal_code'),
                country: formData.get('country'),
                phone: formData.get('phone'),
                is_default: formData.get('is_default') ? 1 : 0
            };

            // Submit via AJAX
            fetch('/client/addresses', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': data._token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success || result.data) {
                        // Close modal
                        closeModal();

                        // Show success message
                        alert(
                            'Address saved successfully! The page will reload to show your new address.'
                        );

                        // Reload page to refresh address list
                        window.location.reload();
                    } else {
                        alert('Error saving address: ' + (result.message ||
                            'Unknown error'));
                        // Re-enable submit button
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                '<span class="inline-flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Save Address</span>';
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
                    // Re-enable submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML =
                            '<span class="inline-flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Save Address</span>';
                    }
                });
        });
    }
})();

// Handle "Change" button clicks on address cards
(function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('changeAddressBtn')) {
            e.preventDefault();
            // Find the parent card and get type
            const card = e.target.closest('div[id$="AddressPreview"]');
            if (card) {
                const isShipping = card.id === 'shippingAddressPreview';
                const isBilling = card.id === 'billingAddressPreview';

                // Reset the corresponding cards and show the address list again
                if (isShipping) {
                    const cards = document.querySelectorAll('.shippingAddressCard');
                    cards.forEach(c => {
                        c.classList.remove('border-primary', 'bg-primary/5');
                        c.querySelector('.shippingAddressRadio').classList.remove(
                            'border-primary', 'bg-primary');
                        c.querySelector('.shippingAddressRadio div').classList.remove(
                            'bg-white');
                    });
                    card.classList.add('hidden');
                } else if (isBilling) {
                    const cards = document.querySelectorAll('.billingAddressCard');
                    cards.forEach(c => {
                        c.classList.remove('border-primary', 'bg-primary/5');
                        c.querySelector('.billingAddressRadio').classList.remove(
                            'border-primary', 'bg-primary');
                        c.querySelector('.billingAddressRadio div').classList.remove(
                            'bg-white');
                    });
                    card.classList.add('hidden');
                }
            }
        }
    });
})();

// Payment Method Label Styling
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodLabels = document.querySelectorAll(
        'div.space-y-4.mb-6 > label'
    );

    function updatePaymentMethodStyles() {
        paymentMethodLabels.forEach(label => {
            const radio = label.querySelector('input[name="payment_method"]');
            if (radio) {
                if (radio.checked) {
                    label.classList.add('border-primary', 'bg-primary/5');
                    label.classList.remove('border-gray-200');
                } else {
                    label.classList.remove('border-primary', 'bg-primary/5');
                    label.classList.add('border-gray-200');
                }
            }
        });
    }

    // Add event listeners
    paymentMethodLabels.forEach(label => {
        const radio = label.querySelector('input[name="payment_method"]');
        if (radio) {
            radio.addEventListener('change', updatePaymentMethodStyles);
        }
    });

    // Initial styling
    updatePaymentMethodStyles();
});

// Show Laravel validation errors on page load
document.addEventListener('DOMContentLoaded', function() {
    // This will be populated by blade template if errors exist
    checkAndShowValidationErrors();
});

function checkAndShowValidationErrors() {
    // Function to show server error banner
    // Note: Errors data should be passed from the blade template
    // Update this if you have actual error data
}

function showServerErrorBanner(title, errors) {
    const banner = document.createElement('div');
    banner.id = 'serverErrorBanner';
    banner.className =
        'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-2xl w-full mx-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl animate-slide-down';

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
        </div>
    `;

    document.body.appendChild(banner);

    // Auto-remove after 10 seconds for server errors
    setTimeout(() => {
        if (banner.parentElement) {
            banner.style.opacity = '0';
            banner.style.transform = 'translate(-50%, -100%)';
            setTimeout(() => banner.remove(), 300);
        }
    }, 10000);
}

// Client-side Form Validation
(function() {
    const validationRules = {
        email: {
            required: 'Email is required',
            pattern: {
                value: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                message: 'Please enter a valid email address'
            }
        },
        phone: {
            required: 'Phone number is required',
            pattern: {
                value: /^[\d\s\+\-\(\)]{7,}$/,
                message: 'Please enter a valid phone number'
            }
        },
        cardNumber: {
            required: 'Card number is required',
            pattern: {
                value: /^\d{13,19}$/,
                message: 'Card number must be between 13 and 19 digits'
            }
        },
        expiry: {
            required: 'Expiry date is required',
            pattern: {
                value: /^(0[1-9]|1[0-2])\/\d{2}$/,
                message: 'Format should be MM/YY'
            }
        },
        cvv: {
            required: 'CVV is required',
            pattern: {
                value: /^\d{3,4}$/,
                message: 'CVV must be 3 or 4 digits'
            }
        },
        // Address fields
        first_name: {
            required: 'First name is required',
            pattern: {
                value: /^[a-zA-Z\s]+$/,
                message: 'First name should only contain letters'
            }
        },
        last_name: {
            required: 'Last name is required',
            pattern: {
                value: /^[a-zA-Z\s]+$/,
                message: 'Last name should only contain letters'
            }
        },
        street: {
            required: 'Street address is required',
            minLength: {
                value: 5,
                message: 'Street address must be at least 5 characters'
            }
        },
        city: {
            required: 'City is required',
            pattern: {
                value: /^[a-zA-Z\s]+$/,
                message: 'City should only contain letters'
            }
        },
        state: {
            required: 'State/Region is required'
        },
        postal_code: {
            required: 'Postal code is required',
            minLength: {
                value: 3,
                message: 'Postal code must be at least 3 characters'
            }
        },
        country: {
            required: 'Country is required'
        },
        type: {
            required: 'Please select an address type'
        }
    };

    function validateField(field) {
        const fieldName = field.name || field.id;
        const rules = validationRules[fieldName];
        const errorMsg = field.parentElement.querySelector('.error-message');

        if (!rules) return true;

        // Check required
        if (rules.required && !field.value.trim()) {
            showFieldError(field, rules.required);
            return false;
        }

        // Check minLength
        if (rules.minLength && field.value.trim().length < rules.minLength.value) {
            showFieldError(field, rules.minLength.message);
            return false;
        }

        // Check pattern
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
        const errorMsg = field.parentElement.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.classList.add('show');
        }
        field.classList.add('error-state');
        field.classList.remove('success-state');
    }

    function clearFieldError(field) {
        const errorMsg = field.parentElement.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.textContent = '';
            errorMsg.classList.remove('show');
        }
        field.classList.remove('error-state');
        field.classList.add('success-state');
    }

    // Real-time validation on input
    document.addEventListener('input', function(e) {
        const field = e.target;
        if (validationRules[field.name || field.id]) {
            validateField(field);
        }
    });

    // Real-time validation on blur
    document.addEventListener('blur', function(e) {
        const field = e.target;
        if (validationRules[field.name || field.id]) {
            validateField(field);
        }
    }, true);

    // Format card number with spaces
    const cardNumberField = document.getElementById('cardNumber');
    if (cardNumberField) {
        cardNumberField.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formatted;
        });
    }

    // Format expiry date MM/YY
    const expiryField = document.getElementById('expiry');
    if (expiryField) {
        expiryField.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });
    }

    // Handle address form submission
    const addressForm = document.getElementById('addAddressForm');
    if (addressForm) {
        addressForm.addEventListener('submit', function(e) {
            const fieldsToValidate = addressForm.querySelectorAll('[name="type"], [name="first_name"], [name="last_name"], [name="street"], [name="city"], [name="state"], [name="postal_code"], [name="country"]');
            let isValid = true;

            fieldsToValidate.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            // Validate phone if provided
            const phoneField = addressForm.querySelector('[name="phone"]');
            if (phoneField && phoneField.value.trim()) {
                if (!validateField(phoneField)) {
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
                showWarningBanner('Please fix the address validation errors');
            }
        });
    }

    // Validate form before main checkout submission
    const form = document.querySelector('form[action*="checkout"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const fieldsToValidate = form.querySelectorAll('[name="email"], [name="phone"], [name="cardNumber"], [name="expiry"], [name="cvv"]');
            let isValid = true;

            fieldsToValidate.forEach(field => {
                // Only validate visible fields
                const cardDetails = document.getElementById('cardDetails');
                if (field.name === 'cardNumber' || field.name === 'expiry' || field.name === 'cvv') {
                    if (cardDetails && cardDetails.style.display === 'none') {
                        return;
                    }
                }
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                showWarningBanner('Please fix the validation errors above');
            }
        });
    }
})();

function showWarningBanner(message) {
    const banner = document.createElement('div');
    banner.className =
        'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4 bg-amber-500 text-white px-6 py-4 rounded-xl shadow-2xl animate-slide-down flex items-center justify-between';
    banner.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white hover:text-amber-100 ml-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    document.body.appendChild(banner);

    setTimeout(() => {
        if (banner.parentElement) {
            banner.style.opacity = '0';
            banner.style.transform = 'translate(-50%, -100%)';
            setTimeout(() => banner.remove(), 300);
        }
    }, 5000);
}
