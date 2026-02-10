@extends('admin.layouts.app')

@section('title', 'Site Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Site Settings</h3>
                </div>
                <div class="card-body">
                    @if($siteSetting)
                        <form id="siteSettingsForm" data-site-id="{{ $siteSetting->id }}">
                            @csrf
                            @method('PUT')

                            {{-- Site Status Toggle --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-{{ $siteSetting->site_status === 'active' ? 'success' : 'warning' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1">
                                                    <i class="fas fa-{{ $siteSetting->site_status === 'active' ? 'check-circle' : 'exclamation-triangle' }}"></i>
                                                    Site Status: <strong>{{ ucfirst($siteSetting->site_status) }}</strong>
                                                </h5>
                                                <p class="mb-0">
                                                    @if($siteSetting->site_status === 'active')
                                                        The website is currently accessible to all users.
                                                    @else
                                                        The website is currently in maintenance mode. Only admins can access.
                                                    @endif
                                                </p>
                                            </div>
                                            <div>
                                                <button type="button"
                                                        class="btn btn-{{ $siteSetting->site_status === 'active' ? 'warning' : 'success' }}"
                                                        onclick="toggleSiteStatus('{{ $siteSetting->site_status === 'active' ? 'inactive' : 'active' }}')">
                                                    <i class="fas fa-{{ $siteSetting->site_status === 'active' ? 'pause' : 'play' }}"></i>
                                                    {{ $siteSetting->site_status === 'active' ? 'Set to Maintenance' : 'Activate Site' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Basic Information --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Site Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="{{ $siteSetting->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="{{ $siteSetting->email }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                               value="{{ $siteSetting->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                               value="{{ $siteSetting->address }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Social Media Links --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="facebook">
                                            <i class="fab fa-facebook"></i> Facebook URL
                                        </label>
                                        <input type="url" class="form-control" id="facebook" name="facebook"
                                               value="{{ $siteSetting->facebook }}" placeholder="https://facebook.com/yourpage">
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="twitter">
                                            <i class="fab fa-twitter"></i> Twitter URL
                                        </label>
                                        <input type="url" class="form-control" id="twitter" name="twitter"
                                               value="{{ $siteSetting->twitter }}" placeholder="https://x.com/yourpage">
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="instagram">
                                            <i class="fab fa-instagram"></i> Instagram URL
                                        </label>
                                        <input type="url" class="form-control" id="instagram" name="instagram"
                                               value="{{ $siteSetting->instagram }}" placeholder="https://instagram.com/yourpage">
                                    </div>
                                </div>
                            </div>

                            {{-- Info Text --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="infotext">Info Text</label>
                                        <textarea class="form-control" id="infotext" name="infotext" rows="3"
                                                  placeholder="Welcome message or important information">{{ $siteSetting->infotext }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <p>No site settings found. Please create initial settings.</p>
                            <button type="button" class="btn btn-primary" onclick="createInitialSettings()">
                                <i class="fas fa-plus"></i> Create Initial Settings
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/site-settings.js') }}"></script>
@endpush
