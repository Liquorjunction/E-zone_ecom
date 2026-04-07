@extends('admin.layouts.app')
@section('title', 'Shipping & Returns')
@section('header-title', 'Shipping & Returns Management')
@section('header-description', 'Create and manage shipping and returns policy displayed on the site')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.shipping-return.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Shipping & Returns</h4>
                <p class="text-secondary small mb-0">Create and manage shipping and returns policy displayed on the site</p>
            </div>
            @can('shipping_return.create')
                <div>
                    @if ($shippingReturnCount == 0)
                        <a href="{{ route('admin.shipping-return.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add New
                        </a>
                    @else
                        <span class="text-muted small">Only one Shipping & Returns entry is allowed.</span>
                    @endif
                </div>
            @endcan
        </div>

        {{-- Shipping & Returns Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction =
                        auth()->user()->can('shipping_return.update') ||
                        auth()->user()->can('shipping_return.delete');
                @endphp
                <table id="shipping-return-table" class="table table-striped w-100"
                    data-route="{{ route('admin.shipping-return.data') }}"
                    data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title (EN)</th>
                            <th>Description (EN)</th>
                            <th>Last Updated</th>
                            @canany(['shipping_return.update', 'shipping_return.delete'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DataTables will populate this --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/shippingReturn.js') }}"></script>
@endpush
