<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Zone Electronics') }}</title>
    <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

{{-- resources/views/client/layouts/app.blade.php --}}

<body x-data="{
    sidebarCollapsed: true,
    mobileSidebarOpen: false,
    accountOpen: false,
    languageOpen: false
}" class="font-sans">

    {{-- Your new header.blade.php will go here --}}
    @include('client.partials.header')

    <main class="flex">
        {{-- Your new sidebar.blade.php will go here --}}
        @include('client.partials.sidebar')

        {{-- This is where all your page content will load --}}
        <div class="flex-1 transition-all duration-300"
            :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-16': sidebarCollapsed }">
            @yield('content')
        </div>
    </main>

    {{-- Your footer.blade.php will go here --}}
    @include('client.partials.footer')

    {{-- Your scripts --}}
    @stack('scripts')
</body>

</html>
