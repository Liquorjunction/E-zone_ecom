@extends('admin.layouts.app')

@section('header-title', 'Create Role')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Create New Role</h4>
                    <a href="{{ route('admin.roles-permissions.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles-permissions.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Sales Manager" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <h5 class="mb-3">Permissions</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 20%;">Module</th>
                                        <th class="text-center" style="width: 15%;">Read</th>
                                        <th class="text-center" style="width: 15%;">Create</th>
                                        <th class="text-center" style="width: 15%;">Update</th>
                                        <th class="text-center" style="width: 15%;">Delete</th>
                                        <th class="text-center" style="width: 15%;">Select All</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissionGroups as $module => $permissions)
                                    <tr>
                                        <td class="fw-bold text-capitalize">
                                            {{ str_replace('-', ' ', $module) }}
                                        </td>

                                        {{-- Loop explicitly through CRUD actions to ensure alignment --}}
                                        @foreach(['read', 'create', 'update', 'delete'] as $action)
                                            @php
                                                $perm = $permissions->firstWhere('name', "$module.$action");
                                            @endphp
                                            <td class="text-center">
                                                @if($perm)
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input perm-checkbox row-{{ $module }}"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $perm->id }}"
                                                               id="perm_{{ $perm->id }}">
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        {{-- Row Select All --}}
                                        <td class="text-center">
                                            <input class="form-check-input select-all-row"
                                                   type="checkbox"
                                                   data-module="row-{{ $module }}">
                                        </td>
                                    </tr>
                                    @endforeach

                                    {{-- Handle Misc Permissions (like dashboard.view) --}}
                                    {{-- Logic: If a module doesn't fit CRUD (has other names), you might want a separate section or ensure your migration names them module.read --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Save Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle "Select All" per row
        const selectAllButtons = document.querySelectorAll('.select-all-row');

        selectAllButtons.forEach(btn => {
            btn.addEventListener('change', function() {
                const moduleClass = this.getAttribute('data-module');
                const checkboxes = document.querySelectorAll('.' + moduleClass);
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        });
    });
</script>
@endpush
@endsection
