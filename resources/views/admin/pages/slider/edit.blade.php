@extends('admin.layouts.app')

@section('title', 'Edit Slider')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">Edit Slider</h4>
            <a href="{{ route('admin.hero-slider.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.hero-slider.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $slider->id }}">

                <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English (Default)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">French</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">Arabic</button>
                    </li>
                </ul>

                <div class="tab-content" id="langTabContent">
                    {{-- English --}}
                    <div class="tab-pane fade show active" id="en" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary btn-translate-all"
                                data-url="{{ route('admin.translate.batch') }}"
                                data-fields='["title", "subtitle", "button_text"]'>
                                <i class="fas fa-language me-2"></i> Translate All Fields
                            </button>
                        </div>

                        <div class="form-group mb-3">
                            <label>Title (EN) <span class="text-danger">*</span></label>
                            <input type="text" name="title[en]" id="title_en" class="form-control" value="{{ $slider->getTranslation('title', 'en') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Subtitle (EN)</label>
                            <input type="text" name="subtitle[en]" id="subtitle_en" class="form-control" value="{{ $slider->getTranslation('subtitle', 'en') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label>Button Text (EN)</label>
                            <input type="text" name="button_text[en]" id="button_text_en" class="form-control" value="{{ $slider->getTranslation('button_text', 'en') }}">
                        </div>
                    </div>

                    {{-- French --}}
                    <div class="tab-pane fade" id="fr" role="tabpanel">
                        <div class="form-group mb-3">
                            <label>Title (FR)</label>
                            <input type="text" name="title[fr]" id="title_fr" class="form-control" value="{{ $slider->getTranslation('title', 'fr') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label>Subtitle (FR)</label>
                            <input type="text" name="subtitle[fr]" id="subtitle_fr" class="form-control" value="{{ $slider->getTranslation('subtitle', 'fr') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label>Button Text (FR)</label>
                            <input type="text" name="button_text[fr]" id="button_text_fr" class="form-control" value="{{ $slider->getTranslation('button_text', 'fr') }}">
                        </div>
                    </div>

                    {{-- Arabic --}}
                    <div class="tab-pane fade" id="ar" role="tabpanel">
                        <div class="form-group mb-3" dir="rtl">
                            <label>Title (AR)</label>
                            <input type="text" name="title[ar]" id="title_ar" class="form-control" value="{{ $slider->getTranslation('title', 'ar') }}">
                        </div>
                        <div class="form-group mb-3" dir="rtl">
                            <label>Subtitle (AR)</label>
                            <input type="text" name="subtitle[ar]" id="subtitle_ar" class="form-control" value="{{ $slider->getTranslation('subtitle', 'ar') }}">
                        </div>
                        <div class="form-group mb-3" dir="rtl">
                            <label>Button Text (AR)</label>
                            <input type="text" name="button_text[ar]" id="button_text_ar" class="form-control" value="{{ $slider->getTranslation('button_text', 'ar') }}">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Button Link</label>
                            <input type="url" name="button_link" class="form-control" value="{{ $slider->button_link }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ $slider->sort_order }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                            @if($slider->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$slider->image) }}" width="150" class="rounded">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ $slider->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Slider</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/image-preview.js') }}"></script>
@endpush
