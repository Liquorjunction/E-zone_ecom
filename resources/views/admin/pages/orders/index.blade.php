@extends('admin.layouts.app')
@section('title', 'Orders')
@section('header-title', 'Orders Management')
@section('header-description', 'Manage all customer orders')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.orders.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Orders</h4>
                <p class="text-secondary small mb-0">Manage all customer orders</p>
            </div>
            <div class="d-flex align-items-center gap-2">
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
        @include('admin.pages.orders.partials.filter-bar')

        {{-- Orders Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <table id="orders-table" class="table table-striped table-hover w-100"
                    data-route="{{ route('admin.orders.data') }}">
                    <thead class="bg-light text-uppercase small">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Order Status</th>
                            <th>Cancelled By</th>
                            <th>Payment Mode</th>
                            <th>Payment Status</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data Loaded via AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- NAPS Status Modal Container --}}
        <div id="naps-modal-container"></div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/orders.js') }}"></script>
    <script src="{{ asset('admin_assets/js/orderFilterBar.js') }}"></script>
    <script src="{{ asset('admin_assets/js/orderNapsStatusModal.js') }}"></script>
@endpush
