@extends('admin.layouts.app')

@section('header-title', 'Quick Stats Management')
@section('header-description', 'Manage the quick stats shown across the site')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.quick-stats.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
            <div>
                <h4 class="fw-semibold mb-1">Quick Stats</h4>
                <p class="text-secondary small mb-0">Manage the quick stats shown across the site</p>
            </div>
            @can('quick_stats.create')
                <a id="add-quick-stat-btn" href="{{ route('admin.quick-stats.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add New Quick Stat
                </a>
            @endcan
        </div>

        {{-- Quick Stats Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction = auth()->user()->can('quick_stats.update') || auth()->user()->can('quick_stats.delete');
                @endphp
                <table id="quick-stats-table" class="table table-striped w-100" role="grid"
                    data-url="{{ route('admin.quick-stats.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr class="light">
                            <th>#</th>
                            <th>Icon</th>
                            <th>Text</th>
                            <th>Status</th>
                            @canany(['quick_stats.update', 'quick_stats.delete'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/quickStats.js') }}"></script>
@endpush
