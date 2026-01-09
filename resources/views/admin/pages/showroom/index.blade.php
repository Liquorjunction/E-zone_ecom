@extends('admin.layouts.app')

@section('title', 'Showrooms List')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Showroom List</h4>
                    </div>
                    @can('showrooms.create')
                    <a href="{{ route('admin.showrooms.create') }}" class="btn btn-primary btn-sm">Add New Showroom</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="showroom-table" class="table table-striped data-table" style="width: 100%"
                            data-route="{{ route('admin.showrooms.data') }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data populated via Yajra DataTables in Controller --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin_assets/js/datatableJs/showroom.js') }}"></script>
@endpush
@endsection
