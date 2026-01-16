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
    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between flex-wrap">
                            <div class="header-title">
                                <h4 class="card-title mb-0">Role &amp; Permission</h4>
                            </div>
                            <div class="">
                                {{-- "New Permission" Button & Modal --}}
                                <a href="#" class=" text-center btn btn-primary btn-icon mt-lg-0 mt-md-0 mt-3"
                                    data-bs-toggle="modal" data-bs-target="#newPermissionModal">
                                    <i class="btn-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </i>
                                    <span>New Permission</span>
                                </a>
                                <div class="modal fade" id="newPermissionModal" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="newPermissionModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            {{-- New Permission Form --}}
                                            <form method="POST" action="{{-- {{ route('admin.permissions.store') }} --}}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="newPermissionModalLabel">Add new permission
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="permission_title" class="form-label">Permission
                                                            Title</label>
                                                        {{-- Corrected type to "text", added name attribute --}}
                                                        <input type="text" class="form-control @error('permission_title') is-invalid @enderror" name="permission_title"
                                                            id="permission_title" placeholder="e.g., create-users" value="{{ old('permission_title') }}">
                                                        @error('permission_title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="text-start">
                                                        {{-- Changed to type="submit" --}}
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- "New Role" Button & Modal --}}
                                <a href="#" class=" text-center btn btn-primary btn-icon mt-lg-0 mt-md-0 mt-3"
                                    data-bs-toggle="modal" data-bs-target="#newRoleModal">
                                    <i class="btn-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </i>
                                    <span>New Role</span>
                                </a>
                                <div class="modal fade" id="newRoleModal" data-bs-backdrop="static" data-bs-keyboard="false"
                                    tabindex="-1" aria-labelledby="newRoleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            {{-- New Role Form --}}
                                            <form method="POST" action="{{-- {{ route('admin.roles.store') }} --}}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="newRoleModalLabel">Add new role</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="role_title" class="form-label">Role Title</label>
                                                        {{-- Corrected type to "text", added name attribute --}}
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                                            id="role_title" placeholder="Enter role title" value="{{ old('name') }}">
                                                        @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="role_permissions" class="form-label">Permissions</label>
                                                        <select class="form-control @error('permissions') is-invalid @enderror @error('permissions.*') is-invalid @enderror"
                                                            name="permissions[]" id="role_permissions" multiple size="5">
                                                            {{-- Options would be populated from controller --}}
                                                            {{-- Example: @foreach($permissions as $permission)
                                                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                                            @endforeach --}}
                                                        </select>
                                                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple permissions</small>
                                                        @error('permissions')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                        @error('permissions.*')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="text-start mt-2">
                                                        {{-- Changed to type="submit" --}}
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Main Permissions Table Form --}}
                            <form method="POST" action="{{-- {{ route('admin.permissions.updateAll') }} --}}">
                                @csrf
                                @method('PUT') {{-- Use PUT or PATCH for mass updates --}}

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Permission</th>
                                                <th class="text-center">ADMIN
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="{{-- {{ route('admin.roles.edit', $adminRole->id) }} --}}"
                                                            aria-label="Edit Role" data-bs-original-title="Edit Role">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path d="M15.1655 4.60254L19.7315 9.16854"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        {{-- Add delete functionality, likely a form inside a modal --}}
                                                        <a class="btn btn-sm btn-icon text-danger"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Role" data-bs-original-title="Delete Role">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg"
                                                                    stroke="currentColor">
                                                                    <path
                                                                        d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path d="M20.708 6.23975H3.75" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round"></path>
                                                                    <path
                                                                        d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973"
                                                                        stroke="currentColor" stroke-width="1.Over"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </th>
                                                <th class="text-center">DEMO ADMIN
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Role" data-bs-original-title="Edit Role">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path d="M15.1655 4.60254L19.7315 9.16854"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Role" data-bs-original-title="Delete Role">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg"
                                                                    stroke="currentColor">
                                                                    <path
                                                                        d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path d="M20.708 6.23975H3.75" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round"></path>
                                                                    <path
                                                                        d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973"
                                                                        stroke="currentColor" stroke-width="1.Over"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </th>
                                                <th class="text-center">USER
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Role" data-bs-original-title="Edit Role">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path d="M15.1655 4.60254L19.7315 9.16854"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Role" data-bs-original-title="Delete Role">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg"
                                                                    stroke="currentColor">
                                                                    <path
                                                                        d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826"
                                                                        stroke="currentColor" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                    <path d="M20.708 6.23975H3.75" stroke="currentColor"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round"></path>
                                                                    <path
                                                                        d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973"
                                                                        stroke="currentColor" stroke-width="1.Over"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Note: You would loop through permissions here --}}
                                            <tr class="">
                                                <td class="d-flex justify-content-between">Role
                                                    {{-- Permission Edit/Delete Buttons --}}
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Permission"
                                                            data-bs-original-title="Edit Permission">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg"><!-- ... edit icon ... --></svg>
                                                            </span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger "
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Permission"
                                                            data-bs-original-title="Delete Permission">
                                                            <span class="btn-inner">
                                                                <svg class="icon-20" width="20" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg"
                                                                    stroke="currentColor"><!-- ... delete icon ... --></svg>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </td>
                                                {{-- Add name attributes in an array format --}}
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[ADMIN][Role]" {{-- @checked(old('permissions.ADMIN.Role', $admin->hasPermissionTo('Role'))) --}}>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[DEMO_ADMIN][Role]" {{-- @checked(old('permissions.DEMO_ADMIN.Role', $demoAdmin->hasPermissionTo('Role'))) --}}>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[USER][Role]" {{-- @checked(old('permissions.USER.Role', $user->hasPermissionTo('Role'))) --}}>
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="d-flex justify-content-between">Role Add
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Permission"
                                                            data-bs-original-title="Edit Permission">
                                                            <span class="btn-inner"><!-- ... edit icon ... --></span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger "
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Permission"
                                                            data-bs-original-title="Delete Permission">
                                                            <span class="btn-inner"><!-- ... delete icon ... --></span>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[ADMIN][Role Add]">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[DEMO_ADMIN][Role Add]" checked="">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[USER][Role Add]" checked="">
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="d-flex justify-content-between">Role List
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Permission"
                                                            data-bs-original-title="Edit Permission">
                                                            <span class="btn-inner"><!-- ... edit icon ... --></span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger "
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Permission"
                                                            data-bs-original-title="Delete Permission">
                                                            <span class="btn-inner"><!-- ... delete icon ... --></span>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[ADMIN][Role List]">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[DEMO_ADMIN][Role List]">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[USER][Role List]">
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="d-flex justify-content-between">Permission
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Permission"
                                                            data-bs-original-title="Edit Permission">
                                                            <span class="btn-inner"><!-- ... edit icon ... --></span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger "
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Permission"
                                                            data-bs-original-title="Delete Permission">
                                                            <span class="btn-inner"><!-- ... delete icon ... --></span>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[ADMIN][Permission]" checked="">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[DEMO_ADMIN][Permission]" checked="">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[USER][Permission]" checked="">
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="d-flex justify-content-between">Permission Add
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Permission"
                                                            data-bs-original-title="Edit Permission">
                                                            <span class="btn-inner"><!-- ... edit icon ... --></span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger "
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Permission"
                                                            data-bs-original-title="Delete Permission">
                                                            <span class="btn-inner"><!-- ... delete icon ... --></span>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[ADMIN][Permission Add]" checked="">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[DEMO_ADMIN][Permission Add]" checked="">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[USER][Permission Add]" checked="">
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="d-flex justify-content-between">Permission List
                                                    <div style="float:right;">
                                                        <a class="btn btn-sm btn-icon text-primary flex-end"
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Edit Permission"
                                                            data-bs-original-title="Edit Permission">
                                                            <span class="btn-inner"><!-- ... edit icon ... --></span>
                                                        </a>
                                                        <a class="btn btn-sm btn-icon text-danger "
                                                            data-bs-toggle="tooltip" href="#"
                                                            aria-label="Delete Permission"
                                                            data-bs-original-title="Delete Permission">
                                                            <span class="btn-inner"><!-- ... delete icon ... --></span>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[ADMIN][Permission List]">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[DEMO_ADMIN][Permission List]">
                                                </td>
                                                <td class="text-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="permissions[USER][Permission List]">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-center">
                                        {{-- Converted link to a submit button --}}
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </div>
                            </form> {{-- Closed the main form --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
