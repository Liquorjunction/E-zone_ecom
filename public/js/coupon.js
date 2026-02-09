/**
 * Client-side coupon functionality
 * Uses vanilla JavaScript and axios (no jQuery dependency)
 * Supports multiple coupon forms on different checkout steps
 */

document.addEventListener('DOMContentLoaded', function() {
    const applyCouponUrl = '/cart/apply-coupon';
    const removeCouponUrl = '/cart/remove-coupon';

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Ensure axios is available
    if (typeof axios === 'undefined') {
        console.error('Coupon JS: axios is not loaded');
        return;
    }

    // Define coupon form variants (for different checkout steps)
    // const couponForms = [
    //     { prefix: '', container: 'coupon-container' },           // Step 1
    //     { prefix: '-step2', container: 'coupon-container-step2' }, // Step 2
    //     { prefix: '-step3', container: 'coupon-container-step3' }  // Step 3
    // ];

    // Apply coupon - handle all variants
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.id === 'apply-coupon-btn' || e.target.id === 'apply-coupon-btn-step2' || e.target.id === 'apply-coupon-btn-step3')) {
            e.preventDefault();

            // Determine which form variant we're dealing with
            let prefix = '';
            if (e.target.id === 'apply-coupon-btn-step2') prefix = '-step2';
            if (e.target.id === 'apply-coupon-btn-step3') prefix = '-step3';

            const btn = e.target;
            const input = document.getElementById('coupon-code-input' + prefix);
            const code = input?.value.trim().toUpperCase();
            const messageEl = document.getElementById('coupon-message' + prefix);

            if (!code) {
                showMessage('Please enter a coupon code.', 'error', messageEl);
                return;
            }

            // Disable button and show loading
            btn.disabled = true;
            btn.textContent = 'Applying...';
            messageEl?.classList.remove('hidden', 'text-green-600', 'text-red-600');

            axios.post(applyCouponUrl, {
                code: code
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-SKIP-TOAST': 'true'
                }
            })
            .then(function(response) {
                if (response.data.success) {
                    showMessage(response.data.message, 'success', messageEl);
                    showUniversalPopup(response.data.message, 'success', 2000);
                    updateOrderSummary(response.data.summary);

                    // Replace input form with applied coupon display
                    setTimeout(function() {
                        const appliedHtml = `
                            <div class="bg-white rounded-xl p-4 border-2 border-purple-200" id="applied-coupon-display${prefix}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">${response.data.coupon.code}</p>
                                            <p class="text-xs text-gray-600">Coupon Applied</p>
                                        </div>
                                    </div>
                                    <button type="button" id="remove-coupon-btn${prefix}" class="text-red-500 hover:text-red-700 transition-colors px-3 py-1 rounded-lg hover:bg-red-50 text-sm font-semibold">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        `;
                        const inputForm = document.getElementById('coupon-input-form' + prefix);
                        if (inputForm) {
                            inputForm.outerHTML = appliedHtml;
                        }
                    }, 1000);
                } else {
                    showMessage(response.data.message, 'error', messageEl);
                    btn.disabled = false;
                    btn.textContent = 'Apply';
                }
            })
            .catch(function(error) {
                let message = 'Failed to apply coupon. Please try again.';
                if (error.response && error.response.data && error.response.data.message) {
                    message = error.response.data.message;
                }
                showMessage(message, 'error', messageEl);
                btn.disabled = false;
                btn.textContent = 'Apply';
            });
        }
    });

    // Remove coupon - handle all variants
    document.addEventListener('click', function(e) {
        const btnId = e.target?.id || '';
        if (btnId === 'remove-coupon-btn' || btnId === 'remove-coupon-btn-step2' || btnId === 'remove-coupon-btn-step3') {
            e.preventDefault();

            // Determine which form variant we're dealing with
            let prefix = '';
            if (btnId === 'remove-coupon-btn-step2') prefix = '-step2';
            if (btnId === 'remove-coupon-btn-step3') prefix = '-step3';

            const btn = e.target;
            btn.disabled = true;
            btn.textContent = 'Removing...';

            axios.post(removeCouponUrl, {}, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-SKIP-TOAST': 'true'
                }
            })
            .then(function(response) {
                if (response.data.success) {
                    const messageEl = document.getElementById('coupon-message' + prefix);
                    showMessage(response.data.message, 'success', messageEl);
                    showUniversalPopup(response.data.message, 'success', 2000);
                    updateOrderSummary(response.data.summary);

                    // Replace applied coupon display with input form
                    setTimeout(function() {
                        const inputHtml = `
                            <div class="flex gap-3" id="coupon-input-form${prefix}">
                                <input type="text" id="coupon-code-input${prefix}" placeholder="Enter coupon code" class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 bg-white shadow-sm uppercase">
                                <button type="button" id="apply-coupon-btn${prefix}" class="px-6 py-3 bg-linear-to-r from-primary to-primary/80 text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">Apply</button>
                            </div>
                        `;
                        const appliedDisplay = document.getElementById('applied-coupon-display' + prefix);
                        if (appliedDisplay) {
                            appliedDisplay.outerHTML = inputHtml;
                        }
                    }, 1000);
                } else {
                    const messageEl = document.getElementById('coupon-message' + prefix);
                    showMessage(response.data.message, 'error', messageEl);
                    btn.disabled = false;
                    btn.textContent = 'Remove';
                }
            })
            .catch(function() {
                const messageEl = document.getElementById('coupon-message' + prefix);
                showMessage('Failed to remove coupon. Please try again.', 'error', messageEl);
                btn.disabled = false;
                btn.textContent = 'Remove';
            });
        }
    });

    // Enter key support for coupon input (all variants)
    document.addEventListener('keypress', function(e) {
        if (e.target && (e.target.id === 'coupon-code-input' || e.target.id === 'coupon-code-input-step2' || e.target.id === 'coupon-code-input-step3') && e.key === 'Enter') {
            e.preventDefault();
            let btnId = 'apply-coupon-btn';
            if (e.target.id === 'coupon-code-input-step2') btnId = 'apply-coupon-btn-step2';
            if (e.target.id === 'coupon-code-input-step3') btnId = 'apply-coupon-btn-step3';
            const btn = document.getElementById(btnId);
            if (btn) btn.click();
        }
    });

    // Helper: Show message
    function showMessage(text, type, messageEl) {
        if (!messageEl) return;

        messageEl.classList.remove('hidden', 'text-green-600', 'text-red-600', 'text-gray-600');

        if (type === 'success') {
            messageEl.classList.add('text-green-600');
        } else if (type === 'error') {
            messageEl.classList.add('text-red-600');
        } else {
            messageEl.classList.add('text-gray-600');
        }

        messageEl.textContent = text;
        messageEl.classList.remove('hidden');

        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                messageEl.classList.add('hidden');
            }, 5000);
        }
    }

    // Helper: Update order summary
    function updateOrderSummary(summary) {
        if (!summary) return;

        // Update subtotal
        if (summary.subtotal !== undefined) {
            const subtotalEl = document.getElementById('order-subtotal');
            if (subtotalEl) {
                subtotalEl.textContent = '$' + parseFloat(summary.subtotal).toFixed(2);
            }
        }

        // Update item discount section
        if (summary.item_discount !== undefined && summary.item_discount > 0) {
            const discountEl = document.getElementById('order-discount');
            if (discountEl) {
                discountEl.textContent = '-$' + parseFloat(summary.item_discount).toFixed(2);
            }

            const subtotalAfterEl = document.getElementById('order-subtotal-after-discount');
            if (subtotalAfterEl) {
                subtotalAfterEl.textContent = '$' + parseFloat(summary.subtotal_after_item_discount).toFixed(2);
            }
        }

        // Update or create coupon discount section
        if (summary.coupon_discount !== undefined && summary.coupon_discount > 0) {
            let couponDiscountEl = document.getElementById('order-coupon-discount');

            if (couponDiscountEl) {
                // Update existing
                couponDiscountEl.textContent = '-$' + parseFloat(summary.coupon_discount).toFixed(2);
            } else {
                // Create new coupon discount row
                const couponHtml = `
                    <div class="flex justify-between items-center text-purple-600 text-lg bg-purple-50 px-4 py-3 rounded-xl" id="coupon-discount-row">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            <span class="font-semibold">Coupon Discount</span>
                        </div>
                        <span id="order-coupon-discount" class="font-bold">-$${parseFloat(summary.coupon_discount).toFixed(2)}</span>
                    </div>
                `;

                // Insert after subtotal after discount or item discount
                const subtotalAfterEl = document.getElementById('order-subtotal-after-discount');
                const discountEl = document.getElementById('order-discount');

                if (subtotalAfterEl) {
                    subtotalAfterEl.closest('div').insertAdjacentHTML('afterend', couponHtml);
                } else if (discountEl) {
                    discountEl.closest('div').insertAdjacentHTML('afterend', couponHtml);
                } else {
                    const subtotalEl = document.getElementById('order-subtotal');
                    if (subtotalEl) {
                        subtotalEl.closest('div').insertAdjacentHTML('afterend', couponHtml);
                    }
                }
            }
        } else {
            // Remove coupon discount row if discount is 0
            const couponRow = document.getElementById('coupon-discount-row');
            if (couponRow) {
                couponRow.remove();
            }
        }

        // Update tax
        if (summary.tax !== undefined) {
            const taxEl = document.getElementById('order-tax');
            if (taxEl) {
                taxEl.textContent = '$' + parseFloat(summary.tax).toFixed(2);
            }
        }

        // Update shipping
        if (summary.shipping !== undefined) {
            const shippingText = summary.shipping > 0 ? '$' + parseFloat(summary.shipping).toFixed(2) : 'FREE';
            const shippingEl = document.getElementById('order-shipping');
            if (shippingEl) {
                shippingEl.textContent = shippingText;
            }
        }

        // Update total
        if (summary.total !== undefined) {
            const totalEl = document.getElementById('order-total');
            if (totalEl) {
                totalEl.textContent = '$' + parseFloat(summary.total).toFixed(2);
            }
        }
    }
});
