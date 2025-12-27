document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById("checkout-root");
    if (!root) return;

    // --- Configuration ---
    const updateUrl = root.dataset.updateUrl;
    const removeUrl = root.dataset.removeUrl;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

    // Timer storage for debouncing (prevents rapid-fire server requests)
    let debounceTimers = {};

    function formatMoney(n) {
        return "$" + Number(n).toFixed(2);
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
            const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
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
            const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
            if (input) {
                let newVal = Math.max(1, Number(input.value || 1) - 1);
                input.value = newVal; // Update UI immediately
                debounceUpdate(id, newVal); // Queue server request
            }
            return;
        }
    });

    // Handle Manual Input Changes (Typing quantity)
    root.addEventListener("input", function(e) {
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
            const fieldIds = ["firstName", "lastName", "address", "city", "state", "zip", "phone", "email"];

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
                    email: addr.email
                };

                for (const [id, val] of Object.entries(mapping)) {
                    const el = document.getElementById(id);
                    if (el && val) {
                        el.value = val;
                        // Visual flash effect
                        el.style.transition = "background-color 0.3s ease";
                        el.style.backgroundColor = "rgba(34, 197, 94, 0.1)"; // Light green
                        setTimeout(() => el.style.backgroundColor = "", 500);
                    }
                }
            } catch (e) {
                console.error("Error parsing address data", e);
            }
        });
    }

    // B. Payment Method Toggle
    function updateCardDetailsVisibility() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        const cardWrapper = document.getElementById("card-details-wrapper") || document.getElementById("cardDetails");

        if (cardWrapper) {
            // Show if 'card' is selected
            cardWrapper.style.display = (selected && selected.value === "card") ? "block" : "none";

            // Toggle 'required' attribute for inputs inside to prevent validation errors on other methods
            const inputs = cardWrapper.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (selected && selected.value === "card") {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                }
            });
        }
    }

    // Attach listeners to payment radios
    document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
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
                    "Accept": "application/json" // Crucial fix for Laravel
                },
                body: JSON.stringify({
                    id: id,
                    item_id: id, // Send both to be safe
                    quantity: quantity
                }),
            });

            if (!resp.ok) throw new Error("Server returned " + resp.status);

            const data = await resp.json();

            if (data.success) {
                // Update Line Total specific to this row
                const lineTotalEl = document.querySelector(`#item-line-total-${id}`);
                if (lineTotalEl && data.item?.line_total) {
                    lineTotalEl.textContent = formatMoney(data.item.line_total);
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
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    id: id,
                    item_id: id
                }),
            });

            if (!resp.ok) throw new Error("Server returned " + resp.status);

            const data = await resp.json();

            if (data.success) {
                // Remove row from DOM
                const row = document.querySelector(`.cart-item-row[data-item-id="${id}"]`);
                if (row) {
                    row.remove();
                    // Check if cart is now empty
                    if (document.querySelectorAll('.cart-item-row').length === 0) {
                        location.reload();
                        return;
                    }
                }
                updateSummary(data.summary);

                // Show toast if available
                if (window.Toast) window.Toast.success({ title: 'Removed', message: 'Item removed from cart' });
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
            'subtotal': ['.order-subtotal', 'order-subtotal'],
            'tax': ['.order-tax', 'order-tax'],
            'total': ['.order-total', 'order-total', 'summary-total'],
            'itemCount': ['.order-item-count', 'order-item-count'],
            'shipping': ['.order-shipping', 'order-shipping']
        };

        for (const [key, selectors] of Object.entries(map)) {
            if (summary[key] === undefined) continue;

            selectors.forEach(sel => {
                const els = sel.startsWith('.')
                    ? document.querySelectorAll(sel)
                    : [document.getElementById(sel)];

                els.forEach(el => {
                    if (!el) return;
                    if (key === 'shipping') {
                        el.textContent = summary[key] > 0 ? formatMoney(summary[key]) : 'FREE';
                    } else if (key === 'itemCount') {
                        el.textContent = summary[key];
                    } else {
                        el.textContent = formatMoney(summary[key]);
                    }
                });
            });
        }
    }
});
