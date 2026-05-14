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
<div class="container-fluid pt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title mb-0 py-2">Site Settings</h4>
                </div>
                <div class="card-body p-4">
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

                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs mb-4" id="siteSettingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-settings" type="button" role="tab" aria-controls="general-settings" aria-selected="true">
                                    <i class="fas fa-cog me-2"></i> General
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4" id="opening-hours-tab" data-bs-toggle="tab" data-bs-target="#opening-hours-settings" type="button" role="tab" aria-controls="opening-hours-settings" aria-selected="false">
                                    <i class="fas fa-clock me-2"></i> Opening Hours
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4" id="map-iframe-tab" data-bs-toggle="tab" data-bs-target="#map-iframe-settings" type="button" role="tab" aria-controls="map-iframe-settings" aria-selected="false">
                                    <i class="fas fa-map-marked-alt me-2"></i> Map Iframe
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="siteSettingsTabContent">

                            <!-- General Settings Tab -->
                            <div class="tab-pane fade show active" id="general-settings" role="tabpanel" aria-labelledby="general-tab">
                                <form id="siteSettingsForm" data-site-id="{{ $siteSetting->id }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="alert alert-{{ $siteSetting->site_status === 'active' ? 'success' : 'warning' }} d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-0">
                                                <div class="mb-3 mb-md-0">
                                                    <h5 class="mb-1">
                                                        <i class="fas fa-{{ $siteSetting->site_status === 'active' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                                                        Site Status: <strong>{{ ucfirst($siteSetting->site_status) }}</strong>
                                                    </h5>
                                                    <p class="mb-0 text-muted">
                                                        @if($siteSetting->site_status === 'active')
                                                            The website is currently accessible to all users.
                                                        @else
                                                            The website is currently in maintenance mode. Only admins can access.
                                                        @endif
                                                    </p>
                                                </div>
                                                <div>
                                                    <button type="button"
                                                            class="btn btn-{{ $siteSetting->site_status === 'active' ? 'warning' : 'success' }} shadow-sm"
                                                            onclick="toggleSiteStatus('{{ $siteSetting->site_status === 'active' ? 'inactive' : 'active' }}')">
                                                        <i class="fas fa-{{ $siteSetting->site_status === 'active' ? 'pause' : 'play' }} me-1"></i>
                                                        {{ $siteSetting->site_status === 'active' ? 'Set to Maintenance' : 'Activate Site' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="name" class="form-label fw-semibold">Site Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ $siteSetting->name }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ $siteSetting->email }}" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="phone" class="form-label fw-semibold">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $siteSetting->phone }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address" class="form-label fw-semibold">Address</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{ $siteSetting->address }}">
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="facebook" class="form-label fw-semibold">
                                                <i class="fab fa-facebook text-primary me-1"></i> Facebook URL
                                            </label>
                                            <input type="url" class="form-control" id="facebook" name="facebook" value="{{ $siteSetting->facebook }}" placeholder="https://facebook.com/yourpage">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="instagram" class="form-label fw-semibold">
                                                <i class="fab fa-instagram text-danger me-1"></i> Instagram URL
                                            </label>
                                            <input type="url" class="form-control" id="instagram" name="instagram" value="{{ $siteSetting->instagram }}" placeholder="https://instagram.com/yourpage">
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Info Text Section -->
                                    <div class="row mb-4">
                                        <div class="col-12 mb-3">
                                            <h5 class="fw-semibold mb-0">Announcement Bar / Info Text</h5>
                                            <small class="text-muted">Display a small promotional message at the top of the site.</small>
                                        </div>

                                        @php
                                            $infoText = ['text' => ['en' => '', 'fr' => '', 'ar' => ''], 'url' => ''];
                                            if (is_array($siteSetting->infotext)) {
                                                if (array_key_exists('text', $siteSetting->infotext) || array_key_exists('url', $siteSetting->infotext)) {
                                                    $rawText = $siteSetting->infotext['text'] ?? '';
                                                    if (is_array($rawText)) {
                                                        $infoText['text']['en'] = (string) ($rawText['en'] ?? '');
                                                        $infoText['text']['fr'] = (string) ($rawText['fr'] ?? '');
                                                        $infoText['text']['ar'] = (string) ($rawText['ar'] ?? '');
                                                    } elseif (is_string($rawText)) {
                                                        $infoText['text']['en'] = $rawText;
                                                    }
                                                    $infoText['url'] = (string) ($siteSetting->infotext['url'] ?? '');
                                                } else {
                                                    foreach ($siteSetting->infotext as $value) {
                                                        if (is_string($value) && trim($value) !== '') {
                                                            $infoText['text']['en'] = $value; break;
                                                        }
                                                    }
                                                }
                                            } elseif (is_string($siteSetting->infotext) && trim($siteSetting->infotext) !== '') {
                                                $infoText['text']['en'] = $siteSetting->infotext;
                                            }
                                        @endphp

                                        <div class="col-12">
                                            <!-- Row 1: Labels & Tabs aligned at the bottom -->
                                            <div class="row align-items-end mb-2">
                                                <div class="col-md-8 mb-2 mb-md-0 d-flex justify-content-between align-items-end gap-2 flex-wrap">
                                                    <ul class="nav nav-pills" id="infoTextLangTab" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active py-1 px-3 me-1" id="info-en-tab" data-bs-toggle="tab" data-bs-target="#info-en" type="button" role="tab">English</button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link py-1 px-3 me-1" id="info-fr-tab" data-bs-toggle="tab" data-bs-target="#info-fr" type="button" role="tab">French</button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link py-1 px-3" id="info-ar-tab" data-bs-toggle="tab" data-bs-target="#info-ar" type="button" role="tab">Arabic</button>
                                                        </li>
                                                    </ul>

                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-translate-all"
                                                        data-url="{{ route('admin.translate.batch') }}"
                                                        data-fields='["infotext_text"]'>
                                                        <i class="bi bi-translate me-1"></i> Auto Translate
                                                    </button>
                                                </div>
                                                <div class="col-md-4 d-none d-md-block">
                                                    <!-- Label for desktop aligned with the bottom of the pills -->
                                                    <label class="form-label fw-semibold mb-0 pb-1">Target URL</label>
                                                </div>
                                            </div>

                                            <!-- Row 2: Input fields perfectly horizontally aligned -->
                                            <div class="row">
                                                <div class="col-md-8 mb-3 mb-md-0">
                                                    <div class="tab-content" id="infoTextLangTabContent">
                                                        <div class="tab-pane fade show active" id="info-en" role="tabpanel">
                                                            <input type="text" class="form-control infotext-input" id="infotext_text_en" name="infotext[text][en]" value="{{ $infoText['text']['en'] }}" placeholder="Info text in English (max 30 words)">
                                                        </div>
                                                        <div class="tab-pane fade" id="info-fr" role="tabpanel">
                                                            <input type="text" class="form-control" id="infotext_text_fr" name="infotext[text][fr]" value="{{ $infoText['text']['fr'] }}" placeholder="Texte d'information en français (optional)">
                                                        </div>
                                                        <div class="tab-pane fade" id="info-ar" role="tabpanel">
                                                            <input type="text" class="form-control" id="infotext_text_ar" dir="rtl" name="infotext[text][ar]" value="{{ $infoText['text']['ar'] }}" placeholder="نص المعلومات بالعربية (اختياري)">
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted mt-2">English is primary. French/Arabic are optional and auto-filled if left empty.</small>
                                                </div>

                                                <div class="col-md-4">
                                                    <!-- Label for mobile only, so spacing stays intact on desktop -->
                                                    <label class="form-label fw-semibold d-md-none">Target URL</label>
                                                    <input type="url" class="form-control" name="infotext[url]" value="{{ $infoText['url'] }}" placeholder="https://example.com/offer">
                                                    <small class="form-text text-muted mt-2">Where should users go when they click?</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-primary px-4 py-2">
                                                <i class="fas fa-save me-1"></i> Update General Settings
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Opening Hours Tab -->
                            <div class="tab-pane fade" id="opening-hours-settings" role="tabpanel" aria-labelledby="opening-hours-tab">
                                <form id="openingHoursForm" data-site-id="{{ $siteSetting->id }}">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-4">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 20%;">Day</th>
                                                    <th style="width: 30%;">Open Time</th>
                                                    <th style="width: 30%;">Close Time</th>
                                                    <th style="width: 20%;">Status</th>
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
                                                    <tr data-day="{{ $dayKey }}" class="{{ $isClosed ? 'table-active text-muted' : '' }}">
                                                        <td class="fw-semibold">{{ $dayLabel }}</td>
                                                        <td>
                                                            <input type="time" class="form-control day-time-input" data-field="open" value="{{ $openValue }}" {{ $isClosed ? 'disabled' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control day-time-input" data-field="close" value="{{ $closeValue }}" {{ $isClosed ? 'disabled' : '' }}>
                                                        </td>
                                                        <td>
                                                            <!-- Updated to BS5 form-check -->
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input day-closed-checkbox" type="checkbox" role="switch" id="{{ $dayKey }}_closed" {{ $isClosed ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="{{ $dayKey }}_closed">Closed</label>
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
                                            <button type="submit" class="btn btn-primary px-4 py-2">
                                                <i class="fas fa-save me-1"></i> Update Opening Hours
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Map Iframe Tab -->
                            <div class="tab-pane fade" id="map-iframe-settings" role="tabpanel" aria-labelledby="map-iframe-tab">
                                <form id="mapIframeForm" data-site-id="{{ $siteSetting->id }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="map_iframe" class="form-label fw-semibold">Google Maps Iframe Code</label>
                                        <textarea class="form-control" id="map_iframe" name="map_iframe" rows="8" placeholder='<iframe src="https://www.google.com/maps/embed?..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'>{{ $siteSetting->map_iframe }}</textarea>
                                        <small class="form-text text-muted mt-2">Go to Google Maps, search your location, click "Share", choose "Embed a map", and copy the HTML.</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary px-4 py-2">
                                                <i class="fas fa-save me-1"></i> Update Map Iframe
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center py-5 rounded">
                            <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                            <h4>No Site Settings Found</h4>
                            <p class="text-muted">It looks like the initial settings haven't been created yet.</p>
                            <button type="button" class="btn btn-primary mt-2" onclick="createInitialSettings()">
                                <i class="fas fa-plus me-1"></i> Create Initial Settings
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
    <script>
        // Optional: Add event listener to visually grey out rows when toggling 'Closed' switch
        document.querySelectorAll('.day-closed-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const tr = this.closest('tr');
                if(this.checked) {
                    tr.classList.add('table-active', 'text-muted');
                } else {
                    tr.classList.remove('table-active', 'text-muted');
                }
            });
        });
    </script>
@endpush
