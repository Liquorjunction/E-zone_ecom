@extends('admin.layouts.app')
@section('title', 'About Us Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between border-bottom flex-wrap py-3">
                        <div class="header-title">
                            <h4 class="card-title">About Us List</h4>
                        </div>
                        <div class="card-action">
                            @can('about_us.create')
                                <a href="{{ route('admin.about_us.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus mr-2"></i>Add New
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @php
                                $hasAction = auth()->user()->can('about_us.update') || auth()->user()->can('about_us.delete');
                            @endphp
                            <table id="about-us-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.about_us.data') }}"
                                data-has-action="{{ $hasAction ? 1 : 0 }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description (Preview)</th>
                                        <th>Created At</th>
                                        {{-- <th>Status</th> --}}
                                        @canany(['about_us.update', 'about_us.delete'])
                                            <th>Action</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DataTables will populate this via AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/aboutUs.js') }}"></script>
    @endpush
@endsection
