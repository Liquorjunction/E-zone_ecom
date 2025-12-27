<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Zone') | Ecommerce </title>
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

        <div class="container-fluid">
            {{-- Error Message --}}
            @if ($errors->any())
                <div class="alert alert-danger w-100">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Content --}}
        @yield('content')

        {{-- Settings --}}
        @include('admin.components.settings')
        {{-- Footer --}}
        @include('admin.partials.footer')
    </main>

    {{-- Toast Notifications --}}
    @include('admin.components.toast')

    @include('admin.essentials.scripts')

    {{-- Page-specific scripts --}}
    @stack('scripts')

    {{-- Enhanced Sidebar functionality with Pin/Unpin --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const pinBtn = document.getElementById('sidebarPinBtn');
            let isPinned = localStorage.getItem('sidebarPinned') === 'true';
            let hoverTimeout;

            if (sidebar && pinBtn) {
                // Initialize sidebar state
                if (!isPinned) {
                    sidebar.classList.add('sidebar-mini');
                } else {
                    sidebar.classList.add('sidebar-pinned');
                    sidebar.classList.remove('sidebar-mini');
                }

                // Pin/Unpin button click handler
                pinBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    isPinned = !isPinned;
                    localStorage.setItem('sidebarPinned', isPinned);

                    if (isPinned) {
                        sidebar.classList.add('sidebar-pinned');
                        sidebar.classList.remove('sidebar-mini');
                        pinBtn.setAttribute('title', 'Unpin Sidebar');
                    } else {
                        sidebar.classList.remove('sidebar-pinned');
                        sidebar.classList.add('sidebar-mini');
                        pinBtn.setAttribute('title', 'Pin Sidebar');
                    }
                });

                // Hover functionality (only when not pinned)
                sidebar.addEventListener('mouseenter', function() {
                    if (!isPinned) {
                        clearTimeout(hoverTimeout);
                        // Small delay for smoother hover experience
                        hoverTimeout = setTimeout(() => {
                            sidebar.classList.remove('sidebar-mini');
                        }, 50);
                    }
                });

                sidebar.addEventListener('mouseleave', function() {
                    if (!isPinned) {
                        clearTimeout(hoverTimeout);
                        // Delay collapse to prevent flickering
                        hoverTimeout = setTimeout(() => {
                            sidebar.classList.add('sidebar-mini');
                        }, 100);
                    }
                });

                // Prevent sidebar from collapsing when clicking inside
                sidebar.addEventListener('click', function(e) {
                    if (!isPinned) {
                        clearTimeout(hoverTimeout);
                    }
                });
            }
        });
    </script>
</body>

</html>
