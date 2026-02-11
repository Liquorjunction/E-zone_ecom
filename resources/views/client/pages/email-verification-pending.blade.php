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
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                    class="mx-auto w-32 h-24 object-contain">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Verify Your Email
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    @if(session('email_sent'))
                        A fresh verification link has been sent to your email
                    @else
                        Please check your email for the verification link
                    @endif
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-100">
                <div class="px-8 py-10">

                    <!-- Success Message when email is sent -->
                    @if(session('email_sent'))
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-green-700 font-medium">
                                Verification email sent! Please check your inbox.
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Email Display -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
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
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
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
                            class="w-full flex justify-center items-center px-6 py-3.5 text-base font-semibold text-white bg-primary rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-opacity-20">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Resend Verification Email
                        </button>

                        @if ($errors->has('email'))
                            <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-xl">
                                <p class="text-sm text-red-600">{{ $errors->first('email') }}</p>
                            </div>
                        @endif

                        @if (session('resend_sent'))
                            <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-r-xl">
                                <p class="text-sm text-green-600">{{ session('resend_sent') }}</p>
                            </div>
                        @endif
                    </form>

                    <!-- Help Text -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600 text-center">
                            <strong>Didn't get the email?</strong>
                        </p>
                        <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                            <li>Check your spam/junk folder</li>
                            <li>Make sure you entered the correct email</li>
                            <li>Use the resend button above</li>
                        </ul>
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
    </div>
</body>

</html>
