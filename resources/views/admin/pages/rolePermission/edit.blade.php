@extends('admin.layouts.app')

@section('header-title', 'Edit Role')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Edit Role: {{ $role->name }}</h4>
                    <a href="{{ route('admin.roles-permissions.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles-permissions.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <h5 class="mb-3">Permissions</h5>
                        <div class="table-responsive">
                            <table id="permissions-table" class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 20%;">Module</th>
                                        <th class="text-center">Read</th>
                                        <th class="text-center">Create</th>
                                        <th class="text-center">Update</th>
                                        <th class="text-center">Delete</th>
                                        <th class="text-center">Select All</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissionGroups as $module => $permissions)
                                    <tr>
                                        <td class="fw-bold text-capitalize">
                                            {{ str_replace('-', ' ', $module) }}
                                        </td>

                                        @foreach(['read', 'create', 'update', 'delete'] as $action)
                                            @php
                                                $perm = $permissions->firstWhere('name', "$module.$action");
                                                $hasPerm = $perm && $role->hasPermissionTo($perm->name);
                                            @endphp
                                            <td class="text-center">
                                                @if($perm)
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input perm-checkbox row-{{ $module }}"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $perm->id }}"
                                                               id="perm_{{ $perm->id }}"
                                                               {{ $hasPerm ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="text-center">
                                            <input class="form-check-input select-all-row"
                                                   type="checkbox"
                                                   data-module="row-{{ $module }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('admin_assets/js/roleForm.js') }}"></script>
@endpush
@endsection
