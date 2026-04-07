@extends('admin.layouts.app')
@section('title', 'Why Shop With Us')
@section('header-title', 'Why Shop With Us Management')
@section('header-description', 'Manage Why Shop With Us content displayed on the site')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.whyshopwithus.index') as $breadcrumb)
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
                <h4 class="fw-semibold mb-1">Why Shop With Us</h4>
                <p class="text-secondary small mb-0">Manage Why Shop With Us content displayed on the site</p>
            </div>
        </div>

        {{-- Why Shop With Us Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                @php
                    $hasAction =
                        auth()->user()->can('why_choose_us.update') ||
                        auth()->user()->can('why_choose_us.delete');
                @endphp
                <table id="whyshopwithus-table" class="table table-striped table-bordered w-100"
                    data-route="{{ route('admin.whyshopwithus.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>Description</th>
                            @canany(['why_choose_us.update', 'why_choose_us.delete'])
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
    <script src="{{ asset('admin_assets/js/datatableJs/whyShopWithUs.js') }}"></script>
@endpush
