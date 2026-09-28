<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @php
        $rawTitle = trim($__env->yieldContent('title') ?: config('app.name', 'Ezone Electronics'));
        $rawDescription = trim($__env->yieldContent('description') ?: statictranslations('Ezone Electronics - Your trusted source for quality electronics and gadgets.'));
        $title = __($rawTitle);
        $description = __($rawDescription);
        $descriptionText = strip_tags($description);
        if (strlen($descriptionText) > 160) {
            $descriptionText = \Illuminate\Support\Str::limit($descriptionText, 157, '...');
        }
        $ogTitle = trim($__env->yieldContent('og_title') ?: $title);
        $ogDescription = trim($__env->yieldContent('og_description') ?: $descriptionText);
        $defaultOgImage = file_exists(public_path('images/og-default.jpg')) ? asset('images/og-default.jpg') : asset('icon/fav-icon-high-resArtboard-1-150x150.png');
        $ogImage = trim($__env->yieldContent('og_image') ?: $defaultOgImage);
        $ogUrl = trim($__env->yieldContent('og_url') ?: url()->current());

        // dd($title, $description, $descriptionText, $ogTitle, $ogDescription, $ogImage, $ogUrl);
    @endphp

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $descriptionText }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:type" content="@yield('og_type', 'website')">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/ga4-ecommerce.js') }}"></script>

    <link rel="icon" href="{{ asset('icon/fav-icon-high-resArtboard-1-150x150.png') }}" type="image/x-icon">

    @include('client.partials.translations')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @php
        $organizationLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Ezone Electronics',
            'url' => url('/'),
            'logo' => asset('icon/fav-icon-high-resArtboard-1-150x150.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+212665461262',
                'contactType' => 'customer service',
            ],
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($organizationLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <script>
        (function() {
            function syncViewportHeightVar() {
                var viewportHeight = window.innerHeight;
                if (window.visualViewport && window.visualViewport.height) {
                    viewportHeight = window.visualViewport.height;
                }

                document.documentElement.style.setProperty(
                    '--app-vh',
                    (viewportHeight * 0.01) + 'px'
                );
            }

            function syncHeaderHeight() {
                var header = document.querySelector('header');
                if (header) {
                    document.documentElement.style.setProperty(
                        '--header-height',
                        header.getBoundingClientRect().height + 'px'
                    );
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                syncViewportHeightVar();
                syncHeaderHeight();

                var header = document.querySelector('header');
                if (header && window.ResizeObserver) {
                    new ResizeObserver(syncHeaderHeight).observe(header);
                }

                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(syncHeaderHeight);
                }

                var polls = 0;
                var poll = setInterval(function() {
                    syncViewportHeightVar();
                    syncHeaderHeight();
                    if (++polls >= 6) clearInterval(poll);
                }, 100);

                if (window.visualViewport) {
                    window.visualViewport.addEventListener('resize', syncViewportHeightVar, {
                        passive: true
                    });
                    window.visualViewport.addEventListener('scroll', syncViewportHeightVar, {
                        passive: true
                    });
                }
            });

            window.addEventListener('orientationchange', syncViewportHeightVar);
            window.addEventListener('resize', syncViewportHeightVar);
            window.addEventListener('load', syncViewportHeightVar);
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

        <div class="flex-1 w-full transition-[margin-left] duration-300 ease-out relative z-0" id="main-content-inner"
            style="margin-left: 0"
            x-bind:style="isLg ? (sidebarCollapsed ? 'margin-left:4.5rem' : 'margin-left:17rem') : 'margin-left:0'">
            <script>
                (function() {
                    var el = document.getElementById('main-content-inner');
                    if (el && window.innerWidth >= 1024) el.style.marginLeft = '4.5rem';
                })();
            </script>

            <div class="w-full">
                @yield('content')
            </div>

            @if (!request()->routeIs('contact'))
                {{-- WhatsApp Chat Floating Button --}}
                @php
                    $whatsappNumber = preg_replace('/[^0-9]/', '', get_site_setting('phone', ''));
                @endphp
                <a href="https://wa.me/{{ $whatsappNumber }}?text=Bonjour, je souhaite obtenir des informations sur vos produits." target="_blank" rel="noopener"
                    aria-label="Chat on WhatsApp"
                    class="fixed z-50 bottom-6 right-6 md:bottom-8 md:right-8 bg-green-500 hover:bg-green-600 rounded-full shadow-lg p-3 flex items-center justify-center transition-colors duration-300"
                    style="box-shadow: 0 4px 16px rgba(0,0,0,0.18);">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32"
                        fill="white">
                        <path
                            d="M16 3C9.373 3 4 8.373 4 15c0 2.637.86 5.13 2.484 7.2L4 29l7.016-2.293A12.93 12.93 0 0 0 16 27c6.627 0 12-5.373 12-12S22.627 3 16 3zm0 22.5c-2.07 0-4.09-.57-5.83-1.65l-.415-.25-4.17 1.36 1.37-4.06-.27-.42A9.98 9.98 0 0 1 6 15c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10zm5.13-7.47c-.28-.14-1.65-.81-1.9-.9-.25-.09-.43-.14-.61.14-.18.28-.7.9-.86 1.08-.16.18-.32.2-.6.07-.28-.14-1.18-.44-2.25-1.4-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.32.42-.48.14-.16.18-.28.28-.46.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.02-.22-.53-.45-.46-.61-.47-.16-.01-.34-.01-.52-.01-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.3 0 1.36.99 2.68 1.13 2.87.14.18 1.95 2.98 4.73 4.06.66.28 1.18.45 1.58.58.66.21 1.26.18 1.74.11.53-.08 1.65-.67 1.88-1.32.23-.65.23-1.2.16-1.32-.07-.12-.25-.18-.53-.32z" />
                    </svg>
                </a>
            @endif

            @include('client.partials.footer')
        </div>
    </main>

    <div id="global-toasts" aria-live="polite" aria-atomic="true" class="pointer-events-none fixed inset-0 z-50">
        <div id="global-toasts-mobile"
            class="sm:hidden pointer-events-none fixed inset-x-0 bottom-4 px-4 flex justify-center items-end">
            <div id="global-toasts-mobile-inner" class="w-full max-w-lg flex flex-col items-center space-y-3"></div>
        </div>
        <div id="global-toasts-desktop"
            class="hidden sm:flex pointer-events-none fixed top-4 right-4 items-start justify-end">
            <div id="global-toasts-inner" class="w-full flex flex-col items-end space-y-3 max-w-xs"></div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Toast && Toast.success({
                    title: 'Success',
                    message: {!! json_encode(session('success')) !!}
                });
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Toast && Toast.error({
                    title: 'Error',
                    message: {!! json_encode(session('error')) !!}
                });
            });
        </script>
    @endif
    @if (session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Toast && Toast.info({
                    title: '',
                    message: {!! json_encode(session('info')) !!}
                });
            });
        </script>
    @endif
    @if (session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Toast && Toast.warning({
                    title: 'Warning',
                    message: {!! json_encode(session('warning')) !!}
                });
            });
        </script>
    @endif

    @stack('scripts')
    @include('client.components.universal-popup')
    @include('client.components._review_modal')
    <script src="{{ asset('js/reviews.js') }}"></script>
</body>

</html>
