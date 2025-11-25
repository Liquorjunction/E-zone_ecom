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
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Hero Slider List</h4>
                            </div>
                            @can('slider.create')
                                <div class="card-action">
                                    <a href="{{ route('admin.hero-slider.create') }}" class="btn btn-primary">Add New Slider</a>
                                </div>
                            @endcan
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="slider-list-table" class="table table-striped" role="grid"
                                    data-bs-toggle="data-table" data-url="{{ route('admin.hero-slider.data') }}">
                                    <thead>
                                        <tr class="light">
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
