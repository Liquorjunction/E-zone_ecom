@extends('admin.layouts.app')
@section('title', 'Stock Report')
@section('header-title', 'Stock Report')
@section('header-description', 'Track inventory by SKU, batch, and location')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.reports.stock') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Stock Report
                </h4>
                <p class="text-secondary small mb-0">Track inventory by SKU, batch, and location</p>
            </div>
        </div>

        {{-- Main Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">

                {{-- Main DataTable --}}
                <div id="stockReportTableWrapper" class="p-3">
                    <table id="stockReportTable" class="table table-striped table-hover table-bordered w-100 dataTable"
                        data-url="{{ route('admin.reports.stock.data') }}">
                        <thead class="bg-light text-uppercase small">
                            <tr>
                                <th style="width:40px;"></th>
                                <th>#</th>
                                <th>SKU</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Total Qty</th>
                                <th>Sold Qty</th>
                                <th>Reserved Qty</th>
                                <th>Available Qty</th>
                                {{-- <th>Returned Qty</th> --}}
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin_assets/css/stockReportTable.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('admin_assets/js/stockReportTable.js') }}"></script>
@endpush
