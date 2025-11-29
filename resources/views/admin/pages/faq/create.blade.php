@extends('admin.layouts.app')
@section('title', 'Create FAQ')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Create FAQ</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.faqs.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.faqs.store') }}" enctype="multipart/form-data">
                            @csrf
                            {{-- Category Dropdown --}}
                            <div class="form-group">
                                <label for="faq_category_id">Category <span class="text-danger">*</span></label>
                                <select name="faq_category_id" id="faq_category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('faq_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faq_category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Updated Language Tabs using Buttons --}}
                            <ul class="nav nav-tabs" id="langTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en"
                                        type="button" role="tab" aria-controls="en" aria-selected="true">
                                        English <span class="text-danger">*</span>
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
                                        <label for="question_en">Question (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="question[en]" class="form-control" id="question_en"
                                            value="{{ old('question.en') }}" placeholder="Enter Question in English">

                                        @error('question.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="mb-0">Answer (EN) <span class="text-danger">*</span></label>

                                            {{-- TRANSLATE BUTTON FOR QUILL --}}
                                            <button type="button" class="btn btn-sm btn-info text-white btn-translate-auto"
                                                data-url="{{ route('admin.translate.content') }}" data-source="editor_answer_en"
                                                {{-- ID of English Source --}} data-target-base="editor_answer" {{-- Prefix for targets (editor_answer_fr, editor_answer_ar) --}}
                                                data-quill="true"> {{-- Flag identifying this as Quill --}}
                                                <i class="fas fa-language mr-1"></i> Translate to Others
                                            </button>
                                        </div>

                                        <div id="editor_answer_en" style="height: 300px;">{!! old('answer.en') !!}</div>
                                        <textarea name="answer[en]" id="hidden_answer_en" style="display:none"></textarea>

                                        @error('answer.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- French Tab --}}
                                <div class="tab-pane fade" id="fr" role="tabpanel" aria-labelledby="fr-tab">
                                    <div class="form-group">
                                        <label for="question_fr">Question (FR)</label>
                                        <input type="text" name="question[fr]" class="form-control" id="question_fr"
                                            value="{{ old('question.fr') }}" placeholder="Enter Question in French">

                                        @error('question.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="answer_fr">Answer (FR)</label>
                                        <textarea name="answer[fr]" class="form-control" id="answer_fr" rows="4" placeholder="Enter Answer in French">{{ old('answer.fr') }}</textarea>

                                        @error('answer.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Arabic Tab --}}
                                <div class="tab-pane fade" id="ar" role="tabpanel" aria-labelledby="ar-tab">
                                    <div class="form-group">
                                        <label for="question_ar">Question (AR)</label>
                                        <input type="text" name="question[ar]" class="form-control" id="question_ar"
                                            value="{{ old('question.ar') }}" placeholder="Enter Question in Arabic">

                                        @error('question.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Answer (AR)</label>
                                        <div id="editor_answer_ar" style="height: 300px;" dir="rtl">{!! old('answer.ar') !!}</div>
                                        <textarea name="answer[ar]" id="hidden_answer_ar" style="display:none"></textarea>

                                        @error('answer.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Status Field --}}
                            <div class="form-group mt-3">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>

                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- Submit --}}
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success">Create FAQ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
