// ═══════════════════════════════════════════════════════════════
//  GLOBAL UTILS & VALIDATION
// ═══════════════════════════════════════════════════════════════
if (typeof window.trans !== 'function') {
    window.trans = function(key) {
        if (window.translations && window.translations[key] !== undefined) return window.translations[key];
        return key;
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addAddressModal');
    if(modal) document.body.appendChild(modal);
});

// ═══════════════════════════════════════════════════════════════
//  MAIN CHECKOUT PROCESSOR
// ═══════════════════════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById("checkout-root");
    if (!root) return;

    const updateUrl = root.dataset.updateUrl;
    const removeUrl = root.dataset.removeUrl;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

    let debounceTimers = {};
    let goToStepTimer = null;
    let stepLoader = null;

    function formatMoney(n) {
        const sym = window.env?.CURRENCY_SYMBOL || "$";
        const num = typeof n === 'string' ? parseFloat(n.replace(/[,\s]/g, '')) : Number(n);
        const formatted = num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return sym + ' ' + formatted;
    }

    function getCurrentShippingMethod() {
        const el = document.querySelector('input[name="shipping_method"]:checked');
        return el ? el.value : "standard";
    }

    function showToast(message, type = "success") {
        if (typeof showUniversalPopup === "function") {
            showUniversalPopup(message, type, type === "error" ? 3000 : 2000);
        } else if (window.Toast && typeof window.Toast[type] === "function") {
            window.Toast[type]({ message: message });
        } else {
            console.log("[" + type + "]", message);
        }
    }

    // --- Injected Stock & Quantity Handlers ---
    function showStockLimitMessage(max) {
        if (max === null || max === undefined) return;
        let message = Number(max) <= 0
            ? trans('This item is out of stock.')
            : max === 1
            ? trans('Only 1 item available in stock.')
            : trans('Only') + ' ' + max + ' ' + trans('items available in stock.');

        if (window.Toast && typeof window.Toast.info === 'function') {
            window.Toast.info({ title: trans('Stock limit'), message: message });
            return;
        }

        let banner = document.getElementById('stockLimitBanner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'stockLimitBanner';
            banner.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4 bg-amber-500 text-white px-4 py-3 rounded-xl shadow-xl transition-all duration-300';
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

    function parseStockValue(value) {
        if (value === null || value === undefined || value === '') return null;
        const parsed = parseInt(value, 10);
        return Number.isNaN(parsed) ? null : parsed;
    }

    function getMaxStockForItem(id) {
        const input = root.querySelector(`.cart-quantity-input[data-item-id="${id}"]`);
        let max = null;

        if (input) {
            const raw = input.getAttribute('max') || input.dataset.maxStock || input.dataset.max;
            const parsed = parseStockValue(raw);
            if (parsed !== null) max = parsed;
        }

        if (max === null) {
            const summaryRow = document.querySelector(`.summary-item-row[data-summary-item-id="${id}"]`);
            const raw = summaryRow?.dataset?.maxStock;
            const parsed = parseStockValue(raw);
            if (parsed !== null) max = parsed;
        }
        return max;
    }

    function clampQuantity(val, max) {
        let v = Math.max(1, Number(val || 1));
        if (max === 0) return 0;
        if (max !== null && max !== undefined && v > max) v = max;
        return v;
    }

    function selectQuantityInputText(input) {
        if (!input) return;
        try {
            if (typeof input.select === 'function') {
                input.select();
            }
        } catch (e) {}
    }

    function syncCheckoutSubmitState() {
        const btn = document.getElementById("placeOrderBtn");
        if (!btn) return;

        const hasStockIssue = Array.from(root.querySelectorAll(".cart-item-row, .summary-item-row")).some(function (row) {
            const stockState = row.dataset.stockStatus;
            if (stockState === 'out') return true;

            const max = parseStockValue(row.dataset.maxStock);
            if (max === null || max <= 0) return false;

            const quantityField = row.querySelector('.cart-quantity-input, .summary-quantity');
            const quantity = parseInt(quantityField?.value || quantityField?.textContent || '0', 10);
            return !Number.isNaN(quantity) && quantity > max;
        });

        btn.disabled = hasStockIssue || !!window.__checkoutSubmitting;
        btn.classList.toggle('opacity-60', btn.disabled);
        btn.classList.toggle('cursor-not-allowed', btn.disabled);
        if (hasStockIssue) {
            btn.title = trans('Please remove out-of-stock items before placing your order.');
        } else {
            btn.removeAttribute('title');
        }
    }

    function applyStockStateToItem(itemId, maxStock) {
        const max = parseStockValue(maxStock);
        const isOutOfStock = max !== null && max <= 0;

        root.querySelectorAll('.cart-item-row[data-item-id="' + itemId + '"], .summary-item-row[data-summary-item-id="' + itemId + '"]').forEach(function (row) {
            row.dataset.maxStock = max === null ? '' : String(max);
            row.dataset.stockStatus = isOutOfStock ? 'out' : 'available';

            const input = row.querySelector('.cart-quantity-input');
            if (input) {
                if (max !== null && max > 0) {
                    input.max = String(max);
                    input.dataset.maxStock = String(max);
                    input.disabled = false;
                } else if (isOutOfStock) {
                    input.removeAttribute('max');
                    input.dataset.maxStock = '0';
                    input.disabled = true;
                    input.value = input.value || '1';
                }
            }

            row.querySelectorAll('.qty-decrease, .qty-increase, .summary-qty-decrease, .summary-qty-increase').forEach(function (btn) {
                btn.disabled = isOutOfStock;
                btn.classList.toggle('pointer-events-none', isOutOfStock);
                btn.classList.toggle('opacity-50', isOutOfStock);
            });
        });

        syncCheckoutSubmitState();
    }

    function hasBlockingStockIssue() {
        return Array.from(root.querySelectorAll('.cart-item-row, .summary-item-row')).some(function (row) {
            if (row.dataset.stockStatus === 'out') {
                return true;
            }

            const max = parseStockValue(row.dataset.maxStock);
            if (max === null) {
                return false;
            }

            if (max <= 0) {
                return true;
            }

            const quantityField = row.querySelector('.cart-quantity-input, .summary-quantity');
            const quantity = parseInt(quantityField?.value || quantityField?.textContent || '0', 10);
            return !Number.isNaN(quantity) && quantity > max;
        });
    }

    function updateForwardNavigationState() {
        const proceedButton = document.getElementById('proceedToCheckoutBtn') || root.querySelector('[data-spa-step="2"]');
        const blockedByStock = hasBlockingStockIssue();
        const countText = document.getElementById('item-total-count')?.textContent || '0';
        const itemCount = parseInt(String(countText).trim(), 10);
        const blockedByEmptyCart = Number.isNaN(itemCount) ? false : itemCount <= 0;
        const blocked = blockedByStock || blockedByEmptyCart;

        if (proceedButton) {
            proceedButton.disabled = blocked;
            proceedButton.classList.toggle('opacity-60', blocked);
            proceedButton.classList.toggle('cursor-not-allowed', blocked);
            proceedButton.classList.toggle('pointer-events-none', blocked);
            if (blocked) {
                proceedButton.removeAttribute('data-spa-step');
            } else {
                proceedButton.setAttribute('data-spa-step', '2');
            }
            if (blockedByStock) {
                proceedButton.title = trans('Please remove out-of-stock items before continuing to checkout.');
            } else if (blockedByEmptyCart) {
                proceedButton.title = trans('Your cart is empty. Add items to continue to checkout.');
            } else {
                proceedButton.removeAttribute('title');
            }
        }

        const blockMessage = document.getElementById('checkout-stock-block-message');
        if (blockMessage) {
            if (blockedByStock) {
                blockMessage.classList.remove('hidden');
                blockMessage.textContent = trans('Please remove out-of-stock items before continuing to checkout.');
            } else if (blockedByEmptyCart) {
                blockMessage.classList.remove('hidden');
                blockMessage.textContent = trans('Your cart is empty. Add items to continue to checkout.');
            } else {
                blockMessage.classList.add('hidden');
            }
        }
    }

    function syncCheckoutActionStates() {
        syncCheckoutSubmitState();
        updateForwardNavigationState();
    }

    function debounceUpdate(id, quantity) {
        if (debounceTimers[id]) clearTimeout(debounceTimers[id]);
        debounceTimers[id] = setTimeout(() => { updateItem(id, quantity); }, 500);
    }
    // ------------------------------------------

    function setFieldValue(nameOrId, value) {
        var strVal = String(value || "");
        var byId = document.getElementById(nameOrId);
        if (byId) {
            byId.value = strVal;
            byId.setAttribute("value", strVal);
        }
        var form = document.getElementById("checkout-form");
        if (form) {
            var hidden = form.querySelector('input[type="hidden"][name="' + nameOrId + '"]');
            if (hidden && hidden !== byId) {
                hidden.value = strVal;
                hidden.setAttribute("value", strVal);
            }
        }
    }

    function flashFields(ids) {
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && el.value) {
                el.style.transition = "background-color 0.3s ease";
                el.style.backgroundColor = "rgba(34, 197, 94, 0.1)";
                setTimeout(function () { el.style.backgroundColor = ""; }, 800);
            }
        });
    }

    function splitName(fullName) {
        var parts = String(fullName || "").trim().split(/\s+/).filter(Boolean);
        return { firstName: parts[0] || "", lastName: parts.slice(1).join(" ") };
    }

    function readAddressDataFromOption(opt) {
        if (!opt || !opt.value) return null;
        return {
            name:       opt.dataset.name       || "",
            street:     opt.dataset.street     || "",
            city:       opt.dataset.city       || "",
            postalCode: opt.dataset.postalCode || "",
            country:    opt.dataset.country    || "Morocco",
            phone:      opt.dataset.phone      || "",
        };
    }

    function showValidationError(element, message) {
        clearValidationErrorFor(element);
        var errorDiv = document.createElement("p");
        errorDiv.className = "validation-error mt-2 text-sm text-red-600 font-semibold flex items-center gap-1";
        errorDiv.innerHTML =
            '<svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">' +
            '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>' +
            "</svg>" + message;
        element.classList.add("border-red-500", "border-2", "bg-red-50");
        element.classList.remove("border-gray-200");
        if (element.parentElement) element.parentElement.appendChild(errorDiv);
    }

    function getValidationSummaryBox() {
        var box = document.getElementById("checkout-validation-summary");
        if (!box) {
            box = document.createElement("div");
            box.id = "checkout-validation-summary";
            box.className = "hidden mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm";
            var anchor = document.getElementById("checkout-step-content") || document.getElementById("checkout-form");
            if (anchor) {
                anchor.parentNode.insertBefore(box, anchor);
            }
        }
        return box;
    }

    function showValidationSummary(messages) {
        var box = getValidationSummaryBox();
        if (!box) return;

        if (!messages || !messages.length) {
            box.innerHTML = "";
            box.classList.add("hidden");
            return;
        }

        box.innerHTML =
            '<p class="font-semibold">' + trans('Please fix the highlighted fields before continuing.') + '</p>' +
            '<ul class="mt-2 list-disc space-y-1 pl-5 text-sm">' +
            messages.map(function (message) { return '<li>' + message + '</li>'; }).join('') +
            '</ul>';
        box.classList.remove("hidden");
        box.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    function resolveValidationTarget(field) {
        var candidates = [field, field.replace(/\./g, '_')];
        for (var i = 0; i < candidates.length; i++) {
            var candidate = candidates[i];
            var byId = document.getElementById(candidate);
            if (byId) return byId;
            var byName = document.querySelector('[name="' + candidate + '"]');
            if (byName) return byName;
        }

        if (field.indexOf('payment_method') === 0) {
            var paymentInput = document.querySelector('input[name="payment_method"]');
            return paymentInput ? paymentInput.closest('.space-y-4') || paymentInput : null;
        }

        if (field.indexOf('shipping_address.') === 0) {
            return document.getElementById('shippingAddressSection');
        }

        if (field.indexOf('billing_address.') === 0 || field.indexOf('billing_') === 0) {
            return document.getElementById('billingFields') || document.getElementById('billingAddressSection');
        }

        return null;
    }

    function renderValidationErrors(errors) {
        clearAllValidationErrors();

        var messages = [];
        if (!errors) {
            showValidationSummary(messages);
            return;
        }

        Object.entries(errors).forEach(function ([field, fieldMessages]) {
            var message = Array.isArray(fieldMessages) ? fieldMessages[0] : fieldMessages;
            if (!message) return;

            messages.push(message);
            var target = resolveValidationTarget(field);
            if (target) {
                showValidationError(target, message);
                target.setAttribute('aria-invalid', 'true');
            }
        });

        showValidationSummary(messages);

        var firstError = document.querySelector(".validation-error");
        if (firstError) {
            firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    }

    function clearValidationErrorFor(element) {
        if (!element || !element.parentElement) return;
        var existing = element.parentElement.querySelector(".validation-error");
        if (existing) existing.remove();
        element.classList.remove("border-red-500", "border-2", "bg-red-50", "focus:border-red-500", "focus:ring-red-500");
        element.classList.add("border-gray-200");
    }

    function clearAllValidationErrors() {
        document.querySelectorAll(".validation-error").forEach(function (e) { e.remove(); });
        document.querySelectorAll(".border-red-500").forEach(function (f) {
            f.classList.remove("border-red-500", "border-2", "bg-red-50", "focus:border-red-500", "focus:ring-red-500");
            f.classList.add("border-gray-200");
        });
        document.querySelectorAll("[aria-invalid='true']").forEach(function (el) {
            el.removeAttribute('aria-invalid');
        });
        showValidationSummary([]);
    }

    function updateProgressBar(step) {
        var fill = document.getElementById("progress-bar-fill");
        if (fill) {
            fill.className = fill.className.replace(/bg-linear[^\s]*/g, "").replace(/from-[^\s]*/g, "").replace(/to-[^\s]*/g, "").trim();
            if (step === 2) {
                fill.classList.add("bg-linear-to-r", "from-primary", "to-primary/80");
                fill.style.width = "calc(50% - 8%)";
                fill.style.marginLeft = "8%";
            } else if (step === 3) {
                fill.classList.add("bg-linear-to-r", "from-green-500", "to-green-400");
                fill.style.width = "calc(100% - 16%)";
                fill.style.marginLeft = "8%";
            } else {
                fill.style.width = "0";
            }
        }
        document.querySelectorAll("[data-step-circle]").forEach(function (circle) {
            var num = parseInt(circle.dataset.stepCircle);
            circle.className = circle.className.replace(/bg-linear[^\s]*/g, "").replace(/from-[^\s]*/g, "").replace(/to-[^\s]*/g, "").replace(/ring-[^\s]*/g, "").replace(/text-(?:white|gray-600)/g, "").replace(/bg-gray-200/g, "").trim();
            if (num === step) {
                circle.classList.add("bg-linear-to-br", "from-primary", "to-primary/80", "text-white", "ring-4", "ring-primary/20");
            } else if (num < step) {
                circle.classList.add("bg-linear-to-br", "from-green-500", "to-green-400", "text-white");
            } else {
                circle.classList.add("bg-gray-200", "text-gray-600");
            }
        });
        root.dataset.step = step;
    }

    async function goToStep(step) {
    // Debounce: prevent rapid multiple calls
    if (goToStepTimer) {
        clearTimeout(goToStepTimer);
    }
    goToStepTimer = setTimeout(async () => {
        const currentStep = parseInt(root.dataset.step || '1', 10) || 1;
        if (step > currentStep && hasBlockingStockIssue()) {
            showToast(trans('Please remove out-of-stock items before continuing to checkout.'), 'error');
            syncCheckoutActionStates();
            return;
        }

        var routes  = window.__checkoutRoutes || {};
        var showUrl = routes.show || window.location.pathname;
        var content = document.getElementById("checkout-step-content");
        // Show loader animation
        if (content) {
            if (!stepLoader) {
                stepLoader = document.createElement("div");
                stepLoader.className = "flex items-center justify-center py-12";
                stepLoader.innerHTML = `<svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            }
            content.innerHTML = "";
            content.appendChild(stepLoader);
        }
        try {
            var resp = await fetch(showUrl + "?step=" + step, {
                credentials: "same-origin",
                headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json", "X-CSRF-TOKEN": csrf },
            });

            if (resp.redirected && resp.url) {
                window.location.href = resp.url;
                return;
            }

            var contentType = resp.headers.get("content-type") || "";
            if (!contentType.includes("application/json")) {
                window.location.href = (root.dataset.loginUrl || "/login");
                return;
            }

            var data = await resp.json();
            if (data.redirect) { window.location.href = data.redirect; return; }
            if (data.html) {
                if (content) content.innerHTML = data.html;
                updateProgressBar(step);
                if (Object.prototype.hasOwnProperty.call(data, "ecommerce")) {
                    window.__ga4Checkout = data.ecommerce;
                }
                trackCheckoutStep(step, data.ecommerce || null);
                runStepInits();
                syncCheckoutActionStates();
                window.scrollTo({ top: 0, behavior: "smooth" });
            }
        } catch (e) {
            console.error("goToStep failed", e);
            showToast(trans('Navigation failed. Please refresh the page.'), "error");
        }
    }, 250); // 250ms debounce
}

    function validateStep2() {
        clearAllValidationErrors();
        var hasErrors = false;

        var email = document.getElementById("email");
        var phone = document.getElementById("phone");

        if (email && !email.value.trim()) { showValidationError(email, trans('Email address is required')); hasErrors = true; }
        if (phone && !phone.value.trim()) { showValidationError(phone, trans('Phone number is required.')); hasErrors = true; }

        var shippingSelect = document.getElementById("shippingAddressSelect");
        var manualFirst    = document.getElementById("firstName");

        if (shippingSelect) {
            if (!shippingSelect.value) { showValidationError(shippingSelect, trans('Please select a shipping address')); hasErrors = true; }
        } else if (manualFirst) {
            ["firstName", "lastName", "address", "city", "country"].forEach(function (id) {
                var f = document.getElementById(id);
                if (f && !f.value.trim()) { showValidationError(f, trans('This field is required.')); hasErrors = true; }
            });
        }

        var billingSame    = document.getElementById("billingSameAsShipping");
        var billingFields  = document.getElementById("billingFields");
        var billingSelect  = document.getElementById("billingAddressSelect");

        var billingVisible = billingSame ? !billingSame.checked : true;

        if (billingVisible && billingFields && !billingFields.classList.contains("hidden")) {
            if (billingSelect) {
                // Auth user with saved billing addresses - one must be selected.
                if (!billingSelect.value) {
                    showValidationError(billingSelect, trans('Please select a billing address'));
                    hasErrors = true;
                }
            } else {
                // No billingAddressSelect in the DOM means the user has zero saved
                // billing addresses. If they unchecked "same as shipping" there is
                // nothing valid to use — block unconditionally and tell them to add
                // one via the modal. Never rely on hidden-field values here because
                // they may still hold stale shipping data from the initial copy.
                var billingSection = document.getElementById("billingAddressSection");
                var anchorEl = billingFields || billingSection;
                if (anchorEl) {
                    showValidationError(anchorEl, trans('Please add a billing address, or use the same as shipping'));
                }
                var addBillingBtn = document.getElementById("addBillingAddressBtnEmpty");
                if (addBillingBtn) {
                    addBillingBtn.classList.add("ring-4", "ring-red-400", "scale-105");
                    setTimeout(function() {
                        addBillingBtn.classList.remove("ring-4", "ring-red-400", "scale-105");
                    }, 2500);
                }
                hasErrors = true;
            }
        }

        var paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) { showToast(trans('Please select a payment method.'), "error"); hasErrors = true; }

        if (hasErrors) {
            var firstError = document.querySelector(".validation-error");
            if (firstError) firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        return !hasErrors;
    }

    function copyShippingToBillingHidden() {
        var form = document.getElementById("checkout-form");
        if (!form) return;
        var map = {
            billing_firstName:    document.getElementById("firstName")?.value    || "",
            billing_lastName:     document.getElementById("lastName")?.value     || "",
            billing_address_line: document.getElementById("address")?.value      || "",
            billing_city:         document.getElementById("city")?.value         || "",
            billing_zip:          document.getElementById("zip")?.value          || "",
            billing_country:      document.getElementById("country")?.value      || "Morocco",
            billing_phone:        document.getElementById("phone")?.value        || "",
        };

        Object.entries(map).forEach(function ([name, val]) {
            var h = form.querySelector('input[type="hidden"][name="' + name + '"]');
            if (h) { h.value = val; h.setAttribute("value", val); }
        });
    }

    function syncBillingSameFlag() {
        var checkbox = document.getElementById("billingSameAsShipping");
        var form     = document.getElementById("checkout-form");
        if (!form || !checkbox) return;
        var hiddenFlag = form.querySelector('input[type="hidden"][name="billing_same_as_shipping"]');

        if (checkbox.checked) {
            if (hiddenFlag) hiddenFlag.remove();
            copyShippingToBillingHidden();
        } else {
            if (!hiddenFlag) {
                hiddenFlag = document.createElement("input");
                hiddenFlag.type  = "hidden";
                hiddenFlag.name  = "billing_same_as_shipping";
                hiddenFlag.value = "0";
                form.appendChild(hiddenFlag);
            }
        }
    }

    async function submitCheckout() {
        if (!validateStep2()) return;
        syncBillingSameFlag();

        var form = document.getElementById("checkout-form");
        if (!form) return;

        var btn = document.getElementById("placeOrderBtn");
        var originalBtnHtml = btn ? btn.innerHTML : null;
        if (btn) { btn.disabled = true; btn.textContent = trans('Processing...'); }

        var routes     = window.__checkoutRoutes || {};
        var processUrl = routes.process || form.action;

        try {
            var resp = await fetch(processUrl, {
                method: "POST", credentials: "same-origin",
                headers: { "X-CSRF-TOKEN": csrf, "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
                body: new FormData(form),
            });

            if (resp.redirected && resp.url) {
                window.location.href = resp.url;
                return;
            }

            var data = await resp.json();
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            if (data.success && data.html) {
                var content = document.getElementById("checkout-step-content");
                if (content) content.innerHTML = data.html;
                updateProgressBar(3);
                if (data.ecommerce && window.ezoneGa4) {
                    window.ezoneGa4.event("purchase", data.ecommerce);
                }
                window.__checkoutSubmitting = false;
                window.scrollTo({ top: 0, behavior: "smooth" });
            } else {
                if (data.errors) {
                    renderValidationErrors(data.errors);
                }
                if (data.html) {
                    var content = document.getElementById("checkout-step-content");
                    if (content) content.innerHTML = data.html;
                    updateProgressBar(3);
                }
                showToast(data.message || trans('Order could not be placed. Please check the form.'), "error");
            }
        } catch (e) {
            showToast(trans('A network error occurred. Please try again.'), "error");
        } finally {
            if (btn) {
                btn.disabled = false;
                if (originalBtnHtml) {
                    btn.innerHTML = originalBtnHtml;
                }
            }
            syncCheckoutActionStates();
        }
    }

    function updateSummary(summary) {
        if (!summary) return;

        const finalTot   = summary.final_total ?? summary.total ?? 0;
        const subTot     = summary.subtotal ?? 0;
        const itemDisc   = summary.total_item_discount ?? summary.item_discount ?? 0;
        const couponDisc = summary.coupon_discount ?? 0;
        const shipCost   = summary.shipping_cost ?? summary.shipping ?? 0;

        // The Blade uses IDs with "" (Step 1) and "-step2" (Step 2)
        ["", "-step2"].forEach(suffix => {
            const elSub = document.getElementById("order-subtotal" + suffix);
            if (elSub) elSub.textContent = formatMoney(subTot);

            const elDisc  = document.getElementById("order-discount" + suffix);
            const rowDisc = document.getElementById("item-discount-row" + suffix);
            if (elDisc && rowDisc) {
                if (itemDisc > 0) {
                    elDisc.textContent = "-" + formatMoney(itemDisc);
                    rowDisc.style.display = ""; // flex
                } else {
                    rowDisc.style.display = "none";
                }
            }

            const elCoup  = document.getElementById("order-coupon-discount" + suffix);
            const rowCoup = document.getElementById("coupon-discount-row" + suffix);
            if (elCoup && rowCoup) {
                if (couponDisc > 0) {
                    elCoup.textContent = "-" + formatMoney(couponDisc);
                    rowCoup.style.display = ""; // flex
                } else {
                    rowCoup.style.display = "none";
                }
            }

            const elShip = document.getElementById("order-shipping" + suffix);
            if (elShip) elShip.textContent = shipCost > 0 ? formatMoney(shipCost) : trans("FREE");

            const elTot = document.getElementById("order-total" + suffix);
            if (elTot) elTot.textContent = formatMoney(finalTot);
        });

        // Update main Item Count Span
        const countEl = document.getElementById("item-total-count");
        if (countEl && summary.itemCount !== undefined) {
            countEl.textContent = summary.itemCount + (summary.itemCount === 1 ? " Item" : " Items");
        }

        updateForwardNavigationState();
    }

    async function updateItem(itemId, quantity) {
        if (!updateUrl) return;
        try {
            var resp = await fetch(updateUrl, {
                method: "POST", credentials: "same-origin",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf, Accept: "application/json" },
                body: JSON.stringify({ item_id: itemId, quantity: Number(quantity), shipping_method: getCurrentShippingMethod() }),
            });
            var data = await resp.json();
            if (!data || !data.success) {
                if (data && data.item && data.item.max_stock !== undefined) {
                    applyStockStateToItem(itemId, data.item.max_stock);
                }
                if (data && data.message) {
                    showToast(data.message, "error");
                }
                syncCheckoutActionStates();
                return;
            }

            var lineEl = document.getElementById("item-line-total-" + itemId);
            var calculatedLineTotal = data?.item?.discount?.line_total_after_discount
                ?? data?.item?.line_total
                ?? data?.item?.line_total_before_discount;

            if (lineEl && calculatedLineTotal !== undefined && calculatedLineTotal !== null) {
                lineEl.textContent = formatMoney(calculatedLineTotal);
            }
            document.querySelectorAll('[data-summary-item-id="' + itemId + '"] .summary-item-price')
                .forEach(function (el) {
                    if (calculatedLineTotal !== undefined && calculatedLineTotal !== null) {
                        el.textContent = formatMoney(calculatedLineTotal);
                    }
                });
            document.querySelectorAll('[data-summary-item-id="' + itemId + '"] .summary-quantity')
                .forEach(function (el) { el.textContent = quantity; });

            if (data.item && data.item.max_stock !== undefined && data.item.max_stock !== null) {
                applyStockStateToItem(itemId, data.item.max_stock);
            }

            var cartRow = document.querySelector('.cart-item-row[data-item-id="' + itemId + '"]');
            if (cartRow && data.item && data.item.discount) {
                var sym   = window.env?.CURRENCY_SYMBOL || "$";
                var badge = cartRow.querySelector(".inline-flex.items-center.text-xs.font-semibold.text-green-700");
                if (data.item.discount.has_discount && data.item.discount.discount_amount > 0) {
                    var saveAmt = (data.item.discount.discount_amount * quantity).toFixed(2);
                    if (badge) {
                        var txt = badge.querySelector("svg")?.nextSibling;
                        if (txt) txt.textContent = " " + trans('Save') + " " + sym + " " + saveAmt;
                    } else {
                        var pc = cartRow.querySelector(".text-right");
                        if (pc) {
                            var nb = document.createElement("div");
                            nb.className = "inline-flex items-center text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-full mb-1";
                            nb.innerHTML = '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' + trans('Save') + ' ' + sym + ' ' + saveAmt;
                            pc.insertBefore(nb, pc.firstChild);
                        }
                    }
                } else if (badge) { badge.remove(); }
            }
            updateSummary(data.summary);
            syncCheckoutActionStates();

            if (data.summary && typeof data.summary.itemCount !== 'undefined') {
                window.dispatchEvent(new CustomEvent('cart:changed', { detail: { summary: data.summary } }));
            }
        } catch (e) { console.error("updateItem failed", e); }
    }

    async function refreshTotalsForShipping() {
        if (!updateUrl) return;
        var firstInput = document.querySelector(".cart-quantity-input[data-item-id]");
        if (firstInput) {
            await updateItem(firstInput.dataset.itemId, Math.max(1, Number(firstInput.value || 1)));
            return;
        }
        try {
            var resp = await fetch(updateUrl, {
                method: "POST", credentials: "same-origin",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf, Accept: "application/json" },
                body: JSON.stringify({ shipping_method: getCurrentShippingMethod() }),
            });
            var data = await resp.json();
            if (resp.ok && data && data.success) {
                updateSummary(data.summary);
                syncCheckoutActionStates();
            }
        } catch (e) { console.error("refreshTotalsForShipping failed", e); }
    }

    async function removeItem(itemId) {
        if (!removeUrl) return submitFallback(itemId);
        try {
            var resp = await fetch(removeUrl, {
                method: "POST", credentials: "same-origin",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf, Accept: "application/json" },
                body: JSON.stringify({ item_id: itemId, shipping_method: getCurrentShippingMethod() }),
            });
            var data = await resp.json();
            if (data && data.success) {
                var mainRow    = document.querySelector('.cart-item-row[data-item-id="' + itemId + '"]');
                var summaryRow = document.querySelector('[data-summary-item-id="' + itemId + '"]');
                if (mainRow)    mainRow.remove();
                if (summaryRow) summaryRow.remove();
                updateSummary(data.summary);

                if (data.summary && typeof data.summary.itemCount !== 'undefined') {
                    window.dispatchEvent(new CustomEvent('cart:changed', { detail: { summary: data.summary } }));
                }
                syncCheckoutActionStates();
                showToast(data.message || "Item removed from cart.", "success");
            } else {
                syncCheckoutActionStates();
                submitFallback(itemId);
            }
        } catch (e) {
            syncCheckoutActionStates();
            submitFallback(itemId);
            showToast("Failed to remove item.", "error");
        }
    }

    function submitFallback(itemId) {
        var existing = document.getElementById("remove-item-" + itemId);
        if (existing) { try { existing.submit(); } catch (e) { console.error(e); } return; }
        try {
            var form  = document.createElement("form");
            form.method = "POST";
            form.action = removeUrl || window.location.href;
            form.style.display = "none";
            form.id = "remove-item-" + itemId;

            var token = document.createElement("input");
            token.type = "hidden"; token.name = "_token"; token.value = csrf;
            form.appendChild(token);

            var iid = document.createElement("input");
            iid.type = "hidden"; iid.name = "item_id"; iid.value = itemId;
            form.appendChild(iid);

            document.body.appendChild(form);
            form.submit();
        } catch (e) { console.error("submitFallback failed", e); }
    }

    function initShippingAddressSelect() {
        var sel = document.getElementById("shippingAddressSelect");
        if (!sel || sel.dataset.bound) return;
        sel.dataset.bound = "1";

        sel.addEventListener("change", function () {
            var addr    = readAddressDataFromOption(this.options[this.selectedIndex]);
            var display = document.getElementById("selectedShippingDisplay");

            if (!addr || !addr.name) {
                ["firstName","lastName","address","city","zip","country"].forEach(function (id) {
                    setFieldValue(id, "");
                });
                if (display) display.classList.add("hidden");
                return;
            }

            var n = splitName(addr.name);
            setFieldValue("firstName", n.firstName);
            setFieldValue("lastName",  n.lastName);
            setFieldValue("address",   addr.street);
            setFieldValue("city",      addr.city);
            setFieldValue("zip",       addr.postalCode);
            setFieldValue("country",   addr.country);
            setFieldValue("phone",     addr.phone);

            if (display) {
                var nameEl   = document.getElementById("displayShippingName");
                var streetEl = document.getElementById("displayShippingStreet");
                var cityEl   = document.getElementById("displayShippingCity");
                if (nameEl)   nameEl.textContent   = addr.name;
                if (streetEl) streetEl.textContent = addr.street;
                if (cityEl)   cityEl.textContent   = [addr.city, addr.postalCode].filter(Boolean).join(", ");
                display.classList.remove("hidden");
            }

            flashFields(["firstName", "lastName", "address", "city", "zip", "country"]);

            var sameChk = document.getElementById("billingSameAsShipping");
            if (sameChk && sameChk.checked) {
                setTimeout(copyShippingToBillingHidden, 50);
            }
        });

        var manualIds = ["firstName", "lastName", "address", "city", "zip", "country"];
        manualIds.forEach(function (id) {
            var input = document.getElementById(id);
            if (input) input.required = false;
        });
        sel.required = true;
        if (sel.value) sel.dispatchEvent(new Event("change"));
    }

    function initBillingAddressSelect() {
        var sel = document.getElementById("billingAddressSelect");
        if (!sel || sel.dataset.bound) return;
        sel.dataset.bound = "1";

        sel.addEventListener("change", function () {
            var addr    = readAddressDataFromOption(this.options[this.selectedIndex]);
            var display = document.getElementById("selectedBillingDisplay");
            var form    = document.getElementById("checkout-form");

            if (!addr || !addr.name) {
                if (display) display.classList.add("hidden");
                clearBillingHiddenFields();
                return;
            }

            var n = splitName(addr.name);
            var billingMap = {
                billing_firstName:    n.firstName,
                billing_lastName:     n.lastName,
                billing_address_line: addr.street,
                billing_city:         addr.city,
                billing_zip:          addr.postalCode,
                billing_country:      addr.country,
                billing_phone:        addr.phone,
            };

            Object.entries(billingMap).forEach(function ([name, val]) {
                if (form) {
                    var h = form.querySelector('input[type="hidden"][name="' + name + '"]');
                    if (h) { h.value = val; h.setAttribute("value", val); }
                }
                var vis = document.getElementById(name);
                if (vis && vis.type !== "hidden") { vis.value = val; }
            });

            if (display) {
                var nameEl   = document.getElementById("displayBillingName");
                var streetEl = document.getElementById("displayBillingStreet");
                var cityEl   = document.getElementById("displayBillingCity");
                if (nameEl)   nameEl.textContent   = addr.name;
                if (streetEl) streetEl.textContent = addr.street;
                if (cityEl)   cityEl.textContent   = [addr.city, addr.postalCode].filter(Boolean).join(", ");
                display.classList.remove("hidden");
            }
        });

        if (sel.value) sel.dispatchEvent(new Event("change"));
    }

    function clearBillingHiddenFields() {
        var form = document.getElementById("checkout-form");
        if (!form) return;
        ["billing_firstName","billing_lastName","billing_address_line", "billing_city","billing_zip","billing_country","billing_phone"].forEach(function (name) {
            var h = form.querySelector('input[type="hidden"][name="' + name + '"]');
            if (h) { h.value = ""; h.setAttribute("value", ""); }
        });
    }

    function initBillingToggle() {
        var checkbox = document.getElementById("billingSameAsShipping");
        var fields   = document.getElementById("billingFields");
        if (!checkbox || !fields || checkbox.dataset.bound) return;
        checkbox.dataset.bound = "1";

        function applyToggle() {
            var same = checkbox.checked;
            fields.classList.toggle("hidden", same);

            if (same) {
                // Checkbox checked: copy shipping data into billing hidden fields
                // and remove any stale "billing_same_as_shipping=0" flag.
                copyShippingToBillingHidden();
                var form = document.getElementById("checkout-form");
                if (form) {
                    var hiddenFlag = form.querySelector('input[type="hidden"][name="billing_same_as_shipping"]');
                    if (hiddenFlag) hiddenFlag.remove();
                }
            } else {
                // Checkbox unchecked: wipe billing hidden fields so stale shipping
                // data cannot silently pass validation or be submitted as billing.
                clearBillingHiddenFields();
            }
        }
        checkbox.addEventListener("change", applyToggle);
        setTimeout(applyToggle, 150);
    }

    function initPaymentMethodToggle() {
        document.querySelectorAll('input[name="payment_method"]').forEach(function (r) {
            if (!r.dataset.bound) {
                r.dataset.bound = "1";
                r.addEventListener("change", syncCardDetailsVisibility);
            }
        });
    }

    function syncCardDetailsVisibility() {
        var sel     = document.querySelector('input[name="payment_method"]:checked')?.value;
        var details = document.getElementById("cardDetails");
        if (!details) return;
        details.style.display = sel === "card" ? "" : "none";
        const inputs = details.querySelectorAll("input, select");
        inputs.forEach((input) => {
            if (sel === "card") input.setAttribute("required", "required");
            else input.removeAttribute("required");
        });
    }

    function runStepInits() {
        initShippingAddressSelect();
        initBillingAddressSelect();
        initBillingToggle();
        initPaymentMethodToggle();
        syncCardDetailsVisibility();
    }

    // --- Click Listeners (Injected Max Stock) ---
    root.addEventListener("click", function (e) {
        var spaBtn = e.target.closest("[data-spa-step]");
        if (spaBtn) { e.preventDefault(); goToStep(parseInt(spaBtn.dataset.spaStep, 10)); return; }

        if (e.target.closest("#placeOrderBtn")) { e.preventDefault(); submitCheckout(); return; }

        var qtyInc = e.target.closest(".qty-increase");
        if (qtyInc) {
            var id    = qtyInc.dataset.itemId;
            var input = root.querySelector('.cart-quantity-input[data-item-id="' + id + '"]');
            if (!input) return;
            var max    = getMaxStockForItem(id);
            var newVal = Number(input.value || 0) + 1;
            if (max) newVal = Math.min(newVal, max);
            input.value = newVal;
            if (max && newVal === max) showStockLimitMessage(max);
            debounceUpdate(id, newVal);
            return;
        }

        var qtyDec = e.target.closest(".qty-decrease");
        if (qtyDec) {
            var id    = qtyDec.dataset.itemId;
            var input = root.querySelector('.cart-quantity-input[data-item-id="' + id + '"]');
            if (!input) return;
            var newVal = Math.max(1, Number(input.value || 1) - 1);
            input.value = newVal;
            debounceUpdate(id, newVal);
            return;
        }

        var sumInc = e.target.closest(".summary-qty-increase");
        if (sumInc) {
            if (root.dataset.step === '3') return;
            var id   = sumInc.dataset.itemId;
            var row  = root.querySelector('[data-summary-item-id="' + id + '"]');
            var span = row ? row.querySelector(".summary-quantity") : null;
            if (!span) return;
            var max    = getMaxStockForItem(id);
            var newVal = Number(span.textContent || 1) + 1;
            if (max) newVal = Math.min(newVal, max);
            span.textContent = newVal;
            var mi = root.querySelector('.cart-quantity-input[data-item-id="' + id + '"]');
            if (mi) mi.value = newVal;
            if (max && newVal === max) showStockLimitMessage(max);
            debounceUpdate(id, newVal);
            return;
        }

        var sumDec = e.target.closest(".summary-qty-decrease");
        if (sumDec) {
            if (root.dataset.step === '3') return;
            var id   = sumDec.dataset.itemId;
            var row  = root.querySelector('[data-summary-item-id="' + id + '"]');
            var span = row ? row.querySelector(".summary-quantity") : null;
            if (!span) return;
            var newVal = Math.max(1, Number(span.textContent || 1) - 1);
            span.textContent = newVal;
            var mi = root.querySelector('.cart-quantity-input[data-item-id="' + id + '"]');
            if (mi) mi.value = newVal;
            debounceUpdate(id, newVal);
            return;
        }

        var removeBtn = e.target.closest(".remove-item-btn, .remove-item-ajax, .summary-remove-item-btn");
        if (removeBtn && removeBtn.dataset.itemId) { removeItem(removeBtn.dataset.itemId); return; }
    });

    // --- Input Listener for Real-Time Quantity Debouncing ---
    root.addEventListener("input", function (e) {
        if (e.target.classList.contains("cart-quantity-input")) {
            const id = e.target.dataset.itemId;
            const max = getMaxStockForItem(id);
            const raw = String(e.target.value || "");
            if (raw === "") {
                return;
            }

            const val = clampQuantity(raw, max);
            e.target.value = String(val);
            if (max && val === max) showStockLimitMessage(max);
            debounceUpdate(id, val);
        }
    });

    root.addEventListener("focusin", function (e) {
        if (e.target.classList && e.target.classList.contains("cart-quantity-input")) {
            selectQuantityInputText(e.target);
        }
    });

    root.addEventListener("blur", function (e) {
        if (e.target.classList && e.target.classList.contains("cart-quantity-input")) {
            const id = e.target.dataset.itemId;
            const max = getMaxStockForItem(id);
            const val = clampQuantity(e.target.value, max);
            e.target.value = String(val);
            if (max && val === max) showStockLimitMessage(max);
            debounceUpdate(id, val);
        }
    }, true);

    // --- Change Listeners ---
    root.addEventListener("change", function (e) {
        if (e.target.name === "shipping_method") { refreshTotalsForShipping(); return; }
        if (e.target.name === "payment_method")  { syncCardDetailsVisibility(); trackAddPaymentInfo(); return; }
    });

    var allowNavigation = false;

    function isCouponApplied() {
        return !!document.querySelector('[id^="applied-coupon-display"], [id^="remove-coupon-btn"]');
    }

    window.addEventListener("beforeunload", function (e) {
        if (isCouponApplied() && !allowNavigation && !window.__checkoutSubmitting) {
            e.preventDefault(); e.returnValue = "";
        }
    });

    document.addEventListener("submit", function (e) {
        if (e.target instanceof HTMLFormElement && window.__checkoutRoutes && e.target.action === window.__checkoutRoutes.process) {
            allowNavigation = true;
            window.__checkoutSubmitting = true;
        }
    }, true);

    document.addEventListener("click", function (e) {
        var link = e.target.closest("a[href]");
        if (!link || !isCouponApplied() || allowNavigation) return;
        var href = link.getAttribute("href");
        if (!href || href === "#" || href.startsWith("#") || href === window.location.pathname) return;
        e.preventDefault();
        if (typeof showUniversalPopup === "function") {
                showUniversalPopup(
                trans('You have a coupon applied. Leave without completing your purchase?'),
                "error", 0,
                {
                    confirmation: true, confirmText: trans('Yes, Leave'), cancelText: trans('No, Stay'),
                    onConfirm: function () { allowNavigation = true; window.location.href = href; },
                }
            );
        }
    }, true);

    function trackCheckoutStep(step, payload) {
        try {
            if (!payload || !window.ezoneGa4) return;
            if (step === 1) window.ezoneGa4.event("view_cart", payload);
            if (step === 2) window.ezoneGa4.event("begin_checkout", payload);
        } catch (e) {}
    }

    function trackAddPaymentInfo() {
        try {
            if (String(root.dataset.step) !== "2") return;
            if (!window.__ga4Checkout || !window.ezoneGa4) return;
            var method = document.querySelector('input[name="payment_method"]:checked');
            window.ezoneGa4.event("add_payment_info", Object.assign({}, window.__ga4Checkout, {
                payment_type: method ? method.value : "",
            }));
        } catch (e) {}
    }

    runStepInits();
    syncCheckoutActionStates();
    trackCheckoutStep(parseInt(root.dataset.step || "1", 10), window.__ga4Checkout || null);
    if (window.__ga4Purchase && window.ezoneGa4) {
        window.ezoneGa4.event("purchase", window.__ga4Purchase);
    }
});

// ═══════════════════════════════════════════════════════════════
//  ADD ADDRESS MODAL & AJAX LOGIC
// ═══════════════════════════════════════════════════════════════
// The modal lives permanently in the DOM (outside #checkout-root),
// so close/submit/keydown listeners are bound once at load time.
// The open-buttons (#addShippingAddressBtn etc.) live INSIDE
// #checkout-step-content which is replaced by goToStep() — we use
// event delegation on document so clicks always reach the handler
// regardless of when the buttons appear.
(function() {
    const modal     = document.getElementById('addAddressModal');
    const closeBtn  = document.getElementById('closeAddressModal');
    const cancelBtn = document.getElementById('cancelAddressModal');
    const form      = document.getElementById('addAddressForm');
    const typeField = document.getElementById('modal_address_type');
    const cityField = document.getElementById('modal_city_id');
    const countryField = document.getElementById('modal_country');
    const submitBtn = document.getElementById('submitAddressBtn');

    if (!modal) return;

    // ── Open ──────────────────────────────────────────────────────
    const openModalWithType = function(type) {
        if (form)      form.reset();
        if (typeField) typeField.value = type;
        if (countryField && !countryField.value) countryField.value = 'Morocco';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        const content = document.getElementById('addAddressContent');
        if (content) {
            // next tick so the transition fires after display:flex is applied
            setTimeout(function() {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    };

    // Delegated click handler — works even after goToStep() replaces the DOM
    document.addEventListener('click', function(e) {
        var btn = e.target.closest(
            '#addShippingAddressBtn, #addShippingAddressBtnEmpty, ' +
            '#addBillingAddressBtn,  #addBillingAddressBtnEmpty'
        );
        if (!btn) return;
        e.preventDefault();
        var type = (btn.id === 'addBillingAddressBtn' || btn.id === 'addBillingAddressBtnEmpty')
            ? 'billing'
            : 'shipping';
        openModalWithType(type);
    });

    // ── Close ─────────────────────────────────────────────────────
    const closeModal = function() {
        const content = document.getElementById('addAddressContent');
        if (content) {
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
        }
        setTimeout(function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            if (form) form.reset();
        }, 200);
    };

    if (closeBtn)  closeBtn.addEventListener('click',  closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    // ── City → Country auto-fill ──────────────────────────────────
    if (cityField && countryField) {
        cityField.addEventListener('change', function() {
            var option  = this.options[this.selectedIndex];
            var country = option ? option.getAttribute('data-country') : '';
            if (country) countryField.value = country;
        });
    }

    // ── Form submit (AJAX save) ───────────────────────────────────
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            var isValid = true;
            form.querySelectorAll('[name="type"], [name="name"], [name="street"], [name="city_id"], [name="country"]')
                .forEach(function(field) {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500', 'bg-red-50');
                        isValid = false;
                    } else {
                        field.classList.remove('border-red-500', 'bg-red-50');
                    }
                });
            if (!isValid) return;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="inline-flex items-center">' +
                    '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                    '</svg>Saving...</span>';
            }

            var formData = new FormData(form);
            var data = {
                _token:        formData.get('_token'),
                type:          formData.get('type'),
                address_label: formData.get('address_label'),
                name:          formData.get('name'),
                street:        formData.get('street'),
                city_id:       formData.get('city_id'),
                postal_code:   formData.get('postal_code'),
                country:       formData.get('country'),
                phone:         formData.get('phone'),
                is_default:    formData.get('is_default') ? 1 : 0
            };

            var resetSubmitBtn = function() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        '<span class="inline-flex items-center">' +
                        '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' +
                        '</svg>Save Address</span>';
                }
            };

            fetch('/client/addresses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success || result.data) {
                    closeModal();
                    if (window.Toast) {
                        window.Toast.success({ message: trans('Address saved successfully!') });
                    } else {
                        alert(trans('Address saved successfully!') + ' ' + trans('The page will reload.'));
                    }
                    window.location.reload();
                } else {
                    alert(trans('Error saving address.') + ': ' + (result.message || trans('Unknown error')));
                    resetSubmitBtn();
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                if (typeof showUniversalPopup === 'function') {
                    showUniversalPopup(trans('Error saving address. Please try again.'), 'error', 3000);
                } else {
                    alert(trans('Error saving address. Please try again.'));
                }
                resetSubmitBtn();
            });
        });
    }
})();

// ═══════════════════════════════════════════════════════════════
//  REAL-TIME INPUT FORMATTING (Card Data)
// ═══════════════════════════════════════════════════════════════
(function() {
    // Format card number with spaces every 4 digits
    document.addEventListener('input', function(e) {
        if (e.target.id === 'cardNumber' || e.target.name === 'cardNumber') {
            let value = e.target.value.replace(/\s/g, '');
            e.target.value = value.match(/.{1,4}/g)?.join(' ') || value;
        }

        // Format expiry MM/YY automatically
        if (e.target.id === 'expiry' || e.target.name === 'expiry') {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) value = value.slice(0, 2) + '/' + value.slice(2, 4);
            e.target.value = value;
        }
    });
})();
