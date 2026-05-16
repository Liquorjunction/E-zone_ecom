@extends('admin.layouts.app')

@section('header-title', 'View Role')
@section('header-description', 'Role details and permissions')

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin_assets/css/roles.css') }}">
@endpush

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.roles-permissions.view', $role) as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div>
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Role Information</h4>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="avatar avatar-lg bg-primary-light rounded">
                                    <span class="avatar-text">{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <h5 class="mb-2 text-capitalize">{{ str_replace('-', ' ', $role->name) }}</h5>
                            <p class="text-muted mb-3">
                                <small>{{ $role->permissions->count() }} Permissions</small>
                            </p>
                            @can('role.update')
                                <a href="{{ route('admin.roles-permissions.edit', ['id' => $role->id]) }}" class="btn btn-sm btn-primary w-100 mb-2">
                                    <i class="fa fa-edit mr-2"></i>Edit Role
                                </a>
                            @endcan
                            <a href="{{ route('admin.roles-permissions.index') }}" class="btn btn-sm btn-secondary w-100">
                                <i class="fa fa-arrow-left mr-2"></i>Back to Roles
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Assigned Permissions</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($role->permissions->isEmpty())
                                <div class="alert alert-info" role="alert">
                                    <strong>No permissions assigned</strong> to this role yet.
                                </div>
                            @else
                                <div class="row">
                                    @foreach ($permissionGroups as $module => $permissions)
                                        @php
                                            // Filter to only show assigned permissions
                                            $assignedPermissions = $permissions->filter(function($permission) use ($role) {
                                                return $role->hasPermissionTo($permission->name);
                                            });
                                        @endphp

                                        @if ($assignedPermissions->isNotEmpty())
                                            <div class="col-md-6 mb-4">
                                                <div class="permission-group">
                                                    <h6 class="mb-3 text-uppercase fw-bold">
                                                        <i class="fa fa-folder mr-2"></i>{{ str_replace('_', ' ', $module) }}
                                                    </h6>
                                                    <div class="permission-list">
                                                        @foreach ($assignedPermissions as $permission)
                                                            <div class="permission-item mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="perm_{{ $permission->id }}" checked disabled>
                                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                        <span class="permission-name">{{ str_replace($module . '.', '', $permission->name) }}</span>
                                                                        @if ($permission->description)
                                                                            <small class="text-muted d-block">{{ $permission->description }}</small>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Summary Card --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <div class="header-title">
                                <h4 class="card-title">Summary</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="shrink-0">
                                            <div class="avatar avatar-md bg-primary-light rounded">
                                                <span class="avatar-text text-primary">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="grow ms-3">
                                            <h6 class="mb-0">Total Permissions</h6>
                                            <p class="text-muted mb-0">{{ $role->permissions->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="shrink-0">
                                            <div class="avatar avatar-md bg-success-light rounded">
                                                <span class="avatar-text text-success">
                                                    <i class="fa fa-users"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="grow ms-3">
                                            <h6 class="mb-0">Permission Groups</h6>
                                            <p class="text-muted mb-0">{{ $permissionGroups->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="shrink-0">
                                            <div class="avatar avatar-md bg-info-light rounded">
                                                <span class="avatar-text text-info">
                                                    <i class="fa fa-shield"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="grow ms-3">
                                            <h6 class="mb-0">Guard</h6>
                                            <p class="text-muted mb-0 text-capitalize">{{ $role->guard_name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
