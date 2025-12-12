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
                                    <a class="nav-link active" id="step1-tab" data-bs-toggle="pill" href="#step1"
                                        role="tab" aria-controls="step1" aria-selected="true">Step 1: Basic Info</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link disabled" id="step2-tab" data-bs-toggle="pill" href="#step2"
                                        role="tab" aria-controls="step2" aria-selected="false">Step 2: Specifications</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link disabled" id="step3-tab" data-bs-toggle="pill" href="#step3"
                                        role="tab" aria-controls="step3" aria-selected="false">Step 3: Variants & Media</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent-product">

                                {{-- STEP 1: BASIC INFO --}}
                                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                    <div class="row">
                                        {{-- Translatable Details --}}
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Product Details</strong> <span
                                                        class="text-danger">*</span></label>
                                                <ul class="nav nav-pills nav-pills-sm" id="langTabBasic" role="tablist">
                                                    <li class="nav-item" role="presentation"><a class="nav-link active py-1 px-3"
                                                            data-bs-toggle="tab" href="#basic-en" role="tab" aria-controls="basic-en" aria-selected="true">English</a></li>
                                                    <li class="nav-item" role="presentation"><a class="nav-link py-1 px-3" data-bs-toggle="tab"
                                                            href="#basic-fr" role="tab" aria-controls="basic-fr" aria-selected="false">French</a></li>
                                                    <li class="nav-item" role="presentation"><a class="nav-link py-1 px-3" data-bs-toggle="tab"
                                                            href="#basic-ar" role="tab" aria-controls="basic-ar" aria-selected="false">Arabic</a></li>
                                                </ul>
                                            </div>

                                            <div class="tab-content border p-3 rounded">
                                                {{-- English --}}
                                                <div class="tab-pane fade show active" id="basic-en">
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name (EN) <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name[en]" class="form-control" required
                                                            value="{{ old('name.en') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (EN) <span
                                                                class="text-danger">*</span></label>
                                                        <div id="quill-description-en" class="quill-editor"
                                                            style="height: 200px;">{!! old('description.en') !!}</div>
                                                        <input type="hidden" name="description[en]"
                                                            id="description_en" required>
                                                    </div>
                                                </div>
                                                {{-- French --}}
                                                <div class="tab-pane fade" id="basic-fr">
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name (FR)</label>
                                                        <input type="text" name="name[fr]" class="form-control"
                                                            value="{{ old('name.fr') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (FR)</label>
                                                        <div id="quill-description-fr" class="quill-editor"
                                                            style="height: 200px;">{!! old('description.fr') !!}</div>
                                                        <input type="hidden" name="description[fr]"
                                                            id="description_fr">
                                                    </div>
                                                </div>
                                                {{-- Arabic --}}
                                                <div class="tab-pane fade" id="basic-ar">
                                                    <div class="mb-3 text-end" dir="rtl">
                                                        <label class="form-label">Product Name (AR)</label>
                                                        <input type="text" name="name[ar]" class="form-control"
                                                            value="{{ old('name.ar') }}">
                                                    </div>
                                                    <div class="mb-3 text-end" dir="rtl">
                                                        <label class="form-label">Description (AR)</label>
                                                        <div id="quill-description-ar" class="quill-editor"
                                                            style="height: 200px;">{!! old('description.ar') !!}</div>
                                                        <input type="hidden" name="description[ar]"
                                                            id="description_ar">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Dropdowns & Pricing --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Brand <span class="text-danger">*</span></label>
                                            <select name="brand_id" id="brand-select" class="form-select select2-dropdown" required>
                                                <option value="">Select Brand</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Category <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category-select" class="form-select select2-dropdown" required>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->getTranslation('category-name', 'en') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sub Category <span
                                                    class="text-danger">*</span></label>
                                            <select name="sub_category_id" id="subcategory-select" class="form-select select2-dropdown" required>
                                                <option value="">Select Sub Category</option>
                                                @foreach ($subCategories as $subCategory)
                                                    <option value="{{ $subCategory->id }}"
                                                        data-category="{{ $subCategory->category_id }}">
                                                        {{ $subCategory->getTranslation('sub-category-name', 'en') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Base Price <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="price" step="0.01" min="0" class="form-control"
                                                    required value="{{ old('price') }}" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Cover Image <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" name="cover_image" class="form-control"
                                                accept="image/*" required>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" selected>Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" name="is_bestseller"
                                                    value="1" id="is_bestseller" {{ old('is_bestseller') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_bestseller">
                                                    Mark as Bestseller
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-primary next-step" data-next="step2">Next</button>
                                    </div>
                                </div>

                                {{-- STEP 2: SPECIFICATIONS --}}
                                <div class="tab-pane fade" id="step2" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0"><strong>Technical
                                                        Specifications</strong></label>
                                                <ul class="nav nav-pills nav-pills-sm" id="langTabSpecs" role="tablist">
                                                    <li class="nav-item" role="presentation"><a class="nav-link active py-1 px-3"
                                                            data-bs-toggle="tab" href="#specs-en" role="tab" aria-controls="specs-en" aria-selected="true">English</a></li>
                                                    <li class="nav-item" role="presentation"><a class="nav-link py-1 px-3"
                                                            data-bs-toggle="tab" href="#specs-fr" role="tab" aria-controls="specs-fr" aria-selected="false">French</a></li>
                                                    <li class="nav-item" role="presentation"><a class="nav-link py-1 px-3"
                                                            data-bs-toggle="tab" href="#specs-ar" role="tab" aria-controls="specs-ar" aria-selected="false">Arabic</a></li>
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
                                                                    class="form-control" placeholder="e.g. 10x10x5 cm">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Warranty
                                                                    ({{ strtoupper($lang) }})</label>
                                                                <input type="text"
                                                                    name="warranty[{{ $lang }}]"
                                                                    class="form-control" placeholder="e.g. 2 Years">
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
                                                                    <tbody></tbody>
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
                                                            <th>Variant Name (EN) <span class="text-danger">*</span></th>
                                                            <th>Additional Price</th>
                                                            <th>SKU (Auto)</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="no-variants-row">
                                                            <td colspan="4" class="text-center text-muted">No variants
                                                                added. Click "Add Variant" to create one.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Product Gallery Images</label>
                                            <div class="input-group">
                                                <input type="file" name="product_images[]" class="form-control"
                                                    multiple accept="image/*">
                                            </div>
                                            <small class="text-muted">You can select multiple images.</small>
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

    @push('scripts')
        <script src="{{ asset('admin_assets/js/product-form.js') }}"></script>
    @endpush
@endsection
