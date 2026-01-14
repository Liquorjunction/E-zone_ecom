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
    <div class="container-fluid content-inner mt-n5 py-0">
        <div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="header-title">
                                <h4 class="card-title">Hero Slider List</h4>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @include('admin.partials.batch-actions')

                                @can('slider.create')
                                    <div class="card-action d-flex gap-2 align-items-center">
                                        <a href="{{ route('admin.hero-slider.create') }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>Add New Slider
                                        </a>
                                    </div>
                                @endcan
                            </div>

                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="slider-list-table" class="table table-striped" role="grid"
                                    data-bs-toggle="data-table" data-url="{{ route('admin.hero-slider.data') }}">
                                    <thead>
                                        <tr class="light">
                                            <th><input type="checkbox" id="select-all-sliders"></th>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Sort Order</th>
                                            <th style="min-width: 100px">Action</th>
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
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/sliderManagement.js') }}"></script>
@endpush
