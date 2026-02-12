<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Zone Electronics') }} - Verify Email</title>
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
                        <span class="font-semibold">Back to Home</span>
                    </a>
                </div>
                <div class="relative z-10 flex justify-center">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo" class="h-30 object-contain brightness-0 invert">
                </div>

                <!-- Main Content -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-4xl font-bold mb-4">Email Verification</h1>
                        <p class="text-lg text-white/90">Secure your account by verifying your email address.</p>
                    </div>

                    <!-- Features -->
                    <div class="space-y-4 mt-12">
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-base">Account Security</h3>
                                <p class="text-sm text-white/80 mt-1">Verify your email to protect your account</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-base">Check Your Inbox</h3>
                                <p class="text-sm text-white/80 mt-1">Verification email sent to your address</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-base">Quick Process</h3>
                                <p class="text-sm text-white/80 mt-1">One click and you're ready to go</p>
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
                        Secure & Encrypted
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        SSL Protected
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Verification Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
            <div class="w-full max-w-md">

                <!-- Mobile Back Button -->
                <div class="lg:hidden mb-6">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-primary hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-semibold">Back to Home</span>
                    </a>
                </div>

                <!-- Logo for Mobile -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo" class="mx-auto w-24 h-18 object-contain">
                </div>

                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Verify Your Email</h2>
                    <p class="text-gray-600">
                        @if(session('email_sent'))
                            A fresh verification link has been sent to your email
                        @else
                            Please check your email for the verification link
                        @endif
                    </p>
                </div>

                {{-- Success Message when email is sent --}}
                @if(session('email_sent'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700 font-medium">Verification email sent! Please check your inbox.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Email Display -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                    <p class="text-sm text-blue-700">
                        <span class="font-semibold">Verification Email Sent To:</span><br>
                        <span class="text-blue-900 font-medium">{{ Auth::user()->email }}</span>
                    </p>
                </div>

                <!-- Instructions -->
                <div class="space-y-4 mb-8">
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-primary text-white text-sm font-semibold">
                                1
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Check Your Email</h3>
                            <p class="mt-1 text-sm text-gray-600">Look for our email with the subject "Verify Your Email Address"</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-primary text-white text-sm font-semibold">
                                2
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Click the Verification Link</h3>
                            <p class="mt-1 text-sm text-gray-600">Open the link in the email to verify your account</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-primary text-white text-sm font-semibold">
                                3
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">You're Ready!</h3>
                            <p class="mt-1 text-sm text-gray-600">After verification, you can access your account</p>
                        </div>
                    </div>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-6">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Verification links expire in 5 minutes.</strong> If yours expired, request a new one below.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resend Button -->
                <form action="{{ route('verification.resend') }}" method="POST" class="space-y-4">
                    @csrf

                    <button type="submit"
                        class="w-full flex justify-center items-center px-6 py-3.5 text-base font-semibold text-white bg-primary rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Resend Verification Email
                    </button>

                    @if ($errors->has('email'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <p class="text-sm text-red-600">{{ $errors->first('email') }}</p>
                        </div>
                    @endif

                    @if (session('resend_sent'))
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                            <p class="text-sm text-green-600">{{ session('resend_sent') }}</p>
                        </div>
                    @endif
                </form>

                <!-- Help Text -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 text-center font-semibold mb-3">
                        Troubleshooting
                    </p>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-start">
                            <svg class="h-4 w-4 text-gray-400 mr-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Check your spam/junk folder</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-4 w-4 text-gray-400 mr-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Make sure you entered the correct email</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-4 w-4 text-gray-400 mr-2 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span>Use the resend button above</span>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit"
                        class="w-full text-center text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
