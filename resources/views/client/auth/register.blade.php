<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Zone Electronics') }} - Register</title>
    <link rel="icon" href="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-white min-h-screen">
    <div
        class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-linear-to-br from-gray-50 to-white">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="E-Zone Logo"
                    class="mx-auto w-32 h-24 object-contain">
                <h2 class="mt-6 text-4xl font-extrabold text-gray-900">
                    Create Account
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Join us to track orders and easier checkout
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-100">
                <div class="px-8 py-10">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                            <p class="text-sm text-red-700 font-medium">Please fix the following errors:</p>
                            <ul class="mt-1 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="space-y-6" action="{{ route('register.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full
                                Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="appearance-none block w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                placeholder="John Doe">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email
                                Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="appearance-none block w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                placeholder="you@example.com">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" id="password" required
                                class="appearance-none block w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                placeholder="••••••••">
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="appearance-none block w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-opacity-20 focus:border-primary transition-all duration-300"
                                placeholder="••••••••">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full flex justify-center items-center px-6 py-3.5 text-base font-semibold text-white bg-primary rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-opacity-20">
                            Create Account
                        </button>

                        <!-- Divider -->
                        <div class="relative my-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">Or sign up with</span>
                            </div>
                        </div>

                        <!-- Social Login -->
                        <div class="grid grid-cols-2 gap-3">
                            <a href=""
                                class="flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-300 hover:border-gray-300">
                                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                        fill="#4285F4" />
                                    <path
                                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                        fill="#34A853" />
                                    <path
                                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                        fill="#FBBC05" />
                                    <path
                                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                        fill="#EA4335" />
                                </svg>
                                Google
                            </a>
                            <a href=""
                                class="flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-300 hover:border-gray-300">
                                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.09997 22C7.78997 22.05 6.79997 20.68 5.95997 19.47C4.24997 17 2.93997 12.45 4.69997 9.39C5.56997 7.87 7.12997 6.91 8.81997 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.92 6.84C16.57 6.87 18.39 7.1 19.56 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5ZM13 3.5C13.73 2.67 14.94 2.04 15.94 2C16.07 3.17 15.6 4.35 14.9 5.19C14.21 6.04 13.07 6.7 11.95 6.61C11.8 5.46 12.36 4.26 13 3.5Z" />
                                </svg>
                                Apple
                            </a>
                        </div>
                    </form>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t-2 border-gray-100">
                    <p class="text-center text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-primary hover:opacity-80 transition-opacity">
                            Sign in here
                            <span class="inline-block transition-transform group-hover:translate-x-1"
                                aria-hidden="true">→</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
