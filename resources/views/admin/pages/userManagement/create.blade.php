@extends('admin.layouts.app')

@section('header-title', 'Create User')
@section('header-description', 'Add a new user to the system')

@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.user-management.create') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
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
                                <h4 class="card-title">Add New User</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            {{--
                                Note: This form is separate from the main user info form.
                                You might want to combine these or ensure this form has its own
                                route and controller method.
                            --}}
                            <form method="POST" action="{{ route('admin.user-management.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="profile-img-edit position-relative">
                                        <img src="{{ asset('admin_assets/images/avatars/01.png') }}" alt="profile-pic" class="theme-color-default-img profile-pic rounded avatar-100">
                                        <img src="{{ asset('admin_assets/images/avatars/avtar_1.png') }}" alt="profile-pic" class="theme-color-purple-img profile-pic rounded avatar-100">
                                        <img src="{{ asset('admin_assets/images/avatars/avtar_2.png') }}" alt="profile-pic" class="theme-color-blue-img profile-pic rounded avatar-100">
                                        <img src="{{ asset('admin_assets/images/avatars/avtar_4.png') }}" alt="profile-pic" class="theme-color-green-img profile-pic rounded avatar-100">
                                        <img src="{{ asset('admin_assets/images/avatars/avtar_5.png') }}" alt="profile-pic" class="theme-color-yellow-img profile-pic rounded avatar-100">
                                        <img src="{{ asset('admin_assets/images/avatars/avtar_3.png') }}" alt="profile-pic" class="theme-color-pink-img profile-pic rounded avatar-100">
                                        <div class="upload-icone bg-primary">
                                            <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24">
                                                <path fill="#ffffff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z"></path>
                                            </svg>
                                            {{-- Ideally, this input should have a 'name' attribute --}}
                                            <input class="file-upload" type="file" name="profile_picture" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="img-extension mt-3">
                                        <div class="d-inline-block align-items-center">
                                            <span>Only</span>
                                            <a href="javascript:void();">.jpg</a>
                                            <a href="javascript:void();">.png</a>
                                            <a href="javascript:void();">.jpeg</a>
                                            <span>allowed</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">User Role:</label>
                                    {{-- Added name="role" --}}
                                    <select name="role" class="selectpicker form-control" data-style="py-0">
                                        <option>Select</option>
                                        <option>Web Designer</option>
                                        <option>Web Developer</option>
                                        <option>Tester</option>
                                        <option>Php Developer</option>
                                        <option>Ios Developer </option>
                                    </select>
                                </div>
                            </form> {{-- This form was unclosed. I've closed it here. --}}
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">New User Information</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="new-user-info">
                                {{-- Added method, action, and @csrf token --}}
                                <form method="POST" action="{{ route('admin.user-management.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="fname">Full Name:</label>
                                            {{-- Added name="first_name" --}}
                                            <input type="text" name="first_name" class="form-control" id="fname" placeholder="Full Name">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label class="form-label">Country:</label>
                                            {{-- Added name="country" --}}
                                            <select name="country" class="selectpicker form-control" data-style="py-0">
                                                <option>Select Country</option>
                                                <option>Caneda</option>
                                                <option>Noida</option>
                                                <option>USA</option>
                                                <option>India</option>
                                                <option>Africa</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="email">Email:</label>
                                            {{-- Added name="email" --}}
                                            <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="mb-3">Security</h5>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label" for="uname">User Name:</label>
                                            {{-- Added name="username" --}}
                                            <input type="text" name="username" class="form-control" id="uname" placeholder="User Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="pass">Password:</label>
                                            {{-- Added name="password" --}}
                                            <input type="password" name="password" class="form-control" id="pass" placeholder="Password">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="rpass">Repeat Password:</label>
                                            {{-- Added name="password_confirmation" --}}
                                            <input type="password" name="password_confirmation" class="form-control" id="rpass" placeholder="Repeat Password ">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Add New User</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
