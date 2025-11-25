@extends('admin.layouts.app')
@section('header-title', 'Edit Category')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header"><h4>Edit Category</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.category-subcategory.categories.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $category->id }}">

                <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">French</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">Arabic</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                            <label>Category Name (EN)</label>
                            <input type="text" name="category-name[en]" class="form-control" value="{{ $category->getTranslation('category-name', 'en') }}" required>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="fr">
                        <div class="form-group mb-3">
                            <label>Category Name (FR)</label>
                            <input type="text" name="category-name[fr]" class="form-control" value="{{ $category->getTranslation('category-name', 'fr') }}">
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ar">
                        <div class="form-group mb-3" dir="rtl">
                            <label>Category Name (AR)</label>
                            <input type="text" name="category-name[ar]" class="form-control" value="{{ $category->getTranslation('category-name', 'ar') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label>Current Icon</label>
                    @if($category->{'category-icon'})
                        <img src="{{ asset('storage/'.$category->{'category-icon'}) }}" width="50" class="d-block mb-2">
                    @endif
                    <input type="file" name="category-icon" class="form-control">
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="status" value="active" {{ $category->status == 'active' ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Category</button>
            </form>
        </div>
    </div>
</div>
@endsection
