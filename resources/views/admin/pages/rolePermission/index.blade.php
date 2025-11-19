@extends('admin.layouts.app')

@section('header-title', 'Role & Permission')
@section('header-description', 'Manage roles and permissions for the system')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.user-management.role-permission') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Roles List</h4>

                        {{-- Link to Create Page --}}
                        <a href="{{ route('admin.roles-permissions.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 me-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" width="20">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Role
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="roles-table" class="table table-striped table-bordered" role="grid"
                                data-bs-toggle="data-table" data-url="{{ route('admin.roles-permissions.data') }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Role Name</th>
                                        <th>Guard</th>
                                        <th>Actions</th>
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
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/rolePermission.js') }}"></script>
@endpush
