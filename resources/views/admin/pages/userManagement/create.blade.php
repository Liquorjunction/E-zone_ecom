@extends('admin.layouts.app')

@section('header-title', 'Create User')
@section('header-description', 'Add a new user to the system')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.user-management.create') as $breadcrumb)
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
            <form method="POST" action="{{ route('admin.user-management.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-xl-3 col-lg-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Add New User</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="profile-img-edit position-relative">
                                        <img id="profile-preview" src="{{ asset('admin_assets/images/avatars/01.png') }}"
                                            alt="profile-pic"
                                            class="theme-color-default-img profile-pic rounded avatar-100">
                                        {{-- ... other theme images ... --}}
                                        <div class="upload-icone bg-primary">
                                            <label for="profile_image" style="cursor: pointer;">
                                                <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24">
                                                    <path fill="#ffffff"
                                                        d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z">
                                                    </path>
                                                </svg>
                                            </label>
                                            {{-- FIX: Changed name to profile_image --}}
                                            <input class="file-upload" type="file" id="profile_image"
                                                name="profile_image" accept="image/*" style="display: none;"
                                                onchange="previewImage(this)">
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
                                    {{-- FIX: Dynamically populate roles --}}
                                    <select name="role" class="selectpicker form-control" data-style="py-0" required>
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">New User Information</h4>
                                </div>
                                <a href="{{ route('admin.user-management.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-arrow-left mr-2"></i>Back
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="new-user-info">
                                    {{-- This form tag is removed, we are using the outer one --}}
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="name">Full Name:</label>
                                            {{-- FIX: Changed name attribute --}}
                                            <input type="text" name="name" class="form-control" id="name"
                                                placeholder="Full Name" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="email">Email:</label>
                                            <input type="email" name="email" class="form-control" id="email"
                                                placeholder="Email" required>
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="phone">Phone:</label>
                                            <input type="text" name="phone" class="form-control" id="phone"
                                                placeholder="Phone Number" value="{{ old('phone', '') }}">
                                            @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="date_of_birth">Date of Birth:</label>
                                            <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" value="{{ old('date_of_birth', '') }}">
                                            @error('date_of_birth')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="remarks">Remarks:</label>
                                            <textarea name="remarks" class="form-control" id="remarks"
                                                placeholder="Remarks" rows="3">{{ old('remarks', '') }}</textarea>
                                            @error('remarks')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="mb-3">Security</h5>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="password">Password:</label>
                                            <div class="position-relative">
                                                <input type="password" name="password" class="form-control" id="password"
                                                    placeholder="Password" required>
                                                <button type="button" class="btn-toggle-password" onclick="togglePassword('password'); return false;" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                                                    <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="password_confirmation">Confirm Password:</label>
                                            <div class="position-relative">
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    id="password_confirmation" placeholder="Repeat Password " required>
                                                <button type="button" class="btn-toggle-password" onclick="togglePassword('password_confirmation'); return false;" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                                                    <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </button>
                                            </div>
                                            @error('password_confirmation')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Add New User</button>
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

@section('scripts')
    <script src="{{ asset('admin_assets/js/image-preview.js') }}"></script>
@endsection

@push('scripts')
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const svg = field.parentElement.querySelector('svg');

            if (field.type === 'password') {
                field.type = 'text';
                // Change to eye-off icon (hide)
                svg.setAttribute('stroke', 'currentColor');
                svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                field.type = 'password';
                // Change back to eye icon (show)
                svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
@endpush
