@extends('admin.layouts.app')
@section('header-title', 'Edit Category')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Edit Category</h4>
            <a href="{{ route('admin.category-subcategory.categories.index') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.category-subcategory.categories.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $category->id }}">

                <div class="row g-3">
                    {{-- Left Column: Inputs --}}
                    <div class="col-lg-8 col-md-12">
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
                                data-fields='["category-name"]'>
                                <i class="fas fa-language me-1"></i> Auto Translate
                            </button>
                        </div>

                        <div class="tab-content" id="langTabContent">
                            {{-- English --}}
                            <div class="tab-pane fade show active" id="en" role="tabpanel">
                                <div class="mb-3">
                                    <label for="category-name_en" class="form-label">Category Name (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="category-name[en]" id="category-name_en" class="form-control"
                                           value="{{ old('category-name.en', $category->getTranslation('category-name', 'en')) }}"
                                           required placeholder="Enter category name in English">
                                    @error('category-name.en')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- French --}}
                            <div class="tab-pane fade" id="fr" role="tabpanel">
                                <div class="mb-3">
                                    <label for="category-name_fr" class="form-label">Category Name (FR)</label>
                                    <input type="text" name="category-name[fr]" id="category-name_fr" class="form-control"
                                           value="{{ old('category-name.fr', $category->getTranslation('category-name', 'fr')) }}">
                                    @error('category-name.fr')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Arabic --}}
                            <div class="tab-pane fade" id="ar" role="tabpanel">
                                <div class="mb-3" dir="rtl">
                                    <label for="category-name_ar" class="form-label">Category Name (AR)</label>
                                    <input type="text" name="category-name[ar]" id="category-name_ar" class="form-control"
                                           value="{{ old('category-name.ar', $category->getTranslation('category-name', 'ar')) }}">
                                    @error('category-name.ar')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Image Upload Input (Global) --}}
                        <div class="mb-4">
                            <label for="category-icon" class="form-label">Icon / Image <span class="text-info">(Recommended: 60x60 px)</span></label>
                            <input id="category-icon" type="file" name="category-icon" class="form-control" accept="image/*">
                            <div class="form-text">Accepted: jpeg, png, jpg, gif, webp, bmp. Max 2MB.</div>
                            @error('category-icon')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="mt-2" id="iconFeedback" style="display:none;"></div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update Category</button>
                            <a href="{{ route('admin.category-subcategory.categories.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </div>

                    {{-- Right Column: Preview & Status --}}
                    <div class="col-lg-4 col-md-12">
                        <div class="card border-1 p-3 shadow-sm">
                            <div class="text-center mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Icon Preview</label>
                                <div class="bg-light p-2 rounded">
                                    <img id="iconPreview"
                                         src="{{ $category->{'category-icon'} ? asset('storage/'.$category->{'category-icon'}) : '' }}"
                                         alt="Icon preview"
                                         class="img-fluid rounded"
                                         style="max-width: 120px; max-height: 120px; object-fit: cover; {{ $category->{'category-icon'} ? '' : 'display:none;' }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block fw-bold">Status</label>
                                <input type="hidden" name="status" value="inactive">
                                <div class="form-check form-switch form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active"
                                           {{ $category->status == 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                </div>
                            </div>

                            <div class="alert alert-soft-info font-size-12 mb-0 p-2">
                                <i class="fas fa-info-circle me-1"></i> Tip: Use a square image (1:1 ratio) for the best appearance on the website.
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Improvements for preview area */
    #iconPreview { transition: all 0.3s ease; }
    #iconFeedback { color: #d63384; font-size: 0.875em; }
    .alert-soft-info { background-color: rgba(13, 202, 240, 0.15); color: #0dcaf0; border: none; }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('admin_assets/js/cat/subcat_image-preview.js') }}"></script>
@endpush
