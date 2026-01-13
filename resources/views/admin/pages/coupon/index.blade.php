@extends('admin.layouts.app')

@section('title', 'Manage Coupons')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Coupons List</h5>
        @can('coupon.create')
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus"></i> Create Coupon
            </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="coupons-table" class="table table-bordered table-striped" style="width:100%;" data-source="{{ route('admin.coupons.data') }}">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/datatableJs/coupons.js') }}"></script>
@endpush
