@extends('admin.layouts.app')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">NAPS Payment Logs</h5>
        </div>
        <div class="card-body">
            <table id="napsLogTable" class="table table-bordered table-striped"
                data-source="{{ route('admin.naps-log.data') }}">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Log ID</th>
                        <th>Order ID</th>
                        <th>API Type</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/napsLog.js') }}"></script>
@endpush
