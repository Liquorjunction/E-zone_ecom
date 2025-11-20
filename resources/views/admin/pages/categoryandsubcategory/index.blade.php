@extends('admin.layouts.app')

@section('title', 'Categories & Subcategories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Categories & Subcategories</h4>
                    <a href="{{ route('admin.category-subcategory.create') }}" class="btn btn-primary">Add New</a>
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

                    <div class="tab-content" id="categoryTabContent">
                        {{-- Categories Table --}}
                        <div class="tab-pane fade show active" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                            <div class="table-responsive mt-3">
                                {{-- ADDED data-url attribute here --}}
                                <table id="categories-table" class="table table-striped" data-url="{{ route('admin.category-subcategory.dataCategory') }}">
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

                        {{-- Subcategories Table --}}
                        <div class="tab-pane fade" id="subcategories" role="tabpanel" aria-labelledby="subcategories-tab">
                            <div class="table-responsive mt-3">
                                {{-- ADDED data-url attribute here --}}
                                <table id="subcategories-table" class="table table-striped" data-url="{{ route('admin.category-subcategory.dataSubCategory') }}">
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

@section('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/categorySubCategory.js') }}"></script>
@endsection
