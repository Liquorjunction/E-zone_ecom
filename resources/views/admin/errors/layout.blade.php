<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Error') | E-Zone Admin</title>
    @include('admin.essentials.styles')
    <style>
        .admin-error-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
            padding: 1rem;
        }

        .admin-error-card {
            width: 100%;
            max-width: 640px;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            padding: 2rem;
        }

        .admin-error-code {
            font-size: 3rem;
            line-height: 1;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        .admin-error-message {
            font-size: 1.125rem;
            color: #334155;
            margin-bottom: 1.5rem;
        }

        .admin-error-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="admin-error-wrap">
        <div class="admin-error-card">
            <p class="text-uppercase text-muted mb-2">Admin Panel</p>
            <h1 class="admin-error-code">@yield('code')</h1>
            <h2 class="h3 mb-2">@yield('heading')</h2>
            <p class="admin-error-message">@yield('message')</p>
            <div class="admin-error-actions">
                @if (auth('admin')->check())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                @else
                    <a href="{{ route('admin.login') }}" class="btn btn-primary">Go to Login</a>
                @endif
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Go Back</a>
            </div>
        </div>
    </div>

    @include('admin.essentials.scripts')
    @stack('scripts')
</body>

</html>
