document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById("checkout-root");
    if (!root) return;

    const updateUrl = root.dataset.updateUrl;
    const removeUrl = root.dataset.removeUrl;
    const csrf =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

    function formatMoney(n) {
        return "$" + Number(n).toFixed(2);
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
        } catch (e) {
            console.error(e);
        }
    }

    // Update single item via AJAX
    async function updateItem(itemId, quantity) {
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
                    item_id: itemId,
                    quantity: Number(quantity),
                }),
            });
            const data = await resp.json();
            if (data && data.success) {
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
                updateSummary(data.summary);
            }
        } catch (e) {
            console.error("Failed updateItem", e);
        }
    }

    // Remove item via AJAX, fallback to hidden form submit
    async function removeItem(itemId) {
        if (!removeUrl) return submitFallback(itemId);
        try {
            const resp = await fetch(removeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    Accept: "application/json",
                },
                body: JSON.stringify({ item_id: itemId }),
            });
            const data = await resp.json();
            if (data && data.success) {
                // remove DOM row
                const row = document.querySelector(
                    '.cart-item-row[data-item-id="' + itemId + '"]'
                );
                if (row && row.parentNode) row.parentNode.removeChild(row);
                updateSummary(data.summary);
            } else {
                submitFallback(itemId);
            }
        } catch (e) {
            // fallback to form submit
            submitFallback(itemId);
        }
    }

    function submitFallback(itemId) {
        const f = document.getElementById("remove-item-" + itemId);
        if (f) {
            try {
                f.submit();
            } catch (e) {
                console.error(e);
            }
            return;
        }

        // If there's no existing form, create one and submit it as a safe fallback.
        try {
            const form = document.createElement("form");
            form.method = "POST";
            form.action = removeUrl || window.location.href;
            form.style.display = "none";
            form.id = "remove-item-" + itemId;

            const token = document.createElement("input");
            token.type = "hidden";
            token.name = "_token";
            token.value = csrf;
            form.appendChild(token);

            const iid = document.createElement("input");
            iid.type = "hidden";
            iid.name = "item_id";
            iid.value = itemId;
            form.appendChild(iid);

            document.body.appendChild(form);
            form.submit();
        } catch (e) {
            console.error("submitFallback failed", e);
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

    // Remove handlers — support both old and new button class names
    document
        .querySelectorAll(".remove-item-btn, .remove-item-ajax")
        .forEach((btn) => {
            btn.addEventListener("click", function () {
                const id = this.dataset.itemId;
                if (!id) return;
                removeItem(id);
            });
        });

});
