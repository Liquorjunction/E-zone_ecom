@extends('admin.layouts.app')

@section('header-title', 'Quick Stats Management')
@section('header-description', 'Manage the quick stats shown across the site')

{{-- @section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.quick-stats.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection --}}

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Quick Stats List</h4>
                        </div>
                        @can('quick_stats.create')
                            <div class="card-action">
                                <a href="{{ route('admin.quick-stats.create') }}" id="add-quick-stat-btn" class="btn btn-primary">Add Quick Stat</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table id="quick-stats-table" class="table table-striped" role="grid"
                                   data-url="{{ route('admin.quick-stats.data') }}">
                                <thead>
                                    <tr class="light">
                                        <th>#</th>
                                        <th>Icon</th>
                                        <th>Text</th>
                                        <th>Status</th>
                                        <th style="min-width: 120px">Action</th>
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
    <script src="{{ asset('admin_assets/js/datatableJs/quickStats.js') }}"></script>
@endpush
