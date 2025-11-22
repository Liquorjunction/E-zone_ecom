@extends('admin.layouts.app')
@section('title', 'Create SubCategory')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.category-subcategory.subcategories.create') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Create SubCategory</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.category-subcategory.subcategories.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{-- This was already correct, keeping it safe --}}
                                    {{ $cat->{'category-name'} }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sub Category Name</label>
                        <input type="text" name="sub-category-name" id="sub-category-name" class="form-control"
                            value="{{ old('sub-category-name') }}">
                        @error('sub-category-name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sub Category Icon</label>
                        <input type="file" name="sub-category-icon" id="sub-category-icon" class="form-control">
                        <img id="sub-category-image-preview" alt="Preview" style="display:none; max-width:100px;">
                        @error('sub-category-icon')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="status" id="active" value="1"
                            checked>
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                    <button type="submit" class="btn btn-success">Save SubCategory</button>
                    <a href="{{ route('admin.category-subcategory.subcategories.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin_assets/js/subCategoryForm.js') }}"></script>
@endsection
