@extends('admin.layouts.app')
@section('title', 'Dispatch Management')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
        <div>
            <h4 class="fw-semibold mb-1">Dispatch Management</h4>
            <p class="text-secondary small mb-0">Track shipments and update delivery statuses</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-box-seam"></i> Go to Orders
        </a>
    </div>

    {{-- Filters --}}
    <div class="card border rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-medium text-muted">Order Number</label>
                    <input type="text" id="filter_order_number" class="form-control form-control-sm" placeholder="e.g. ORD-123">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium text-muted">Status</label>
                    <select id="filter_status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="dispatched">Dispatched (In Transit)</option>
                        <option value="delivered">Delivered</option>
                        <option value="returned">Returned (RTO)</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" id="clear_filters" class="btn btn-sm btn-light border">Clear Filters</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Dispatches DataTable --}}
    <div class="card border rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dispatchTable" style="width: 100%;" data-url="{{ route('admin.dispatch.data') }}">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-muted small fw-semibold">Dispatch ID</th>
                            <th class="text-uppercase text-muted small fw-semibold">Order</th>
                            <th class="text-uppercase text-muted small fw-semibold">Item & Qty</th>
                            <th class="text-uppercase text-muted small fw-semibold">Source Location</th>
                            <th class="text-uppercase text-muted small fw-semibold">Status</th>
                            <th class="text-uppercase text-muted small fw-semibold">Dispatch Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Populated by Yajra DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/dispatch.js') }}"></script>
@endpush
