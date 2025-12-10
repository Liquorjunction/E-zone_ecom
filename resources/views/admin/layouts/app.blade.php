<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    {{-- Sidebar hover functionality --}}
    <style>
        .sidebar {
            transition: width 0.3s ease, transform 0.3s ease;
        }

        .sidebar.sidebar-mini {
            width: 80px;
        }

        .sidebar.sidebar-mini:hover {
            width: 260px;
        }

        .sidebar.sidebar-mini:not(:hover) .item-name,
        .sidebar.sidebar-mini:not(:hover) .right-icon {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar .item-name,
        .sidebar .right-icon {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');

            if (sidebar) {
                // Start with collapsed sidebar
                sidebar.classList.add('sidebar-mini');
                document.body.classList.add('sidebar-mini');

                // Expand on hover
                sidebar.addEventListener('mouseenter', function() {
                    sidebar.classList.remove('sidebar-mini');
                    document.body.classList.remove('sidebar-mini');
                });

                // Collapse on mouse leave
                sidebar.addEventListener('mouseleave', function() {
                    sidebar.classList.add('sidebar-mini');
                    document.body.classList.add('sidebar-mini');
                });
            }
        });
    </script>
</body>

</html>
