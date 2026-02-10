<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300 ease-out" style="z-index: 99999;">
    <!-- Backdrop -->
    <div class="fixed inset-0" id="modalBackdrop"></div>

    <!-- Modal Content -->
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 p-8 transform scale-95 opacity-0 transition-all duration-300 ease-out border-2 border-gray-100">
        <!-- Close Button -->
        <button type="button" id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 rounded-full p-1.5 transition-all duration-200 hover:bg-gray-100 group">
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Icon -->
        <div class="flex justify-center mb-5 transform transition-all duration-500 ease-out">
            <div class="bg-blue-50 rounded-full p-4">
                <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h3 class="text-center text-2xl font-bold text-gray-800 mb-2" id="modal-title">
            Forgot Password?
        </h3>
        <p class="text-center text-sm text-gray-600 mb-6">
            No worries! Enter your email and we'll send you reset instructions.
        </p>

        <!-- Modal Body -->
        <!-- Success Message (Hidden by default) -->
        <div id="successMessage" class="hidden mb-4 bg-green-50 border border-green-200 p-4 rounded-xl">
            <div class="flex items-center justify-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-green-700 font-semibold">Reset link sent! Check your email.</p>
            </div>
        </div>

        <!-- Error Message (Hidden by default) -->
        <div id="errorMessage" class="hidden mb-4 bg-red-50 border border-red-200 p-4 rounded-xl">
            <div class="flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-red-700 font-semibold" id="errorText">Something went wrong. Please try again.</p>
            </div>
        </div>

        <form id="forgotPasswordForm" action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="reset_email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input
                        id="reset_email"
                        name="email"
                        type="email"
                        required
                        class="appearance-none block w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-primary/30 focus:border-primary transition-all duration-300"
                        placeholder="you@example.com">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
                <button
                    type="submit"
                    id="submitBtn"
                    class="w-full px-6 py-3 text-base font-semibold text-white bg-primary rounded-full shadow-lg hover:shadow-xl hover:scale-105 focus:ring-4 focus:ring-primary/30 focus:outline-none border border-primary/20 transition-all duration-200 active:scale-95 flex items-center justify-center">
                    <span id="submitBtnText">Send Reset Link</span>
                    <svg id="submitBtnIcon" class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    <svg id="submitBtnSpinner" class="hidden ml-2 w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
                <button
                    type="button"
                    id="cancelBtn"
                    class="w-full px-6 py-3 text-base font-semibold text-gray-700 bg-gray-200 rounded-full shadow-md hover:shadow-lg hover:scale-105 focus:ring-4 focus:ring-gray-300/30 focus:outline-none border border-gray-300 transition-all duration-200 active:scale-95 hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </form>

        <!-- Footer Note -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Remember your password?
                <button type="button" id="backToLoginBtn" class="font-semibold text-primary hover:opacity-80 transition-opacity">
                    Back to Login
                </button>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('forgotPasswordModal');
        const modalCard = modal ? modal.querySelector('.relative.bg-white') : null;
        const modalBackdrop = document.getElementById('modalBackdrop');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const backToLoginBtn = document.getElementById('backToLoginBtn');
        const forgotPasswordLink = document.getElementById('forgotPasswordLink');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnIcon = document.getElementById('submitBtnIcon');
        const submitBtnSpinner = document.getElementById('submitBtnSpinner');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const resetEmailInput = document.getElementById('reset_email');

        // Show modal with animation
        function showModal() {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');

            if (modalCard) {
                setTimeout(() => {
                    modalCard.classList.remove('scale-95', 'opacity-0');
                    modalCard.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            document.body.style.overflow = 'hidden';
            if (resetEmailInput) resetEmailInput.focus();
        }

        // Hide modal with animation
        function hideModal() {
            if (modalCard) {
                modalCard.classList.remove('scale-100', 'opacity-100');
                modalCard.classList.add('scale-95', 'opacity-0');
            }

            setTimeout(() => {
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');
                document.body.style.overflow = 'auto';

                // Reset form and messages after animation
                if (forgotPasswordForm) forgotPasswordForm.reset();
                if (successMessage) successMessage.classList.add('hidden');
                if (errorMessage) errorMessage.classList.add('hidden');
            }, 200);
        }

        // Event listeners
        if (forgotPasswordLink) {
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                showModal();
            });
        }

        if (closeModalBtn) closeModalBtn.addEventListener('click', hideModal);
        if (cancelBtn) cancelBtn.addEventListener('click', hideModal);
        if (backToLoginBtn) backToLoginBtn.addEventListener('click', hideModal);
        if (modalBackdrop) modalBackdrop.addEventListener('click', hideModal);

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('pointer-events-none')) {
                hideModal();
            }
        });

        // Handle form submission via AJAX
        if (forgotPasswordForm) {
            forgotPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Reset messages
                if (successMessage) successMessage.classList.add('hidden');
                if (errorMessage) errorMessage.classList.add('hidden');

                // Show loading state
                submitBtn.disabled = true;
                submitBtnText.textContent = 'Sending...';
                submitBtnIcon.classList.add('hidden');
                submitBtnSpinner.classList.remove('hidden');

                // Get form data
                const formData = new FormData(forgotPasswordForm);

                // Send AJAX request
                fetch(forgotPasswordForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Send Reset Link';
                    submitBtnIcon.classList.remove('hidden');
                    submitBtnSpinner.classList.add('hidden');

                    if (data.status === 'success' || data.message) {
                        successMessage.classList.remove('hidden');
                        forgotPasswordForm.reset();

                        // Auto close after 3 seconds
                        setTimeout(() => {
                            hideModal();
                        }, 3000);
                    } else {
                        errorText.textContent = data.error || 'Something went wrong. Please try again.';
                        errorMessage.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Send Reset Link';
                    submitBtnIcon.classList.remove('hidden');
                    submitBtnSpinner.classList.add('hidden');
                    errorText.textContent = 'Network error. Please check your connection.';
                    errorMessage.classList.remove('hidden');
                });
            });
        }
    });
</script>
