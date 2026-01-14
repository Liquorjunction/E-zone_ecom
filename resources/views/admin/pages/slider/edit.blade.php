@extends('admin.layouts.app')
@section('header-title', 'Edit Hero Slider')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Edit Slider</h4>
                <a href="{{ route('admin.hero-slider.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left mr-2"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hero-slider.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $slider->id }}">

                    <div class="row g-3">
                        {{-- Left Column: Main Inputs --}}
                        <div class="col-lg-8 col-md-12">

                            {{-- Language Tabs & Translate --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <ul class="nav nav-tabs" id="langTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="en-tab" data-bs-toggle="tab"
                                            data-bs-target="#en" type="button" role="tab">English</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr"
                                            type="button" role="tab">French</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar"
                                            type="button" role="tab">Arabic</button>
                                    </li>
                                </ul>

                                <button type="button" class="btn btn-sm btn-outline-primary btn-translate-all"
                                    data-url="{{ route('admin.translate.batch') }}"
                                    data-fields='["title", "subtitle", "button_text"]'>
                                    <i class="bi bi-translate me-1"></i> Auto Translate
                                </button>
                            </div>

                            <div class="tab-content" id="langTabContent">
                                {{-- English --}}
                                <div class="tab-pane fade show active" id="en" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="title[en]" id="title_en" class="form-control"
                                            value="{{ old('title.en', $slider->getTranslation('title', 'en')) }}" required>
                                        @error('title.en')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Subtitle (EN)</label>
                                        <input type="text" name="subtitle[en]" id="subtitle_en" class="form-control"
                                            value="{{ old('subtitle.en', $slider->getTranslation('subtitle', 'en')) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Button Text (EN)</label>
                                        <input type="text" name="button_text[en]" id="button_text_en"
                                            class="form-control"
                                            value="{{ old('button_text.en', $slider->getTranslation('button_text', 'en')) }}">
                                    </div>
                                </div>

                                {{-- French --}}
                                <div class="tab-pane fade" id="fr" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label">Title (FR)</label>
                                        <input type="text" name="title[fr]" id="title_fr" class="form-control"
                                            value="{{ old('title.fr', $slider->getTranslation('title', 'fr')) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Subtitle (FR)</label>
                                        <input type="text" name="subtitle[fr]" id="subtitle_fr" class="form-control"
                                            value="{{ old('subtitle.fr', $slider->getTranslation('subtitle', 'fr')) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Button Text (FR)</label>
                                        <input type="text" name="button_text[fr]" id="button_text_fr"
                                            class="form-control"
                                            value="{{ old('button_text.fr', $slider->getTranslation('button_text', 'fr')) }}">
                                    </div>
                                </div>

                                {{-- Arabic --}}
                                <div class="tab-pane fade" id="ar" role="tabpanel">
                                    <div class="mb-3" dir="rtl">
                                        <label class="form-label">Title (AR)</label>
                                        <input type="text" name="title[ar]" id="title_ar" class="form-control"
                                            value="{{ old('title.ar', $slider->getTranslation('title', 'ar')) }}">
                                    </div>
                                    <div class="mb-3" dir="rtl">
                                        <label class="form-label">Subtitle (AR)</label>
                                        <input type="text" name="subtitle[ar]" id="subtitle_ar" class="form-control"
                                            value="{{ old('subtitle.ar', $slider->getTranslation('subtitle', 'ar')) }}">
                                    </div>
                                    <div class="mb-3" dir="rtl">
                                        <label class="form-label">Button Text (AR)</label>
                                        <input type="text" name="button_text[ar]" id="button_text_ar"
                                            class="form-control"
                                            value="{{ old('button_text.ar', $slider->getTranslation('button_text', 'ar')) }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Common Link Field --}}
                            <div class="mb-3">
                                <label class="form-label">Button Link (URL)</label>
                                <input type="url" name="button_link" class="form-control"
                                    value="{{ old('button_link', $slider->button_link) }}">
                                @error('button_link')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update Slider</button>
                            </div>
                        </div>

                        {{-- Right Column: Sidebar --}}
                        <div class="col-lg-4 col-md-12">
                            <div class="card border-1 p-3 shadow-sm">

                                {{-- Image Upload --}}
                                <div class="text-center mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Slider Image</label>
                                    <div class="bg-light p-2 rounded mb-2">
                                        <img id="imagePreview"
                                            src="{{ $slider->image ? asset('storage/' . $slider->image) : asset('assets/images/no-image.png') }}"
                                            alt="Preview" class="img-fluid rounded"
                                            style="max-width: 100%; max-height: 150px; object-fit: contain;">
                                    </div>
                                    <input id="slider-image" type="file" name="image"
                                        class="form-control form-control-sm"
                                        accept=".jpeg,.png,.jpg,.gif,.webp,.bmp,.ico,.tiff,.avif">
                                    <div class="form-text mt-1 text-xs">
                                        Allowed: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.<br>
                                        Max Size: 5MB.
                                    </div>
                                    <div class="mt-2 text-danger small" id="imageFeedback" style="display:none;"></div>
                                    @error('image')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Image Size and Resolution --}}
                                <div class="alert alert-info alert-sm py-2 px-3 mb-0">
                                    <small class="d-block mb-2">
                                        <strong>📐 Recommended Specifications:</strong>
                                    </small>
                                    <small class="d-block">
                                        <i class="bi bi-check text-success me-1"></i>
                                        <strong>Aspect Ratio:</strong> 16:9
                                    </small>
                                    <small class="d-block">
                                        <i class="bi bi-check text-success me-1"></i>
                                        <strong>Resolutions:</strong>
                                    </small>
                                    <small class="d-block ms-4">
                                        • 1920 × 1080 (Full HD)<br>
                                        • 2560 × 1440 (2K)<br>
                                        • 3840 × 2160 (4K)
                                    </small>
                                </div>

                                <hr class="my-3">

                                {{-- Sort Order --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control"
                                        value="{{ old('sort_order', $slider->sort_order) }}">
                                </div>

                                {{-- Status Switch --}}
                                <div class="mb-3">
                                    <label class="form-label d-block fw-bold">Status</label>
                                    <input type="hidden" name="status" value="inactive">
                                    {{-- Check both 'is_active' (from your DB) or 'status' (if standardizing) --}}
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="statusSwitch" name="status"
                                            value="active"
                                            {{ $slider->status == 'active' || $slider->is_active == 1 ? 'checked' : '' }}>
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
    <script src="{{ asset('admin_assets/js/image-preview5mb.js') }}"></script>
    <script src="{{ asset('admin_assets/js/slider-link-guard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize image preview with 5MB limit
            setupImagePreview('slider-image', 'imagePreview', 'imageFeedback', 5);

            setupButtonLinkGuard({
                formSelector: 'form[action="{{ route('admin.hero-slider.update') }}"]',
                textSelector: 'input[name^="button_text["]',
                linkSelector: 'input[name="button_link"]',
                message: 'Button link is required when button text is provided.'
            });
        });
    </script>
@endpush
