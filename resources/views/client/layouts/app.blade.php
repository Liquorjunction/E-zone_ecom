<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name', 'E-Zone Electronics'))</title>
    <meta name="description" content="@yield('description', 'E-Zone Electronics - Your trusted source for quality electronics and gadgets. Shop the latest products with fast shipping and great prices.')">

    <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Swiper CSS (If not already in app.css/js) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Structured Data for SEO --}}
    @php
        $organizationLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'E-Zone Electronics',
            'url' => url('/'),
            'logo' => asset('icon/fav-icon-high-resArtboard-1-150x150.png'),
            'description' =>
                'Your trusted source for quality electronics and gadgets. Shop the latest products with fast shipping and great prices.',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+1-555-123-4567',
                'contactType' => 'customer service',
            ],
            'sameAs' => [
                'https://www.facebook.com/ezoneelectronics',
                'https://www.twitter.com/ezoneelectronics',
                'https://www.instagram.com/ezoneelectronics',
            ],
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
    languageOpen: false
}" class="font-sans antialiased bg-background text-foreground">

    {{-- Header (Fixed at Top) --}}
    {{-- Ensure your header partial has 'fixed top-0 w-full z-50' --}}
    @include('client.partials.header')

    {{-- Main Layout --}}
    <main class="flex min-h-screen">

        {{-- Sidebar (Fixed at Left) --}}
        {{-- Ensure your sidebar partial handles its own 'fixed' positioning or flex behavior --}}
        @include('client.partials.sidebar')

        {{-- Main Content Wrapper --}}
        {{-- Pushes content right based on sidebar state --}}
        <div class="flex-1 transition-all duration-300 relative z-0"
             :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-16': sidebarCollapsed }">

            @yield('content')

            {{-- Footer --}}
            @include('client.partials.footer')
        </div>
    </main>

    {{-- Stack for Page Specific Scripts --}}
    @stack('scripts')
</body>

</html>
