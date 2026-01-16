@extends('admin.layouts.app')

@section('header-title', 'View User')
@section('header-description', 'User profile and information')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.user-management.view', $admin) as $breadcrumb)
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
                                <h4 class="card-title">User Profile</h4>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="form-group">
                                <img id="profile-preview"
                                    src="{{ $admin->profile_image ? asset('storage/avatars/' . $admin->profile_image) : asset('admin_assets/images/avatars/01.png') }}"
                                    alt="profile-pic"
                                    class="theme-color-default-img profile-pic rounded avatar-100">
                            </div>
                            <h5 class="mb-2">{{ $admin->name }}</h5>
                            <p class="text-muted mb-3">{{ $admin->roles->pluck('name')->join(', ') }}</p>
                            <div class="mb-3">
                                @if ($admin->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                            <a href="{{ route('admin.user-management.edit', ['id' => $admin->id]) }}" class="btn btn-sm btn-primary w-100 mb-2">
                                <i class="fa fa-edit mr-2"></i>Edit User
                            </a>
                            <a href="{{ route('admin.user-management.index') }}" class="btn btn-sm btn-secondary w-100">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">User Information</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="new-user-info">
                                <h5 class="mb-3">Basic Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Full Name:</strong></label>
                                        <p class="text-muted">{{ $admin->name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Email:</strong></label>
                                        <p class="text-muted">{{ $admin->email }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Phone:</strong></label>
                                        <p class="text-muted">{{ $admin->phone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Date of Birth:</strong></label>
                                        <p class="text-muted">{{ $admin->date_of_birth ? \Carbon\Carbon::parse($admin->date_of_birth)->format('Y-m-d') : 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label"><strong>Remarks:</strong></label>
                                        <p class="text-muted">{{ $admin->remarks ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <hr>
                                <h5 class="mb-3">Account Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Username:</strong></label>
                                        <p class="text-muted">{{ $admin->username }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Status:</strong></label>
                                        <p>
                                            @if ($admin->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Role:</strong></label>
                                        <p class="text-muted">{{ $admin->roles->pluck('name')->join(', ') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Created At:</strong></label>
                                        <p class="text-muted">{{ $admin->created_at->format('Y-m-d H:i:s') }}</p>
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
