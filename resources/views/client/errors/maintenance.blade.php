<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Under Maintenance - {{ config('app.name', 'Ezone Electronics') }}</title>
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
                <img src="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" alt="Ezone Electronics"
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
                    <svg class="w-16 h-16 sm:w-20 sm:h-20 text-primary" fill="none" stroke="currentColor"
                        viewBox="0 0 256 256" preserveAspectRatio="xMidYMid meet">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M254,188V2H2v186h111v29H75v22h106v-22h-38v-29H254z M19,19h217v151H19L19,19z M169.385,132.8
                        c0.131,1.179,0,2.575-0.306,3.885l-11.131-10.258l-10.04,10.957l11.262,10.214c-1.31,0.437-2.575,0.742-3.885,0.742
                        c-3.885,0.131-7.203-1.048-10.04-3.623c-2.837-2.706-4.365-5.849-4.496-9.734c0-1.353,0.131-2.881,0.437-4.191l-2.706-2.575
                        l-16.195-14.798l-27.413,30.207c-1.659,2.226-4.234,3.754-7.203,3.754c-4.802,0-8.687-3.885-8.687-8.687
                        c0-2.575,1.004-4.933,2.968-6.591l28.461-29.509L93.213,86.835c-1.353,0.611-2.881,0.917-4.191,0.917
                        c-3.885,0.131-7.203-1.048-10.04-3.623s-4.365-5.718-4.496-9.603c-0.131-1.179,0-2.575,0.306-3.885l11.262,10.214l9.953-10.913
                        L84.745,59.728c1.179-0.437,2.575-0.742,3.885-0.742c3.885-0.131,7.203,1.048,10.04,3.623c2.837,2.532,4.365,5.849,4.496,9.734
                        c0.131,1.31,0,2.575-0.306,3.885l17.723,16.238l12.965-14.012l-19.381-17.068l17.592-20.036l49.807,43.826l-17.592,20.036
                        l-19.905-17.548l-12.528,14.798l19.469,17.81c1.31-0.437,2.575-0.742,3.885-0.742c3.885-0.131,7.203,1.048,10.04,3.623
                        C167.77,125.685,169.298,128.828,169.385,132.8z" />
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
                <button onclick="window.location.reload()"
                    class="inline-flex items-center justify-center bg-primary hover:bg-primary-dark text-white font-semibold px-10 py-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-primary/30">
                    <svg class="w-5 h-5 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Refresh Page
                </button>
            </div>
        </div>
    </main>
</body>

</html>
