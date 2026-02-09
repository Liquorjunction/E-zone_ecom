document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterCheckbox = document.getElementById('newsletter-checkbox');
    const emailInput = document.getElementById('footer-email');
    const submitButton = newsletterForm.querySelector('button[type="submit"]');

    newsletterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!newsletterCheckbox.checked) {
            showUniversalPopup('Please accept the Terms & Conditions to subscribe.', 'error');
            return;
        }

        const email = emailInput.value.trim();
        if (!email) {
            showUniversalPopup('Please enter your email address.', 'error');
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Subscribing...';

        try {
            const formData = new FormData(newsletterForm);
            const response = await fetch(newsletterForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const contentType = response.headers.get('content-type') || '';
            const isJson = contentType.includes('application/json');
            const data = isJson ? await response.json() : null;

            if (response.ok) {
                showUniversalPopup((data && data.message) || 'Subscribed successfully!', 'success');
                emailInput.value = '';
                newsletterCheckbox.checked = false;
            } else if (data && data.errors) {
                const errorMessage = Object.values(data.errors).flat().join(' ');
                showUniversalPopup(errorMessage, 'error');
            } else if (data && data.message) {
                showUniversalPopup(data.message, 'error');
            } else {
                const fallbackText = isJson ? '' : await response.text();
                showUniversalPopup('An error occurred. Please try again.', 'error');
            }
        } catch (error) {
            showUniversalPopup('An error occurred. Please try again later.', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Subscribe';
        }
    });
});
