@extends('admin.layouts.app')
@section('title', 'Edit Why Shop With Us')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Why Shop With Us</h4>
                    <a href="{{ route('admin.whyshopwithus.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.whyshopwithus.update') }}" method="POST" id="whyShopWithUsEditForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $entry->id }}">

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
                                            data-fields='["title", "description"]'>
                                        <i class="bi bi-translate me-1"></i> Translate All
                                    </button>
                                </div>

                                <div class="tab-content" id="langTabContent">
                                    <div class="tab-pane fade show active" id="en" role="tabpanel">
                                        <div class="mb-3">
                                            <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                            <input type="text" name="title[en]" id="title_en" class="form-control"
                                                   value="{{ old('title.en', $entry->getTranslation('title', 'en')) }}" required>
                                            @error('title.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description (EN) <span class="text-danger">*</span></label>
                                            <div id="editor_description_en" style="height: 200px;">{!! old('description.en', $entry->getTranslation('description', 'en')) !!}</div>
                                            <textarea name="description[en]" id="description_en" class="d-none">{{ old('description.en', $entry->getTranslation('description', 'en')) }}</textarea>
                                            @error('description.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="fr" role="tabpanel">
                                        <div class="mb-3">
                                            <label class="form-label">Title (FR)</label>
                                            <input type="text" name="title[fr]" id="title_fr" class="form-control"
                                                   value="{{ old('title.fr', $entry->getTranslation('title', 'fr')) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description (FR)</label>
                                            <div id="editor_description_fr" style="height: 200px;">{!! old('description.fr', $entry->getTranslation('description', 'fr')) !!}</div>
                                            <textarea name="description[fr]" id="description_fr" class="d-none">{{ old('description.fr', $entry->getTranslation('description', 'fr')) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="ar" role="tabpanel">
                                        <div class="mb-3" dir="rtl">
                                            <label class="form-label">Title (AR)</label>
                                            <input type="text" name="title[ar]" id="title_ar" class="form-control"
                                                   value="{{ old('title.ar', $entry->getTranslation('title', 'ar')) }}">
                                        </div>
                                        <div class="mb-3" dir="rtl">
                                            <label class="form-label">Description (AR)</label>
                                            <div id="editor_description_ar" style="height: 200px;">{!! old('description.ar', $entry->getTranslation('description', 'ar')) !!}</div>
                                            <textarea name="description[ar]" id="description_ar" class="d-none">{{ old('description.ar', $entry->getTranslation('description', 'ar')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="order" class="form-control" value="{{ old('order', $entry->order) }}" placeholder="e.g. 1">
                                    @error('order') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status', $entry->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $entry->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary mt-2">Update Why Shop With Us</button>
                            </div>

                            <div class="col-lg-4 col-md-12">
                                <div class="card border p-3 shadow-sm">
                                    <div class="text-center mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Icon</label>
                                        <div class="bg-light p-2 rounded mb-2">
                                            <img id="iconPreview" src="{{ $entry->icon ? asset('storage/' . $entry->icon) : asset('images/no_image.jpg') }}" alt="Current Icon" class="img-fluid rounded" style="max-width: 100%; max-height: 150px; object-fit: contain;">
                                        </div>
                                        <input id="icon-input" type="file" name="icon" class="form-control form-control-sm" accept=".jpeg,.png,.jpg,.gif,.webp,.bmp,.ico,.tiff,.avif">
                                        <div class="form-text mt-1 text-xs">Leave empty to keep current icon. Allowed: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif. Max size: 2MB.</div>
                                        <div class="mt-2 text-danger small" id="iconFeedback" style="display:none;"></div>
                                        @error('icon') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <hr class="my-3">
                                    <div class="alert alert-light border-0 small mb-0">
                                        <div class="fw-bold mb-1">Notes</div>
                                        <ul class="mb-0 ps-3">
                                            <li>Use batch translate to fill FR/AR from EN.</li>
                                            <li>Upload a new icon to replace the current one.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/whyShopWithUsEditor.js') }}"></script>
    <script src="{{ asset('admin_assets/js/image-preview5mb.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupImagePreview('icon-input', 'iconPreview', 'iconFeedback', 2);
        });
    </script>
@endpush
