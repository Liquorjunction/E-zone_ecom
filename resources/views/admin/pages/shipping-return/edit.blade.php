@extends('admin.layouts.app')

@section('title', 'Edit Shipping & Return Policy')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Policy</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.shipping-return.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shipping-return.update') }}" method="POST" id="shippingForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $shippingAndReturn->id }}">

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en"
                                        type="button" role="tab">English</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr"
                                        type="button" role="tab">French</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar"
                                        type="button" role="tab">Arabic</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="langTabContent">
                                {{-- English Tab --}}
                                <div class="tab-pane fade show active" id="en" role="tabpanel">
                                    <button type="button" class="btn btn-primary btn-translate-all"
                                        data-url="{{ route('admin.translate.batch') }}"
                                        data-fields='["title", "content"]'>
                                        <i class="fas fa-language me-2"></i> Translate All Fields
                                    </button>

                                    <div class="form-group mb-3">
                                        <label>Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="title[en]" id="title_en" class="form-control"
                                            value="{{ old('title.en', $shippingAndReturn->getTranslation('title', 'en')) }}"
                                            required>
                                        @error('title.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Content (EN) <span class="text-danger">*</span></label>
                                        <div id="editor_content_en" style="height: 300px;">{!! old('content.en', $shippingAndReturn->getTranslation('content', 'en')) !!}</div>
                                        <textarea name="content[en]" id="content_en" class="d-none">{{ old('content.en', $shippingAndReturn->getTranslation('content', 'en')) }}</textarea>
                                        @error('content.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- French Tab --}}
                                <div class="tab-pane fade" id="fr" role="tabpanel">
                                    <div class="form-group mb-3">
                                        <label>Title (FR)</label>
                                        <input type="text" name="title[fr]" id="title_fr" class="form-control"
                                            value="{{ old('title.fr', $shippingAndReturn->getTranslation('title', 'fr')) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Content (FR)</label>
                                        <div id="editor_content_fr" style="height: 300px;">{!! old('content.fr', $shippingAndReturn->getTranslation('content', 'fr')) !!}</div>
                                        <textarea name="content[fr]" id="content_fr" class="d-none">{{ old('content.fr', $shippingAndReturn->getTranslation('content', 'fr')) }}</textarea>
                                    </div>
                                </div>

                                {{-- Arabic Tab --}}
                                <div class="tab-pane fade" id="ar" role="tabpanel">
                                    <div class="form-group mb-3" dir="rtl">
                                        <label>Title (AR)</label>
                                        <input type="text" name="title[ar]" id="title_ar" class="form-control"
                                            value="{{ old('title.ar', $shippingAndReturn->getTranslation('title', 'ar')) }}">
                                    </div>
                                    <div class="form-group mb-3" dir="rtl">
                                        <label>Content (AR)</label>
                                        <div id="editor_content_ar" style="height: 300px;">{!! old('content.ar', $shippingAndReturn->getTranslation('content', 'ar')) !!}</div>
                                        <textarea name="content[ar]" id="content_ar" class="d-none">{{ old('content.ar', $shippingAndReturn->getTranslation('content', 'ar')) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="form-group mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status"
                                        value="active" {{ $shippingAndReturn->status == 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Update Policy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/shippingReturnEditor.js') }}"></script>
    @endpush
@endsection
