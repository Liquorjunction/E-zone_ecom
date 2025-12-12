@extends('admin.layouts.app')
@section('header-title', 'Create SubCategory')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Create SubCategory</h4>
                <a href="{{ route('admin.category-subcategory.subcategories.index') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left mr-2"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.category-subcategory.subcategories.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mb-3">
                        <label>Parent Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                {{-- Display English name in dropdown --}}
                                <option value="{{ $cat->id }}">{{ $cat->getTranslation('category-name', 'en') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#en">English</button></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="en">
                            <div class="form-group mb-3">
                                <label>SubCategory Name (EN) *</label>
                                <input type="text" name="sub-category-name[en]" class="form-control" required
                                    value="{{ old('sub-category-name.en') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Icon</label>
                        <input type="file" name="sub-category-icon" class="form-control">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" value="active" checked>
                        <label class="form-check-label">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Create SubCategory</button>
                </form>
            </div>
        </div>
    </div>
@endsection
