document.addEventListener('DOMContentLoaded', function() {
    // Global function to show custom toast
    window.showToast = function(message, type = 'info', title = 'Notification', actionCallback = null) {
        const toastElement = document.getElementById('globalToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastActionBtn = document.getElementById('toastActionBtn');

        // Set message
        toastMessage.textContent = message;

        // Set background color based on type
        toastElement.className = 'toast fade show';
        if (type === 'success') {
            toastElement.classList.add('bg-success', 'text-white');
        } else if (type === 'error') {
            toastElement.classList.add('bg-danger', 'text-white');
        } else if (type === 'warning') {
            toastElement.classList.add('bg-warning');
        } else {
            toastElement.classList.add('bg-info', 'text-white');
        }

        // Handle action button
        if (actionCallback) {
            toastActionBtn.style.display = 'inline-block';
            toastActionBtn.textContent = 'Take action';
            toastActionBtn.onclick = actionCallback;
        } else {
            toastActionBtn.style.display = 'none';
        }

        // Show the toast
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    };

    // Automatically show toasts for flashed session messages
    var sessionMessages = {
        success: document.querySelector('[data-session-success]'),
        error: document.querySelector('[data-session-error]'),
        warning: document.querySelector('[data-session-warning]'),
        info: document.querySelector('[data-session-info]')
    };

    if (sessionMessages.success && sessionMessages.success.getAttribute('data-session-success')) {
        showToast(sessionMessages.success.getAttribute('data-session-success'), 'success', 'Success');
    }
    if (sessionMessages.error && sessionMessages.error.getAttribute('data-session-error')) {
        showToast(sessionMessages.error.getAttribute('data-session-error'), 'error', 'Error');
    }
    if (sessionMessages.warning && sessionMessages.warning.getAttribute('data-session-warning')) {
        showToast(sessionMessages.warning.getAttribute('data-session-warning'), 'warning', 'Warning');
    }
    if (sessionMessages.info && sessionMessages.info.getAttribute('data-session-info')) {
        showToast(sessionMessages.info.getAttribute('data-session-info'), 'info', 'Info');
    }
});
