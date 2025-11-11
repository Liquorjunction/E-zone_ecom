<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Zone Electronics') }}</title>
    <link rel="icon" href="{{ asset('icon/Ezone-Logo-New-2025-Website.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
  </head>
  <body class="bg-white min-h-screen" x-data="sidebar">
    @include('client.partials.header')
    @include('client.partials.sidebar')
    <!-- Main Content -->
    <main class="min-h-screen transition-all duration-300" :class="{ 'lg:ml-16': sidebarCollapsed, 'lg:ml-72': !sidebarCollapsed }">
      @yield('content')
      @include('client.partials.footer')
    </main>
    @stack('scripts')
  </body>
</html>
