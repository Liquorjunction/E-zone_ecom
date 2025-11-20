@extends('admin.layouts.app')

@section('title', 'Categories & Subcategories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Management</h4>
                    <div>
                        <a href="{{ route('admin.category-subcategory.categories.create') }}" class="btn btn-primary me-2">Add Category</a>
                        <a href="{{ route('admin.category-subcategory.subcategories.create') }}" class="btn btn-secondary">Add SubCategory</a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="categoryTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="true">Categories</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subcategories-tab" data-bs-toggle="tab" data-bs-target="#subcategories" type="button" role="tab" aria-controls="subcategories" aria-selected="false">Subcategories</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="categoryTabContent">
                        {{-- Categories Table --}}
                        <div class="tab-pane fade show active" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                            <div class="table-responsive">
                                <table id="categories-table" class="table table-striped table-bordered w-100" data-route="{{ route('admin.category-subcategory.categories.data') }}">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        {{-- SubCategories Table --}}
                        <div class="tab-pane fade" id="subcategories" role="tabpanel" aria-labelledby="subcategories-tab">
                             <div class="table-responsive">
                                <table id="subcategories-table" class="table table-striped table-bordered w-100" data-route="{{ route('admin.category-subcategory.subcategories.data') }}">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Parent Category</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/categorySubCategory.js') }}"></script>
@endpush
