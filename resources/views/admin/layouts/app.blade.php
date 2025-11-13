<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Hope UI') | Responsive Bootstrap 5 Admin Dashboard Template</title>
    @include('admin.essentials.styles')
    {{-- Page-specific styles --}}
    @stack('styles')
</head>

<body class="  " data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">
    {{-- Loading --}}
    @include('admin.components.laoding')
    {{-- Sidebar --}}
    @include('admin.partials.sidebar')

    <main class="main-content">
        {{-- Navbar --}}
        @include('admin.partials.navbar')

        {{-- Breadcrumbs --}}
        @include('admin.components.breadcrumbs')

        {{-- Content --}}
        @yield('content')

        {{-- Settings --}}
        @include('admin.components.settings')
        {{-- Footer --}}
        @include('admin.partials.footer')
    </main>

    @include('admin.essentials.scripts')

    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>

</html>
