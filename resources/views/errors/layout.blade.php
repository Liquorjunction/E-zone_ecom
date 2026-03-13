<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">
        @vite('resources/css/app.css')
        @vite('resources/js/app.js')
    </head>
    <body x-data="{
        sidebarCollapsed: true,
        mobileSidebarOpen: false,
        accountOpen: false,
        languageOpen: false
    }" class="font-sans">
        @include('client.partials.header')
        <main class="flex">
            @include('client.partials.sidebar')
            <div class="flex-1 transition-all duration-300"
                :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-16': sidebarCollapsed }">
                <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
                    <div class="container mx-auto px-4 text-center">
                        <div class="max-w-md mx-auto">
                            @yield('content')
                        </div>
                    </div>
                </div>
                @include('client.partials.footer')
            </div>
        </main>
    </body>
</html>
