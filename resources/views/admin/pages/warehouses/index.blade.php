@extends('admin.layouts.app')

@section('title', 'Warehouses')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Warehouses Management</h4>
                    </div>
                    @can('warehouse.create')
                        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Add New Warehouse
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @php
                            $hasAction = auth()->user()->can('warehouse.update') || auth()->user()->can('warehouse.delete');
                        @endphp
                        <table id="warehouses-table" class="table table-striped table-hover" style="width:100%"
                            data-route="{{ route('admin.warehouses.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Warehouse Name</th>
                                    <th>Warehouse ID</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    @canany(['warehouse.update', 'warehouse.delete'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
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
    <script src="{{ asset('admin_assets/js/datatableJs/warehouse.js') }}"></script>
@endpush
