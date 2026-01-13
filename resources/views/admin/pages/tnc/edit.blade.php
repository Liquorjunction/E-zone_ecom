@extends('admin.layouts.app')
@section('title', 'Edit Terms and Conditions')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Terms and Conditions</h4>
                        </div>
                        <a href="{{ route('admin.tncs.index') }}" class="btn btn-sm btn-primary">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route('admin.tncs.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $tnc->id }}">

                                    {{-- Language Tabs with Translate Button --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <ul class="nav nav-tabs mb-0" id="langTab" role="tablist">
                                            <li class="nav-item"><button class="nav-link active" id="en-tab"
                                                    data-bs-toggle="tab" data-bs-target="#en" type="button">English</button>
                                            </li>
                                            <li class="nav-item"><button class="nav-link" id="fr-tab" data-bs-toggle="tab"
                                                    data-bs-target="#fr" type="button">French</button>
                                            </li>
                                            <li class="nav-item"><button class="nav-link" id="ar-tab" data-bs-toggle="tab"
                                                    data-bs-target="#ar" type="button">Arabic</button>
                                            </li>
                                        </ul>

                                        <button type="button" class="btn btn-sm btn-primary btn-translate-all"
                                            id="btn-translate-all" data-url="{{ route('admin.translate.batch') }}"
                                            data-fields='["title","content"]'>
                                            <i class="bi bi-translate me-2"></i> Translate All Fields
                                        </button>
                                    </div>

                                    {{-- Language Tabs Content --}}
                                    <div class="tab-content mt-3">
                                        {{-- English --}}
                                        <div class="tab-pane fade show active" id="en">
                                            <div class="form-group">
                                                <label class="form-label">Title (EN)<span class="text-danger">*</span></label>
                                                <input type="text" name="title[en]" id="title_en" class="form-control"
                                                    value="{{ old('title.en', $tnc->getTranslation('title', 'en')) }}">
                                                @error('title.en')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="mb-0">Content (EN) <span
                                                            class="text-danger">*</span></label>
                                                    <button type="button"
                                                        class="btn btn-sm btn-info text-white btn-translate-auto"
                                                        data-url="{{ route('admin.translate.content') }}"
                                                        data-source="editor_content_en" data-target-base="editor_content"
                                                        data-quill="true">
                                                        <i class="bi bi-translate mr-1"></i> Translate Content
                                                    </button>
                                                </div>
                                                <div id="editor_content_en" style="height: 300px;"></div>
                                                <textarea name="content[en]" id="content_en" style="display:none">{{ old('content.en', $tnc->getTranslation('content', 'en')) }}</textarea>
                                                @error('content.en')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- French --}}
                                        <div class="tab-pane fade" id="fr">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="mb-0">Title (FR)</label>
                                                </div>
                                                <input type="text" name="title[fr]" id="title_fr" class="form-control"
                                                    value="{{ old('title.fr', $tnc->getTranslation('title', 'fr')) }}">
                                                @error('title.fr')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="mb-0">Content (FR)</label>
                                                </div>
                                                <div id="editor_content_fr" style="height: 300px;"></div>
                                                <textarea name="content[fr]" id="content_fr" style="display:none">{{ old('content.fr', $tnc->getTranslation('content', 'fr')) }}</textarea>
                                                @error('content.fr')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- Arabic --}}
                                        <div class="tab-pane fade" id="ar">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="mb-0">Title (AR)</label>
                                                </div>
                                                <input type="text" name="title[ar]" id="title_ar"
                                                    class="form-control"
                                                    value="{{ old('title.ar', $tnc->getTranslation('title', 'ar')) }}"
                                                    dir="rtl">
                                                @error('title.ar')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="mb-0">Content (AR)</label>
                                                </div>
                                                <div id="editor_content_ar" style="height: 300px;"></div>
                                                <textarea name="content[ar]" id="content_ar" style="display:none" dir="rtl">{{ old('content.ar', $tnc->getTranslation('content', 'ar')) }}</textarea>
                                                @error('content.ar')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status Field --}}
                                    <div class="form-group mt-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="1" {{ $tnc->status == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $tnc->status == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>

                                    {{-- Submit Button --}}
                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-success">Update Terms and
                                            Conditions</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/tncEditor.js') }}"></script>
@endpush
