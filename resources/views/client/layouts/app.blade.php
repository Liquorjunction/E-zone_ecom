<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name', 'E-Zone Electronics'))</title>
    <meta name="description" content="@yield('description', 'E-Zone Electronics - Your trusted source for quality electronics and gadgets.')">

    <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- Structured Data for SEO --}}
    @php
        $organizationLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'E-Zone Electronics',
            'url' => url('/'),
            'logo' => asset('icon/fav-icon-high-resArtboard-1-150x150.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+1-555-123-4567',
                'contactType' => 'customer service',
            ]
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($organizationLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

</head>

<body x-data="{
    sidebarCollapsed: true,
    mobileSidebarOpen: false,
    accountOpen: false,
    languageOpen: false,
    init() {
        // Auto-collapse sidebar on smaller desktop screens if needed, or check localStorage
        if (window.innerWidth >= 1024 && window.innerWidth < 1280) {
            this.sidebarCollapsed = true;
        }
    }
}" class="font-sans antialiased bg-gray-50 text-slate-800 overflow-x-hidden">

    {{-- Header (Fixed at Top) --}}
    @include('client.partials.header')

    {{-- Main Layout --}}
    <main class="flex min-h-screen"> {{-- Added padding-top to account for fixed header --}}

        {{-- Sidebar (Handles both Mobile & Desktop inside the partial) --}}
        @include('client.partials.sidebar')

        {{-- Main Content Wrapper --}}
        {{-- Mobile: ml-0 (no margin). Desktop: ml-72 or ml-16 based on collapse state --}}
        <div class="flex-1 w-full transition-all duration-300 relative z-0"
             :class="{
                'lg:ml-72': !sidebarCollapsed,
                'lg:ml-16': sidebarCollapsed,
                'ml-0': true
             }">

            <div>
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
