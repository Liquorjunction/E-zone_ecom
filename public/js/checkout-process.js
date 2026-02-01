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
                alert(data.message || "Could not update item.");
            }
        } catch (e) {
            console.error("AJAX Error:", e);
            if (e?.message) {
                alert(e.message);
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
