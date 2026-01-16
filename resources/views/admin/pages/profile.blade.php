@extends('admin.layouts.app')

@section('header-title', 'Profile')
@section('header-description', 'Manage your account settings and preferences')

@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.profile') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')

    {{-- I've added a @push('styles') directive to add page-specific CSS --}}
    {{-- This will be injected into the <head> if your layout.app file has @stack('styles') --}}
@push('styles')
    <style>
        .user-profile {
            /* This class already has position-relative, which is needed */
            /* We make it inline-block to wrap its content snugly */
            display: inline-block;
        }

        .edit-profile-button {
            position: absolute;
            bottom: 5px;  /* Position button at the bottom */
            right: 10px; /* Position button from the right */
            background-color: #ffffff; /* White background */
            border: 1px solid #dee2e6; /* Light grey border */
            border-radius: 50%;        /* Make it perfectly circular */
            width: 32px;               /* Fixed width */
            height: 32px;              /* Fixed height */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            transition: all 0.2s ease-in-out;
        }

        .edit-profile-button svg {
            width: 16px;  /* Size the icon inside */
            height: 16px;
            color: #495057; /* Icon color */
        }

        .edit-profile-button:hover {
            background-color: #f8f9fa; /* Slight hover color change */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Hide the default file input */
        #profileImageInput {
            display: none;
        }
    </style>
@endpush

    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex flex-wrap align-items-center">
                                <div class="d-flex flex-wrap align-items-center mb-3 mb-sm-0">
                                    {{-- Get auth user name and role --}}
                                    <h4 class="me-2 h4">{{ \App\Helpers\AdminHelper::name() ?? 'Admin User' }}</h4>
                                    <span>- {{ \App\Helpers\AdminHelper::role() ?? 'Administrator' }}</span>
                                </div>
                            </div>
                            <ul class="d-flex nav nav-pills mb-0 text-center profile-tab nav-slider"
                                data-toggle="slider-tab" id="profile-pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link show active" data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                                        type="button">Change Password</button>
                                </li>
                            </ul>
                            {{-- Modals --}}
                            @include('admin.partials.profile.change_password')
                        </div>
                    </div>
                </div>
                <div class="profile-content tab-content">
                    <div id="profile-profile" class="tab-pane fade active show" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <div class="header-title">
                                    <h4 class="card-title">Profile</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    {{--
                                        UPDATED IMAGE CONTAINER:
                                        - The form now wraps the image and the new <label> button.
                                        - The old .edit-icon div is removed.
                                        - The <label> is styled with CSS to be the overlay button.
                                        - This structure works for both existing and default images.
                                    --}}
                                    <div class="user-profile position-relative">
                                    <form id="profileImageForm" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input type="file" id="profileImageInput" name="profile_image" accept="image/*">

                                            <img id="profileImage" src="{{ \App\Helpers\AdminHelper::profileImageUrl() }}" alt="User-Profile"
                                                class="theme-color-default-img img-fluid rounded-pill avatar-100">

                                            {{-- This label is the new edit button. Clicking it triggers the file input. --}}
                                            <label for="profileImageInput" class="edit-profile-button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                </svg>
                                            </label>
                                        </form>
                                    </div>
                                    <div class="mt-3">
                                        <h3 class="d-inline-block">{{ \App\Helpers\AdminHelper::name() ?? 'Admin User' }}</h3>
                                        <p class="d-inline-block pl-3"> - {{ \App\Helpers\AdminHelper::role() ?? 'Administrator' }}</p>
                                        <p class="mb-0">Welcome, {{ \App\Helpers\AdminHelper::name() ?? 'Admin User' }} </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="header-title">
                                    <h4 class="card-title">About User</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mt-2">
                                    <h6 class="mb-1">Joined:</h6>
                                    <p>{{ \App\Helpers\AdminHelper::user() ? \App\Helpers\AdminHelper::user()->created_at : now() }}</p>
                                </div>
                                <div class="mt-2">
                                    <h6 class="mb-1">Email:</h6>
                                    <p><a href="#" class="text-body"> {{ \App\Helpers\AdminHelper::email() ?? 'N/A' }}</a></p>
                                </div>
                                <div class="mt-2">
                                    <h6 class="mb-1">Email Verified:</h6>
                                    {{-- Show Verified or not --}}
                                    <p>
                                        @if (\App\Helpers\AdminHelper::user() && \App\Helpers\AdminHelper::user()->email_verified_at)
                                            <span style="color: green">Verified</span>
                                        @else
                                            <span style="color: red">Not Verified</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--
    SIMPLIFIED JAVASCRIPT:
    - Moved to a @push('scripts') stack.
    - Removed the old click listeners for 'profileImage' and 'editIcon'
      as the <label> now handles this functionality.
    - Kept only the auto-submit logic.
--}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profileImageInput');
    const profileImageForm = document.getElementById('profileImageForm');
    const profileImage = document.getElementById('profileImage');
    const changePasswordForm = document.getElementById('changePasswordForm');

    // Auto-submit profile image form via AJAX when file is selected
    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            const formData = new FormData(profileImageForm);
            fetch(profileImageForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the image src
                    profileImage.src = data.image_url;
                    // Update navbar profile image
                    const navbarImage = document.getElementById('navbar-profile-image');
                    if (navbarImage) {
                        navbarImage.src = data.image_url;
                    }
                    // Show success message using toast
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while updating the profile image.', 'error');
            });
        }
    });

    // Submit change password form via AJAX
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(changePasswordForm);
            fetch(changePasswordForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                    modal.hide();
                    // Reset the form
                    changePasswordForm.reset();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while changing the password.', 'error');
            });
        });
    }
});
</script>
@endpush
