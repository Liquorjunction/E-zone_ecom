@extends('admin.layouts.app')
@section('header-title', 'Create Category')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Create Category</h4>
                <a href="{{ route('admin.category-subcategory.categories.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left mr-2"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.category-subcategory.categories.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#en">English</button></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="en">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="category-name-en" class="form-label">Category Name (EN) <span class="text-danger">*</span></label>
                                        <input id="category-name-en" type="text" name="category-name[en]" class="form-control" required aria-required="true" placeholder="Enter category name in English" value="{{ old('category-name.en') }}">
                                        @error('category-name.en')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="category-icon" class="form-label">Icon / Image <span class="text-info">(Recommended: 60x60 px)</span> <span class="text-danger">*</span></label>
                                        <input id="category-icon" type="file" name="category-icon" class="form-control" accept="image/*" aria-describedby="iconHelp">
                                        <div id="iconHelp" class="form-text">Accepted: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif. Max 2MB.</div>
                                        @error('category-icon')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-2" id="iconFeedback" style="display:none;"></div>
                                    </div>

                                    <div class="mb-3">
                                        <button id="submitBtn" type="submit" class="btn btn-primary">Create Category</button>
                                        <a href="{{ route('admin.category-subcategory.categories.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border-1 p-3">
                                        <div class="text-center mb-3">
                                            <img id="iconPreview" src="" alt="Icon preview" class="img-thumbnail" style="width:120px;height:120px;object-fit:cover;display:none;">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label d-block">Status</label>
                                            <input type="hidden" name="status" value="inactive">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active" checked aria-checked="true">
                                                <label class="form-check-label" for="statusSwitch">Active</label>
                                            </div>
                                        </div>

                                        <div class="small text-muted">Tip: Use a square image for best results. You can drag-and-drop an image onto the input.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <style>
                    /* Small improvements for preview area */
                    #iconPreview{background:#f8f9fa;border-radius:6px}
                    #iconFeedback{color:#d63384}
                </style>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/cat/subcat_image-preview.js') }}"></script>
@endpush
