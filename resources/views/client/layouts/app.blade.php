<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name', 'E-Zone Electronics'))</title>
    <meta name="description"
        content="@yield('description', 'E-Zone Electronics - Your trusted source for quality electronics and gadgets.')">

    <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Structured Data for SEO --}}
    @php
        $organizationLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'E-Zone Electronics',
            'url' => url('/'),
            'logo' => asset('icon/fav-icon-high-resArtboard-1-150x150.png'),
            'description' => 'Your trusted source for quality electronics and gadgets.',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+1-555-123-4567',
                'contactType' => 'customer service',
            ],
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($organizationLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
</head>

<body x-data="{
    sidebarCollapsed: false,
    mobileSidebarOpen: false,
    accountOpen: false,
    languageOpen: false
}" class="font-sans antialiased bg-gray-50 text-gray-900 overflow-x-hidden"
    :class="{ 'overflow-hidden': mobileSidebarOpen }">

    {{-- Header (Fixed) --}}
    @include('client.partials.header')

    {{-- Main Layout --}}
    {{-- PT-140px for mobile (header + search bar space), PT-88px for desktop --}}
    <main class="flex min-h-screen pt-[88px]">

        {{-- Sidebar --}}
        @include('client.partials.sidebar')

        {{-- Main Content Wrapper --}}
        <div class="flex-1 w-full transition-all duration-300"
            :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-16': sidebarCollapsed }">

            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>

            {{-- Footer --}}
            @include('client.partials.footer')
        </div>
    </main>

    {{-- Stack for Page Specific Scripts --}}
    @stack('scripts')
</body>

</html>
