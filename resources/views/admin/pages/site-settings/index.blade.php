@extends('admin.layouts.app')
@section('title', 'Site Settings')
@section('header-title', 'Site Settings Management')
@section('header-description', 'Manage site-wide settings such as contact info, social media links, opening hours, and map iframe')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.site-settings.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Site Settings</h3>
                </div>
                <div class="card-body">
                    @if($siteSetting)
                        @php
                            $weekDays = [
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday',
                            ];

                            $openingHours = [];
                            if (!empty($siteSetting->opening_hours)) {
                                $decodedOpeningHours = json_decode($siteSetting->opening_hours, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedOpeningHours)) {
                                    $openingHours = $decodedOpeningHours;
                                }
                            }
                        @endphp

                        <ul class="nav nav-tabs" id="siteSettingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-settings" type="button" role="tab" aria-controls="general-settings" aria-selected="true">
                                    <i class="fas fa-cog"></i> General Settings
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="opening-hours-tab" data-bs-toggle="tab" data-bs-target="#opening-hours-settings" type="button" role="tab" aria-controls="opening-hours-settings" aria-selected="false">
                                    <i class="fas fa-clock"></i> Opening Hours
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="map-iframe-tab" data-bs-toggle="tab" data-bs-target="#map-iframe-settings" type="button" role="tab" aria-controls="map-iframe-settings" aria-selected="false">
                                    <i class="fas fa-map-marked-alt"></i> Map Iframe
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-4" id="siteSettingsTabContent">
                            <div class="tab-pane fade show active" id="general-settings" role="tabpanel" aria-labelledby="general-tab">
                                <form id="siteSettingsForm" data-site-id="{{ $siteSetting->id }}">
                                    @csrf
                                    @method('PUT')

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


                                    <div class="row">
                                        <div class="col-12">
                                            <label>Info Text</label>
                                        </div>
                                        @php
                                            $infoText = ['text' => '', 'url' => ''];
                                            if (is_array($siteSetting->infotext)) {
                                                if (array_key_exists('text', $siteSetting->infotext) || array_key_exists('url', $siteSetting->infotext)) {
                                                    $infoText['text'] = (string) ($siteSetting->infotext['text'] ?? '');
                                                    $infoText['url'] = (string) ($siteSetting->infotext['url'] ?? '');
                                                } else {
                                                    $legacyText = '';
                                                    foreach ($siteSetting->infotext as $value) {
                                                        if (is_string($value) && trim($value) !== '') {
                                                            $legacyText = $value;
                                                            break;
                                                        }
                                                    }
                                                    $infoText['text'] = $legacyText;
                                                }
                                            } elseif (is_string($siteSetting->infotext) && trim($siteSetting->infotext) !== '') {
                                                $infoText['text'] = $siteSetting->infotext;
                                            }
                                        @endphp
                                        <div class="col-md-8 mb-2">
                                            <input type="text" class="form-control infotext-input" name="infotext[text]" maxlength="1000"
                                                value="{{ $infoText['text'] }}" placeholder="Info text">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <input type="url" class="form-control" name="infotext[url]" maxlength="1000"
                                                value="{{ $infoText['url'] }}" placeholder="https://example.com/offer">
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
                            </div>

                            <div class="tab-pane fade" id="opening-hours-settings" role="tabpanel" aria-labelledby="opening-hours-tab">
                                <form id="openingHoursForm" data-site-id="{{ $siteSetting->id }}">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-3">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%;">Day</th>
                                                    <th style="width: 25%;">Open Time</th>
                                                    <th style="width: 25%;">Close Time</th>
                                                    <th style="width: 30%;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($weekDays as $dayKey => $dayLabel)
                                                    @php
                                                        $dayData = $openingHours[$dayKey] ?? [];
                                                        $isClosed = filter_var($dayData['is_closed'] ?? false, FILTER_VALIDATE_BOOLEAN);
                                                        $openValue = $dayData['open'] ?? '';
                                                        $closeValue = $dayData['close'] ?? '';
                                                    @endphp
                                                    <tr data-day="{{ $dayKey }}">
                                                        <td class="font-weight-bold align-middle">{{ $dayLabel }}</td>
                                                        <td>
                                                            <input type="time"
                                                                   class="form-control day-time-input"
                                                                   data-field="open"
                                                                   value="{{ $openValue }}"
                                                                   {{ $isClosed ? 'disabled' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="time"
                                                                   class="form-control day-time-input"
                                                                   data-field="close"
                                                                   value="{{ $closeValue }}"
                                                                   {{ $isClosed ? 'disabled' : '' }}>
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                       class="custom-control-input day-closed-checkbox"
                                                                       id="{{ $dayKey }}_closed"
                                                                       {{ $isClosed ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="{{ $dayKey }}_closed">Closed</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <input type="hidden" id="opening_hours" name="opening_hours" value="{{ $siteSetting->opening_hours }}">

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Opening Hours
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="map-iframe-settings" role="tabpanel" aria-labelledby="map-iframe-tab">
                                <form id="mapIframeForm" data-site-id="{{ $siteSetting->id }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="map_iframe">Map Iframe Code</label>
                                        <textarea class="form-control"
                                                  id="map_iframe"
                                                  name="map_iframe"
                                                  rows="8"
                                                  placeholder="Paste Google map iframe code here...">{{ $siteSetting->map_iframe }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Map Iframe
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
