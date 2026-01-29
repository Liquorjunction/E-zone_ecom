// Move Modal to Body on Load (Fixes stacking context "under header" issue)
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addAddressModal');
    if(modal) document.body.appendChild(modal);
});

document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById("checkout-root");
    if (!root) return;

    // --- Configuration ---
    const updateUrl = root.dataset.updateUrl;
    const removeUrl = root.dataset.removeUrl;
    const csrf =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

    // Timer storage for debouncing (prevents rapid-fire server requests)
    let debounceTimers = {};

    function formatMoney(n) {
        const symbol = window.env?.CURRENCY_SYMBOL || "$";
        // Convert to number, handle commas and spaces
        const num = typeof n === 'string' ? parseFloat(n.replace(/[,\s]/g, '')) : Number(n);
        // Format with thousands separators and 2 decimal places
        const formatted = num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return symbol + ' ' + formatted;
    }

    // ============================================================
    //  SECTION 1: CART ACTIONS (Event Delegation)
    // ============================================================

    // Handle Clicks (Remove, Increase, Decrease)
    root.addEventListener("click", function (e) {
        const target = e.target;

        // A. Remove Item Button
        const removeBtn = target.closest(".remove-item-btn, .remove-item-ajax");
        if (removeBtn) {
            e.preventDefault(); // STOP page reload/form submit
            const id = removeBtn.dataset.itemId;

            // Optional: Confirm dialog
            // if (!confirm("Are you sure you want to remove this item?")) return;

            if (id) removeItem(id);
            return;
        }

        // B. Increase Quantity Button (+)
        const increaseBtn = target.closest(".qty-increase");
        if (increaseBtn) {
            e.preventDefault();
            const id = increaseBtn.dataset.itemId;
            const input = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );
            if (input) {
                let newVal = Number(input.value || 0) + 1;
                input.value = newVal; // Update UI immediately
                debounceUpdate(id, newVal); // Queue server request
            }
            return;
        }

        // C. Decrease Quantity Button (-)
        const decreaseBtn = target.closest(".qty-decrease");
        if (decreaseBtn) {
            e.preventDefault();
            const id = decreaseBtn.dataset.itemId;
            const input = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );
            if (input) {
                let newVal = Math.max(1, Number(input.value || 1) - 1);
                input.value = newVal; // Update UI immediately
                debounceUpdate(id, newVal); // Queue server request
            }
            return;
        }

        // D. Summary Increase Quantity Button (+)
        const summaryIncreaseBtn = target.closest(".summary-qty-increase");
        if (summaryIncreaseBtn) {
            e.preventDefault();

            // Check if we're on step 3 (order confirmation) - don't allow changes
            const isStep3 = document.querySelector('.text-xl.font-extrabold.text-gray-900.mb-4')?.textContent.includes('Order Successfully Placed');
            if (isStep3) {
                return; // Order is complete, no changes allowed
            }

            const id = summaryIncreaseBtn.dataset.itemId;
            const quantityDisplay = document.querySelector(
                `.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`
            );
            const mainInput = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );

            if (mainInput) {
                // Step 1: Main cart exists, sync with it
                let newVal = Number(mainInput.value || 0) + 1;
                mainInput.value = newVal;
                if (quantityDisplay) quantityDisplay.textContent = newVal;
                debounceUpdate(id, newVal);
            } else if (quantityDisplay) {
                // Step 2: No main cart, update directly from summary
                let currentQty = Number(quantityDisplay.textContent || 0);
                let newVal = currentQty + 1;
                quantityDisplay.textContent = newVal;
                debounceUpdate(id, newVal);
            }
            return;
        }

        // E. Summary Decrease Quantity Button (-)
        const summaryDecreaseBtn = target.closest(".summary-qty-decrease");
        if (summaryDecreaseBtn) {
            e.preventDefault();

            // Check if we're on step 3 (order confirmation) - don't allow changes
            const isStep3 = document.querySelector('.text-xl.font-extrabold.text-gray-900.mb-4')?.textContent.includes('Order Successfully Placed');
            if (isStep3) {
                return; // Order is complete, no changes allowed
            }

            const id = summaryDecreaseBtn.dataset.itemId;
            const quantityDisplay = document.querySelector(
                `.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`
            );
            const mainInput = root.querySelector(
                `.cart-quantity-input[data-item-id="${id}"]`
            );

            if (mainInput) {
                // Step 1: Main cart exists, sync with it
                let newVal = Math.max(1, Number(mainInput.value || 1) - 1);
                mainInput.value = newVal;
                if (quantityDisplay) quantityDisplay.textContent = newVal;
                debounceUpdate(id, newVal);
            } else if (quantityDisplay) {
                // Step 2: No main cart, update directly from summary
                let currentQty = Number(quantityDisplay.textContent || 1);
                let newVal = Math.max(1, currentQty - 1);
                quantityDisplay.textContent = newVal;
                debounceUpdate(id, newVal);
            }
            return;
        }

        // F. Summary Remove Item Button
        const summaryRemoveBtn = target.closest(".summary-remove-item-btn");
        if (summaryRemoveBtn) {
            e.preventDefault();
            const id = summaryRemoveBtn.dataset.itemId;
            if (id) removeItem(id);
            return;
        }
    });

    // Handle Manual Input Changes (Typing quantity)
    root.addEventListener("input", function (e) {
        if (e.target.classList.contains("cart-quantity-input")) {
            const id = e.target.dataset.itemId;
            const val = Math.max(1, Number(e.target.value || 1));
            debounceUpdate(id, val);
        }
    });

    // ============================================================
    //  SECTION 2: CHECKOUT FORM LOGIC
    // ============================================================

    // A. Saved Address Logic
    const addressSelect = document.getElementById("savedAddressSelect");
    if (addressSelect) {
        addressSelect.addEventListener("change", function () {
            const opt = this.options[this.selectedIndex];

            // Fields to clear/fill
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

            // 1. Clear fields if "New Address" or invalid selection
            if (!opt || !opt.dataset || !opt.dataset.addr) {
                fieldIds.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = "";
                });
                return;
            }

            // 2. Parse and Fill
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
                        // Visual flash effect
                        el.style.transition = "background-color 0.3s ease";
                        el.style.backgroundColor = "rgba(34, 197, 94, 0.1)"; // Light green
                        setTimeout(() => (el.style.backgroundColor = ""), 500);
                    }
                }
            } catch (e) {
                console.error("Error parsing address data", e);
            }
        });
    }

    // B. Payment Method Toggle
    function updateCardDetailsVisibility() {
        const selected = document.querySelector(
            'input[name="payment_method"]:checked'
        );
        const cardWrapper =
            document.getElementById("card-details-wrapper") ||
            document.getElementById("cardDetails");

        if (cardWrapper) {
            // Show if 'card' is selected
            cardWrapper.style.display =
                selected && selected.value === "card" ? "block" : "none";

            // Toggle 'required' attribute for inputs inside to prevent validation errors on other methods
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

    // Attach listeners to payment radios
    document
        .querySelectorAll('input[name="payment_method"]')
        .forEach((radio) => {
            radio.addEventListener("change", updateCardDetailsVisibility);
        });
    // Run once on load
    updateCardDetailsVisibility();

    // ============================================================
    //  SECTION 3: NETWORK / AJAX FUNCTIONS
    // ============================================================

    // Wrapper to debounce requests (wait 500ms after last click)
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
                    Accept: "application/json", // Crucial fix for Laravel
                },
                body: JSON.stringify({
                    id: id,
                    item_id: id, // Send both to be safe
                    quantity: quantity,
                }),
            });

            if (!resp.ok) throw new Error("Server returned " + resp.status);

            const data = await resp.json();

            if (data.success) {
                // Get the cart item row
                const cartRow = document.querySelector(`.cart-item-row[data-item-id="${id}"]`);

                // Update Line Total specific to this row
                const lineTotalEl = document.querySelector(
                    `#item-line-total-${id}`
                );
                if (lineTotalEl && data.item?.discount) {
                    // If item has discount, update with discount info
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

                // Sync summary item quantity display with cart quantity
                const summaryQtyDisplay = document.querySelector(
                    `.summary-item-row[data-summary-item-id="${id}"] .summary-quantity`
                );
                const cartInput = root.querySelector(
                    `.cart-quantity-input[data-item-id="${id}"]`
                );
                if (summaryQtyDisplay && cartInput) {
                    summaryQtyDisplay.textContent = cartInput.value;
                }

                // Update summary item price
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

                // Update Global Summaries
                updateSummary(data.summary);
            } else {
                console.error("Update failed:", data.message);
                alert(data.message || "Could not update item.");
            }
        } catch (e) {
            console.error("AJAX Error:", e);
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
                // Remove main cart row from DOM
                const row = document.querySelector(
                    `.cart-item-row[data-item-id="${id}"]`
                );
                if (row) {
                    row.remove();
                }

                // Remove summary row from DOM
                const summaryRow = document.querySelector(
                    `.summary-item-row[data-summary-item-id="${id}"]`
                );
                if (summaryRow) {
                    summaryRow.remove();
                }

                // Check if cart is now empty
                if (document.querySelectorAll(".cart-item-row").length === 0) {
                    location.reload();
                    return;
                }

                updateSummary(data.summary);

                // Show toast if available
                if (window.Toast)
                    window.Toast.success({
                        title: "Removed",
                        message: "Item removed from cart",
                    });
            } else {
                // Fallback if JSON says failed
                submitFallback(id);
            }
        } catch (e) {
            console.error("Remove failed, trying fallback", e);
            submitFallback(id);
        }
    }

    // Fallback: Create a form and submit it normally (if AJAX fails completely)
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

    // Helper: Update Totals on Page
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

        // --- Update the Item Total count for both step 1 and 2 ---
        if (typeof summary.itemCount !== 'undefined') {
            const itemTotalCount = document.getElementById('item-total-count');
            if (itemTotalCount) {
                // Try to preserve the "Quantity" label as in Blade
                itemTotalCount.textContent = summary.itemCount + ' ' + (summary.itemCount === 1 ? 'Quantity' : 'Quantity');
            }
        }

        // Update summary items in order summary section
        updateOrderSummaryItems(summary);
    }

    // Helper: Update order summary items with quantity controls
    function updateOrderSummaryItems(summary) {
        if (!summary || !summary.items) {
            console.warn(
                "updateOrderSummaryItems: No summary or items data received",
                summary
            );
            return;
        }

        console.log("Updating order summary items:", summary.items);

        // Update each summary item row
        Object.entries(summary.items).forEach(([itemId, itemData]) => {
            const summaryRow = document.querySelector(
                `.summary-item-row[data-summary-item-id="${itemId}"]`
            );
            if (!summaryRow) {
                console.warn(`Summary row not found for item ${itemId}`);
                return;
            }

            // Update quantity display
            const qtyDisplay = summaryRow.querySelector(".summary-quantity");
            if (qtyDisplay) {
                qtyDisplay.textContent = itemData.quantity;
                console.log(
                    `Updated quantity for item ${itemId}: ${itemData.quantity}`
                );
            }

            // Update price - use specific class selector for reliable targeting
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

            // Update original (line-through) price if discount applies
            const lineThroughEl = summaryRow.querySelector(".text-xs.text-gray-400.line-through");
            if (lineThroughEl) {
                // Only show if there is a discount and original price is different
                if (itemData.unit_price && itemData.final_price && itemData.unit_price > itemData.final_price) {
                    const originalLineTotal = itemData.unit_price * itemData.quantity;
                    lineThroughEl.innerHTML = `${window.env?.CURRENCY_SYMBOL || '$'} ${Number(originalLineTotal).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}<br />`;
                    lineThroughEl.style.display = '';
                } else {
                    // Hide if no discount
                    lineThroughEl.style.display = 'none';
                }
            }
        });
    }
});
