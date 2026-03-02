/**
 * coupon.js
 * Handles apply/remove coupon on checkout step 1 & 2 sidebars.
 * No jQuery. Uses axios. Tax is intentionally excluded.
 */

document.addEventListener("DOMContentLoaded", function () {
    const APPLY_URL = "/cart/apply-coupon";
    const REMOVE_URL = "/cart/remove-coupon";

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (typeof axios === "undefined") {
        console.error("coupon.js: axios is not loaded");
        return;
    }

    let isCheckoutSubmitting = false;

    document.addEventListener(
        "submit",
        function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (form.action && form.action.includes("/checkout/process")) {
                isCheckoutSubmitting = true;
            }
        },
        true,
    );

    // ─── Helpers ────────────────────────────────────────────────────────────────

    function currency() {
        return window.env?.CURRENCY_SYMBOL || "$";
    }

    function formatMoney(amount) {
        return currency() + " " + parseFloat(amount).toFixed(2);
    }

    function getShippingMethod() {
        return (
            document.querySelector('input[name="shipping_method"]:checked')
                ?.value || "standard"
        );
    }

    /** Returns '' or '-step2' based on which button ID triggered the action */
    function suffixFromBtnId(id) {
        if (id.endsWith("-step2")) return "-step2";
        if (id.endsWith("-step3")) return "-step3";
        return "";
    }

    function showMessage(text, type, el) {
        if (!el) return;
        el.classList.remove(
            "hidden",
            "text-green-600",
            "text-red-600",
            "text-gray-600",
        );
        el.classList.add(
            type === "success"
                ? "text-green-600"
                : type === "error"
                  ? "text-red-600"
                  : "text-gray-600",
        );
        el.textContent = text;
        el.classList.remove("hidden");
        if (type === "success") {
            setTimeout(() => el.classList.add("hidden"), 5000);
        }
    }

    // ─── Apply coupon ────────────────────────────────────────────────────────────

    document.addEventListener("click", function (e) {
        const btn = e.target.closest('[id^="apply-coupon-btn"]');
        if (!btn) return;
        e.preventDefault();

        const suffix = suffixFromBtnId(btn.id);
        const input = document.getElementById("coupon-code-input" + suffix);
        const messageEl = document.getElementById("coupon-message" + suffix);
        const code = input?.value.trim().toUpperCase();

        if (!code) {
            showMessage("Please enter a coupon code.", "error", messageEl);
            return;
        }

        btn.disabled = true;
        btn.textContent = "Applying...";

        axios
            .post(
                APPLY_URL,
                { code, shipping_method: getShippingMethod() },
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "X-SKIP-TOAST": "true",
                    },
                },
            )
            .then(function (response) {
                if (response.data.success) {
                    showMessage(response.data.message, "success", messageEl);
                    if (typeof showUniversalPopup === "function") {
                        showUniversalPopup(
                            response.data.message,
                            "success",
                            2000,
                        );
                    }
                    updateOrderSummary(response.data.summary);

                    // Replace the input form with the applied-coupon display after a short delay
                    setTimeout(function () {
                        const coupon = response.data.coupon;
                        const appliedHtml = `
                        <div class="bg-white rounded-xl p-4 border-2 border-purple-200" id="applied-coupon-display${suffix}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">${coupon.code}</p>
                                        <p class="text-xs text-gray-600">Coupon Applied</p>
                                    </div>
                                </div>
                                <button type="button" id="remove-coupon-btn${suffix}"
                                    class="text-red-500 hover:text-red-700 transition-colors px-3 py-1 rounded-lg hover:bg-red-50 text-sm font-semibold">
                                    Remove
                                </button>
                            </div>
                        </div>`;

                        const inputForm = document.getElementById(
                            "coupon-input-form" + suffix,
                        );
                        if (inputForm) inputForm.outerHTML = appliedHtml;
                    }, 800);
                } else {
                    showMessage(response.data.message, "error", messageEl);
                    btn.disabled = false;
                    btn.textContent = "Apply";
                }
            })
            .catch(function (error) {
                const msg =
                    error.response?.data?.message ||
                    "Failed to apply coupon. Please try again.";
                showMessage(msg, "error", messageEl);
                btn.disabled = false;
                btn.textContent = "Apply";
            });
    });

    // ─── Remove coupon ───────────────────────────────────────────────────────────

    document.addEventListener("click", function (e) {
        const btn = e.target.closest('[id^="remove-coupon-btn"]');
        if (!btn) return;
        e.preventDefault();

        const suffix = suffixFromBtnId(btn.id);
        const messageEl = document.getElementById("coupon-message" + suffix);

        btn.disabled = true;
        btn.textContent = "Removing...";

        axios
            .post(
                REMOVE_URL,
                { shipping_method: getShippingMethod() },
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "X-SKIP-TOAST": "true",
                    },
                },
            )
            .then(function (response) {
                if (response.data.success) {
                    showMessage(response.data.message, "success", messageEl);
                    if (typeof showUniversalPopup === "function") {
                        showUniversalPopup(
                            response.data.message,
                            "success",
                            2000,
                        );
                    }
                    updateOrderSummary(response.data.summary);

                    setTimeout(function () {
                        const inputHtml = `
                        <div class="flex gap-3" id="coupon-input-form${suffix}">
                            <input type="text" id="coupon-code-input${suffix}"
                                placeholder="Enter coupon code"
                                class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm uppercase">
                            <button type="button" id="apply-coupon-btn${suffix}"
                                class="px-6 py-3 bg-linear-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                Apply
                            </button>
                        </div>`;

                        const display = document.getElementById(
                            "applied-coupon-display" + suffix,
                        );
                        if (display) display.outerHTML = inputHtml;
                    }, 800);
                } else {
                    showMessage(response.data.message, "error", messageEl);
                    btn.disabled = false;
                    btn.textContent = "Remove";
                }
            })
            .catch(function () {
                showMessage(
                    "Failed to remove coupon. Please try again.",
                    "error",
                    messageEl,
                );
                btn.disabled = false;
                btn.textContent = "Remove";
            });
    });

    // ─── Enter key on coupon input ───────────────────────────────────────────────

    document.addEventListener("keypress", function (e) {
        const input = e.target.closest('[id^="coupon-code-input"]');
        if (!input || e.key !== "Enter") return;
        e.preventDefault();
        let code = input.value.trim();
        if (!code) return;
        code = code.toUpperCase();
        input.value = code;
        // Only apply coupon if user explicitly presses Enter with a non-empty code
        const suffix = suffixFromBtnId(
            input.id.replace("coupon-code-input", "apply-coupon-btn"),
        );
        document.getElementById("apply-coupon-btn" + suffix)?.click();
    });

    // Remove coupon from session when user leaves or reloads checkout page.
    // Skip auto-removal while checkout form submission is in progress.
    window.addEventListener("beforeunload", function () {
        if (isCheckoutSubmitting || window.__checkoutSubmitting === true) return;

        if (document.querySelector('[id^="applied-coupon-display"]')) {
            navigator.sendBeacon(
                REMOVE_URL,
                new URLSearchParams({
                    shipping_method: getShippingMethod(),
                    _token: csrfToken,
                }),
            );
        }
    });

    // ─── Update order summary DOM ────────────────────────────────────────────────

    /**
     * Updates all summary elements on the page.
     * Works across both step-1 (ids without suffix) and step-2 (ids with -step2 suffix)
     * sidebars simultaneously.
     */
    function updateOrderSummary(summary) {
        if (!summary) return;

        const suffixes = ["", "-step2"];

        suffixes.forEach(function (s) {
            setText(
                "order-subtotal" + s,
                summary.subtotal !== undefined
                    ? formatMoney(summary.subtotal)
                    : null,
            );

            // Item discount row
            const discRow = document.getElementById("item-discount-row" + s);
            if (discRow) {
                if ((summary.item_discount ?? 0) > 0) {
                    setText(
                        "order-discount" + s,
                        "-" + formatMoney(summary.item_discount),
                    );
                    discRow.style.display = "";
                } else {
                    discRow.style.display = "none";
                }
            }

            // Coupon discount row
            const couponRow = document.getElementById(
                "coupon-discount-row" + s,
            );
            if (couponRow) {
                if ((summary.coupon_discount ?? 0) > 0) {
                    setText(
                        "order-coupon-discount" + s,
                        "-" + formatMoney(summary.coupon_discount),
                    );
                    couponRow.style.display = "";
                } else {
                    couponRow.style.display = "none";
                }
            }

            // Shipping
            if (summary.shipping_cost !== undefined) {
                const shippingText =
                    summary.shipping_cost > 0
                        ? formatMoney(summary.shipping_cost)
                        : "FREE";
                setText("order-shipping" + s, shippingText);
            }

            if (summary.final_total !== undefined) {
                const totalText = formatMoney(summary.final_total);
                setText("order-total" + s, totalText);
            }
        });
    }

    function setText(id, text) {
        if (text === null || text === undefined) return;
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    }
});
