<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    {{-- iOS FIX: removed maximum-scale=1 to keep user accessibility zoom intact;
         input font-size is set to 16px in app.css to prevent auto-zoom instead --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', config('app.name', 'E-Zone Electronics'))</title>
    <meta name="description" content="@yield('description', statictranslations('E-Zone Electronics - Your trusted source for quality electronics and gadgets.'))">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">

    @include('client.partials.translations')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

    <script>
        (function () {
            function syncHeaderHeight() {
                var header = document.querySelector('header');
                if (header) {
                    document.documentElement.style.setProperty(
                        '--header-height',
                        header.getBoundingClientRect().height + 'px'
                    );
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                syncHeaderHeight();

                var header = document.querySelector('header');
                if (header && window.ResizeObserver) {
                    new ResizeObserver(syncHeaderHeight).observe(header);
                }

                // iOS FIX: fonts may not be loaded at DOMContentLoaded;
                // re-sync once fonts are ready so announcement bar / font metrics are correct
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(syncHeaderHeight);
                }

                // iOS FIX: short polling fallback for WebKit where font load
                // events fire unpredictably (e.g. WKWebView / in-app browsers)
                var polls = 0;
                var poll = setInterval(function () {
                    syncHeaderHeight();
                    if (++polls >= 6) clearInterval(poll);
                }, 100);
            });

            window.addEventListener('load', syncHeaderHeight);
            window.addEventListener('resize', syncHeaderHeight);
        })();
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
    pinned: $persist(false),
    isLg: window.innerWidth >= 1024,
    init() {
        if (this.pinned && this.isLg) {
            this.sidebarCollapsed = false;
        }
        window.addEventListener('resize', () => {
            this.isLg = window.innerWidth >= 1024;
        });
    }
}"
    @keydown.escape.window="accountOpen = false; languageOpen = false; mobileMenuOpen = false; mobileSidebarOpen = false"
    class="font-sans antialiased bg-gray-50 text-slate-800">

    @include('client.partials.header')

    <main class="flex min-h-screen main-content-wrapper" style="padding-top: var(--header-height);">

        @include('client.partials.sidebar')

        <div class="flex-1 w-full transition-[margin-left] duration-300 ease-out relative z-0"
             id="main-content-inner"
             style="margin-left: 0"
             x-bind:style="isLg ? (sidebarCollapsed ? 'margin-left:4.5rem' : 'margin-left:17rem') : 'margin-left:0'">
        <script>
            (function(){
                var el = document.getElementById('main-content-inner');
                if (el && window.innerWidth >= 1024) el.style.marginLeft = '4.5rem';
            })();
        </script>

            <div class="w-full">
                @yield('content')
            </div>

            @include('client.partials.footer')
        </div>
    </main>

    <div id="global-toasts" aria-live="polite" aria-atomic="true" class="pointer-events-none fixed inset-0 z-50">
        <div id="global-toasts-mobile" class="sm:hidden pointer-events-none fixed inset-x-0 bottom-4 px-4 flex justify-center items-end">
            <div id="global-toasts-mobile-inner" class="w-full max-w-lg flex flex-col items-center space-y-3"></div>
        </div>
        <div id="global-toasts-desktop" class="hidden sm:flex pointer-events-none fixed top-4 right-4 items-start justify-end">
            <div id="global-toasts-inner" class="w-full flex flex-col items-end space-y-3 max-w-xs"></div>
        </div>
    </div>

    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded',()=>{ Toast && Toast.success({ title: 'Success', message: {!! json_encode(session('success')) !!} }); });</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded',()=>{ Toast && Toast.error({ title: 'Error', message: {!! json_encode(session('error')) !!} }); });</script>
    @endif
    @if(session('info'))
        <script>document.addEventListener('DOMContentLoaded',()=>{ Toast && Toast.info({ title: '', message: {!! json_encode(session('info')) !!} }); });</script>
    @endif
    @if(session('warning'))
        <script>document.addEventListener('DOMContentLoaded',()=>{ Toast && Toast.warning({ title: 'Warning', message: {!! json_encode(session('warning')) !!} }); });</script>
    @endif

    @stack('scripts')
    @include('client.components.universal-popup')
    @include('client.components._review_modal')
    <script src="{{ asset('js/reviews.js') }}"></script>
</body>

</html>
