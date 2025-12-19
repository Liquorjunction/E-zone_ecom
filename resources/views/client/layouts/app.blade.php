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
    mobileMenuOpen: false,
    accountOpen: false,
    languageOpen: false,
    languageCloseTimeout: null,
    accountCloseTimeout: null,
    pinned: false,
    isLg: window.innerWidth >= 1024,
    init() {
        const setHeaderHeight = () => {
            const header = document.querySelector('header');
            const h = header ? header.offsetHeight : 120;
            document.documentElement.style.setProperty('--header-height', h + 'px');
        };
        setHeaderHeight();
        window.addEventListener('resize', () => {
            setHeaderHeight();
            this.isLg = window.innerWidth >= 1024;
        });
        // Auto-collapse sidebar on some narrower desktop widths
        if (window.innerWidth >= 1024 && window.innerWidth < 1280) {
            this.sidebarCollapsed = true;
        }
    }
}"
    @keydown.escape.window="accountOpen = false; languageOpen = false; mobileMenuOpen = false; mobileSidebarOpen = false"
    style="--header-height:140px;"
    class="font-sans antialiased bg-gray-50 text-slate-800">

    {{-- Header (Fixed at Top) --}}
    @include('client.partials.header')

    {{-- Main Layout with padding for fixed header --}}
    <main class="flex min-h-screen main-content-wrapper" style="padding-top: var(--header-height);">

        {{-- Sidebar (Handles both Mobile & Desktop inside the partial) --}}
        @include('client.partials.sidebar')

        {{-- Main Content Wrapper with proper transitions --}}
        <div class="flex-1 w-full transition-all duration-300 relative z-0"
             :style="isLg ? (sidebarCollapsed ? 'margin-left: 4rem' : 'margin-left: 18rem') : 'margin-left: 0'">

            <div class="w-full">
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
