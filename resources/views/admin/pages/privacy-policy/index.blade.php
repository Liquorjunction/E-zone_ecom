@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between border-bottom flex-wrap py-3">
                        <div class="header-title">
                            <h4 class="card-title">Privacy Policy List</h4>
                        </div>
                        <div class="card-action">
                            @can('privacy_policies.create')
                                <a href="{{ route('admin.privacy-policy.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus mr-2"></i>Add New
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- KEPT: id="datatable" and data-url --}}
                            <table id="privacy-policy-table" class="table table-striped" data-route="{{ route('admin.privacy-policy.data') }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Last Updated</th>
                                        <th>Status</th>
                                        <th>Action</th>
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
        <script src="{{ asset('admin_assets/js/datatableJs/privacyPolicy.js') }}"></script>
    @endpush
@endsection
