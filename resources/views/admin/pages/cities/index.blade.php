@extends('admin.layouts.app')
@section('title', 'Cities Management')
@section('header-title', 'Cities Management')
@section('header-description', 'Manage all cities')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Cities</h4>
                        @can('cities.create')
                            <div>
                                <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add City
                                </a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @php
                                $hasAction = auth()->user()->can('cities.update') || auth()->user()->can('cities.delete');
                            @endphp
                            <table id="city-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.cities.data') }}"
                                data-has-action="{{ $hasAction ? 1 : 0 }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>City Name</th>
                                        <th>Country Name</th>
                                        <th>Status</th>
                                        @if ($hasAction)
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
        <script src="{{ asset('admin_assets/js/datatableJs/cities.js') }}"></script>
    @endpush
@endsection
