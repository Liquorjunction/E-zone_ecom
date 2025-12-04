@extends('admin.layouts.app')
@section('title', 'Create Product')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Create Product</h4>
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">Back</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="productForm">
                            @csrf

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab"
                                        data-bs-target="#en" type="button" role="tab">English (Default)</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="fr-tab" data-bs-toggle="tab"
                                        data-bs-target="#fr" type="button" role="tab">French</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="ar-tab" data-bs-toggle="tab"
                                        data-bs-target="#ar" type="button" role="tab">Arabic</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="langTabContent">
                                <div class="tab-pane fade show active" id="en">
                                    @include('admin.pages.product.partials.form_fields', ['lang' => 'en'])
                                </div>
                                <div class="tab-pane fade" id="fr">
                                    @include('admin.pages.product.partials.form_fields', ['lang' => 'fr'])
                                </div>
                                <div class="tab-pane fade" id="ar" dir="rtl">
                                    @include('admin.pages.product.partials.form_fields', ['lang' => 'ar'])
                                </div>
                            </div>

                            {{-- Non-translatable Fields --}}
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Product Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                                                        <select name="brand_id" class="form-control" required>
                                                            <option value="">Select Brand</option>
                                                            @foreach(\App\Models\Brand::all() as $brand)
                                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" class="form-control" id="category_id" required>
                                                            <option value="">Select Category</option>
                                                            @foreach(\App\Models\Categories::all() as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Sub Category <span class="text-danger">*</span></label>
                                                        <select name="sub_category_id" class="form-control" id="sub_category_id" required>
                                                            <option value="">Select Sub Category</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Cover Image <span class="text-danger">*</span></label>
                                                        <input type="file" name="cover_image" class="form-control" accept="image/*" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                                        <input type="number" name="price" class="form-control" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Status</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="status" value="active" checked>
                                                            <label class="form-check-label">Active</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Variants --}}
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5>Product Variants</h5>
                                            <button type="button" class="btn btn-sm btn-primary" id="addVariant">Add Variant</button>
                                        </div>
                                        <div class="card-body">
                                            <div id="variantsContainer">
                                                <!-- Variants will be added here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Product Images --}}
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Product Images</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Gallery Images</label>
                                                <input type="file" name="product_images[]" class="form-control" accept="image/*" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('admin_assets/js/product.js') }}"></script>
    @endpush
@endsection
