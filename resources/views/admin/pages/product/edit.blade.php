@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Product: {{ $product->getTranslation('name', 'en') }}</h4>
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data" id="product-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $product->id }}">

                            {{-- Navigation Pills --}}
                            <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-product" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1" type="button" role="tab">Step 1: Basic Info</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2" type="button" role="tab">Step 2: Specifications (Attributes)</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3" type="button" role="tab">Step 3: Variants & Images</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent-product">

                                {{-- STEP 1: BASIC INFO --}}
                                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Product Details</strong> <span
                                                        class="text-danger">*</span></label>
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
                                                            <i class="fas fa-language me-2"></i> Translate All Fields
                                                        </button>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name (EN) <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name[en]" class="form-control"
                                                            value="{{ old('name.en', $product->getTranslation('name', 'en')) }}"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (EN)</label>
                                                        <div id="quill-description-en" class="quill-editor"
                                                            style="height: 200px;">
                                                            {!! old('description.en', $product->getTranslation('description', 'en')) !!}
                                                        </div>
                                                        <input type="hidden" name="description[en]" id="description_en"
                                                            value="{{ $product->getTranslation('description', 'en') }}">
                                                    </div>
                                                </div>
                                                {{-- FR --}}
                                                <div class="tab-pane fade" id="basic-fr">
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name (FR)</label>
                                                        <input type="text" name="name[fr]" class="form-control"
                                                            value="{{ old('name.fr', $product->getTranslation('name', 'fr', false)) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (FR)</label>
                                                        <div id="quill-description-fr" class="quill-editor"
                                                            style="height: 200px;">
                                                            {!! old('description.fr', $product->getTranslation('description', 'fr', false)) !!}
                                                        </div>
                                                        <input type="hidden" name="description[fr]" id="description_fr"
                                                            value="{{ $product->getTranslation('description', 'fr', false) }}">
                                                    </div>
                                                </div>
                                                {{-- AR --}}
                                                <div class="tab-pane fade" id="basic-ar">
                                                    <div class="mb-3 text-end" dir="rtl">
                                                        <label class="form-label">Product Name (AR)</label>
                                                        <input type="text" name="name[ar]" class="form-control"
                                                            value="{{ old('name.ar', $product->getTranslation('name', 'ar', false)) }}">
                                                    </div>
                                                    <div class="mb-3 text-end" dir="rtl">
                                                        <label class="form-label">Description (AR)</label>
                                                        <div id="quill-description-ar" class="quill-editor"
                                                            style="height: 200px;">
                                                            {!! old('description.ar', $product->getTranslation('description', 'ar', false)) !!}
                                                        </div>
                                                        <input type="hidden" name="description[ar]" id="description_ar"
                                                            value="{{ $product->getTranslation('description', 'ar', false) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Relations --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Brand</label>
                                            <select name="brand_id" id="brand-select"
                                                class="form-select select2-dropdown" required>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Category</label>
                                            <select name="category_id" id="category-select"
                                                class="form-select select2-dropdown" required>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->getTranslation('category-name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sub Category</label>
                                            <select name="sub_category_id" id="subcategory-select"
                                                class="form-select select2-dropdown" required>
                                                @foreach ($subCategories as $subCategory)
                                                    <option value="{{ $subCategory->id }}"
                                                        data-category="{{ $subCategory->category_id }}"
                                                        {{ $product->sub_category_id == $subCategory->id ? 'selected' : '' }}>
                                                        {{ $subCategory->getTranslation('sub-category-name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Parent Fields --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Display Price (Starts From)</label>
                                            <input type="text" class="form-control" disabled
                                                value="Calculated from Variants">
                                            <small class="text-muted">Actual prices are managed in Step 3.</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Update Cover Image <span class="text-muted">(Recommended: 800x800px)</span></label>
                                            <input type="file" name="cover_image" class="form-control"
                                                accept="image/*">
                                            <small class="text-muted">Supported formats: jpeg, png, jpg, webp, gif, bmp, ico, tiff, avif.</small>
                                            <span class="text-danger d-block">Max size: 2MB.</span>
                                            @if ($product->cover_image)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $product->cover_image) }}"
                                                        class="img-thumbnail" style="height: 60px;">
                                                    <small class="text-muted d-block">Current Cover</small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active"
                                                    {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive"
                                                    {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" name="is_bestseller"
                                                    value="1" id="is_bestseller"
                                                    {{ $product->is_bestseller ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_bestseller">Mark as
                                                    Bestseller</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-primary next-step"
                                            data-next="step2">Next</button>
                                    </div>
                                </div>

                                {{-- STEP 2: SPECIFICATIONS (Restored Attributes Field) --}}
                                <div class="tab-pane fade" id="step2" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Technical
                                                        Specifications</strong></label>
                                                <ul class="nav nav-pills nav-pills-sm" id="langTabSpecs" role="tablist">
                                                    <li class="nav-item"><button class="nav-link active py-1 px-3" data-bs-toggle="tab" data-bs-target="#specs-en" type="button">English</button></li>
                                                    <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#specs-fr" type="button">French</button></li>
                                                    <li class="nav-item"><button class="nav-link py-1 px-3" data-bs-toggle="tab" data-bs-target="#specs-ar" type="button">Arabic</button></li>
                                                </ul>
                                            </div>

                                            <div class="tab-content border p-3 rounded">
                                                @foreach (['en', 'fr', 'ar'] as $lang)
                                                    <div class="tab-pane fade {{ $lang === 'en' ? 'show active' : '' }}"
                                                        id="specs-{{ $lang }}">
                                                        <div class="row {{ $lang === 'ar' ? 'text-end' : '' }}"
                                                            dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Dimensions
                                                                    ({{ strtoupper($lang) }})</label>
                                                                <input type="text"
                                                                    name="dimensions[{{ $lang }}]"
                                                                    class="form-control"
                                                                    value="{{ $product->getTranslation('dimensions', $lang, false) }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Warranty
                                                                    ({{ strtoupper($lang) }})</label>
                                                                <input type="text" name="warranty[{{ $lang }}]"
                                                                    class="form-control"
                                                                    value="{{ $product->getTranslation('warranty', $lang, false) }}">
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label class="form-label d-flex justify-content-between">
                                                                    <span>Detailed Specifications
                                                                        ({{ strtoupper($lang) }})</span>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-info add-spec-btn"
                                                                        data-lang="{{ $lang }}">
                                                                        <i class="fas fa-plus-circle"></i> Add Field
                                                                    </button>
                                                                </label>
                                                                <table class="table table-bordered table-sm"
                                                                    id="spec-table-{{ $lang }}">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th>Title / Feature</th>
                                                                            <th>Value / Description</th>
                                                                            <th style="width: 50px;"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $rawSpecs = $product->getTranslation('specifications', $lang, false);
                                                                            $specs = is_array($rawSpecs) ? $rawSpecs : json_decode($rawSpecs, true) ?? [];
                                                                        @endphp
                                                                        @foreach ($specs as $key => $entry)
                                                                            <tr>
                                                                                <td><input type="text"
                                                                                        name="specifications[{{ $lang }}][{{ $key }}][title]"
                                                                                        class="form-control form-control-sm"
                                                                                        value="{{ $entry['title'] ?? '' }}">
                                                                                </td>
                                                                                <td><input type="text"
                                                                                        name="specifications[{{ $lang }}][{{ $key }}][value]"
                                                                                        class="form-control form-control-sm"
                                                                                        value="{{ $entry['value'] ?? '' }}">
                                                                                </td>
                                                                                <td class="text-center"><button
                                                                                        type="button"
                                                                                        class="btn btn-danger btn-sm py-0 px-2 remove-spec-row">&times;</button>
                                                                                </td>
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
                                        <button type="button" class="btn btn-primary next-step"
                                            data-next="step3">Next</button>
                                    </div>
                                </div>

                                {{-- STEP 3: VARIANTS (Fixed Stock Logic) --}}
                                <div class="tab-pane fade" id="step3" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Product Variants</strong></label>
                                                <button type="button" class="btn btn-sm btn-success"
                                                    id="add-variant-btn">
                                                    <i class="bi bi-plus"></i> Add New Variant
                                                </button>
                                            </div>
                                            <div class="alert alert-light border small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Images and Prices are managed per variant.
                                                <strong>Stock is read-only here. Please use the Inventory module to update
                                                    quantities.</strong>
                                            </div>
                                            <div class="table-responsive border rounded p-2">
                                                <table class="table table-striped mb-0" id="variants-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 40%">Variant Details & Images <span
                                                                    class="text-danger">*</span></th>
                                                            <th style="width: 20%">Selling Price ($) <span
                                                                    class="text-danger">*</span></th>
                                                            <th style="width: 15%">Attributes</th>
                                                            <th style="width: 15%">SKU</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($product->productVariants as $index => $variant)
                                                            <tr>
                                                                <td style="vertical-align: top;">
                                                                    {{-- Hidden ID for Update --}}
                                                                    <input type="hidden"
                                                                        name="variants[{{ $index }}][id]"
                                                                        value="{{ $variant->id }}">

                                                                    <label class="small text-muted mb-1">Variant Name
                                                                        (EN)</label>
                                                                    <input type="text"
                                                                        name="variants[{{ $index }}][name][en]"
                                                                        class="form-control form-control-sm mb-2"
                                                                        value="{{ $variant->getTranslation('variant_name', 'en', false) }}"
                                                                        required>

                                                                    {{-- Hidden Locales --}}
                                                                    <input type="hidden"
                                                                        name="variants[{{ $index }}][name][fr]"
                                                                        value="{{ $variant->getTranslation('variant_name', 'fr', false) }}">
                                                                    <input type="hidden"
                                                                        name="variants[{{ $index }}][name][ar]"
                                                                        value="{{ $variant->getTranslation('variant_name', 'ar', false) }}">

                                                                    {{-- Existing Images --}}
                                                                    @if ($variant->images->count() > 0)
                                                                        <div
                                                                            class="d-flex gap-1 mb-2 overflow-auto p-1 bg-white border rounded">
                                                                            @foreach ($variant->images as $img)
                                                                                <div class="position-relative"
                                                                                    title="Existing Image">
                                                                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                                                                        class="rounded"
                                                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif

                                                                    <label class="small text-muted"><i
                                                                            class="fas fa-plus-circle"></i> Add New
                                                                        Images</label>
                                                                    <input type="file"
                                                                        name="variants[{{ $index }}][images][]"
                                                                        multiple class="form-control form-control-sm"
                                                                        accept="image/*">
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <div class="input-group input-group-sm">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number"
                                                                            name="variants[{{ $index }}][price]"
                                                                            step="0.01" min="0"
                                                                            class="form-control"
                                                                            value="{{ $variant->price }}" required>
                                                                    </div>
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-variant-attributes" data-index="{{ $index }}">Edit Attributes</button>
                                                                    <input type="hidden" name="variants[{{ $index }}][attributes][en]" value="{{ json_encode($variant->getTranslation('attributes', 'en', false) ?? []) }}">
                                                                    <input type="hidden" name="variants[{{ $index }}][attributes][fr]" value="{{ json_encode($variant->getTranslation('attributes', 'fr', false) ?? []) }}">
                                                                    <input type="hidden" name="variants[{{ $index }}][attributes][ar]" value="{{ json_encode($variant->getTranslation('attributes', 'ar', false) ?? []) }}">
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <span
                                                                        class="badge bg-secondary">{{ $variant->sku }}</span>
                                                                </td>
                                                                <td class="text-end" style="vertical-align: top;">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-variant"
                                                                        title="Delete Variant">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-secondary prev-step"
                                            data-prev="step2">Previous</button>
                                        <button type="submit" class="btn btn-warning">Update Product</button>
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
