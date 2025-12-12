@extends('admin.layouts.app')
@section('title', 'Create FAQ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title"><h4 class="card-title">Create FAQ</h4></div>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.faqs.store') }}" id="faqForm">
                        @csrf
                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="faq_category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('faq_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('faq_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <ul class="nav nav-tabs" id="langTab" role="tablist">
                            <li class="nav-item"><button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button">English</button></li>
                        </ul>

                        <div class="tab-content mt-3">
                            {{-- English --}}
                            <div class="tab-pane fade show active" id="en">
                                <div class="form-group">
                                    <label>Question (EN) <span class="text-danger">*</span></label>
                                    <input type="text" name="question[en]" id="question_en" class="form-control" value="{{ old('question.en') }}">
                                    @error('question.en') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="mb-0">Answer (EN) <span class="text-danger">*</span></label>
                                        {{-- Individual Button points to the DIV ID --}}
                                        <button type="button" class="btn btn-sm btn-info text-white btn-translate-auto"
                                            data-url="{{ route('admin.translate.content') }}"
                                            data-source="editor_answer_en"
                                            data-target-base="answer"
                                            data-quill="true"><i class="fas fa-language"></i> Translate</button>
                                    </div>
                                    <div id="editor_answer_en" style="height: 200px;"></div>
                                    {{-- ID CHANGED HERE to match 'answer_en' pattern --}}
                                    <textarea name="answer[en]" id="answer_en" style="display:none">{{ old('answer.en') }}</textarea>
                                    @error('answer.en') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- French --}}
                            <div class="tab-pane fade" id="fr">
                                <div class="form-group">
                                    <label>Question (FR)</label>
                                    <input type="text" name="question[fr]" id="question_fr" class="form-control" value="{{ old('question.fr') }}">
                                </div>
                                <div class="form-group">
                                    <label>Answer (FR)</label>
                                    <div id="editor_answer_fr" style="height: 200px;"></div>
                                    {{-- ID CHANGED HERE --}}
                                    <textarea name="answer[fr]" id="answer_fr" style="display:none">{{ old('answer.fr') }}</textarea>
                                </div>
                            </div>

                            {{-- Arabic --}}
                            <div class="tab-pane fade" id="ar">
                                <div class="form-group">
                                    <label>Question (AR)</label>
                                    <input type="text" name="question[ar]" id="question_ar" class="form-control" value="{{ old('question.ar') }}" dir="rtl">
                                </div>
                                <div class="form-group">
                                    <label>Answer (AR)</label>
                                    <div id="editor_answer_ar" style="height: 200px;"></div>
                                    {{-- ID CHANGED HERE --}}
                                    <textarea name="answer[ar]" id="answer_ar" style="display:none">{{ old('answer.ar') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Create FAQ</button>
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
