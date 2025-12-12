@extends('admin.layouts.app')
@section('title', 'Create Faq Category')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Create Faq Category</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.faq-categories.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs" id="langTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="en-tab" data-toggle="tab" href="#en"
                                        role="tab" aria-controls="en" aria-selected="true">
                                        English <span class="text-danger">*</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="langTabContent">
                                {{-- English Tab --}}
                                <div class="tab-pane fade show active" id="en" role="tabpanel"
                                    aria-labelledby="en-tab">
                                    <div class="form-group">
                                        <label for="name_en">Name (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="name[en]" class="form-control" id="name_en"
                                            value="{{ old('name.en', isset($faqCategory) ? $faqCategory->getTranslation('name', 'en') : '') }}"
                                            placeholder="Enter Name in English">

                                        @error('name.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="description_en">Description (EN) <span
                                                class="text-danger">*</span></label>
                                        <textarea name="description[en]" class="form-control" id="description_en" rows="4"
                                            placeholder="Enter Description in English">{{ old('description.en', isset($faqCategory) ? $faqCategory->getTranslation('description', 'en') : '') }}</textarea>

                                        @error('description.en')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- French Tab --}}
                                <div class="tab-pane fade" id="fr" role="tabpanel" aria-labelledby="fr-tab">
                                    <div class="form-group">
                                        <label for="name_fr">Name (FR)</label>
                                        <input type="text" name="name[fr]" class="form-control" id="name_fr"
                                            value="{{ old('name.fr', isset($faqCategory) ? $faqCategory->getTranslation('name', 'fr') : '') }}"
                                            placeholder="Enter Name in French">

                                        @error('name.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="description_fr">Description (FR)</label>
                                        <textarea name="description[fr]" class="form-control" id="description_fr" rows="4"
                                            placeholder="Enter Description in French">{{ old('description.fr', isset($faqCategory) ? $faqCategory->getTranslation('description', 'fr') : '') }}</textarea>

                                        @error('description.fr')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Arabic Tab --}}
                                <div class="tab-pane fade" id="ar" role="tabpanel" aria-labelledby="ar-tab">
                                    <div class="form-group">
                                        <label for="name_ar">Name (AR)</label>
                                        <input type="text" name="name[ar]" class="form-control" id="name_ar"
                                            value="{{ old('name.ar', isset($faqCategory) ? $faqCategory->getTranslation('name', 'ar') : '') }}"
                                            placeholder="Enter Name in Arabic">

                                        @error('name.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="description_ar">Description (AR)</label>
                                        <textarea name="description[ar]" class="form-control" id="description_ar" rows="4"
                                            placeholder="Enter Description in Arabic">{{ old('description.ar', isset($faqCategory) ? $faqCategory->getTranslation('description', 'ar') : '') }}</textarea>

                                        @error('description.ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Status Field --}}
                            <div class="form-group mt-4">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" id="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Save Faq Category</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
