<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Under Maintenance - {{ config('app.name', 'E-Zone Electronics') }}</title>
    <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="{{ asset('css/maintenance.css') }}" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">

    <!-- Top Accent Bar -->
    <div class="h-1 w-full bg-linear-to-r from-primary to-primary-dark"></div>

    <!-- Header with Logo -->
    <header class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-center">
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}"
                     alt="E-Zone Electronics"
                     class="h-16 sm:h-20 object-contain">
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-12 sm:py-16 lg:py-20">
        <div class="max-w-4xl w-full animate-fade-in">

            <!-- Hero Section -->
            <div class="text-center mb-12">
                <div class="inline-block p-5 bg-primary/10 rounded-full mb-6 animate-bounce-slow">
                    <svg class="w-16 h-16 sm:w-20 sm:h-20 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    We'll Be Right Back!
                </h1>

                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Our website is currently undergoing scheduled maintenance to improve your experience.
                    We apologize for any inconvenience and appreciate your patience.
                </p>
            </div>

            <!-- Action Button -->
            <div class="flex justify-center mb-16">
                <button
                    onclick="window.location.reload()"
                    class="inline-flex items-center justify-center bg-primary hover:bg-primary-dark text-white font-semibold px-10 py-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-primary/30">
                    <svg class="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Page
                </button>
            </div>
        </div>
    </main>
</body>
</html>
