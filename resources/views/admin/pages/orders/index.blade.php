@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Orders</h4>
                        <small class="text-muted">Manage all customer orders</small>
                    </div>
                </div>
                <div class="card-body">
                     <div class="table-responsive">
                        <table id="orders-table" class="table table-striped table-hover w-100"
                            data-route="{{ route('admin.orders.data') }}">
                            <thead class="bg-light text-uppercase small">
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Cancelled By</th>
                                    <th>Cancel Reason</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th class="text-center" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data Loaded via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/orders.js') }}"></script>
@endpush
