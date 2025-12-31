@extends('admin.layouts.app')

@section('title', 'Manage Discounts')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Discounts List</h5>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create Discount
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="discounts-table" class="table table-bordered table-striped" style="width:100%;" data-source="{{ route('admin.discounts.datatable') }}">
                <thead>
                    <tr>
                        <th></th> <!-- For the expand icon -->
                        <th>ID</th>
                        <th>Name (EN)</th>
                        <th>Type</th>
                        <th>Value</th>
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
<script src="{{ asset('admin_assets/js/datatableJs/discounts.js') }}"></script>
@endpush
