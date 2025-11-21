@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Categories</h4>
                        @can('category.create')
                            <div>
                                <a href="{{ route('admin.category-subcategory.categories.create') }}" class="btn btn-primary">Add
                                    Category</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="categories-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.category-subcategory.categories.data') }}">
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/categorySubCategory.js') }}"></script>
@endpush
