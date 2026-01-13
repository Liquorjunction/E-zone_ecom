@extends('admin.layouts.app')
@section('title', 'Products Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Products</h3>
                        @can('products.create')
                            <div>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fa fa-file-excel"></i> Import
                                </button>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Product
                                </a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <table id="product-table" class="table table-bordered table-striped" style="width:100%;"
                            data-source="{{ route('admin.products.data') }}">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Bestseller</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal (Keep Existing) --}}
    @include('admin.pages.product.partials.import-modal')

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('admin_assets/css/import-progress-notification.css') }}">
        <script src="{{ asset('admin_assets/js/import-progress-notification.js') }}"></script>
        <script src="{{ asset('admin_assets/js/import-detailed-report.js') }}"></script>
        {{-- DataTable --}}
        <script src="{{ asset('admin_assets/js/datatableJs/products.js') }}"></script>
    @endpush
@endsection
