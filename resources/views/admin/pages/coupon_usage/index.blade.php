@extends('admin.layouts.app')
@section('title', 'Coupon Usage')
@section('header-title', 'Coupon Usage Report')
@section('header-description', 'View coupon usage history across users and orders')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.coupon-usage.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
        <div>
            <h4 class="fw-semibold mb-1">Coupon Usage Report</h4>
            <p class="text-secondary small mb-0">View coupon usage history across users and orders</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="coupon-usage-table" class="table table-striped table-hover w-100"
                    data-source="{{ route('admin.coupon-usage.data') }}">
                    <thead class="bg-light text-uppercase small">
                        <tr>
                            <th>#</th>
                            <th>Coupon Name</th>
                            <th>Coupon Code</th>
                            <th>User Name</th>
                            <th>User Email</th>
                            <th>Order ID</th>
                            <th>Used At</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/datatableJs/coupon-usage.js') }}"></script>
@endpush
