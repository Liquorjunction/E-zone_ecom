@extends('admin.layouts.app')
@section('header-title', 'Edit SubCategory')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Edit SubCategory</h4>
            <a href="{{ route('admin.category-subcategory.subcategories.index') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.category-subcategory.subcategories.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $subCategory->id }}">

                <div class="row g-3">
                    {{-- Left Column: Main Info --}}
                    <div class="col-lg-8 col-md-12">

                        {{-- Parent Category Select --}}
                        <div class="mb-4">
                            <label class="form-label">Parent Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $subCategory->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->getTranslation('category-name', 'en') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Language Tabs --}}
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

                            {{-- Translation Button --}}
                            <button type="button" class="btn btn-sm btn-outline-primary btn-translate-all"
                                data-url="{{ route('admin.translate.batch') }}"
                                data-fields='["sub-category-name"]'>
                                <i class="bi bi-translate me-1"></i> Auto Translate
                            </button>
                        </div>

                        <div class="tab-content" id="langTabContent">
                            {{-- English --}}
                            <div class="tab-pane fade show active" id="en" role="tabpanel">
                                <div class="mb-3">
                                    <label for="sub-category-name_en" class="form-label">SubCategory Name (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="sub-category-name[en]" id="sub-category-name_en" class="form-control"
                                           value="{{ old('sub-category-name.en', $subCategory->getTranslation('sub-category-name', 'en')) }}"
                                           required>
                                    @error('sub-category-name.en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- French --}}
                            <div class="tab-pane fade" id="fr" role="tabpanel">
                                <div class="mb-3">
                                    <label for="sub-category-name_fr" class="form-label">SubCategory Name (FR)</label>
                                    <input type="text" name="sub-category-name[fr]" id="sub-category-name_fr" class="form-control"
                                           value="{{ old('sub-category-name.fr', $subCategory->getTranslation('sub-category-name', 'fr')) }}">
                                </div>
                            </div>

                            {{-- Arabic --}}
                            <div class="tab-pane fade" id="ar" role="tabpanel">
                                <div class="mb-3" dir="rtl">
                                    <label for="sub-category-name_ar" class="form-label">SubCategory Name (AR)</label>
                                    <input type="text" name="sub-category-name[ar]" id="sub-category-name_ar" class="form-control"
                                           value="{{ old('sub-category-name.ar', $subCategory->getTranslation('sub-category-name', 'ar')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update SubCategory</button>
                            <a href="{{ route('admin.category-subcategory.subcategories.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </div>

                    {{-- Right Column: Sidebar (Image & Status) --}}
                    <div class="col-lg-4 col-md-12">
                        <div class="card border-1 p-3 shadow-sm">

                            {{-- Image Upload --}}
                            <div class="text-center mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    Icon / Image <span class="text-info" style="font-size: 0.8em; text-transform: none;">(Recommended: 60x60 px)</span>
                                </label>
                                <div class="bg-light p-2 rounded mb-2">
                                    <img id="iconPreview"
                                         src="{{ $subCategory->{'sub-category-icon'} ? asset('storage/'.$subCategory->{'sub-category-icon'}) : asset('images/no_image.jpg') }}"
                                         alt="Icon preview"
                                         class="img-fluid rounded"
                                         style="max-width: 120px; max-height: 120px; object-fit: cover;">
                                </div>
                                <input id="sub-category-icon" type="file" name="sub-category-icon" class="form-control form-control-sm" accept="image/*">
                                <div class="form-text mt-1 text-xs">Accepted: jpeg, png, jpg, gif, webp, bmp. Max 2MB.</div>
                                <div class="form-text mt-0 text-xs text-muted">Leave empty to keep current image.</div>
                                <div class="mt-2" id="iconFeedback" style="display:none; color: #d63384; font-size: 0.875em;"></div>
                            </div>

                            <hr class="my-3">

                            {{-- Status Switch --}}
                            <div class="mb-3">
                                <label class="form-label d-block fw-bold">Status</label>
                                <input type="hidden" name="status" value="inactive">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active"
                                           {{ $subCategory->status == 'active' ? 'checked' : '' }}>
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
<script src="{{ asset('admin_assets/js/cat/subcat_image-preview.js') }}"></script>
@endpush
