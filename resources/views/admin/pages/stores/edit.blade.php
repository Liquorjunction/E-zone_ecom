@extends('admin.layouts.app')

@section('title', 'Edit Store')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Edit Store: {{ $store->getTranslation('store_name', 'en') }}</h4>
                    </div>
                    <a href="{{ route('admin.stores.index') }}" class="btn btn-primary btn-sm">Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stores.update', $store->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $store->id }}">

                        <div class="row">
                            {{-- Store ID (Read-only) --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Store ID</label>
                                <input type="text" class="form-control" value="{{ $store->store_id }}" disabled>
                                <small class="text-muted">Auto-generated and cannot be changed</small>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $store->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $store->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Translatable Fields Container --}}
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0"><strong>Store Details</strong></label>
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
                                            <!-- Store Name (EN) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Name (English) <span class="text-danger">*</span></label>
                                                <input type="text" name="store_name[en]" class="form-control @error('store_name.en') is-invalid @enderror" value="{{ old('store_name.en', $store->getTranslation('store_name', 'en')) }}" required maxlength="255">
                                                @error('store_name.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- Location (EN) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (English)<span class="text-danger">*</span></label>
                                                <input type="text" name="location[en]" class="form-control @error('location.en') is-invalid @enderror" required value="{{ old('location.en', $store->getTranslation('location', 'en')) }}">
                                                @error('location.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- Store Footer (EN) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Footer (English)</label>
                                                <textarea name="store_footer[en]" class="form-control @error('store_footer.en') is-invalid @enderror" maxlength="500">{{ old('store_footer.en', $store->getTranslation('store_footer', 'en')) }}</textarea>
                                                @error('store_footer.en')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- FR --}}
                                    <div class="tab-pane fade" id="edit-lang-fr">
                                        <div class="row">
                                            <!-- Store Name (FR) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Name (French)</label>
                                                <input type="text" name="store_name[fr]" class="form-control @error('store_name.fr') is-invalid @enderror" value="{{ old('store_name.fr', $store->getTranslation('store_name', 'fr')) }}" maxlength="255">
                                                @error('store_name.fr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- Location (FR) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (French)</label>
                                                <input type="text" name="location[fr]" class="form-control @error('location.fr') is-invalid @enderror" value="{{ old('location.fr', $store->getTranslation('location', 'fr')) }}">
                                                @error('location.fr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- Store Footer (FR) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Footer (French)</label>
                                                <textarea name="store_footer[fr]" class="form-control @error('store_footer.fr') is-invalid @enderror" maxlength="500">{{ old('store_footer.fr', $store->getTranslation('store_footer', 'fr')) }}</textarea>
                                                @error('store_footer.fr')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- AR --}}
                                    <div class="tab-pane fade" id="edit-lang-ar">
                                        <div class="row">
                                            <!-- Store Name (AR) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Name (Arabic)</label>
                                                <input type="text" name="store_name[ar]" class="form-control @error('store_name.ar') is-invalid @enderror" value="{{ old('store_name.ar', $store->getTranslation('store_name', 'ar')) }}" maxlength="255">
                                                @error('store_name.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- Location (AR) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Location (Arabic)</label>
                                                <input type="text" name="location[ar]" class="form-control @error('location.ar') is-invalid @enderror" value="{{ old('location.ar', $store->getTranslation('location', 'ar')) }}">
                                                @error('location.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- Store Footer (AR) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store Footer (Arabic)</label>
                                                <textarea name="store_footer[ar]" class="form-control @error('store_footer.ar') is-invalid @enderror" maxlength="500">{{ old('store_footer.ar', $store->getTranslation('store_footer', 'ar')) }}</textarea>
                                                @error('store_footer.ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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
                                <small class="text-muted">Leave empty to keep existing logo. Recommended size: 200x200px</small>

                                <div class="mt-2">
                                    @if($store->store_logo)
                                        <div class="mb-2">
                                            <strong>Current Logo:</strong><br>
                                            <img src="{{ asset('storage/' . $store->store_logo) }}" alt="Current Logo"
                                                style="max-height: 150px;" class="img-thumbnail">
                                        </div>
                                    @endif
                                    <img id="logo-preview" src="#" alt="New Logo Preview"
                                        style="max-height: 150px; display: none;" class="img-thumbnail">
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Store</button>
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
