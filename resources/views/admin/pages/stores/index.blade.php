@extends('admin.layouts.app')

@section('title', 'Stores List')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Store List</h4>
                    </div>
                    @can('store.create')
                    <a href="{{ route('admin.stores.create') }}" class="btn btn-primary btn-sm">Add New Store</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="store-table" class="table table-striped data-table" style="width: 100%"
                            data-route="{{ route('admin.stores.data') }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Logo</th>
                                    <th>Store ID</th>
                                    <th>Store Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data populated via Yajra DataTables in Controller --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin_assets/js/datatableJs/store.js') }}"></script>
@endpush
@endsection
