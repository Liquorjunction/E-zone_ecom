<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Zone Electronics') }} - Registration Successful</title>
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
                <div class="absolute inset-0"
                    style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;">
                </div>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                <!-- Logo and Back Button -->
                <div class="space-y-6">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center text-white hover:opacity-80 transition-opacity">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-semibold">Back to Home</span>
                    </a>
                </div>
                <div class="relative z-10 flex justify-center">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                        class="h-30 object-contain brightness-0 invert">
                </div>

                <!-- Main Content -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-4xl font-bold mb-4">Welcome to E-Zone!</h1>
                        <p class="text-lg text-white/90">Complete your registration by verifying your email address to start
                            your shopping journey!</p>
                    </div>

                    <!-- Features -->
                    <div class="space-y-4 mt-12">
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-base">Registration Submitted</h3>
                                <p class="text-sm text-white/80 mt-1">Your E-Zone account awaits email verification</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-base">Complete Registration</h3>
                                <p class="text-sm text-white/80 mt-1">Verify your email to complete your registration</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-base">Start Shopping</h3>
                                <p class="text-sm text-white/80 mt-1">Explore thousands of premium electronics</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center space-x-6 text-sm text-white/80">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Secure & Encrypted
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        SSL Protected
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Success Content -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
            <div class="w-full max-w-md">

                <!-- Mobile Back Button -->
                <div class="lg:hidden mb-6">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center text-primary hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-semibold">Back to Home</span>
                    </a>
                </div>

                <!-- Logo for Mobile -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                        class="mx-auto w-24 h-18 object-contain">
                </div>

                <!-- Header -->
                <div class="mb-8">
                    <!-- Success Icon -->
                    <div class="flex justify-center mb-4">
                        <div class="relative">
                            <div class="absolute inset-0 bg-green-200 rounded-full animate-pulse"></div>
                            <div class="relative bg-green-100 rounded-full p-3">
                                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <h2 class="text-3xl lg:text-3xl font-bold text-gray-900 mb-2 text-center">You're Almost There! 🎉</h2>
                    <p class="text-gray-600 text-center">Just one final step to unlock your E-Zone account</p>
                </div>

                <!-- Success Message -->
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-green-800">Welcome! ✓</p>
                            <p class="text-sm text-green-700 mt-1">Great! We've received your registration. We've sent a confirmation email to verify your address. Simply click the link in that email, and you'll be ready to explore our amazing electronics collection!</p>
                        </div>
                    </div>
                </div>

                <!-- Email Display -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                    <p class="text-sm text-blue-700">
                        <span class="font-semibold">Confirmation Email Sent To:</span><br>
                        <span
                            class="text-blue-900 font-medium">{{ session('email') ?? 'your-email@example.com' }}</span>
                    </p>
                    <p class="text-xs text-blue-600 mt-2">Check this inbox (including spam folder) for our email. Once you click the verification link, your account will be fully active!</p>
                </div>

                <!-- Instructions -->
                <div class="space-y-4 mb-8">
                    <h3 class="text-base font-semibold text-gray-900">Quick Steps to Get Started</h3>

                    <div class="flex items-start">
                        <div class="shrink-0">
                            <div
                                class="flex items-center justify-center h-8 w-8 rounded-md bg-primary text-white text-sm font-semibold">
                                1
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">📧 Find the Email</h4>
                            <p class="mt-1 text-sm text-gray-600">Check your inbox (and spam folder just in case) for an email from E-Zone with the subject "Verify Your E-Zone Account"</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="shrink-0">
                            <div
                                class="flex items-center justify-center h-8 w-8 rounded-md bg-primary text-white text-sm font-semibold">
                                2
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">✓ Click the Link</h4>
                            <p class="mt-1 text-sm text-gray-600">Click the verification link to confirm your email and activate your account. That's it!</p>
                        </div>
                    </div>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-6">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>⏰ Time remaining: <span id="timer" data-timer-seconds="300" data-expired-label="0:00" data-timer-storage-key="register-success-{{ md5((string) (session('token') ?? session('email') ?? request()->path())) }}" data-expired-message-target="register-timer-message" data-expired-message="The verification window has ended. Please register again to receive a fresh verification email.">5:00</span></strong><span class="mx-1">-</span><span id="register-timer-message">Please complete verification before the timer ends.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Primary Action Button -->
                <div class="flex gap-3 pt-2">
                    @if (session('token'))
                        <a href="{{ route('register.edit', ['token' => session('token')]) }}"
                            class="flex-1 flex justify-center items-center px-6 py-3.5 text-base font-semibold text-primary bg-gray-100 rounded-xl shadow-lg hover:bg-gray-200 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2"
                            title="Modify your registration details">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Details
                        </a>
                    @endif
                </div>

                <!-- Help Text -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 text-center font-semibold mb-3">
                        Can't Find the Email?
                    </p>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-start">
                            <svg class="h-4 w-4 text-gray-400 mr-2 mt-0.5 shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span><strong>Check Spam:</strong> Emails sometimes hide in the spam folder - look there first!</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-4 w-4 text-gray-400 mr-2 mt-0.5 shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span><strong>Verify Email:</strong> Double-check that the email address {{ session('email') ?? 'you provided' }} is correct</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
