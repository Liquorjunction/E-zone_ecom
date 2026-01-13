@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Create New Product</h4>
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                            id="product-form">
                            @csrf

                            {{-- Navigation Pills --}}
                            <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-product" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1" type="button" role="tab" aria-controls="step1" aria-selected="true">Step 1: Basic Info</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link disabled" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2" type="button" role="tab" aria-controls="step2" aria-selected="false">Step 2: Specifications</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link disabled" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3" type="button" role="tab" aria-controls="step3" aria-selected="false">Step 3: Variants & Inventory</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent-product">

                                {{-- STEP 1: BASIC INFO --}}
                                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Product Details</strong> <span class="text-danger">*</span></label>
                                                <ul class="nav nav-pills nav-pills-sm" id="langTabBasic" role="tablist">
                                                    <li class="nav-item"><button class="nav-link active py-1 px-3" data-bs-toggle="tab" data-bs-target="#basic-en" type="button">English</button></li>
                                                    <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#basic-fr" type="button">French</button></li>
                                                    <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#basic-ar" type="button">Arabic</button></li>
                                                </ul>
                                            </div>

                                            <div class="tab-content border p-3 rounded">
                                                {{-- EN --}}
                                                <div class="tab-pane fade show active" id="basic-en">
                                                    <div class="d-flex justify-content-end mb-3">
                                                        <button type="button" class="btn btn-primary btn-translate-all"
                                                            data-url="{{ route('admin.translate.batch') }}"
                                                            data-fields='["name","description","dimensions","warranty","specifications"]'>
                                                            <i class="bi bi-translate me-2"></i> Translate All Fields
                                                        </button>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name (EN) <span class="text-danger">*</span></label>
                                                        <input type="text" name="name[en]" class="form-control"
                                                            value="{{ old('name.en') }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (EN)</label>
                                                        <div id="quill-description-en" class="quill-editor" style="height: 200px;">
                                                            {!! old('description.en') !!}
                                                        </div>
                                                        <input type="hidden" name="description[en]" id="description_en" value="{{ old('description.en') }}">
                                                    </div>
                                                </div>
                                                {{-- FR --}}
                                                <div class="tab-pane fade" id="basic-fr">
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name (FR)</label>
                                                        <input type="text" name="name[fr]" class="form-control" value="{{ old('name.fr') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (FR)</label>
                                                        <div id="quill-description-fr" class="quill-editor" style="height: 200px;">
                                                            {!! old('description.fr') !!}
                                                        </div>
                                                        <input type="hidden" name="description[fr]" id="description_fr" value="{{ old('description.fr') }}">
                                                    </div>
                                                </div>
                                                {{-- AR --}}
                                                <div class="tab-pane fade" id="basic-ar">
                                                    <div class="mb-3 text-end" dir="rtl">
                                                        <label class="form-label">Product Name (AR)</label>
                                                        <input type="text" name="name[ar]" class="form-control" value="{{ old('name.ar') }}">
                                                    </div>
                                                    <div class="mb-3 text-end" dir="rtl">
                                                        <label class="form-label">Description (AR)</label>
                                                        <div id="quill-description-ar" class="quill-editor" style="height: 200px;">
                                                            {!! old('description.ar') !!}
                                                        </div>
                                                        <input type="hidden" name="description[ar]" id="description_ar" value="{{ old('description.ar') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Relations --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Brand</label>
                                            <select name="brand_id" id="brand-select" class="form-select select2-dropdown" required>
                                                <option value="" disabled {{ old('brand_id') ? '' : 'selected' }}>Select Brand</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Category</label>
                                            <select name="category_id" id="category-select" class="form-select select2-dropdown" required>
                                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->getTranslation('category-name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sub Category</label>
                                            <select name="sub_category_id" id="subcategory-select" class="form-select select2-dropdown" required>
                                                <option value="" disabled {{ old('sub_category_id') ? '' : 'selected' }}>Select Sub Category</option>
                                                @foreach ($subCategories as $subCategory)
                                                    <option value="{{ $subCategory->id }}" data-category="{{ $subCategory->category_id }}" {{ old('sub_category_id') == $subCategory->id ? 'selected' : '' }}>
                                                        {{ $subCategory->getTranslation('sub-category-name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Parent Fields --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Display Price (Starts From)</label>
                                            <input type="text" class="form-control" disabled value="Calculated from Variants">
                                            <small class="text-muted">Actual prices are managed in Step 3.</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Cover Image <span class="text-muted">(Recommended: 800x800px)</span> <span class="text-danger">*</span></label>
                                            <input type="file" name="cover_image" class="form-control" accept="image/*" required>
                                            <small class="text-muted">Supported formats: jpeg, png, jpg, webp, gif, bmp, ico, tiff, avif.</small>
                                            <span class="text-danger d-block">Max size: 2MB.</span>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="is_bestseller" {{ old('is_bestseller') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_bestseller">Mark as Bestseller</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-primary next-step" data-next="step2">Next</button>
                                    </div>
                                </div>

                                {{-- STEP 2: SPECIFICATIONS (Unchanged) --}}
                                <div class="tab-pane fade" id="step2" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Technical Specifications</strong></label>
                                                <ul class="nav nav-pills nav-pills-sm" id="langTabSpecs" role="tablist">
                                                    <li class="nav-item" role="presentation"><button class="nav-link active py-1 px-3" data-bs-toggle="tab" data-bs-target="#specs-en" type="button" role="tab" aria-controls="specs-en" aria-selected="true">English</button></li>
                                                    <li class="nav-item" role="presentation"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#specs-fr" type="button" role="tab" aria-controls="specs-fr" aria-selected="false">French</button></li>
                                                    <li class="nav-item" role="presentation"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#specs-ar" type="button" role="tab" aria-controls="specs-ar" aria-selected="false">Arabic</button></li>
                                                </ul>
                                            </div>

                                            <div class="tab-content border p-3 rounded">
                                                @foreach (['en', 'fr', 'ar'] as $lang)
                                                    <div class="tab-pane fade {{ $lang === 'en' ? 'show active' : '' }}"
                                                        id="specs-{{ $lang }}">
                                                        <div class="row {{ $lang === 'ar' ? 'text-end' : '' }}"
                                                            dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Dimensions ({{ strtoupper($lang) }})</label>
                                                                <input type="text" name="dimensions[{{ $lang }}]"
                                                                    class="form-control" placeholder="e.g. 10x10x5 cm">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Warranty ({{ strtoupper($lang) }})</label>
                                                                <input type="text" name="warranty[{{ $lang }}]"
                                                                    class="form-control" placeholder="e.g. 2 Years">
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label class="form-label d-flex justify-content-between">
                                                                    <span>Detailed Specifications ({{ strtoupper($lang) }})</span>
                                                                    <button type="button" class="btn btn-sm btn-info add-spec-btn"
                                                                        data-lang="{{ $lang }}">
                                                                        <i class="bi bi-plus-circle"></i> Add Field
                                                                    </button>
                                                                </label>
                                                                <table class="table table-bordered table-sm" id="spec-table-{{ $lang }}">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th>Title / Feature</th>
                                                                            <th>Value / Description</th>
                                                                            <th style="width: 50px;"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $oldSpecs = old("specifications.{$lang}", []);
                                                                        @endphp
                                                                        @foreach($oldSpecs as $key => $spec)
                                                                        <tr>
                                                                            <td><input type="text" name="specifications[{{ $lang }}][{{ $key }}][title]" class="form-control form-control-sm" value="{{ $spec['title'] ?? '' }}"></td>
                                                                            <td><input type="text" name="specifications[{{ $lang }}][{{ $key }}][value]" class="form-control form-control-sm" value="{{ $spec['value'] ?? '' }}"></td>
                                                                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-spec-row">&times;</button></td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-secondary prev-step"
                                            data-prev="step1">Previous</button>
                                        <button type="button" class="btn btn-primary next-step" data-next="step3">Next</button>
                                    </div>
                                </div>

                                {{-- STEP 3: VARIANTS & MEDIA --}}
                                <div class="tab-pane fade" id="step3" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Product Variants (SKUs & Stock)</strong></label>
                                                <button type="button" class="btn btn-sm btn-success" id="add-variant-btn">
                                                    <i class="bi bi-plus"></i> Add Variant
                                                </button>
                                            </div>
                                            <div class="alert alert-light border small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Upload images specific to each variant (e.g. Red Shirt). Define the selling price and initial stock here.
                                            </div>
                                            <div class="table-responsive border rounded p-2">
                                                <table class="table table-striped mb-0" id="variants-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 30%">Variant Details & Images <span class="text-danger">*</span></th>
                                                            <th style="width: 12%">Selling Price ($) <span class="text-danger">*</span></th>
                                                            <th style="width: 12%">Bestseller</th>
                                                            <th style="width: 12%">Status</th>
                                                            <th style="width: 12%">Attributes</th>
                                                            <th style="width: 12%">SKU (Auto)</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $oldVariants = old('variants', []);
                                                            $hasVariants = !empty($oldVariants);
                                                        @endphp
                                                        @if(!$hasVariants)
                                                        <tr id="no-variants-row">
                                                            <td colspan="7" class="text-center text-muted py-4">
                                                                No variants added. Click "Add Variant" to create one.
                                                            </td>
                                                        </tr>
                                                        @else
                                                            @foreach($oldVariants as $index => $variant)
                                                            <tr>
                                                                <td style="vertical-align: top;">
                                                                    <input type="text" name="variants[{{ $index }}][name][en]" class="form-control form-control-sm mb-2 variant-name-input" placeholder="Name (e.g. Blue / Small)" value="{{ $variant['name']['en'] ?? '' }}" required>
                                                                    <input type="hidden" name="variants[{{ $index }}][name][fr]" class="variant-name-fr" value="{{ $variant['name']['fr'] ?? '' }}">
                                                                    <input type="hidden" name="variants[{{ $index }}][name][ar]" class="variant-name-ar" value="{{ $variant['name']['ar'] ?? '' }}">
                                                                    <div class="mt-2">
                                                                        <label class="small text-muted">
                                                                            Upload Images <span class="text-muted">(You can select multiple)</span>
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <input type="file" name="variants[{{ $index }}][images][]" class="form-control form-control-sm" multiple accept="image/*">
                                                                        <small class="text-muted">Supported formats: jpeg, png, jpg, webp, gif, bmp, ico, tiff, avif.</small>
                                                                        <span class="text-danger d-block">Max size per image: 2MB.</span>
                                                                    </div>
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <div class="input-group input-group-sm">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number" name="variants[{{ $index }}][price]" step="0.01" min="0" class="form-control" placeholder="0.00" value="{{ $variant['price'] ?? '' }}" required>
                                                                    </div>
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <div class="form-check form-switch form-check-inline pe-0">
                                                                        <input class="form-check-input" type="checkbox" name="variants[{{ $index }}][is_bestseller]" value="1" id="variant-bestseller-{{ $index }}" {{ isset($variant['is_bestseller']) && $variant['is_bestseller'] ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="variant-bestseller-{{ $index }}"></label>
                                                                    </div>
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <div class="form-check form-switch form-check-inline pe-0">
                                                                        <input class="form-check-input" type="checkbox" name="variants[{{ $index }}][status]" value="active" id="variant-status-{{ $index }}" {{ isset($variant['status']) && $variant['status'] === 'active' ? 'checked' : 'checked' }}>
                                                                        <label class="form-check-label" for="variant-status-{{ $index }}"></label>
                                                                    </div>
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-variant-attributes" data-index="{{ $index }}">Edit Attributes</button>
                                                                    <input type="hidden" name="variants[{{ $index }}][attributes][en]" value="{{ isset($variant['attributes']['en']) ? json_encode($variant['attributes']['en']) : '[]' }}">
                                                                    <input type="hidden" name="variants[{{ $index }}][attributes][fr]" value="{{ isset($variant['attributes']['fr']) ? json_encode($variant['attributes']['fr']) : '[]' }}">
                                                                    <input type="hidden" name="variants[{{ $index }}][attributes][ar]" value="{{ isset($variant['attributes']['ar']) ? json_encode($variant['attributes']['ar']) : '[]' }}">
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <span class="badge bg-secondary">Auto-Gen</span>
                                                                </td>
                                                                <td class="text-end">
                                                                    <button type="button" class="btn btn-danger btn-sm remove-variant"><i class="bi bi-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-secondary prev-step"
                                            data-prev="step2">Previous</button>
                                        <button type="submit" class="btn btn-success">Create Product</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Variant Translation & Attribute Modal -->
    <div class="modal fade" id="variantTranslationModal" tabindex="-1" aria-labelledby="variantTranslationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="variantTranslationModalLabel">Edit Variant Translations & Attributes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal-variant-index">
                    <ul class="nav nav-tabs mb-3" id="variantLangTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#variant-en" type="button">English</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#variant-fr" type="button">French</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#variant-ar" type="button">Arabic</button></li>
                    </ul>
                    <div class="tab-content">
                        <!-- EN -->
                        <div class="tab-pane fade show active" id="variant-en">
                            <div class="mb-3">
                                <label class="form-label">Variant Name (EN)</label>
                                <input type="text" class="form-control" id="modal-variant-name-en">
                            </div>
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Attributes (EN)</label>
                                <button type="button" class="btn btn-sm btn-outline-primary add-variant-attr-btn" data-lang="en">Add Attribute</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="variant-attr-table-en">
                                    <thead><tr><th>Key</th><th>Value</th><th></th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <!-- FR -->
                        <div class="tab-pane fade" id="variant-fr">
                            <div class="mb-3">
                                <label class="form-label">Variant Name (FR)</label>
                                <input type="text" class="form-control" id="modal-variant-name-fr">
                            </div>
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Attributes (FR)</label>
                                <button type="button" class="btn btn-sm btn-outline-primary add-variant-attr-btn" data-lang="fr">Add Attribute</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="variant-attr-table-fr">
                                    <thead><tr><th>Key</th><th>Value</th><th></th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <!-- AR -->
                        <div class="tab-pane fade" id="variant-ar">
                            <div class="mb-3">
                                <label class="form-label">Variant Name (AR)</label>
                                <input type="text" class="form-control" id="modal-variant-name-ar">
                            </div>
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Attributes (AR)</label>
                                <button type="button" class="btn btn-sm btn-outline-primary add-variant-attr-btn" data-lang="ar">Add Attribute</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="variant-attr-table-ar">
                                    <thead><tr><th>Key</th><th>Value</th><th></th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-variant-translation">Save</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/product-form.js') }}"></script>
    @endpush
@endsection
