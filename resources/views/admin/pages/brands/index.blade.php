@extends('admin.layouts.app')
@section('title', 'Brands Management')
@section('header-title', 'Brands Management')
@section('header-description', 'Manage all brands associated with products')
@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.brands.index') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
     <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Brands</h4>
                        @can('brands.create')
                            <div>
                                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Brand
                                </a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @php
                                $hasAction = auth()->user()->can('brands.edit') || auth()->user()->can('brands.delete');
                            @endphp
                            <table id="brand-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.brands.data') }}"
                                data-has-action="{{ $hasAction ? 1 : 0 }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        @if($hasAction)
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/brands.js') }}"></script>
    @endpush
@endsection
