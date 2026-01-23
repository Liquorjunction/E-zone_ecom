@extends('admin.layouts.app')

@section('title', 'Inventory Batches')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Inventory Batches</h4>
                        <small class="text-muted">Grouped by Inward Entry</small>
                    </div>
                    @can('inventories.create')
                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> New Inward Stock
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="inventory-table" class="table table-striped table-hover w-100"
                            data-route="{{ route('admin.inventory.data') }}">
                            <thead class="bg-light text-uppercase small">
                                <tr>
                                    <th>#</th>
                                    <th>Batch No</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Summary</th>
                                    <th>Total Quantity</th>
                                    <th>Subtotal Cost</th>
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
    <script src="{{ asset('admin_assets/js/datatableJs/inventory.js') }}"></script>
@endpush
