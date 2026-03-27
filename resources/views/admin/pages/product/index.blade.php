@extends('admin.layouts.app')
@section('title', 'Products Management')

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
            <div>
                <h4 class="fw-semibold mb-1">Products</h4>
                <p class="text-secondary small mb-0">Manage your products</p>
            </div>
            <div class="d-flex gap-2">
                {{-- Filter Toggle Button --}}
                <button type="button"
                    class="btn btn-sm btn-outline-secondary"
                    id="filter-toggle-btn"
                    data-bs-toggle="collapse"
                    data-bs-target="#filter-panel"
                    aria-expanded="false"
                    aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i>
                    Filters
                    <span id="filter-active-badge" class="badge bg-primary ms-1 d-none">0</span>
                </button>

                @can('products.create')
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Product
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-1"></i> Import
                    </button>
                @endcan
            </div>
        </div>

        {{-- Filter Bar --}}
        @include('admin.pages.product.partials.filter-bar')

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction = auth()->user()->can('products.update') || auth()->user()->can('products.delete');
                @endphp
                <table id="product-table" class="table table-bordered table-striped" style="width:100%;"
                    data-source="{{ route('admin.products.data') }}"
                    data-can-update="{{ $hasAction ? 1 : 0 }}">
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
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Import Modal --}}
    @include('admin.pages.product.partials.import-modal')

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('admin_assets/css/import-progress-notification.css') }}">
        <script src="{{ asset('admin_assets/js/import-progress-notification.js') }}"></script>
        <script src="{{ asset('admin_assets/js/import-detailed-report.js') }}"></script>
        <script src="{{ asset('admin_assets/js/productFilterBar.js') }}"></script>

        {{-- DataTable --}}
        <script src="{{ asset('admin_assets/js/datatableJs/products.js') }}"></script>
    @endpush
@endsection
