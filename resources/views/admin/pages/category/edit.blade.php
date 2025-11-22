@extends('admin.layouts.app')
@section('title', 'Edit Category')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.category-subcategory.categories.edit', $category) as $breadcrumb)
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
                <h4>Edit Category</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.category-subcategory.categories.update', $category->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $category->id }}">

                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category-name" id="category-name" class="form-control"
                            value="{{ old('category-name', $category->{'category-name'}) }}">
                        @error('category-name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category Icon</label>
                        <input type="file" name="category-icon" id="category-icon" class="form-control">
                        @if($category->{'category-icon'})
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $category->{'category-icon'}) }}" alt="Current Icon" width="50">
                            </div>
                        @endif
                        @error('category-icon')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        {{-- FIX: Checked logic against Enum 'active' --}}
                        <input type="checkbox" class="form-check-input" name="status" id="active" value="1"
                            {{ (old('status') || $category->status === 'active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="active">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Category</button>
                    <a href="{{ route('admin.category-subcategory.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin_assets/js/categoryForm.js') }}"></script>
@endsection
