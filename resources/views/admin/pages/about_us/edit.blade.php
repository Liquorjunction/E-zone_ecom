@extends('admin.layouts.app')
@section('title', 'Edit About Us')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit About Us</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.about_us.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.about_us.update', $aboutUs->id) }}" method="POST" id="aboutUsForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $aboutUs->id }}">

                            {{-- Updated Language Tabs using Buttons --}}
                            <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en"
                                        type="button" role="tab" aria-controls="en" aria-selected="true">
                                        English (Default) <span class="text-danger">*</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr"
                                        type="button" role="tab" aria-controls="fr" aria-selected="false">
                                        French
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar"
                                        type="button" role="tab" aria-controls="ar" aria-selected="false">
                                        Arabic
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="langTabContent">
                                {{-- English Tab --}}
                                <div class="tab-pane fade show active" id="en" role="tabpanel"
                                    aria-labelledby="en-tab">
                                    <div class="form-group">
                                        <label class="form-label">Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="title[en]" id="title_en" class="form-control" value="{{ old('title.en', $aboutUs->getTranslation('title', 'en')) }}" required>
                                        @error('title.en') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Description (EN) <span
                                                    class="text-danger">*</span></label>

                                            {{-- TRANSLATE BUTTON FOR QUILL --}}
                                            <button type="button" class="btn btn-sm btn-info text-white btn-translate-auto"
                                                data-url="{{ route('admin.translate.content') }}" data-source="editor_en"
                                                {{-- ID of English Source --}} data-target-base="editor" {{-- Prefix for targets (editor_fr, editor_ar) --}}
                                                data-quill="true"> {{-- Flag identifying this as Quill --}}
                                                <i class="fas fa-language mr-1"></i> Translate to Others
                                            </button>
                                        </div>

                                        <div id="editor_en" style="height: 300px;">{!! old('description.en', $aboutUs->getTranslation('description', 'en')) !!}</div>
                                        <textarea name="description[en]" id="hidden_description_en" style="display:none"></textarea>
                                        @error('description.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- French Tab --}}
                                <div class="tab-pane fade" id="fr" role="tabpanel" aria-labelledby="fr-tab">
                                    <div class="form-group">
                                        <label>Title (FR)</label>
                                        <input type="text" name="title[fr]" id="title_fr" class="form-control" value="{{ old('title.fr', $aboutUs->getTranslation('title', 'fr')) }}">
                                         @error('title.fr') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Description (FR)</label>
                                        <div id="editor_fr" style="height: 300px;">{!! old('description.fr', $aboutUs->getTranslation('description', 'fr')) !!}</div>
                                        <textarea name="description[fr]" id="hidden_description_fr" style="display:none"></textarea>

                                        @error('description.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Arabic Tab --}}
                                <div class="tab-pane fade" id="ar" role="tabpanel" aria-labelledby="ar-tab">
                                    <div class="form-group">
                                        <label>Title (AR)</label>
                                        <input type="text" name="title[ar]" id="title_ar" class="form-control" value="{{ old('title.ar', $aboutUs->getTranslation('title', 'ar')) }}">
                                        @error('title.ar') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Description (AR)</label>
                                        <div id="editor_ar" style="height: 300px;" dir="rtl">{!! old('description.ar', $aboutUs->getTranslation('description', 'ar')) !!}
                                        </div>
                                        <textarea name="description[ar]" id="hidden_description_ar" style="display:none"></textarea>

                                        @error('description.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Update About Us</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('admin_assets/js/aboutUs.js') }}"></script>
    @endpush
@endsection
