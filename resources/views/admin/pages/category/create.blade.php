@extends('admin.layouts.app')
@section('header-title', 'Create Category')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header"><h4>Create Category</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.category-subcategory.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#en">English</button></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                            <label>Category Name (EN) *</label>
                            <input type="text" name="category-name[en]" class="form-control" required value="{{ old('category-name.en') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label>Icon/Image</label>
                    <input type="file" name="category-icon" class="form-control">
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="status" value="active" checked>
                    <label class="form-check-label">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Create Category</button>
            </form>
        </div>
    </div>
</div>
@endsection
