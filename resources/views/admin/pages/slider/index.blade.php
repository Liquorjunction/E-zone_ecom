@extends('admin.layouts.app')

@section('header-title', 'Hero Slider Management')
@section('header-description', 'Manage hero sliders for the website')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.hero-slider.index') as $breadcrumb)
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
            <h5 class="mb-0">Hero Slider List</h5>
            @can('slider.create')
                <div class="card-action d-flex gap-2 align-items-center">
                    @include('admin.partials.batch-actions')
                    <a href="{{ route('admin.hero-slider.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New Slider
                    </a>
                </div>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @php
                    $hasAction = auth()->user()->can('slider.update') || auth()->user()->can('slider.delete');
                @endphp
                <table id="slider-list-table" class="table table-striped" role="grid" data-bs-toggle="data-table"
                    data-url="{{ route('admin.hero-slider.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr class="light">
                            <th><input type="checkbox" id="select-all-sliders"></th>
                            <th>#</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            @canany(['slider.update', 'slider.delete'])
                                <th style="min-width: 100px">Action</th>
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
    <script src="{{ asset('admin_assets/js/datatableJs/sliderManagement.js') }}"></script>
@endpush
