@extends('admin.layouts.app')
@section('title', 'Edit FAQ')
@section('header-title', 'Edit FAQ')
@section('header-description', 'Edit the frequently asked question details')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.faqs.edit', $faq->id) as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title"><h4 class="card-title">Edit FAQ</h4></div>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.faqs.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $faq->id }}">

                        <div class="form-group">
                            <label>Category</label>
                            <select name="faq_category_id" class="form-control">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $faq->faq_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <ul class="nav nav-tabs" id="langTab" role="tablist">
                            <li class="nav-item"><button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button">English</button></li>
                            <li class="nav-item"><button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button">French</button></li>
                            <li class="nav-item"><button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button">Arabic</button></li>
                        </ul>

                        <div class="tab-content mt-3">
                            <div class="tab-pane fade show active" id="en">
                                <div class="d-flex justify-content-end mb-3">
                                    {{-- Batch Button --}}
                                    <button type="button" class="btn btn-primary btn-translate-all"
                                        data-url="{{ route('admin.translate.batch') }}"
                                        data-fields='["question", "answer"]'>
                                        <i class="bi bi-translate me-2"></i> Translate All Fields
                                    </button>
                                </div>
                                <div class="form-group">
                                    <label>Question (EN)</label>
                                    <input type="text" name="question[en]" id="question_en" class="form-control" value="{{ old('question.en', $faq->getTranslation('question', 'en')) }}">
                                </div>
                                <div class="form-group">
                                    <label>Answer (EN)</label>
                                    <div id="editor_answer_en" style="height: 200px;"></div>
                                    {{-- Standardized ID --}}
                                    <textarea name="answer[en]" id="answer_en" style="display:none">{!! old('answer.en', $faq->getTranslation('answer', 'en')) !!}</textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="fr">
                                <div class="form-group">
                                    <label>Question (FR)</label>
                                    <input type="text" name="question[fr]" id="question_fr" class="form-control" value="{{ old('question.fr', $faq->getTranslation('question', 'fr')) }}">
                                </div>
                                <div class="form-group">
                                    <label>Answer (FR)</label>
                                    <div id="editor_answer_fr" style="height: 200px;"></div>
                                    <textarea name="answer[fr]" id="answer_fr" style="display:none">{!! old('answer.fr', $faq->getTranslation('answer', 'fr')) !!}</textarea>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="ar">
                                <div class="form-group">
                                    <label>Question (AR)</label>
                                    <input type="text" name="question[ar]" id="question_ar" class="form-control" value="{{ old('question.ar', $faq->getTranslation('question', 'ar')) }}" dir="rtl">
                                </div>
                                <div class="form-group">
                                    <label>Answer (AR)</label>
                                    <div id="editor_answer_ar" style="height: 200px;"></div>
                                    <textarea name="answer[ar]" id="answer_ar" style="display:none">{!! old('answer.ar', $faq->getTranslation('answer', 'ar')) !!}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ $faq->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $faq->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update FAQ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/faqEditor.js') }}"></script>
@endpush
