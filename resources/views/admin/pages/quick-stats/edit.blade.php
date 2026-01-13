@extends('admin.layouts.app')
@section('header-title', 'Edit Quick Stat')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Update Quick Stat</h4>
            <a href="{{ route('admin.quick-stats.index') }}" class="btn btn-sm btn-secondary">
                <i class="fa fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.quick-stats.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $quickStat->id }}">

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
                                data-fields='["text1", "text2"]'>
                                <i class="bi bi-translate me-1"></i> Auto Translate
                            </button>
                        </div>

                        <div class="tab-content" id="langTabContent">
                            <div class="tab-pane fade show active" id="en" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Primary Text (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="text1[en]" id="text1_en" class="form-control"
                                           value="{{ old('text1.en', $quickStat->getTranslation('text1', 'en')) }}" required>
                                    @error('text1.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    @error('title1.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Secondary Text (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="text2[en]" id="text2_en" class="form-control"
                                           value="{{ old('text2.en', $quickStat->getTranslation('text2', 'en')) }}" required>
                                    @error('text2.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    @error('title2.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="tab-pane fade" id="fr" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Primary Text (FR)</label>
                                    <input type="text" name="text1[fr]" id="text1_fr" class="form-control"
                                           value="{{ old('text1.fr', $quickStat->getTranslation('text1', 'fr')) }}">
                                    @error('text1.fr') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    @error('title1.fr') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Secondary Text (FR)</label>
                                    <input type="text" name="text2[fr]" id="text2_fr" class="form-control"
                                           value="{{ old('text2.fr', $quickStat->getTranslation('text2', 'fr')) }}">
                                    @error('text2.fr') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    @error('title2.fr') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="tab-pane fade" id="ar" role="tabpanel">
                                <div class="mb-3" dir="rtl">
                                    <label class="form-label">Primary Text (AR)</label>
                                    <input type="text" name="text1[ar]" id="text1_ar" class="form-control"
                                           value="{{ old('text1.ar', $quickStat->getTranslation('text1', 'ar')) }}">
                                    @error('text1.ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    @error('title1.ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3" dir="rtl">
                                    <label class="form-label">Secondary Text (AR)</label>
                                    <input type="text" name="text2[ar]" id="text2_ar" class="form-control"
                                           value="{{ old('text2.ar', $quickStat->getTranslation('text2', 'ar')) }}">
                                    @error('text2.ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    @error('title2.ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Quick Stat</button>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="card border-1 p-3 shadow-sm">
                            <div class="text-center mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Icon</label>
                                <div class="bg-light p-2 rounded mb-2">
                                    <img id="iconPreview"
                                         src="{{ $quickStat->icon ? asset('storage/' . ltrim($quickStat->icon, '/')) : asset('assets/images/no-image.png') }}"
                                         alt="Preview"
                                         class="img-fluid rounded"
                                         style="max-width: 100%; max-height: 150px; object-fit: contain;">
                                </div>
                                <input id="icon-input" type="file" name="icon" class="form-control form-control-sm"
                                       accept=".jpeg,.png,.jpg,.gif,.webp,.bmp,.ico,.tiff,.avif">
                                <div class="form-text mt-1 text-xs">
                                    Allowed: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif. Max size: 2MB.
                                </div>
                                <div class="mt-2 text-danger small" id="iconFeedback" style="display:none;"></div>
                                @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <label class="form-label d-block fw-bold">Status</label>
                                <input type="hidden" name="status" value="inactive">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active"
                                           {{ old('status', $quickStat->status) === 'active' ? 'checked' : '' }}>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupImagePreview('icon-input', 'iconPreview', 'iconFeedback', 2);
        });
    </script>
@endpush
