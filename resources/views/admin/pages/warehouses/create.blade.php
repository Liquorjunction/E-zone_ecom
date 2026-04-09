@extends('admin.layouts.app')

@section('title', 'Create Warehouse')

@section('content')
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Create New Warehouse</h4>
                    </div>
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.warehouses.store') }}" method="POST" id="warehouse-form">
                        @csrf

                        <div class="row">
                            {{-- Status --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Translatable Fields Container --}}
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0"><strong>Warehouse Details</strong> <span class="text-danger">*</span></label>
                                    <ul class="nav nav-pills nav-pills-sm" id="langTab" role="tablist">
                                        <li class="nav-item"><a class="nav-link active py-1 px-3" data-bs-toggle="tab" href="#lang-en">English</a></li>
                                        <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#lang-fr">French</a></li>
                                        <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#lang-ar">Arabic</a></li>
                                    </ul>
                                </div>

                                <div class="tab-content border p-3 rounded">

                                    {{-- ENGLISH TAB (Mandatory) --}}
                                    <div class="tab-pane fade show active" id="lang-en">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Warehouse Name (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="warehouse_name[en]" class="form-control @error('warehouse_name.en') is-invalid @enderror"
                                                    placeholder="Warehouse Name in English" required value="{{ old('warehouse_name.en') }}">
                                                @error('warehouse_name.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="location[en]" class="form-control @error('location.en') is-invalid @enderror"
                                                    placeholder="Location in English" required value="{{ old('location.en') }}">
                                                @error('location.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- FRENCH TAB --}}
                                    <div class="tab-pane fade" id="lang-fr">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Warehouse Name (FR)</label>
                                                <input type="text" name="warehouse_name[fr]" class="form-control @error('warehouse_name.fr') is-invalid @enderror"
                                                    placeholder="Nom de l'entrepôt en français" value="{{ old('warehouse_name.fr') }}">
                                                @error('warehouse_name.fr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (FR)</label>
                                                <input type="text" name="location[fr]" class="form-control @error('location.fr') is-invalid @enderror"
                                                    placeholder="Emplacement en français" value="{{ old('location.fr') }}">
                                                @error('location.fr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ARABIC TAB --}}
                                    <div class="tab-pane fade" id="lang-ar">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Warehouse Name (AR)</label>
                                                <input type="text" name="warehouse_name[ar]" class="form-control @error('warehouse_name.ar') is-invalid @enderror"
                                                    placeholder="اسم المستودع بالعربية" value="{{ old('warehouse_name.ar') }}" dir="rtl">
                                                @error('warehouse_name.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (AR)</label>
                                                <input type="text" name="location[ar]" class="form-control @error('location.ar') is-invalid @enderror"
                                                    placeholder="الموقع بالعربية" value="{{ old('location.ar') }}" dir="rtl">
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
                            <button type="submit" class="btn btn-primary">Create Warehouse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
