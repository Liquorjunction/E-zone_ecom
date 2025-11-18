@extends('admin.layouts.app')

@section('header-title', 'Role & Permission')
@section('header-description', 'Manage roles and permissions for the system')

@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.user-management.role-permission') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 me-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Role
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Role Name</th>
                                        <th>Guard</th>
                                        <th>Permissions Assigned</th>
                                        <th style="width: 150px; text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><span class="badge bg-primary">{{ $role->name }}</span></td>
                                            <td>{{ $role->guard_name }}</td>
                                            <td>
                                                {{-- Show first 5 permissions, then count --}}
                                                @foreach($role->permissions->take(5) as $perm)
                                                    <span class="badge bg-info mt-1">{{ $perm->name }}</span>
                                                @endforeach
                                                @if($role->permissions->count() > 5)
                                                    <span class="badge bg-secondary mt-1">+{{ $role->permissions->count() - 5 }} more</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    {{-- Edit Button --}}
                                                    <a href="{{ route('admin.roles-permissions.edit', $role->id) }}"
                                                       class="btn btn-sm btn-warning"
                                                       data-bs-toggle="tooltip"
                                                       title="Edit Role & Permissions">
                                                        Edit
                                                    </a>

                                                    {{-- Delete Button --}}
                                                    <form action="{{ route('admin.roles-permissions.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete Role">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No roles found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
