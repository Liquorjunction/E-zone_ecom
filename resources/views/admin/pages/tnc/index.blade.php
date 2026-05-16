@extends('admin.layouts.app')
@section('title', 'Terms and Conditions')
@section('header-title', 'Terms and Conditions Management')
@section('header-description', 'Create and manage terms and conditions displayed on the site')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.tncs.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Terms and Conditions</h4>
                <p class="text-secondary small mb-0">Create and manage terms and conditions displayed on the site</p>
            </div>
            @can('tnc.create')
                <div>
                    @if ($tncCount == 0)
                        <a href="{{ route('admin.tncs.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add Terms and Conditions
                        </a>
                    @else
                        <span class="text-muted small">Only one Terms and Conditions entry is allowed.</span>
                    @endif
                </div>
            @endcan
        </div>

        {{-- Terms and Conditions Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction = auth()->user()->can('tnc.update') || auth()->user()->can('tnc.delete');
                @endphp
                <table id="tnc-table" class="table table-striped table-bordered w-100"
                    data-route="{{ route('admin.tncs.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Last Updated</th>
                            @canany(['tnc.update', 'tnc.delete'])
                                <th>Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/tnc.js') }}"></script>
@endpush
