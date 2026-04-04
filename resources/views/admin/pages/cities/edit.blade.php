@extends('admin.layouts.app')
@section('title', 'Edit City')
@section('header-title', 'Edit City')
@section('header-description', 'Modify city details')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.cities.edit', request('id')) as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    @php
        $cityItem = $city;

        if ($city instanceof \Illuminate\Database\Eloquent\Collection) {
            $cityItem = $city->firstWhere('id', request('id')) ?? $city->first();
        }
    @endphp

    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Edit City</h4>
                <a href="{{ route('admin.cities.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left mr-2"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.cities.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ old('id', optional($cityItem)->id ?? request('id')) }}">

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Please fix the highlighted fields.</strong>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-lg-8 col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <ul class="nav nav-tabs" id="langTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">French</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">Arabic</button>
                                    </li>
                                </ul>

                                <button type="button" class="btn btn-sm btn-outline-primary btn-translate-all"
                                    data-url="{{ route('admin.translate.batch') }}"
                                    data-fields='["city_name", "country_name"]'>
                                    <i class="bi bi-translate me-1"></i> Auto Translate
                                </button>
                            </div>

                            <div class="tab-content" id="langTabContent">
                                <div class="tab-pane fade show active" id="en" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="city_name_en" class="form-label">City Name (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="city_name[en]" id="city_name_en" class="form-control @error('city_name.en') is-invalid @enderror"
                                            value="{{ old('city_name.en', optional($cityItem)?->getTranslation('city_name', 'en')) }}"
                                            required>
                                        @error('city_name.en')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="country_name[en]" id="country_name_en" value="{{ config('app.COUNTRY') }}">
                                </div>

                                <div class="tab-pane fade" id="fr" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="city_name_fr" class="form-label">City Name (FR)</label>
                                        <input type="text" name="city_name[fr]" id="city_name_fr" class="form-control @error('city_name.fr') is-invalid @enderror"
                                            value="{{ old('city_name.fr', optional($cityItem)?->getTranslation('city_name', 'fr')) }}">
                                        @error('city_name.fr')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="country_name[fr]" id="country_name_fr" value="{{ config('app.COUNTRY') }}">
                                </div>

                                <div class="tab-pane fade" id="ar" role="tabpanel">
                                    <div class="mb-3" dir="rtl">
                                        <label for="city_name_ar" class="form-label">City Name (AR)</label>
                                        <input type="text" name="city_name[ar]" id="city_name_ar" class="form-control @error('city_name.ar') is-invalid @enderror"
                                            value="{{ old('city_name.ar', optional($cityItem)?->getTranslation('city_name', 'ar')) }}">
                                        @error('city_name.ar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="country_name[ar]" id="country_name_ar" value="{{ config('app.COUNTRY') }}">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update City</button>
                                <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-12">
                            <div class="card border p-3 shadow-sm">
                                <div class="mb-3">
                                    <label class="form-label d-block fw-bold">Status</label>
                                    <input type="hidden" name="status" value="inactive">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active"
                                            {{ old('status', optional($cityItem)->status) === 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusSwitch">Active</label>
                                    </div>
                                    @error('status')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let targetTab = null;

                @if ($errors->has('city_name.fr') || $errors->has('country_name.fr'))
                    targetTab = document.querySelector('#fr-tab');
                @elseif ($errors->has('city_name.ar') || $errors->has('country_name.ar'))
                    targetTab = document.querySelector('#ar-tab');
                @elseif ($errors->has('city_name.en') || $errors->has('country_name.en') || $errors->has('status'))
                    targetTab = document.querySelector('#en-tab');
                @endif

                if (targetTab && window.bootstrap && window.bootstrap.Tab) {
                    window.bootstrap.Tab.getOrCreateInstance(targetTab).show();
                }
            });
        </script>
    @endif
@endpush
