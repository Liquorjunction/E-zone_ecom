@extends('admin.layouts.app')
@section('title', 'NAPS Payment Activity Logs')
@section('header-title', 'NAPS Payment Activity Logs')
@section('header-description', 'View logs of NAPS payment activities for monitoring and troubleshooting')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.naps-log.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">NAPS Payment Activity Logs</h4>
                <p class="text-secondary small mb-0">View logs of NAPS payment activities for monitoring and troubleshooting
                </p>
            </div>
        </div>

        <div class="card mb-4">
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
