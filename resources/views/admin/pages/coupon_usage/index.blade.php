@extends('admin.layouts.app')

@section('title', 'Coupon Usage')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Coupon Usage Report</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="coupon-usage-table" class="table table-bordered table-striped" style="width:100%;" data-source="{{ route('admin.coupon-usage.data') }}">
                <thead>
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
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/datatableJs/coupon-usage.js') }}"></script>
@endpush
