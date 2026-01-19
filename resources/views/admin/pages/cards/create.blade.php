@extends('admin.layouts.app')
@section('title', 'Create Card')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.cards.create') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Create Card</h4>
            <a href="{{ route('admin.cards.index') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cards.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    {{-- Left Column: Main Inputs --}}
                    <div class="col-lg-8 col-md-12">

                        {{-- Language Tabs & Translate --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <ul class="nav nav-tabs" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English</button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content" id="langTabContent">
                            {{-- English --}}
                            <div class="tab-pane fade show active" id="en" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="title[en]" id="title_en" class="form-control"
                                           value="{{ old('title.en') }}" required placeholder="Card Title">
                                    @error('title.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description (EN) <span class="text-danger">*</span></label>
                                    <textarea name="description[en]" id="description_en" class="form-control" rows="3" required placeholder="Card Description">{{ old('description.en') }}</textarea>
                                    @error('description.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Link Text (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="link_text[en]" id="link_text_en" class="form-control"
                                           value="{{ old('link_text.en') }}" placeholder="e.g. Shop Now">
                                </div>
                            </div>

                            {{-- French --}}
                            <div class="tab-pane fade" id="fr" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Title (FR)</label>
                                    <input type="text" name="title[fr]" id="title_fr" class="form-control"
                                           value="{{ old('title.fr') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description (FR)</label>
                                    <textarea name="description[fr]" id="description_fr" class="form-control" rows="3">{{ old('description.fr') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Link Text (FR)</label>
                                    <input type="text" name="link_text[fr]" id="link_text_fr" class="form-control"
                                           value="{{ old('link_text.fr') }}">
                                </div>
                            </div>

                            {{-- Arabic --}}
                            <div class="tab-pane fade" id="ar" role="tabpanel">
                                <div class="mb-3" dir="rtl">
                                    <label class="form-label">Title (AR)</label>
                                    <input type="text" name="title[ar]" id="title_ar" class="form-control"
                                           value="{{ old('title.ar') }}">
                                </div>
                                <div class="mb-3" dir="rtl">
                                    <label class="form-label">Description (AR)</label>
                                    <textarea name="description[ar]" id="description_ar" class="form-control" rows="3">{{ old('description.ar') }}</textarea>
                                </div>
                                <div class="mb-3" dir="rtl">
                                    <label class="form-label">Link Text (AR)</label>
                                    <input type="text" name="link_text[ar]" id="link_text_ar" class="form-control"
                                           value="{{ old('link_text.ar') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Common Fields --}}
                        <div class="mb-3">
                            <label class="form-label">Link URL <span class="text-danger">*</span></label>
                            <input type="url" name="link" class="form-control"
                                   value="{{ old('link') }}" placeholder="https://example.com">
                            @error('link') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Create Card</button>
                        </div>
                    </div>

                    {{-- Right Column: Sidebar --}}
                    <div class="col-lg-4 col-md-12">
                        <div class="card border-1 p-3 shadow-sm">

                            {{-- Image Upload --}}
                            <div class="text-center mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Card Image</label>
                                <div class="bg-light p-2 rounded mb-2">
                                    <img id="imagePreview"
                                         src="{{ asset('images/no_image.jpg') }}"
                                         alt="Preview"
                                         class="img-fluid rounded"
                                         style="max-width: 100%; max-height: 200px; object-fit: contain; opacity: 0.6;">
                                </div>
                                <input id="card-image" type="file" name="image" class="form-control form-control-sm"
                                       accept=".jpeg,.png,.jpg,.gif,.webp,.bmp,.ico,.tiff,.avif" required>
                                <div class="form-text mt-1 text-xs">
                                    Accepted: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.<br>
                                    Max Size: 5MB.
                                </div>
                                <div class="mt-2 text-danger small" id="imageFeedback" style="display:none;"></div>
                                @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-3">

                            {{-- Sort Order --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Display Order</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                            </div>

                            {{-- Status Switch --}}
                            <div class="mb-3">
                                <label class="form-label d-block fw-bold">Status</label>
                                <input type="hidden" name="status" value="inactive">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active" checked>
                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/image-preview5mb.js') }}"></script>
@endpush
