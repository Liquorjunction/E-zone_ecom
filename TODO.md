# TODO: Fix Admin Panel - Auth, Profile, User Management, Roles

## Current Status
- Admin auth (login/logout) is mostly implemented.
- Profile view and update have issues: method name mismatch, incorrect route names in views.
- User management views have commented-out form actions, index has no data.
- Breadcrumbs not using {{ Breadcrumbs::render() }}.
- Roles controller store method is empty.

## Plan
1. Fix ProfileController method name from updateProfile to update to match route.
2. Update profile views to use correct route name 'admin.profile.update'.
3. Fix breadcrumbs in affected views to use {{ Breadcrumbs::render() }}.
4. Fix user management views: uncomment and correct form routes, implement data loading in index.
5. Implement RoleController store method and related CRUD.

## Affected Files
- `app/Http/Controllers/Admin/ProfileController.php`
- `resources/views/admin/pages/profile.blade.php`
- `resources/views/admin/partials/profile/change_password.blade.php`
- `resources/views/admin/pages/userManagement/index.blade.php`
- `resources/views/admin/pages/userManagement/create.blade.php`
- `resources/views/admin/pages/userManagement/edit.blade.php`
- `resources/views/admin/components/breadcrumbs.blade.php` (per TODO)
- `app/Http/Controllers/Admin/RoleController.php`
- `routes/admin.php` (for roles routes)

## Steps
- [ ] Rename ProfileController method updateProfile to update
- [ ] Update profile.blade.php form action to 'admin.profile.update'
- [ ] Update change_password.blade.php form action to 'admin.profile.update'
- [ ] Update breadcrumbs in profile.blade.php to {{ Breadcrumbs::render() }}
- [ ] Update breadcrumbs in userManagement views to {{ Breadcrumbs::render() }}
- [ ] Update breadcrumbs component to @yield('breadcrumbs')
- [ ] Fix userManagement create.blade.php forms to use 'admin.user-management.store'
- [ ] Fix userManagement edit.blade.php forms to use 'admin.user-management.update' with {id}
- [ ] Implement data loading in userManagement index.blade.php
- [ ] Add roles routes to admin.php
- [ ] Implement RoleController store method
- [ ] Test profile update (image and password)
- [ ] Test user management CRUD
- [ ] Test roles CRUD
