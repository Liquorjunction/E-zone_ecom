@extends('admin.layouts.app')
@section('title', 'Create Store')
@section('header-title', 'Create New Store')
@section('header-description', 'Add a new store to manage your inventory across multiple locations.')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.stores.create') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Create New Store</h4>
                    </div>
                    <a href="{{ route('admin.stores.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stores.store') }}" method="POST" enctype="multipart/form-data" id="store-form">
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
                                    <label class="form-label mb-0"><strong>Store Details</strong> <span class="text-danger">*</span></label>
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
                                                <label class="form-label">Store Name (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="store_name[en]" class="form-control @error('store_name.en') is-invalid @enderror"
                                                    placeholder="Store Name in English" required value="{{ old('store_name.en') }}">
                                                @error('store_name.en')
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
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Footer (EN)</label>
                                                <textarea name="store_footer[en]" class="form-control @error('store_footer.en') is-invalid @enderror"
                                                    rows="3" placeholder="Footer text in English">{{ old('store_footer.en') }}</textarea>
                                                @error('store_footer.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- FRENCH TAB --}}
                                    <div class="tab-pane fade" id="lang-fr">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Name (FR)</label>
                                                <input type="text" name="store_name[fr]" class="form-control @error('store_name.fr') is-invalid @enderror"
                                                    placeholder="Nom du magasin en français" value="{{ old('store_name.fr') }}">
                                                @error('store_name.fr')
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
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Footer (FR)</label>
                                                <textarea name="store_footer[fr]" class="form-control @error('store_footer.fr') is-invalid @enderror"
                                                    rows="3" placeholder="Texte de pied de page en français">{{ old('store_footer.fr') }}</textarea>
                                                @error('store_footer.fr')
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
                                                <label class="form-label">Store Name (AR)</label>
                                                <input type="text" name="store_name[ar]" class="form-control @error('store_name.ar') is-invalid @enderror"
                                                    placeholder="اسم المتجر بالعربية" value="{{ old('store_name.ar') }}" dir="rtl">
                                                @error('store_name.ar')
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
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Footer (AR)</label>
                                                <textarea name="store_footer[ar]" class="form-control @error('store_footer.ar') is-invalid @enderror"
                                                    rows="3" placeholder="نص التذييل بالعربية" dir="rtl">{{ old('store_footer.ar') }}</textarea>
                                                @error('store_footer.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">If left empty, will be auto-translated from English</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- Store Logo --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Store Logo</label>
                                <input type="file" name="store_logo" class="form-control @error('store_logo') is-invalid @enderror"
                                    accept="image/*" onchange="previewImage(event, 'logo-preview')">
                                @error('store_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Recommended size: 200x200px. Formats: JPG, PNG, SVG</small>
                                <div class="mt-2">
                                    <img id="logo-preview" src="#" alt="Logo Preview" style="max-height: 150px; display: none;" class="img-thumbnail">
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Store</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event, previewId) {
        const file = event.target.files[0];
        const preview = document.getElementById(previewId);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
</script>
@endpush
@endsection
