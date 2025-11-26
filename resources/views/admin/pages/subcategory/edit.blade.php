@extends('admin.layouts.app')

@section('title', 'Edit SubCategory')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">Edit SubCategory</h4>
            <a href="{{ route('admin.category-subcategory.subcategories.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.category-subcategory.subcategories.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $subCategory->id }}">

                <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English (Default)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">French</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">Arabic</button>
                    </li>
                </ul>

                <div class="tab-content" id="langTabContent">
                    {{-- English --}}
                    <div class="tab-pane fade show active" id="en" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary btn-translate-all"
                                data-url="{{ route('admin.translate.batch') }}"
                                data-fields='["sub-category-name"]'>
                                <i class="fas fa-language me-2"></i> Translate All Fields
                            </button>
                        </div>

                        <div class="form-group mb-3">
                            <label>SubCategory Name (EN) <span class="text-danger">*</span></label>
                            <input type="text" name="sub-category-name[en]" id="sub-category-name_en" class="form-control" value="{{ $subCategory->getTranslation('sub-category-name', 'en') }}" required>
                        </div>
                    </div>

                    {{-- French --}}
                    <div class="tab-pane fade" id="fr" role="tabpanel">
                        <div class="form-group mb-3">
                            <label>SubCategory Name (FR)</label>
                            <input type="text" name="sub-category-name[fr]" id="sub-category-name_fr" class="form-control" value="{{ $subCategory->getTranslation('sub-category-name', 'fr') }}">
                        </div>
                    </div>

                    {{-- Arabic --}}
                    <div class="tab-pane fade" id="ar" role="tabpanel">
                        <div class="form-group mb-3" dir="rtl">
                            <label>SubCategory Name (AR)</label>
                            <input type="text" name="sub-category-name[ar]" id="sub-category-name_ar" class="form-control" value="{{ $subCategory->getTranslation('sub-category-name', 'ar') }}">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Parent Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $subCategory->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->getTranslation('category-name', 'en') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $subCategory->sort_order }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $subCategory->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $subCategory->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Icon / Image</label>
                            <input type="file" name="sub-category-icon" class="form-control">
                            @if($subCategory->{'sub-category-icon'})
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$subCategory->{'sub-category-icon'}) }}" width="80" class="rounded">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update SubCategory</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/image-preview.js') }}"></script>
@endpush
