<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ statictranslations(config('app.name', 'E-Zone Electronics')) }} - {{ statictranslations('Reset Password') }}</title>
    <link rel="icon" href="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-white min-h-screen">
    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- Left Side - Branding (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 bg-linear-to-br from-primary to-primary/90 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                <!-- Logo and Back Button -->
                <div class="space-y-6">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-white hover:opacity-80 transition-opacity">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-semibold">{{ statictranslations('Back to Home') }}</span>
                    </a>
                </div>
                <div class="relative z-10 flex justify-center">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo" class="h-30 object-contain brightness-0 invert">
                </div>

                <!-- Main Content -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-4xl font-bold mb-4">{{ statictranslations('Secure Password Reset') }}</h1>
                        <p class="text-lg text-white/90">{{ statictranslations('Create a strong new password to protect your account.') }}</p>
                    </div>

                    <!-- Security Tips -->
                    <div class="space-y-4 mt-12">
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm">{{ statictranslations('Strong Password') }}</h3>
                                <p class="text-white/80">{{ statictranslations('Use 8+ characters with mixed case & numbers') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm">{{ statictranslations('Account Security') }}</h3>
                                <p class="text-white/80">{{ statictranslations('Your data is protected with encryption') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-sm">{{ statictranslations('Time Limited') }}</h3>
                                <p class="text-white/80">{{ statictranslations('Link expires in 5 minutes for security') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center space-x-6 text-sm text-white/80">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ statictranslations('Secure & Encrypted') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ statictranslations('SSL Protected') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Reset Password Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
            <div class="w-full max-w-md">

                <!-- Mobile Back Button -->
                <div class="lg:hidden mb-6">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-primary hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-semibold">{{ statictranslations('Back to Home') }}</span>
                    </a>
                </div>

                <!-- Logo for Mobile -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo" class="mx-auto w-24 h-18 object-contain">
                </div>

                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">{{ statictranslations('Reset Password') }}</h2>
                    <p class="text-gray-600">{{ statictranslations('Create a new password for your account') }}</p>
                </div>

                {{-- Display Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
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

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Reset Form -->
                <form class="space-y-5" action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <!-- Hidden Fields -->
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- Email Display (Read-only) -->
                    <div>
                        <label for="email_display" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Email Address') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email_display" type="email" disabled value="{{ $email }}"
                                class="appearance-none block w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-xl text-gray-600 bg-gray-50 placeholder-gray-400 focus:outline-none transition-all duration-200 cursor-not-allowed">
                        </div>
                    </div>

                    <!-- New Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('New Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required
                                class="appearance-none block w-full pl-12 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                placeholder="{{ statictranslations('Enter your new password') }}">
                            <button type="button" id="togglePassword" tabindex="-1"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                aria-label="{{ statictranslations('Toggle password visibility') }}">
                                <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">{{ statictranslations('Must be at least 8 characters with uppercase, lowercase, and numbers.') }}</p>

                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-gray-600">{{ statictranslations('Password Strength:') }}</span>
                                <span id="strengthText" class="text-xs font-medium text-gray-600">{{ statictranslations('Weak') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div id="strengthBar" class="h-2 rounded-full transition-all duration-300 bg-red-500" style="width: 20%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">{{ statictranslations('Confirm New Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="appearance-none block w-full pl-12 pr-12 py-3.5 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                placeholder="{{ statictranslations('Re-enter your new password') }}">
                            <button type="button" id="togglePasswordConfirm" tabindex="-1"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                aria-label="{{ statictranslations('Toggle confirm password visibility') }}">
                                <svg id="eyeIconConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Info Notice -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">{{ statictranslations('This reset link will expire in 5 minutes. If it has expired, request a new one.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center items-center px-6 py-3.5 text-base font-semibold text-white bg-primary rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-primary focus:ring-opacity-50">
                            <span>{{ statictranslations('Reset Password') }}</span>
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Footer Link -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        {{ statictranslations('Password reset link invalid or expired?') }}
                        <a href="{{ route('login') }}" class="font-semibold text-primary hover:opacity-80 transition-opacity ml-1">
                            {{ statictranslations('Request a new one') }} →
                        </a>
                    </p>
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

                const strengthLevels = [
                    @json(statictranslations('Weak')),
                    @json(statictranslations('Fair')),
                    @json(statictranslations('Good')),
                    @json(statictranslations('Strong')),
                    @json(statictranslations('Very Strong')),
                ];
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
