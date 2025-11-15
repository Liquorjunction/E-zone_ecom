# TODO: Fix Breadcrumbs to Use {{ Breadcrumbs::render() }}

## Plan
1. Change the breadcrumbs component to `@yield('breadcrumbs')` instead of `{{ Breadcrumbs::render() }}`.
2. Update each page's `@section('breadcrumbs')` to simply `{{ Breadcrumbs::render() }}` instead of the custom loop.

## Affected Files
- `resources/views/admin/components/breadcrumbs.blade.php`
- `resources/views/admin/pages/home.blade.php`
- `resources/views/admin/pages/profile.blade.php`
- `resources/views/admin/pages/userManagement/create.blade.php`
- `resources/views/admin/pages/userManagement/edit.blade.php`
- `resources/views/admin/pages/userManagement/role-permission.blade.php`

## Steps
- [ ] Edit `resources/views/admin/components/breadcrumbs.blade.php` to use `@yield('breadcrumbs')`
- [ ] Edit `resources/views/admin/pages/home.blade.php` to use `{{ Breadcrumbs::render() }}`
- [ ] Edit `resources/views/admin/pages/profile.blade.php` to use `{{ Breadcrumbs::render() }}`
- [ ] Edit `resources/views/admin/pages/userManagement/create.blade.php` to use `{{ Breadcrumbs::render() }}`
- [ ] Edit `resources/views/admin/pages/userManagement/edit.blade.php` to use `{{ Breadcrumbs::render() }}`
- [ ] Edit `resources/views/admin/pages/userManagement/role-permission.blade.php` to use `{{ Breadcrumbs::render() }}`
