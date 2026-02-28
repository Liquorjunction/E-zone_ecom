/**
 * Address Dropdown Handler for Checkout Process
 * Handles dropdown selection and populates hidden form fields
 */

document.addEventListener('DOMContentLoaded', function() {
    // Shipping Address Dropdown Handler
    const shippingSelect = document.getElementById('shippingAddressSelect');
    const shippingDisplay = document.getElementById('selectedShippingDisplay');

    if (shippingSelect) {
        shippingSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (!option || !option.dataset.addr) {
                clearShippingFields();
                if (shippingDisplay) shippingDisplay.classList.add('hidden');
                return;
            }
            try {
                const addr = JSON.parse(option.dataset.addr);
                const nameParts = addr.name ? addr.name.split(' ') : ['', ''];
                const firstName = nameParts[0] || '';
                const lastName = nameParts.slice(1).join(' ') || '';
                setFieldValue('firstName', firstName);
                setFieldValue('lastName', lastName);
                setFieldValue('address', addr.street || '');
                setFieldValue('city', addr.city || '');
                setFieldValue('state', addr.state || '');
                setFieldValue('zip', addr.postal_code || '');
                setFieldValue('country', addr.country || 'Morocco');
                if (shippingDisplay) {
                    document.getElementById('displayShippingName').textContent = addr.name || '';
                    document.getElementById('displayShippingStreet').textContent = addr.street || '';
                    document.getElementById('displayShippingCity').textContent = `${addr.city || ''}, ${addr.state || ''} ${addr.postal_code || ''}`;
                    shippingDisplay.classList.remove('hidden');
                }
                // If billing same as shipping is checked, copy the shipping data immediately
                const billingSameCheckbox = document.getElementById('billingSameAsShipping');
                if (billingSameCheckbox && billingSameCheckbox.checked) {
                    setTimeout(() => {
                        setFieldValue('billing_firstName', firstName);
                        setFieldValue('billing_lastName', lastName);
                        setFieldValue('billing_address_line', addr.street || '');
                        setFieldValue('billing_city', addr.city || '');
                        setFieldValue('billing_state', addr.state || '');
                        setFieldValue('billing_zip', addr.postal_code || '');
                        setFieldValue('billing_country', addr.country || 'Morocco');
                    }, 50);
                }
            } catch (e) {
                console.error('Error parsing shipping address:', e);
            }
        });
        // Trigger change on load if default is selected
        if (shippingSelect.value) {
            shippingSelect.dispatchEvent(new Event('change'));
        }
    }

    // Removed logic for toggling manual address fields and Add/Use Different Address button


    // Billing Address Dropdown Handler
    const billingSelect = document.getElementById('billingAddressSelect');
    const billingDisplay = document.getElementById('selectedBillingDisplay');

    if (billingSelect) {
        billingSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (!option || !option.dataset.addr) {
                // Clear fields if no valid selection
                clearBillingFields();
                if (billingDisplay) billingDisplay.classList.add('hidden');
                return;
            }

            try {
                const addr = JSON.parse(option.dataset.addr);

                // Parse name into first and last
                const nameParts = addr.name ? addr.name.split(' ') : ['', ''];
                const firstName = nameParts[0] || '';
                const lastName = nameParts.slice(1).join(' ') || '';

                // Populate hidden fields for form submission
                setFieldValue('billing_firstName', firstName);
                setFieldValue('billing_lastName', lastName);
                setFieldValue('billing_address_line', addr.street || '');
                setFieldValue('billing_city', addr.city || '');
                setFieldValue('billing_state', addr.state || '');
                setFieldValue('billing_zip', addr.postal_code || '');
                setFieldValue('billing_country', addr.country || 'Morocco');
                setFieldValue('billing_phone', addr.phone || '');

                // Update selected address display
                if (billingDisplay) {
                    document.getElementById('displayBillingName').textContent = addr.name || '';
                    document.getElementById('displayBillingStreet').textContent = addr.street || '';
                    document.getElementById('displayBillingCity').textContent = `${addr.city || ''}, ${addr.state || ''} ${addr.postal_code || ''}`;
                    billingDisplay.classList.remove('hidden');
                }
            } catch (e) {
                console.error('Error parsing billing address:', e);
            }
        });

        // Trigger change on load if default is selected
        if (billingSelect.value) {
            billingSelect.dispatchEvent(new Event('change'));
        }
    }

    // Billing Same as Shipping Checkbox Handler
    const billingSameCheckbox = document.getElementById('billingSameAsShipping');
    const billingFields = document.getElementById('billingFields');

    if (billingSameCheckbox && billingFields) {
        // Function to copy shipping to billing
        function copyShippingToBilling() {
            const shippingData = {
                firstName: document.getElementById('firstName')?.value || '',
                lastName: document.getElementById('lastName')?.value || '',
                address: document.getElementById('address')?.value || '',
                city: document.getElementById('city')?.value || '',
                state: document.getElementById('state')?.value || '',
                zip: document.getElementById('zip')?.value || '',
                country: document.getElementById('country')?.value || 'Morocco',
                phone: document.getElementById('phone')?.value || ''
            };

            setFieldValue('billing_firstName', shippingData.firstName);
            setFieldValue('billing_lastName', shippingData.lastName);
            setFieldValue('billing_address_line', shippingData.address);
            setFieldValue('billing_city', shippingData.city);
            setFieldValue('billing_state', shippingData.state);
            setFieldValue('billing_zip', shippingData.zip);
            setFieldValue('billing_country', shippingData.country);
            setFieldValue('billing_phone', shippingData.phone);
        }

        billingSameCheckbox.addEventListener('change', function() {
            if (this.checked) {
                billingFields.classList.add('hidden');
                // Copy shipping to billing when checkbox is checked
                copyShippingToBilling();
                if (billingDisplay) billingDisplay.classList.add('hidden');
            } else {
                billingFields.classList.remove('hidden');
                // Clear billing fields when unchecked
                clearBillingFields();
            }
        });

        // Trigger on page load
        if (billingSameCheckbox.checked) {
            billingFields.classList.add('hidden');
            // Copy shipping to billing on page load if checkbox is checked
            setTimeout(() => {
                copyShippingToBilling();
            }, 100); // Small delay to ensure shipping address is loaded
        }
    }

    // Form Validation on Submit
    const checkoutForm = document.querySelector('form[action*="checkout.process"]');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Clear previous validation errors
            clearValidationErrors();

            let hasErrors = false;

            // Only validate if we're on step 2 (shipping/billing address fields exist)
            const isStep2 = shippingSelect && shippingSelect.offsetParent !== null;

            // --- FIX: Added the missing IF statement here ---
            if (isStep2) {
                const shippingData = {
                    firstName: document.getElementById('firstName')?.value || '',
                    lastName: document.getElementById('lastName')?.value || '',
                    address: document.getElementById('address')?.value || '',
                    city: document.getElementById('city')?.value || '',
                    state: document.getElementById('state')?.value || '',
                    zip: document.getElementById('zip')?.value || '',
                    country: document.getElementById('country')?.value || 'Morocco',
                    phone: document.getElementById('phone')?.value || ''
                };

                // Only copy if shipping has actual data
                if (shippingData.firstName && shippingData.lastName && shippingData.address) {
                    setFieldValue('billing_firstName', shippingData.firstName);
                    setFieldValue('billing_lastName', shippingData.lastName);
                    setFieldValue('billing_address_line', shippingData.address);
                    setFieldValue('billing_city', shippingData.city);
                    setFieldValue('billing_state', shippingData.state);
                    setFieldValue('billing_zip', shippingData.zip);
                    setFieldValue('billing_country', shippingData.country);
                    setFieldValue('billing_phone', shippingData.phone);
                } else {
                    showValidationError(shippingSelect, 'Please select a shipping address first');
                    hasErrors = true;
                }
            }
            // --- End FIX ---

            // Validate shipping address
            if (shippingSelect && !shippingSelect.value) {
                showValidationError(shippingSelect, 'Please select a shipping address');
                hasErrors = true;
            }

            // Validate billing address (if not same as shipping)
            if (billingSameCheckbox && !billingSameCheckbox.checked) {
                if (billingSelect && !billingSelect.value) {
                    showValidationError(billingSelect, 'Please select a billing address');
                    hasErrors = true;
                }
            }

            // Validate required fields
            const requiredFields = ['email', 'phone'];
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && !field.value.trim()) {
                    showValidationError(field, `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`);
                    hasErrors = true;
                }
            });


            if (hasErrors) {
                e.preventDefault();

                // Show error notification banner
                showErrorNotification('Please fix the errors below before continuing');

                // Scroll to first error
                const firstError = document.querySelector('.validation-error');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });
    }

    // Helper Functions
    function setFieldValue(fieldId, value) {
        const field = document.getElementById(fieldId);
        if (field) {
            // Ensure value is always a string
            const stringValue = String(value || '');
            field.value = stringValue;
            // Set the attribute as well to ensure it's in the DOM
            field.setAttribute('value', stringValue);
        }
    }

    function clearShippingFields() {
        setFieldValue('firstName', '');
        setFieldValue('lastName', '');
        setFieldValue('address', '');
        setFieldValue('city', '');
        setFieldValue('state', '');
        setFieldValue('zip', '');
    }

    function clearBillingFields() {
        setFieldValue('billing_firstName', '');
        setFieldValue('billing_lastName', '');
        setFieldValue('billing_address_line', '');
        setFieldValue('billing_city', '');
        setFieldValue('billing_state', '');
        setFieldValue('billing_zip', '');
        setFieldValue('billing_country', 'Morocco');
        setFieldValue('billing_phone', '');
    }

    function showValidationError(element, message) {
        // Remove existing error for this element
        const existingError = element.parentElement.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }

        // Create error message with animation
        const errorDiv = document.createElement('p');
        errorDiv.className = 'validation-error mt-2 text-sm text-red-600 font-semibold flex items-center gap-1 animate-shake';
        errorDiv.innerHTML = `
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            ${message}
        `;

        // Add error styling to field with visual feedback
        element.classList.add('border-red-500', 'border-2', 'focus:border-red-500', 'focus:ring-red-500', 'bg-red-50');
        element.classList.remove('border-gray-200');

        // Insert error message
        element.parentElement.appendChild(errorDiv);
    }

    function clearValidationErrors() {
        const errors = document.querySelectorAll('.validation-error');

        // Remove all validation error messages
        errors.forEach(error => error.remove());

        // Remove error styling from all fields
        document.querySelectorAll('.border-red-500').forEach(field => {
            field.classList.remove('border-red-500', 'border-2', 'focus:border-red-500', 'focus:ring-red-500', 'bg-red-50');
            field.classList.add('border-gray-200');
        });

        // Remove notification banner if exists
        const banner = document.getElementById('validationErrorBanner');
        if (banner) {
            banner.remove();
        }
    }

    function showErrorNotification(message) {
        // Remove existing banner
        const existingBanner = document.getElementById('validationErrorBanner');
        if (existingBanner) {
            existingBanner.remove();
        }

        // Create notification banner
        const banner = document.createElement('div');
        banner.id = 'validationErrorBanner';
        banner.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-slide-down';
        banner.innerHTML = `
            <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span class="flex-1 font-semibold">${message}</span>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-red-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        document.body.appendChild(banner);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (banner.parentElement) {
                banner.style.opacity = '0';
                banner.style.transform = 'translate(-50%, -100%)';
                setTimeout(() => banner.remove(), 300);
            }
        }, 5000);
    }
});
