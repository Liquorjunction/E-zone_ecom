document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById("checkout-root");
    if (!root) return;

    const updateUrl = root.dataset.updateUrl;
    const removeUrl = root.dataset.removeUrl;
    const loginUrl = root.dataset.loginUrl || "/login";

    function getCsrfToken() {
        return (
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || ""
        );
    }

    async function requestJson(url, payload) {
        const resp = await fetch(url, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });

        if (resp.status === 419) {
            window.location.href = loginUrl;
            return null;
        }

        const contentType = resp.headers.get("content-type") || "";
        if (!contentType.includes("application/json")) {
            throw new Error("Non-JSON response");
        }

        return await resp.json();
    }

    function formatMoney(n) {
        return "$" + Number(n).toFixed(2);
    }

    function getCurrentShippingMethod() {
        const shippingMethodEl = document.querySelector('input[name="shipping_method"]:checked');
        return shippingMethodEl ? shippingMethodEl.value : 'standard';
    }

    // Saved address populate
    const addressSelect = document.getElementById("savedAddressSelect");
    if (addressSelect) {
        addressSelect.addEventListener("change", function () {
            const opt = this.options[this.selectedIndex];
            if (!opt || !opt.dataset || !opt.dataset.addr) {
                [
                    "firstName",
                    "lastName",
                    "address",
                    "city",
                    "state",
                    "zip",
                ].forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = "";
                });
                return;
            }
            try {
                const addr = JSON.parse(opt.dataset.addr);
                if (addr.first_name)
                    document.getElementById("firstName").value =
                        addr.first_name;
                if (addr.last_name)
                    document.getElementById("lastName").value = addr.last_name;
                if (addr.street || addr.address)
                    document.getElementById("address").value =
                        addr.street || addr.address;
                if (addr.city)
                    document.getElementById("city").value = addr.city;
                if (addr.state || addr.region)
                    document.getElementById("state").value =
                        addr.state || addr.region;
                if (addr.postal_code || addr.zip)
                    document.getElementById("zip").value =
                        addr.postal_code || addr.zip;
                [
                    "firstName",
                    "lastName",
                    "address",
                    "city",
                    "state",
                    "zip",
                ].forEach((id) => {
                    const input = document.getElementById(id);
                    if (input && input.value) {
                        input.style.transition = "all 0.3s ease";
                        input.style.backgroundColor = "rgba(34, 197, 94, 0.1)";
                        setTimeout(() => {
                            input.style.backgroundColor = "";
                        }, 800);
                    }
                });
            } catch (e) {
                /* ignore */
            }
        });
    }

    // Payment method toggle
    function updateCardDetailsVisibility() {
        const sel = document.querySelector(
            'input[name="payment_method"]:checked'
        )?.value;
        const cardDetails = document.getElementById("cardDetails");
        if (!cardDetails) return;
        cardDetails.style.display = sel === "card" ? "" : "none";
    }
    document
        .querySelectorAll('input[name="payment_method"]')
        .forEach((r) =>
            r.addEventListener("change", updateCardDetailsVisibility)
        );
    updateCardDetailsVisibility();

    // Helpers to update order summary in DOM
    function updateSummary(summary) {
        try {
            if (!summary) return;
            const subtotalEl = document.getElementById("order-subtotal");
            const taxEl = document.getElementById("order-tax");
            const totalEl = document.getElementById("order-total");
            const countEl = document.getElementById("order-item-count");
            const summaryTotal = document.getElementById("summary-total");
            const orderShipping = document.getElementById("order-shipping");

            if (subtotalEl && typeof summary.subtotal !== "undefined")
                subtotalEl.textContent = formatMoney(summary.subtotal);
            if (taxEl && typeof summary.tax !== "undefined")
                taxEl.textContent = formatMoney(summary.tax);
            if (totalEl && typeof summary.total !== "undefined")
                totalEl.textContent = formatMoney(summary.total);
            if (countEl && typeof summary.itemCount !== "undefined")
                countEl.textContent = summary.itemCount;
            if (summaryTotal && typeof summary.total !== "undefined")
                summaryTotal.textContent = formatMoney(summary.total);
            if (orderShipping && typeof summary.shipping !== "undefined")
                orderShipping.textContent =
                    summary.shipping > 0
                        ? formatMoney(summary.shipping)
                        : "FREE";

            // Step 1 side panel count
            const itemTotalCountEl = document.getElementById("item-total-count");
            if (itemTotalCountEl && typeof summary.itemCount !== "undefined") {
                const count = Number(summary.itemCount || 0);
                itemTotalCountEl.textContent = `${count} ${count === 1 ? "Item" : "Items"}`;
            }

            syncProceedButtonState(summary.itemCount);
        } catch (e) {
            console.error(e);
        }
    }

    function syncProceedButtonState(itemCount) {
        const proceedBtn = document.getElementById("proceedToCheckoutBtn");
        if (!proceedBtn) return;

        const messageEl = document.getElementById("checkout-stock-block-message");
        const hasStockBlock = messageEl?.dataset?.stockBlock === "1";

        const isEmpty = Number(itemCount || 0) <= 0;
        const shouldDisable = hasStockBlock || isEmpty;

        proceedBtn.disabled = shouldDisable;
        proceedBtn.classList.toggle("opacity-60", shouldDisable);
        proceedBtn.classList.toggle("cursor-not-allowed", shouldDisable);
        proceedBtn.classList.toggle("pointer-events-none", shouldDisable);

        if (!messageEl) return;

        if (hasStockBlock) {
            messageEl.classList.remove("hidden");
            messageEl.textContent = trans("Please remove out-of-stock items before continuing to checkout.");
            return;
        }

        if (isEmpty) {
            messageEl.classList.remove("hidden");
            messageEl.textContent = trans("Your cart is empty. Add items to continue to checkout.");
            return;
        }

        messageEl.classList.add("hidden");
    }

    const initialItemCount = Number(
        document.getElementById("item-total-count")?.textContent?.trim().split(" ")[0] || 0
    );
    syncProceedButtonState(initialItemCount);

    // Update single item via AJAX
    async function updateItem(itemId, quantity) {
        if (!updateUrl) return;
        try {
            const data = await requestJson(updateUrl, {
                item_id: itemId,
                quantity: Number(quantity),
                shipping_method: getCurrentShippingMethod(),
            });
            if (data && data.success) {
                // Get the cart item row
                const cartRow = document.querySelector(`.cart-item-row[data-item-id="${itemId}"]`);

                // update line total if returned
                const lineEl = document.getElementById(
                    "item-line-total-" + itemId
                );
                if (
                    lineEl &&
                    data.item &&
                    typeof data.item.line_total !== "undefined"
                )
                    lineEl.textContent = formatMoney(data.item.line_total);

                // Update discount badge
                if (cartRow && data.item?.discount) {
                    const discountBadge = cartRow.querySelector('.inline-flex.items-center.text-xs.font-semibold.text-green-700');
                    if (data.item.discount.has_discount && data.item.discount.discount_amount > 0) {
                        if (discountBadge) {
                            // Update existing badge with new discount amount
                            const discountText = discountBadge.querySelector('svg').nextSibling;
                            if (discountText) {
                                discountText.textContent = ` Save ${ window.env?.CURRENCY_SYMBOL || '$' } ${ (data.item.discount.discount_amount * data.item.quantity).toFixed(2) }`;
                            }
                        } else {
                            // Badge doesn't exist but should - create it
                            const priceContainer = cartRow.querySelector('.text-right');
                            if (priceContainer) {
                                const newBadge = document.createElement('div');
                                newBadge.className = 'inline-flex items-center text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-full mb-1';
                                newBadge.innerHTML = `<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>Save ${ window.env?.CURRENCY_SYMBOL || '$' } ${ (data.item.discount.discount_amount * data.item.quantity).toFixed(2) }`;
                                priceContainer.insertBefore(newBadge, priceContainer.firstChild);
                            }
                        }
                    } else if (discountBadge) {
                        // Remove badge if there's no discount
                        discountBadge.remove();
                    }
                }

                updateSummary(data.summary);
            }
        } catch (e) {
            console.error("Failed updateItem", e);
        }
    }

    async function refreshTotalsForShipping() {
        if (!updateUrl) return;

        const firstInput = document.querySelector('.cart-quantity-input[data-item-id]');
        if (firstInput) {
            await updateItem(firstInput.dataset.itemId, Math.max(1, Number(firstInput.value || 1)));
            return;
        }

        try {
            const data = await requestJson(updateUrl, {
                shipping_method: getCurrentShippingMethod(),
            });
            if (data && data.success) {
                updateSummary(data.summary);
            }
        } catch (e) {
            console.error("Failed to refresh summary for shipping", e);
        }
    }

    // Remove item via AJAX
    async function removeItem(itemId) {
        if (!removeUrl) return;
        try {
            const data = await requestJson(removeUrl, {
                item_id: itemId,
                shipping_method: getCurrentShippingMethod(),
            });

            if (data && data.success) {
                // remove DOM row
                const row = document.querySelector(
                    '.cart-item-row[data-item-id="' + itemId + '"]'
                );
                if (row && row.parentNode) row.parentNode.removeChild(row);
                updateSummary(data.summary);
                if (typeof showUniversalPopup === 'function') {
                    showUniversalPopup((data && data.message) ? data.message : trans('Item removed from cart.'), 'success', 2000);
                } else if (window.Toast && typeof window.Toast.success === 'function') {
                    window.Toast.success({ title: trans('Success'), message: (data && data.message) ? data.message : trans('Item removed from cart.') });
                }
            }
        } catch (e) {
            if (typeof showUniversalPopup === 'function') {
                showUniversalPopup(trans('Failed to remove item.')  , 'error', 3000);
            } else if (window.Toast && typeof window.Toast.error === 'function') {
                window.Toast.error({ title: trans('Error'), message: trans('Failed to remove item.') });
            }
        }
    }

    // Attach event listeners for qty buttons and inputs
    document.querySelectorAll(".qty-increase").forEach((btn) => {
        btn.addEventListener("click", function () {
            const id = this.dataset.itemId;
            const input = document.querySelector(
                '.cart-quantity-input[data-item-id="' + id + '"]'
            );
            if (!input) return;
            input.value = Number(input.value || 0) + 1;
            updateItem(id, input.value);
        });
    });

    document.querySelectorAll(".qty-decrease").forEach((btn) => {
        btn.addEventListener("click", function () {
            const id = this.dataset.itemId;
            const input = document.querySelector(
                '.cart-quantity-input[data-item-id="' + id + '"]'
            );
            if (!input) return;
            const newVal = Math.max(1, Number(input.value || 1) - 1);
            input.value = newVal;
            updateItem(id, input.value);
        });
    });

    document.querySelectorAll(".cart-quantity-input").forEach((input) => {
        input.addEventListener("change", function () {
            const id = this.dataset.itemId;
            const val = Math.max(1, Number(this.value || 1));
            this.value = val;
            updateItem(id, val);
        });
    });

    document.querySelectorAll('input[name="shipping_method"]').forEach((radio) => {
        radio.addEventListener("change", refreshTotalsForShipping);
    });

    // Remove handlers — support both old and new button class names
    document
        .querySelectorAll(".remove-item-btn, .remove-item-ajax, .summary-remove-item-btn")
        .forEach((btn) => {
            btn.addEventListener("click", function () {
                const id = this.dataset.itemId;
                if (!id) return;
                removeItem(id);
            });
        });

});
