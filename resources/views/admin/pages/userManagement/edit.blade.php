@extends('admin.layouts.app')

@section('header-title', 'Edit User')
@section('header-description', 'Update user information and settings')

@section('breadcrumbs')
    {{-- Assuming you have a breadcrumb for this --}}
    @foreach (Breadcrumbs::generate('admin.user-management.edit', $admin) as $breadcrumb)
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
            {{-- FIX: We will use ONE form for the entire page --}}
            <form method="POST" action="{{ route('admin.user-management.update', $admin->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- FIX: Added PUT method for updates --}}
                <div class="row">
                    <div class="col-xl-3 col-lg-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Edit User</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="profile-img-edit position-relative">
                                        {{-- FIX: Show existing profile picture --}}
                                        <img src="{{ $admin->profile_image ? asset('admin_assets/images/avatars/' . $admin->profile_image) : asset('admin_assets/images/avatars/01.png') }}"
                                            alt="profile-pic"
                                            class="theme-color-default-img profile-pic rounded avatar-100">
                                        {{-- ... other theme images ... --}}
                                        <div class="upload-icone bg-primary">
                                            <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24">
                                                <path fill="#ffffff"
                                                    d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z">
                                                </path>
                                            </svg>
                                            {{-- FIX: Changed name to profile_image --}}
                                            <input class="file-upload" type="file" name="profile_image" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="img-extension mt-3">
                                        <div class="d-inline-block align-items-center">
                                            <span>Only .jpg, .png, .jpeg allowed</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">User Role:</label>
                                    {{-- FIX: Dynamically populate roles and select the current one --}}
                                    <select name="role" class="selectpicker form-control" data-style="py-0" required>
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ $admin->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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
                                    {{-- This form tag is removed --}}
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="name">Full Name:</label>
                                            {{-- FIX: Populate existing data --}}
                                            <input type="text" name="name" class="form-control" id="name"
                                                placeholder="Full Name" value="{{ old('name', $admin->name) }}" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="email">Email:</label>
                                            {{-- FIX: Populate existing data --}}
                                            <input type="email" name="email" class="form-control" id="email"
                                                placeholder="Email" value="{{ old('email', $admin->email) }}" required>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="mb-3">Security</h5>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="username">User Name:</label>
                                            <input type="text" name="username" class="form-control" id="username"
                                                value="{{ $admin->username }}" readonly disabled>
                                            <small>Usernames are auto-generated and cannot be changed.</small>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="password">Password:</label>
                                            <input type="password" name="password" class="form-control" id="password"
                                                placeholder="Password">
                                            <small>Leave blank to keep current password</small>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="password_confirmation">Repeat Password:</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                id="password_confirmation" placeholder="Repeat Password">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update User</button>
                                    {{-- This form tag is removed --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form> {{-- FIX: Closed the single, main form --}}
        </div>
    </div>
@endsection
