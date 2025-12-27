@extends('admin.layouts.app')

@section('title', 'Edit Warehouse')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Edit Warehouse: {{ $warehouse->getTranslation('warehouse_name', 'en') }}</h4>
                    </div>
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $warehouse->id }}">

                        <div class="row">
                            {{-- Warehouse ID (Read-only) --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Warehouse ID</label>
                                <input type="text" class="form-control" value="{{ $warehouse->warehouse_id }}" disabled>
                                <small class="text-muted">Auto-generated and cannot be changed</small>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $warehouse->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $warehouse->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Translatable Fields Container --}}
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0"><strong>Warehouse Details</strong></label>
                                    <ul class="nav nav-pills nav-pills-sm" id="langTabEdit" role="tablist">
                                        <li class="nav-item"><a class="nav-link active py-1 px-3" data-bs-toggle="tab" href="#edit-lang-en">English</a></li>
                                        <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#edit-lang-fr">French</a></li>
                                        <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#edit-lang-ar">Arabic</a></li>
                                    </ul>
                                </div>

                                <div class="tab-content border p-3 rounded">
                                    {{-- EN --}}
                                    <div class="tab-pane fade show active" id="edit-lang-en">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Warehouse Name (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="warehouse_name[en]" class="form-control @error('warehouse_name.en') is-invalid @enderror"
                                                    value="{{ old('warehouse_name.en', $warehouse->getTranslation('warehouse_name', 'en')) }}" required>
                                                @error('warehouse_name.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="location[en]" class="form-control @error('location.en') is-invalid @enderror"
                                                    value="{{ old('location.en', $warehouse->getTranslation('location', 'en')) }}" required>
                                                @error('location.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- FR --}}
                                    <div class="tab-pane fade" id="edit-lang-fr">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Warehouse Name (FR)</label>
                                                <input type="text" name="warehouse_name[fr]" class="form-control @error('warehouse_name.fr') is-invalid @enderror"
                                                    value="{{ old('warehouse_name.fr', $warehouse->getTranslation('warehouse_name', 'fr')) }}">
                                                @error('warehouse_name.fr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (FR)</label>
                                                <input type="text" name="location[fr]" class="form-control @error('location.fr') is-invalid @enderror"
                                                    value="{{ old('location.fr', $warehouse->getTranslation('location', 'fr')) }}">
                                                @error('location.fr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- AR --}}
                                    <div class="tab-pane fade" id="edit-lang-ar">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Warehouse Name (AR)</label>
                                                <input type="text" name="warehouse_name[ar]" class="form-control @error('warehouse_name.ar') is-invalid @enderror"
                                                    value="{{ old('warehouse_name.ar', $warehouse->getTranslation('warehouse_name', 'ar')) }}" dir="rtl">
                                                @error('warehouse_name.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (AR)</label>
                                                <input type="text" name="location[ar]" class="form-control @error('location.ar') is-invalid @enderror"
                                                    value="{{ old('location.ar', $warehouse->getTranslation('location', 'ar')) }}" dir="rtl">
                                                @error('location.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Warehouse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
