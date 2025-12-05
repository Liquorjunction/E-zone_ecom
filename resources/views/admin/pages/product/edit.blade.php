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

                            <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-product" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="step1-tab" data-bs-toggle="pill" href="#step1"
                                        role="tab">Step 1: Basic Info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step2-tab" data-bs-toggle="pill" href="#step2"
                                        role="tab">Step 2: Specifications</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step3-tab" data-bs-toggle="pill" href="#step3"
                                        role="tab">Step 3: Variants & Media</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent-product">

                                {{-- STEP 1 --}}
                                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Product Details</strong> <span
                                                        class="text-danger">*</span></label>
                                                <ul class="nav nav-pills nav-pills-sm" id="langTabBasic" role="tablist">
                                                    <li class="nav-item"><a class="nav-link active py-1 px-3"
                                                            data-bs-toggle="tab" href="#basic-en">English</a></li>
                                                    <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab"
                                                            href="#basic-fr">French</a></li>
                                                    <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab"
                                                            href="#basic-ar">Arabic</a></li>
                                                </ul>
                                            </div>

                                            <div class="tab-content border p-3 rounded">
                                                @foreach (['en', 'fr', 'ar'] as $lang)
                                                    <div class="tab-pane fade {{ $lang === 'en' ? 'show active' : '' }}"
                                                        id="basic-{{ $lang }}">
                                                        <div class="mb-3 {{ $lang === 'ar' ? 'text-end' : '' }}"
                                                            dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
                                                            <label class="form-label">Product Name
                                                                ({{ strtoupper($lang) }})</label>
                                                            <input type="text" name="name[{{ $lang }}]"
                                                                class="form-control"
                                                                value="{{ old('name.' . $lang, $product->getTranslation('name', $lang, false)) }}"
                                                                {{ $lang === 'en' ? 'required' : '' }}>
                                                        </div>
                                                        <div class="mb-3 {{ $lang === 'ar' ? 'text-end' : '' }}"
                                                            dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
                                                            <label class="form-label">Description
                                                                ({{ strtoupper($lang) }})</label>
                                                            <div id="quill-description-{{ $lang }}"
                                                                class="quill-editor" style="height: 200px;">
                                                                {!! old('description.' . $lang, $product->getTranslation('description', $lang, false)) !!}</div>
                                                            <input type="hidden" name="description[{{ $lang }}]"
                                                                id="description-input-{{ $lang }}"
                                                                value="{{ $product->getTranslation('description', $lang, false) }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Brand <span class="text-danger">*</span></label>
                                            <select name="brand_id" class="form-select" required>
                                                <option value="">Select Brand</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Category <span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-select" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->getTranslation('category-name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sub Category <span
                                                    class="text-danger">*</span></label>
                                            <select name="sub_category_id" class="form-select" required>
                                                <option value="">Select Sub Category</option>
                                                @foreach ($subCategories as $subCategory)
                                                    <option value="{{ $subCategory->id }}"
                                                        data-category="{{ $subCategory->category_id }}"
                                                        {{ $product->sub_category_id == $subCategory->id ? 'selected' : '' }}>
                                                        {{ $subCategory->getTranslation('sub-category-name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Base Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="price" step="0.01" class="form-control"
                                                    required value="{{ old('price', $product->price) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Update Cover Image</label>
                                            <input type="file" name="cover_image" class="form-control"
                                                accept="image/*">
                                            @if ($product->cover_image)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $product->cover_image) }}"
                                                        alt="Current Cover" class="img-thumbnail" style="height: 60px;">
                                                    <small class="text-muted d-block">Current Image</small>
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
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-primary next-step" data-next="step2">Next:
                                            Specifications</button>
                                    </div>
                                </div>

                                {{-- STEP 2 --}}
                                <div class="tab-pane fade" id="step2" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Technical
                                                        Specifications</strong></label>
                                                <ul class="nav nav-pills nav-pills-sm" id="langTabSpecs" role="tablist">
                                                    <li class="nav-item"><a class="nav-link active py-1 px-3"
                                                            data-bs-toggle="tab" href="#specs-en">English</a></li>
                                                    <li class="nav-item"><a class="nav-link py-1 px-3"
                                                            data-bs-toggle="tab" href="#specs-fr">French</a></li>
                                                    <li class="nav-item"><a class="nav-link py-1 px-3"
                                                            data-bs-toggle="tab" href="#specs-ar">Arabic</a></li>
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
                                                                <input type="text"
                                                                    name="warranty[{{ $lang }}]"
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
                                                                        <i class="bi bi-plus-circle"></i> Add Field
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
                                                                            // Safety check: Spatie sometimes returns array, sometimes JSON string depending on cast
                                                                            $rawSpecs = $product->getTranslation(
                                                                                'specifications',
                                                                                $lang,
                                                                                false,
                                                                            );
                                                                            $specs = is_array($rawSpecs)
                                                                                ? $rawSpecs
                                                                                : json_decode($rawSpecs, true);
                                                                            $specs = $specs ?: [];
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
                                        <button type="button" class="btn btn-primary next-step" data-next="step3">Next:
                                            Variants & Media</button>
                                    </div>
                                </div>

                                {{-- STEP 3 --}}
                                <div class="tab-pane fade" id="step3" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Product Variants</strong></label>
                                                <button type="button" class="btn btn-sm btn-success"
                                                    id="add-variant-btn">
                                                    <i class="bi bi-plus"></i> Add Variant
                                                </button>
                                            </div>
                                            <div class="table-responsive border rounded p-2">
                                                <table class="table table-striped mb-0" id="variants-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Variant Name (EN)</th>
                                                            <th>Additional Price</th>
                                                            <th>SKU</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($product->productVariants as $index => $variant)
                                                            <tr>
                                                                <td>
                                                                    {{-- Hidden ID for existing variants is CRITICAL for syncing --}}
                                                                    <input type="hidden"
                                                                        name="variants[{{ $index }}][id]"
                                                                        value="{{ $variant->id }}">

                                                                    {{-- Name inputs --}}
                                                                    <input type="text"
                                                                        name="variants[{{ $index }}][name][en]"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ $variant->getTranslation('variant_name', 'en', false) }}"
                                                                        required>
                                                                    <input type="hidden"
                                                                        name="variants[{{ $index }}][name][fr]"
                                                                        value="{{ $variant->getTranslation('variant_name', 'fr', false) }}">
                                                                    <input type="hidden"
                                                                        name="variants[{{ $index }}][name][ar]"
                                                                        value="{{ $variant->getTranslation('variant_name', 'ar', false) }}">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group input-group-sm">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number"
                                                                            name="variants[{{ $index }}][price]"
                                                                            step="0.01" class="form-control"
                                                                            value="{{ $variant->price }}" required>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-secondary">{{ $variant->sku }}</span>
                                                                </td>
                                                                <td class="text-end">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-variant">Delete</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Add New Gallery Images</label>
                                            <input type="file" name="product_images[]" class="form-control" multiple
                                                accept="image/*">

                                            @if ($product->images->count() > 0)
                                                <div class="mt-3">
                                                    <label class="form-label">Existing Gallery:</label>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($product->images as $img)
                                                            <div class="position-relative">
                                                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                                                    class="img-thumbnail"
                                                                    style="height: 80px; width: 80px; object-fit: cover;">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
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

    @push('scripts')
        <script src="{{ asset('admin_assets/js/product-form.js') }}"></script>
    @endpush
@endsection
