@extends('admin.layouts.app')
@section('header-title', 'Create SubCategory')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Create SubCategory</h4>
            <a href="{{ route('admin.category-subcategory.subcategories.index') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.category-subcategory.subcategories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    {{-- Left Column: Main Inputs --}}
                    <div class="col-lg-8 col-md-12">

                        {{-- Parent Category --}}
                        <div class="mb-4">
                            <label class="form-label">Parent Category <span class="text-danger">*</span></label>
                            <select class="select2-ajax browser-default form-control" data-allow-clear="true" data-error="#category-error-div" data-option-url="{{ route('admin.lists.categories') }}" data-placeholder="Select category" id="category_id" name="category_id">
                                @if (old('category_id'))
                                    <option selected value="{{ old('category_id') }}">{{ old('category_id') }}</option>
                                @endif
                            </select>
                            <div id="category-error-div"></div>
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
                            </ul>
                        </div>

                        <div class="tab-content" id="langTabContent">
                            {{-- English --}}
                            <div class="tab-pane fade show active" id="en" role="tabpanel">
                                <div class="mb-3">
                                    <label for="sub-category-name_en" class="form-label">SubCategory Name (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="sub-category-name[en]" id="sub-category-name_en" class="form-control"
                                           value="{{ old('sub-category-name.en') }}" required placeholder="Enter name">
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
                                           value="{{ old('sub-category-name.fr') }}" placeholder="Enter name">
                                </div>
                            </div>

                            {{-- Arabic --}}
                            <div class="tab-pane fade" id="ar" role="tabpanel">
                                <div class="mb-3" dir="rtl">
                                    <label for="sub-category-name_ar" class="form-label">SubCategory Name (AR)</label>
                                    <input type="text" name="sub-category-name[ar]" id="sub-category-name_ar" class="form-control"
                                           value="{{ old('sub-category-name.ar') }}" placeholder="Enter name">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Create SubCategory</button>
                        </div>
                    </div>

                    {{-- Right Column: Sidebar (Image & Status) --}}
                    <div class="col-lg-4 col-md-12">
                        <div class="card border p-3 shadow-sm">

                            {{-- Image Upload --}}
                            <div class="text-center mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    Icon / Image <span class="text-info" style="font-size: 0.8em; text-transform: none;">(Recommended: 60x60 px)</span>
                                </label>
                                <div class="bg-light p-2 rounded mb-2">
                                    <img id="iconPreview"
                                         src="{{ asset('images/no_image.jpg') }}"
                                         alt="Icon preview"
                                         class="img-fluid rounded"
                                         style="max-width: 120px; max-height: 120px; object-fit: cover; opacity: 0.6;">
                                </div>
                                <input id="sub-category-icon" type="file" name="sub-category-icon" class="form-control form-control-sm" accept="image/*">
                                <div class="form-text mt-1 text-xs">Accepted: jpeg, png, jpg, gif, webp, bmp. Max 2MB.</div>
                                <div class="mt-2" id="iconFeedback" style="display:none; color: #d63384; font-size: 0.875em;"></div>
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
<script src="{{ asset('admin_assets/js/cat/subcat_image-preview.js') }}"></script>
@endpush
