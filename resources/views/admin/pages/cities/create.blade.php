@extends('admin.layouts.app')
@section('title', 'Create City')
@section('header-title', 'Create City')
@section('header-description', 'Add a new city')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Create City</h4>
                <a href="{{ route('admin.cities.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left mr-2"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.cities.store') }}" method="POST">
                    @csrf

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
                                        <input type="text" name="city_name[en]" id="city_name_en" class="form-control"
                                            value="{{ old('city_name.en') }}" required placeholder="Enter city name">
                                        @error('city_name.en')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="country_name_en" class="form-label">Country Name (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="country_name[en]" id="country_name_en" class="form-control"
                                            value="{{ old('country_name.en') }}" required placeholder="Enter country name">
                                        @error('country_name.en')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="fr" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="city_name_fr" class="form-label">City Name (FR)</label>
                                        <input type="text" name="city_name[fr]" id="city_name_fr" class="form-control"
                                            value="{{ old('city_name.fr') }}" placeholder="Enter city name in French">
                                        @error('city_name.fr')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="country_name_fr" class="form-label">Country Name (FR)</label>
                                        <input type="text" name="country_name[fr]" id="country_name_fr" class="form-control"
                                            value="{{ old('country_name.fr') }}" placeholder="Enter country name in French">
                                        @error('country_name.fr')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="ar" role="tabpanel">
                                    <div class="mb-3" dir="rtl">
                                        <label for="city_name_ar" class="form-label">City Name (AR)</label>
                                        <input type="text" name="city_name[ar]" id="city_name_ar" class="form-control"
                                            value="{{ old('city_name.ar') }}" placeholder="Enter city name in Arabic">
                                        @error('city_name.ar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3" dir="rtl">
                                        <label for="country_name_ar" class="form-label">Country Name (AR)</label>
                                        <input type="text" name="country_name[ar]" id="country_name_ar" class="form-control"
                                            value="{{ old('country_name.ar') }}" placeholder="Enter country name in Arabic">
                                        @error('country_name.ar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Create City</button>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-12">
                            <div class="card border p-3 shadow-sm">
                                <div class="mb-3">
                                    <label class="form-label d-block fw-bold">Status</label>
                                    <input type="hidden" name="status" value="inactive">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
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
