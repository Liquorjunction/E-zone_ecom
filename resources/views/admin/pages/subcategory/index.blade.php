@extends('admin.layouts.app')

@section('title', 'Subcategories')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Subcategories</h4>
                        @can('subcategory.create')
                            <div>
                                <a href="{{ route('admin.category-subcategory.subcategories.create') }}"
                                    class="btn btn-secondary">Add SubCategory</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="subcategories-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.category-subcategory.subcategories.data') }}">
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
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/categorySubCategory.js') }}"></script>
@endpush
