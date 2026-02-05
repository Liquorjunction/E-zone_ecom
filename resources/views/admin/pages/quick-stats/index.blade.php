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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quick Stats List</h5>
            @can('quick_stats.create')
                <div class="card-action d-flex gap-2 align-items-center">
                    @include('admin.partials.batch-actions')
                    <a id="add-quick-stat-btn" href="{{ route('admin.quick-stats.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New Quick Stat
                    </a>
                </div>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @php
                    $hasAction = auth()->user()->can('quick_stats.update') || auth()->user()->can('quick_stats.delete');
                @endphp
                <table id="quick-stats-table" class="table table-striped" role="grid"
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
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/quickStats.js') }}"></script>
@endpush
