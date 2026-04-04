@extends('admin.layouts.app')
@section('title', 'Inventory Transactions')
@section('header-title', 'Inventory Transactions')
@section('header-description', 'Track and manage all inventory movements with visibility and control.')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.inventory-transactions.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
            <div>
                <h4 class="fw-semibold mb-1">Inventory Transactions</h4>
                <p class="text-muted mb-0">Track and manage all inventory movements with visibility and control.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                    <span id="filter-active-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none"
                        style="font-size:0.65em;">0</span>
                </button>
            </div>
        </div>

        {{-- Collapsible Filter Panel --}}
        @include('admin.pages.inventory_transactions.partials.filter-bar')

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <table id="inventory-transactions-table" class="table table-striped table-bordered w-100"
                    data-source="{{ route('admin.inventory-transactions.data') }}">
                    <thead>
                        <tr>
                            <th style="width:20px;"></th>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity Change</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/inventory-transactions.js') }}"></script>
    <script src="{{ asset('admin_assets/js/inventory-transaction-filter-bar.js') }}"></script>
    <script src="{{ asset('admin_assets/js/inventory-populatelist.js') }}"></script>
@endpush
