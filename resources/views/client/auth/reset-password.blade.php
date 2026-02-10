<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Zone Electronics') }} - Reset Password</title>
    <link rel="icon" href="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-white min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-gray-50 to-white relative">
        <!-- Back Button Top Left -->
        <div class="absolute top-6 left-6 z-10">
            <a href="{{ route('login') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl bg-white text-primary font-semibold shadow-sm hover:bg-gray-100 transition-all duration-200">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Login
            </a>
        </div>

        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                    class="mx-auto w-32 h-24 object-contain">
                <h2 class="mt-6 text-4xl font-extrabold text-gray-900">
                    Reset Password
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Create a new password for your account
                </p>
            </div>

            <!-- Reset Form -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-100">
                <div class="px-8 py-10">

                    {{-- Display Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <ul class="mt-1 list-disc list-inside text-sm text-red-600">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Success Message if redirected back with success --}}
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form class="space-y-6" action="{{ route('password.update') }}" method="POST">
                        @csrf

                        <!-- Hidden Fields -->
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- Email Display (Read-only) -->
                        <div>
                            <label for="email_display" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input id="email_display" type="email" disabled value="{{ $email }}"
                                    class="appearance-none block w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl text-gray-600 bg-gray-50 placeholder-gray-400 focus:outline-none transition-all duration-300">
                            </div>
                        </div>

                        <!-- New Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                New Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" required
                                    class="appearance-none block w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                    placeholder="Enter your new password">
                                <button type="button" id="togglePassword" tabindex="-1"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 focus:outline-none"
                                    aria-label="Toggle password visibility">
                                    <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Must be at least 8 characters with uppercase, lowercase, and numbers.
                            </p>
                        </div>

                        <!-- Password Confirmation Field -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="appearance-none block w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                    placeholder="Confirm your new password">
                                <button type="button" id="togglePasswordConfirm" tabindex="-1"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 focus:outline-none"
                                    aria-label="Toggle password visibility">
                                    <svg id="eyeIconConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-gray-700">
                                    Password Strength
                                </label>
                                <span id="strengthText" class="text-xs font-semibold text-gray-500">
                                    Weak
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="strengthBar" class="bg-red-500 h-2 rounded-full transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Security Note -->
                        <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        This reset link will expire in 60 minutes. If it has expired, request a new one.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                class="w-full flex justify-center items-center px-6 py-3.5 text-base font-semibold text-white bg-primary rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-opacity-20">
                                <span>Reset Password</span>
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="px-8 py-6 bg-gray-50 border-t-2 border-gray-100">
                    <p class="text-center text-sm text-gray-600">
                        Password reset link invalid or expired?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-primary hover:opacity-80 transition-opacity">
                            Request a new one
                        </a>
                    </p>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="mt-8">
                <div class="flex items-center justify-center space-x-6 text-sm text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Secure
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Encrypted
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const togglePassword = document.getElementById('togglePassword');
            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeIconConfirm = document.getElementById('eyeIconConfirm');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            // Password strength checker
            function checkPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;

                const strengthLevels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
                const strengthColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-lime-500', 'bg-green-500'];
                const widths = ['20%', '40%', '60%', '80%', '100%'];

                strengthBar.style.width = widths[strength];
                strengthBar.className = `h-2 rounded-full transition-all duration-300 ${strengthColors[strength]}`;
                strengthText.textContent = strengthLevels[strength];
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                });
            }

            // Toggle password visibility
            let isPasswordVisible = false;
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    isPasswordVisible = !isPasswordVisible;
                    passwordInput.type = isPasswordVisible ? 'text' : 'password';
                    eyeIcon.innerHTML = isPasswordVisible
                        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.671-2.568A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.293 5.032M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />`
                        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
                });
            }

            // Toggle password confirmation visibility
            let isPasswordConfirmVisible = false;
            if (togglePasswordConfirm && passwordConfirmInput) {
                togglePasswordConfirm.addEventListener('click', function() {
                    isPasswordConfirmVisible = !isPasswordConfirmVisible;
                    passwordConfirmInput.type = isPasswordConfirmVisible ? 'text' : 'password';
                    eyeIconConfirm.innerHTML = isPasswordConfirmVisible
                        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.671-2.568A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.293 5.032M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />`
                        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
                });
            }
        });
    </script>
</body>

</html>
