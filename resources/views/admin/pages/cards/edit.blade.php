@extends('admin.layouts.app')

@section('title', 'Edit Card')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.cards.edit') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Edit Card</h4>
                <a href="{{ route('admin.cards.index') }}" class="btn btn-secondary">Back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.cards.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $card->id }}">

                    {{-- Language Tabs --}}
                    <ul class="nav nav-tabs mb-3" id="langTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en"
                                type="button" role="tab" aria-controls="en" aria-selected="true">
                                English (Default)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button"
                                role="tab" aria-controls="fr" aria-selected="false">
                                French
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button"
                                role="tab" aria-controls="ar" aria-selected="false">
                                Arabic
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="langTabContent">

                        {{-- English Fields (Source) --}}
                        <div class="tab-pane fade show active" id="en" role="tabpanel">

                            {{-- TRANSLATE ALL BUTTON --}}
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-primary btn-translate-all"
                                    data-url="{{ route('admin.translate.batch') }}"
                                    data-fields='["title", "description", "link_text"]'>
                                    <i class="fas fa-language me-2"></i> Translate All Fields
                                </button>
                            </div>

                            <div class="form-group mb-3">
                                <label>Title (EN) <span class="text-danger">*</span></label>
                                <input type="text" name="title[en]" id="title_en" class="form-control"
                                    value="{{ $card->getTranslation('title', 'en') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Description (EN)</label>
                                <textarea name="description[en]" id="description_en" class="form-control" rows="3">{{ $card->getTranslation('description', 'en') }}</textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Link Text (EN)</label>
                                <input type="text" name="link_text[en]" id="link_text_en" class="form-control"
                                    value="{{ $card->getTranslation('link_text', 'en') }}">
                            </div>
                        </div>

                        {{-- French Fields (Editable) --}}
                        <div class="tab-pane fade" id="fr" role="tabpanel">
                            <div class="form-group mb-3">
                                <label>Title (FR)</label>
                                {{-- Removed 'readonly' and 'bg-light' --}}
                                <input type="text" name="title[fr]" id="title_fr" class="form-control"
                                    value="{{ $card->getTranslation('title', 'fr') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label>Description (FR)</label>
                                <textarea name="description[fr]" id="description_fr" class="form-control" rows="3">{{ $card->getTranslation('description', 'fr') }}</textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Link Text (FR)</label>
                                <input type="text" name="link_text[fr]" id="link_text_fr" class="form-control"
                                    value="{{ $card->getTranslation('link_text', 'fr') }}">
                            </div>
                        </div>

                        {{-- Arabic Fields (Editable) --}}
                        <div class="tab-pane fade" id="ar" role="tabpanel">
                            <div class="form-group mb-3" dir="rtl">
                                <label>Title (AR)</label>
                                {{-- Removed 'readonly' and 'bg-light' --}}
                                <input type="text" name="title[ar]" id="title_ar" class="form-control"
                                    value="{{ $card->getTranslation('title', 'ar') }}">
                            </div>
                            <div class="form-group mb-3" dir="rtl">
                                <label>Description (AR)</label>
                                <textarea name="description[ar]" id="description_ar" class="form-control" rows="3">{{ $card->getTranslation('description', 'ar') }}</textarea>
                            </div>
                            <div class="form-group mb-3" dir="rtl">
                                <label>Link Text (AR)</label>
                                <input type="text" name="link_text[ar]" id="link_text_ar" class="form-control"
                                    value="{{ $card->getTranslation('link_text', 'ar') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Common Fields --}}
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Link URL</label>
                                <input type="url" name="link" class="form-control" value="{{ $card->link }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                    value="{{ $card->sort_order }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Image</label>
                                <input type="file" name="image" class="form-control">
                                @if ($card->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $card->image) }}" width="100" class="rounded">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" {{ $card->status == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ $card->status == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update Card</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/image-preview.js') }}"></script>
@endpush
