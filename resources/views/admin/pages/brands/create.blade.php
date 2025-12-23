@extends('admin.layouts.app')
@section('title', 'Create Brand')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Create Brand</h4>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    {{-- Left Column: Main Inputs --}}
                    <div class="col-lg-8 col-md-12">

                        {{-- Language Tabs & Translate Button --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <ul class="nav nav-tabs" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English</button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content" id="langTabContent">
                            {{-- English --}}
                            <div class="tab-pane fade show active" id="en" role="tabpanel">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">Brand Name (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="name[en]" id="name_en" class="form-control"
                                           value="{{ old('name.en') }}" required placeholder="Enter brand name">
                                    @error('name.en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- French --}}
                            <div class="tab-pane fade" id="fr" role="tabpanel">
                                <div class="mb-3">
                                    <label for="name_fr" class="form-label">Brand Name (FR)</label>
                                    <input type="text" name="name[fr]" id="name_fr" class="form-control"
                                           value="{{ old('name.fr') }}" placeholder="Enter brand name">
                                </div>
                            </div>

                            {{-- Arabic --}}
                            <div class="tab-pane fade" id="ar" role="tabpanel">
                                <div class="mb-3" dir="rtl">
                                    <label for="name_ar" class="form-label">Brand Name (AR)</label>
                                    <input type="text" name="name[ar]" id="name_ar" class="form-control"
                                           value="{{ old('name.ar') }}" placeholder="Enter brand name">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Create Brand</button>
                        </div>
                    </div>

                    {{-- Right Column: Sidebar (Image & Status) --}}
                    <div class="col-lg-4 col-md-12">
                        <div class="card border-1 p-3 shadow-sm">

                            {{-- Image Upload --}}
                            <div class="text-center mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    Brand Logo <span class="text-danger">*</span> <br>
                                    <span class="text-info" style="font-size: 0.8em; text-transform: none;">(Recommended: Square 1:1)</span>
                                </label>
                                <div class="bg-light p-2 rounded mb-2">
                                    <img id="iconPreview"
                                         src="{{ asset('assets/images/no-image.png') }}"
                                         alt="Icon preview"
                                         class="img-fluid rounded"
                                         style="max-width: 120px; max-height: 120px; object-fit: cover; opacity: 0.6;">
                                </div>
                                <input id="brand-icon" type="file" name="image" class="form-control form-control-sm" accept="image/*" required>
                                 <div class="form-text mt-1 text-xs">Accepted: jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif. Max 2MB.</div>
                                <div class="mt-2 text-danger small" id="iconFeedback" style="display:none;"></div>
                                @error('image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-3">

                            {{-- Status Switch --}}
                            <div class="mb-3">
                                <label class="form-label d-block fw-bold">Status</label>
                                <input type="hidden" name="status" value="inactive">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active" checked>
                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                </div>
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
    {{-- Include common image preview logic --}}
    <script src="{{ asset('admin_assets/js/cat/subcat_image-preview.js') }}"></script>
    <script>
        // Initialize for Brand Form
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof setupImagePreview === 'function') {
                setupImagePreview('brand-icon', 'iconPreview', 'iconFeedback');
            }
        });
    </script>
@endpush
