@extends('admin.layouts.app')
@section('title', 'Edit Brand')
@section('header-title', 'Edit Brand')
@section('header-description', 'Modify brand details, hero banners, and category showcase cards')
@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.about_us.create') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
<div class="container-fluid content-inner mt-n5 py-0">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-1">Edit Brand: {{ $brand->getTranslation('name', 'en') }}</h4>
                <p class="text-muted small mb-0">Configure basic details, promotional hero sliders, and category showcase sections</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back to Brands
                </a>
                <button type="submit" form="brand-wizard-form" class="btn btn-sm btn-primary">
                    <i class="fa fa-save me-1"></i> Save All Changes
                </button>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.brands.update') }}" method="POST" enctype="multipart/form-data" id="brand-wizard-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $brand->id }}">

                {{-- Wizard Navigation Pills --}}
                <ul class="nav nav-pills mb-4 nav-fill bg-light p-1.5 rounded-3 border" id="pills-tab-brand" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold py-2.5" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1" type="button" role="tab">
                            <i class="bi bi-info-circle me-1"></i> Step 1: Brand Info & Story
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2" type="button" role="tab">
                            <i class="bi bi-images me-1"></i> Step 2: Hero Slider Banners
                            <span class="badge bg-secondary ms-1" id="banner-count-badge">{{ $brand->banners->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3" type="button" role="tab">
                            <i class="bi bi-grid me-1"></i> Step 3: Category Showcases
                            <span class="badge bg-secondary ms-1" id="showcase-count-badge">{{ $brand->categoryShowcases->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent-brand">

                    {{-- ======================================================== --}}
                    {{-- STEP 1: BRAND INFO & STORY                               --}}
                    {{-- ======================================================== --}}
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        <div class="row g-3">
                            {{-- Left Column: Translatable Text Inputs --}}
                            <div class="col-lg-8 col-md-12">
                                <div class="card border p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <ul class="nav nav-tabs" id="langTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en" type="button" role="tab">English</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="fr-tab" data-bs-toggle="tab" data-bs-target="#fr" type="button" role="tab">French</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ar-tab" data-bs-toggle="tab" data-bs-target="#ar" type="button" role="tab">Arabic</button>
                                            </li>
                                        </ul>

                                        <button type="button" class="btn btn-sm btn-outline-primary btn-translate-all"
                                            data-url="{{ route('admin.translate.batch') }}"
                                            data-fields='["name", "tagline", "description"]'>
                                            <i class="bi bi-translate me-1"></i> Auto Translate Fields
                                        </button>
                                    </div>

                                    <div class="tab-content" id="langTabContent">
                                        {{-- English Tab --}}
                                        <div class="tab-pane fade show active" id="en" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="name_en" class="form-label fw-semibold">Brand Name (EN) <span class="text-danger">*</span></label>
                                                <input type="text" name="name[en]" id="name_en" class="form-control"
                                                       value="{{ old('name.en', $brand->getTranslation('name', 'en', false)) }}"
                                                       placeholder="e.g. Sharp" required>
                                                @error('name.en')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="tagline_en" class="form-label fw-semibold">Tagline / Subtitle (EN)</label>
                                                <input type="text" name="tagline[en]" id="tagline_en" class="form-control"
                                                       value="{{ old('tagline.en', $brand->getTranslation('tagline', 'en', false)) }}"
                                                       placeholder="e.g. In step with your future">
                                                <div class="form-text text-muted">Displays as the prominent tagline under or beside the brand logo on the brand page.</div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="description_en" class="form-label fw-semibold">Brand Story / Description (EN)</label>
                                                <textarea name="description[en]" id="description_en" rows="4" class="form-control"
                                                          placeholder="Since 1915, an attitude of giving sincere thought to the people who use product...">{{ old('description.en', $brand->getTranslation('description', 'en', false)) }}</textarea>
                                                <div class="form-text text-muted">Paragraph displayed in the intro card introducing the brand history and mission.</div>
                                            </div>
                                        </div>

                                        {{-- French Tab --}}
                                        <div class="tab-pane fade" id="fr" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="name_fr" class="form-label fw-semibold">Brand Name (FR)</label>
                                                <input type="text" name="name[fr]" id="name_fr" class="form-control"
                                                       value="{{ old('name.fr', $brand->getTranslation('name', 'fr', false)) }}"
                                                       placeholder="e.g. Sharp">
                                            </div>

                                            <div class="mb-3">
                                                <label for="tagline_fr" class="form-label fw-semibold">Tagline / Subtitle (FR)</label>
                                                <input type="text" name="tagline[fr]" id="tagline_fr" class="form-control"
                                                       value="{{ old('tagline.fr', $brand->getTranslation('tagline', 'fr', false)) }}"
                                                       placeholder="e.g. En phase avec votre avenir">
                                            </div>

                                            <div class="mb-3">
                                                <label for="description_fr" class="form-label fw-semibold">Brand Story / Description (FR)</label>
                                                <textarea name="description[fr]" id="description_fr" rows="4" class="form-control"
                                                          placeholder="Description en français...">{{ old('description.fr', $brand->getTranslation('description', 'fr', false)) }}</textarea>
                                            </div>
                                        </div>

                                        {{-- Arabic Tab --}}
                                        <div class="tab-pane fade" id="ar" role="tabpanel">
                                            <div class="mb-3" dir="rtl">
                                                <label for="name_ar" class="form-label fw-semibold">Brand Name (AR)</label>
                                                <input type="text" name="name[ar]" id="name_ar" class="form-control"
                                                       value="{{ old('name.ar', $brand->getTranslation('name', 'ar', false)) }}"
                                                       placeholder="e.g. شارب">
                                            </div>

                                            <div class="mb-3" dir="rtl">
                                                <label for="tagline_ar" class="form-label fw-semibold">Tagline / Subtitle (AR)</label>
                                                <input type="text" name="tagline[ar]" id="tagline_ar" class="form-control"
                                                       value="{{ old('tagline.ar', $brand->getTranslation('tagline', 'ar', false)) }}"
                                                       placeholder="e.g. في خطوة مع مستقبلك">
                                            </div>

                                            <div class="mb-3" dir="rtl">
                                                <label for="description_ar" class="form-label fw-semibold">Brand Story / Description (AR)</label>
                                                <textarea name="description[ar]" id="description_ar" rows="4" class="form-control"
                                                          placeholder="وصف بالعربية...">{{ old('description.ar', $brand->getTranslation('description', 'ar', false)) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Brand Logo & Status --}}
                            <div class="col-lg-4 col-md-12">
                                <div class="card border p-3 shadow-xs">
                                    <div class="text-center mb-3">
                                        <label class="form-label fw-bold small text-uppercase text-muted">
                                            Brand Logo <br>
                                            <span class="text-info" style="font-size: 0.8em; text-transform: none;">(Recommended: Square 1:1, transparent PNG)</span>
                                        </label>
                                        <div class="bg-light p-3 rounded mb-2 d-flex align-items-center justify-content-center" style="min-height: 140px;">
                                            <img id="iconPreview"
                                                 src="{{ $brand->image ? asset('storage/'.$brand->image) : asset('images/no_image.jpg') }}"
                                                 alt="Brand Logo"
                                                 class="img-fluid rounded"
                                                 style="max-width: 140px; max-height: 140px; object-fit: contain;">
                                        </div>
                                        <input id="brand-icon" type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                        <div class="form-text mt-1 text-xs">Accepted: jpeg, png, webp, avif. Max 2MB.</div>
                                        <div class="form-text mt-0 text-xs text-muted">Leave empty to keep current logo.</div>
                                        <div class="mt-2 text-danger small" id="iconFeedback" style="display:none;"></div>
                                        @error('image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="my-3">

                                    <div class="mb-3">
                                        <label class="form-label d-block fw-bold">Brand Status</label>
                                        <input type="hidden" name="status" value="inactive">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" value="active"
                                                   {{ $brand->status == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="statusSwitch">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 1 Navigation --}}
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary next-step" data-next="step2">
                                Next: Hero Banners <i class="fa fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- STEP 2: HERO SLIDER BANNERS                              --}}
                    {{-- ======================================================== --}}
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Hero Slider Information:</strong> These slides render in the full-width promotional slider at the top of the brand page.
                                Recommended aspect ratio is <strong>1600 × 562 px</strong> (or 16:9 / 16:6). No text overlay is needed on the image if already styled.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Promotional Banner Slides</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-banner-btn">
                                <i class="bi bi-plus-circle me-1"></i> Add Banner Slide
                            </button>
                        </div>

                        <div id="banners-container" class="space-y-3">
                            @forelse($brand->banners as $bIndex => $banner)
                                <div class="card border mb-3 banner-item shadow-2xs" data-index="{{ $bIndex }}">
                                    <input type="hidden" name="banners[{{ $bIndex }}][id]" value="{{ $banner->id }}">
                                    <input type="hidden" name="banners[{{ $bIndex }}][_delete]" value="0" class="banner-delete-flag">

                                    <div class="card-body p-3">
                                        <div class="row g-3 align-items-center">
                                            {{-- Banner Preview --}}
                                            <div class="col-md-3 col-sm-4 text-center">
                                                <div class="bg-dark rounded overflow-hidden p-1 d-flex align-items-center justify-content-center" style="height: 90px;">
                                                    <img src="{{ asset('storage/' . $banner->image) }}"
                                                         alt="Banner Preview"
                                                         class="img-fluid rounded banner-preview-img"
                                                         style="max-height: 85px; width: 100%; object-fit: cover;">
                                                </div>
                                                <div class="mt-1 small text-muted">Current Slide #{{ $loop->iteration }}</div>
                                            </div>

                                            {{-- Inputs --}}
                                            <div class="col-md-5 col-sm-8">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold mb-1">Replace Image (optional)</label>
                                                    <input type="file" name="banners[{{ $bIndex }}][image]" class="form-control form-control-sm banner-file-input" accept="image/*">
                                                    <div class="form-text text-2xs text-muted">Leave empty to keep existing banner.</div>
                                                </div>
                                                <div>
                                                    <label class="form-label small fw-bold mb-1">Alt Text / Title</label>
                                                    <input type="text" name="banners[{{ $bIndex }}][alt_text][en]" class="form-control form-control-sm"
                                                           value="{{ $banner->getTranslation('alt_text', 'en', false) }}" placeholder="e.g. Sharp Home Appliances Range">
                                                </div>
                                            </div>

                                            {{-- Settings: Sort & Status --}}
                                            <div class="col-md-3 col-sm-6">
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold mb-1">Sort Order</label>
                                                    <input type="number" name="banners[{{ $bIndex }}][sort_order]" class="form-control form-control-sm"
                                                           value="{{ $banner->sort_order ?? $bIndex }}" min="0">
                                                </div>
                                                <div>
                                                    <label class="form-label small fw-bold mb-1">Status</label>
                                                    <select name="banners[{{ $bIndex }}][status]" class="form-select form-select-sm">
                                                        <option value="active" {{ $banner->status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $banner->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Delete Button --}}
                                            <div class="col-md-1 col-sm-6 text-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-banner-btn" title="Remove Banner">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted bg-light rounded border border-dashed" id="no-banners-msg">
                                    <i class="bi bi-images fs-1 text-secondary mb-2 d-block"></i>
                                    <p class="mb-1">No hero banners created yet.</p>
                                    <small>Click <strong>"Add Banner Slide"</strong> above to add promotional slides.</small>
                                </div>
                            @endforelse
                        </div>

                        {{-- Step 2 Navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary prev-step" data-prev="step1">
                                <i class="fa fa-arrow-left me-1"></i> Previous: Brand Info
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="step3">
                                Next: Category Showcases <i class="fa fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- STEP 3: CATEGORY SHOWCASES                               --}}
                    {{-- ======================================================== --}}
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                <strong>Category Showcase Cards:</strong> These render in an alternating left/right layout on the brand page.
                                Linking each card to a store <strong>Category</strong> enables visitors to click "View [Category]" and instantly jump down with the product catalog filtered.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Showcase Cards</h5>
                            <button type="button" class="btn btn-sm btn-success" id="add-showcase-btn">
                                <i class="bi bi-plus-circle me-1"></i> Add Showcase Card
                            </button>
                        </div>

                        <div id="showcases-container">
                            @forelse($brand->categoryShowcases as $sIndex => $showcase)
                                <div class="card border mb-4 showcase-item shadow-2xs" data-index="{{ $sIndex }}">
                                    <input type="hidden" name="showcases[{{ $sIndex }}][id]" value="{{ $showcase->id }}">
                                    <input type="hidden" name="showcases[{{ $sIndex }}][_delete]" value="0" class="showcase-delete-flag">

                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary rounded-pill">#{{ $loop->iteration }}</span>
                                            <span class="fw-bold showcase-header-title text-dark">
                                                {{ $showcase->getTranslation('title', 'en', false) ?: 'Showcase Card' }}
                                            </span>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-showcase-btn py-0 px-2" title="Remove Card">
                                            <i class="fa fa-trash me-1"></i> Remove
                                        </button>
                                    </div>

                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            {{-- Lifestyle Image Column --}}
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Lifestyle Photo</label>
                                                <div class="bg-light rounded border p-1 text-center mb-2" style="height: 120px;">
                                                    <img src="{{ $showcase->image ? asset('storage/' . $showcase->image) : asset('images/no_image.jpg') }}"
                                                         alt="Showcase Preview"
                                                         class="img-fluid rounded showcase-preview-img"
                                                         style="max-height: 110px; width: 100%; object-fit: cover;">
                                                </div>
                                                <input type="file" name="showcases[{{ $sIndex }}][image]" class="form-control form-control-sm showcase-file-input" accept="image/*">
                                                <div class="form-text text-2xs text-muted">Recommended: 800×600 or 1:1 square.</div>
                                            </div>

                                            {{-- Linking & Ordering Column --}}
                                            <div class="col-md-9">
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-5">
                                                        <label class="form-label small fw-bold mb-1">Linked Category (for product filtering) <span class="text-danger">*</span></label>
                                                        <select name="showcases[{{ $sIndex }}][category_id]"
                                                                class="showcase-category-select browser-default form-control form-control-sm"
                                                                data-allow-clear="true"
                                                                data-placeholder="Select category"
                                                                id="category_id_{{ $sIndex }}">
                                                            @if ($showcase->category_id && $showcase->category)
                                                                <option selected value="{{ $showcase->category_id }}">
                                                                    {{ $showcase->category->getTranslation('category-name', 'en', false) }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold mb-1">Linked Subcategory (optional)</label>
                                                        <select name="showcases[{{ $sIndex }}][sub_category_id]"
                                                                class="showcase-subcategory-select browser-default form-control form-control-sm"
                                                                data-allow-clear="true"
                                                                data-placeholder="Select sub category"
                                                                id="sub_category_id_{{ $sIndex }}"
                                                                {{ empty($showcase->category_id) ? 'disabled' : '' }}>
                                                            @if ($showcase->sub_category_id && $showcase->subCategory)
                                                                <option selected value="{{ $showcase->sub_category_id }}">
                                                                    {{ $showcase->subCategory->getTranslation('sub-category-name', 'en', false) }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label small fw-bold mb-1">Sort Order</label>
                                                        <input type="number" name="showcases[{{ $sIndex }}][sort_order]" class="form-control form-control-sm"
                                                               value="{{ $showcase->sort_order ?? $sIndex }}" min="0">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="form-label small fw-bold mb-1">Status</label>
                                                        <select name="showcases[{{ $sIndex }}][status]" class="form-select form-select-sm">
                                                            <option value="active" {{ $showcase->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $showcase->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Multilingual Tabs for this Showcase --}}
                                                <div class="border rounded p-2 bg-light/50">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <ul class="nav nav-tabs nav-tabs-sm" role="tablist">
                                                            <li class="nav-item">
                                                                <button class="nav-link active py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-en-{{ $sIndex }}" type="button">English</button>
                                                            </li>
                                                            <li class="nav-item">
                                                                <button class="nav-link py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-fr-{{ $sIndex }}" type="button">French</button>
                                                            </li>
                                                            <li class="nav-item">
                                                                <button class="nav-link py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-ar-{{ $sIndex }}" type="button">Arabic</button>
                                                            </li>
                                                        </ul>
                                                        <button type="button" class="btn btn-xs btn-outline-primary btn-translate-showcase" data-index="{{ $sIndex }}">
                                                            <i class="bi bi-translate me-1"></i> Auto Translate Card
                                                        </button>
                                                    </div>

                                                    <div class="tab-content">
                                                        {{-- Showcase EN --}}
                                                        <div class="tab-pane fade show active" id="sc-en-{{ $sIndex }}">
                                                            <div class="row g-2">
                                                                <div class="col-md-5">
                                                                    <label class="form-label small fw-bold mb-1">Title (EN)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][title][en]" class="form-control form-control-sm showcase-title-en"
                                                                           value="{{ $showcase->getTranslation('title', 'en', false) }}" placeholder="e.g. Laundry">
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <label class="form-label small fw-bold mb-1">Description (EN)</label>
                                                                    <textarea name="showcases[{{ $sIndex }}][description][en]" rows="2" class="form-control form-control-sm showcase-desc-en"
                                                                              placeholder="With Sharp Washing Machines, enjoy clothes that are ultra-clean...">{{ $showcase->getTranslation('description', 'en', false) }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Showcase FR --}}
                                                        <div class="tab-pane fade" id="sc-fr-{{ $sIndex }}">
                                                            <div class="row g-2">
                                                                <div class="col-md-5">
                                                                    <label class="form-label small fw-bold mb-1">Title (FR)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][title][fr]" class="form-control form-control-sm showcase-title-fr"
                                                                           value="{{ $showcase->getTranslation('title', 'fr', false) }}" placeholder="e.g. Buanderie">
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <label class="form-label small fw-bold mb-1">Description (FR)</label>
                                                                    <textarea name="showcases[{{ $sIndex }}][description][fr]" rows="2" class="form-control form-control-sm showcase-desc-fr"
                                                                              placeholder="Description en français...">{{ $showcase->getTranslation('description', 'fr', false) }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Showcase AR --}}
                                                        <div class="tab-pane fade" id="sc-ar-{{ $sIndex }}">
                                                            <div class="row g-2" dir="rtl">
                                                                <div class="col-md-5">
                                                                    <label class="form-label small fw-bold mb-1">Title (AR)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][title][ar]" class="form-control form-control-sm showcase-title-ar"
                                                                           value="{{ $showcase->getTranslation('title', 'ar', false) }}" placeholder="e.g. غسيل الملابس">
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <label class="form-label small fw-bold mb-1">Description (AR)</label>
                                                                    <textarea name="showcases[{{ $sIndex }}][description][ar]" rows="2" class="form-control form-control-sm showcase-desc-ar"
                                                                              placeholder="وصف بالعربية...">{{ $showcase->getTranslation('description', 'ar', false) }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted bg-light rounded border border-dashed" id="no-showcases-msg">
                                    <i class="bi bi-grid fs-1 text-secondary mb-2 d-block"></i>
                                    <p class="mb-1">No category showcases created yet.</p>
                                    <small>Click <strong>"Add Showcase Card"</strong> above to showcase product categories with lifestyle photography.</small>
                                </div>
                            @endforelse
                        </div>

                        {{-- Step 3 Navigation --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary prev-step" data-prev="step2">
                                <i class="fa fa-arrow-left me-1"></i> Previous: Hero Banners
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save me-1"></i> Save All Changes
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- TEMPLATES FOR ADDING DYNAMIC ROWS                        --}}
{{-- ======================================================== --}}

{{-- 1. Dynamic Banner Row Template --}}
<template id="banner-row-template">
    <div class="card border mb-3 banner-item shadow-2xs" data-index="__INDEX__">
        <input type="hidden" name="banners[__INDEX__][id]" value="">
        <input type="hidden" name="banners[__INDEX__][_delete]" value="0" class="banner-delete-flag">

        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-3 col-sm-4 text-center">
                    <div class="bg-dark rounded overflow-hidden p-1 d-flex align-items-center justify-content-center" style="height: 90px;">
                        <img src="{{ asset('images/no_image.jpg') }}"
                             alt="Banner Preview"
                             class="img-fluid rounded banner-preview-img"
                             style="max-height: 85px; width: 100%; object-fit: cover;">
                    </div>
                    <div class="mt-1 small text-success fw-semibold">New Slide</div>
                </div>

                <div class="col-md-5 col-sm-8">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Banner Image <span class="text-danger">*</span></label>
                        <input type="file" name="banners[__INDEX__][image]" class="form-control form-control-sm banner-file-input" accept="image/*" required>
                        <div class="form-text text-2xs text-muted">Recommended 1600 × 562 px.</div>
                    </div>
                    <div>
                        <label class="form-label small fw-bold mb-1">Alt Text / Title</label>
                        <input type="text" name="banners[__INDEX__][alt_text][en]" class="form-control form-control-sm"
                               placeholder="e.g. Summer Appliances Promotion">
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Sort Order</label>
                        <input type="number" name="banners[__INDEX__][sort_order]" class="form-control form-control-sm"
                               value="__SORT_ORDER__" min="0">
                    </div>
                    <div>
                        <label class="form-label small fw-bold mb-1">Status</label>
                        <select name="banners[__INDEX__][status]" class="form-select form-select-sm">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-1 col-sm-6 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-banner-btn" title="Remove Banner">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

{{-- 2. Dynamic Showcase Card Template --}}
<template id="showcase-card-template">
    <div class="card border mb-4 showcase-item shadow-2xs" data-index="__INDEX__">
        <input type="hidden" name="showcases[__INDEX__][id]" value="">
        <input type="hidden" name="showcases[__INDEX__][_delete]" value="0" class="showcase-delete-flag">

        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-pill">New Card</span>
                <span class="fw-bold showcase-header-title text-dark">New Category Showcase</span>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm remove-showcase-btn py-0 px-2" title="Remove Card">
                <i class="fa fa-trash me-1"></i> Remove
            </button>
        </div>

        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Lifestyle Image Column --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Lifestyle Photo</label>
                    <div class="bg-light rounded border p-1 text-center mb-2" style="height: 120px;">
                        <img src="{{ asset('images/no_image.jpg') }}"
                             alt="Showcase Preview"
                             class="img-fluid rounded showcase-preview-img"
                             style="max-height: 110px; width: 100%; object-fit: cover;">
                    </div>
                    <input type="file" name="showcases[__INDEX__][image]" class="form-control form-control-sm showcase-file-input" accept="image/*">
                    <div class="form-text text-2xs text-muted">Recommended: 800×600 or 1:1 square.</div>
                </div>

                {{-- Linking & Ordering Column --}}
                <div class="col-md-9">
                    <div class="row g-2 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold mb-1">Linked Category (for product filtering) <span class="text-danger">*</span></label>
                            <select name="showcases[__INDEX__][category_id]"
                                    class="showcase-category-select browser-default form-control form-control-sm"
                                    data-allow-clear="true"
                                    data-placeholder="Select category"
                                    id="category_id___INDEX__"
                                    required>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Linked Subcategory (optional)</label>
                            <select name="showcases[__INDEX__][sub_category_id]"
                                    class="showcase-subcategory-select browser-default form-control form-control-sm"
                                    data-allow-clear="true"
                                    data-placeholder="Select sub category"
                                    id="sub_category_id___INDEX__"
                                    disabled>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Sort Order</label>
                            <input type="number" name="showcases[__INDEX__][sort_order]" class="form-control form-control-sm"
                                   value="__SORT_ORDER__" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small fw-bold mb-1">Status</label>
                            <select name="showcases[__INDEX__][status]" class="form-select form-select-sm">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    {{-- Multilingual Tabs for this Showcase --}}
                    <div class="border rounded p-2 bg-light/50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <ul class="nav nav-tabs nav-tabs-sm" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-en-__INDEX__" type="button">English</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-fr-__INDEX__" type="button">French</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-1 px-2.5 small" data-bs-toggle="tab" data-bs-target="#sc-ar-__INDEX__" type="button">Arabic</button>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-translate-showcase" data-index="__INDEX__">
                                <i class="bi bi-translate me-1"></i> Auto Translate Card
                            </button>
                        </div>

                        <div class="tab-content">
                            {{-- Showcase EN --}}
                            <div class="tab-pane fade show active" id="sc-en-__INDEX__">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold mb-1">Title (EN)</label>
                                        <input type="text" name="showcases[__INDEX__][title][en]" class="form-control form-control-sm showcase-title-en"
                                               placeholder="e.g. Laundry">
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label small fw-bold mb-1">Description (EN)</label>
                                        <textarea name="showcases[__INDEX__][description][en]" rows="2" class="form-control form-control-sm showcase-desc-en"
                                                  placeholder="With Sharp Washing Machines, enjoy clothes that are ultra-clean..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Showcase FR --}}
                            <div class="tab-pane fade" id="sc-fr-__INDEX__">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold mb-1">Title (FR)</label>
                                        <input type="text" name="showcases[__INDEX__][title][fr]" class="form-control form-control-sm showcase-title-fr"
                                               placeholder="e.g. Buanderie">
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label small fw-bold mb-1">Description (FR)</label>
                                        <textarea name="showcases[__INDEX__][description][fr]" rows="2" class="form-control form-control-sm showcase-desc-fr"
                                                  placeholder="Description en français..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Showcase AR --}}
                            <div class="tab-pane fade" id="sc-ar-__INDEX__">
                                <div class="row g-2" dir="rtl">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold mb-1">Title (AR)</label>
                                        <input type="text" name="showcases[__INDEX__][title][ar]" class="form-control form-control-sm showcase-title-ar"
                                               placeholder="e.g. غسيل الملابس">
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label small fw-bold mb-1">Description (AR)</label>
                                        <textarea name="showcases[__INDEX__][description][ar]" rows="2" class="form-control form-control-sm showcase-desc-ar"
                                                  placeholder="وصف بالعربية..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
    <style>
        .select2-container--disabled {
            pointer-events: none !important;
            opacity: 0.65 !important;
            cursor: not-allowed !important;
        }
        .select2-container--disabled .select2-selection {
            background-color: #f8f9fa !important;
            cursor: not-allowed !important;
            border-color: #dee2e6 !important;
        }
    </style>
    {{-- Common image preview logic for brand logo --}}
    <script src="{{ asset('admin_assets/js/cat/subcat_image-preview.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Logo preview
            if (typeof setupImagePreview === 'function') {
                setupImagePreview('brand-icon', 'iconPreview', 'iconFeedback');
            }

            // 2. Wizard Tab Navigation (Next / Prev buttons)
            document.querySelectorAll('.next-step').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const nextId = this.getAttribute('data-next');
                    const tabTrigger = document.querySelector(`button[data-bs-target="#${nextId}"]`);
                    if (tabTrigger) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(tabTrigger);
                        tabInstance.show();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });

            document.querySelectorAll('.prev-step').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const prevId = this.getAttribute('data-prev');
                    const tabTrigger = document.querySelector(`button[data-bs-target="#${prevId}"]`);
                    if (tabTrigger) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(tabTrigger);
                        tabInstance.show();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });

            // 3. Dynamic Banners Repeater
            let bannerCounter = {{ $brand->banners->count() }};
            const bannersContainer = document.getElementById('banners-container');
            const addBannerBtn = document.getElementById('add-banner-btn');
            const noBannersMsg = document.getElementById('no-banners-msg');
            const bannerBadge = document.getElementById('banner-count-badge');

            function updateBannerBadge() {
                const activeCount = bannersContainer.querySelectorAll('.banner-item:not([style*="display: none"])').length;
                if (bannerBadge) bannerBadge.textContent = activeCount;
            }

            if (addBannerBtn) {
                addBannerBtn.addEventListener('click', function() {
                    if (noBannersMsg) noBannersMsg.style.display = 'none';

                    const template = document.getElementById('banner-row-template').innerHTML;
                    const newHtml = template
                        .replace(/__INDEX__/g, bannerCounter)
                        .replace(/__SORT_ORDER__/g, bannerCounter);

                    bannersContainer.insertAdjacentHTML('beforeend', newHtml);
                    bannerCounter++;
                    updateBannerBadge();
                });
            }

            // Banner image preview and deletion delegation
            bannersContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('banner-file-input')) {
                    const file = e.target.files[0];
                    if (file) {
                        const card = e.target.closest('.banner-item');
                        const img = card.querySelector('.banner-preview-img');
                        if (img) {
                            img.src = URL.createObjectURL(file);
                        }
                    }
                }
            });

            bannersContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-banner-btn');
                if (removeBtn) {
                    e.preventDefault();
                    const card = removeBtn.closest('.banner-item');
                    const deleteFlag = card.querySelector('.banner-delete-flag');
                    const bannerId = card.querySelector('input[name*="[id]"]').value;

                    if (bannerId) {
                        // Mark for deletion in backend and hide
                        deleteFlag.value = '1';
                        card.style.display = 'none';
                    } else {
                        // Directly remove newly added unsaved row
                        card.remove();
                    }
                    updateBannerBadge();
                }
            });

            // 4. Dynamic Showcases Repeater & Category/Subcategory Select2 Dependency
            let showcaseCounter = {{ $brand->categoryShowcases->count() }};
            const showcasesContainer = document.getElementById('showcases-container');
            const addShowcaseBtn = document.getElementById('add-showcase-btn');
            const noShowcasesMsg = document.getElementById('no-showcases-msg');
            const showcaseBadge = document.getElementById('showcase-count-badge');

            function updateShowcaseBadge() {
                const activeCount = showcasesContainer.querySelectorAll('.showcase-item:not([style*="display: none"])').length;
                if (showcaseBadge) showcaseBadge.textContent = activeCount;
            }

            // Setup isolated Category & Subcategory Select2 with strict parent dependency
            function setupShowcaseCategorySelects(card) {
                const $card = $(card);
                const $catSelect = $card.find('.showcase-category-select');
                const $subSelect = $card.find('.showcase-subcategory-select');

                if (!$catSelect.length || !$subSelect.length) return;

                // 1. Initialize Category Select2
                if (!$catSelect.data('select2')) {
                    $catSelect.select2({
                        width: '100%',
                        placeholder: 'Select category',
                        allowClear: true,
                        ajax: {
                            url: '{{ route("admin.lists.categories") }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return { q: params.term || '' };
                            },
                            processResults: function(data) {
                                return { results: (data && data.results) ? data.results : [] };
                            }
                        }
                    });
                }

                // 2. Initialize Subcategory Select2
                if (!$subSelect.data('select2')) {
                    $subSelect.select2({
                        width: '100%',
                        placeholder: 'Select sub category',
                        allowClear: true,
                        ajax: {
                            url: '{{ route("admin.lists.sub-categories-by-category") }}',
                            dataType: 'json',
                            delay: 250,
                            transport: function(params, success, failure) {
                                const currentCatId = $catSelect.val();
                                if (!currentCatId) {
                                    return success({ results: [] });
                                }
                                return $.ajax(params).then(success).fail(failure);
                            },
                            data: function(params) {
                                return {
                                    q: params.term || '',
                                    category_id: $catSelect.val() || ''
                                };
                            },
                            processResults: function(data) {
                                return { results: (data && data.results) ? data.results : [] };
                            }
                        }
                    });
                }

                // 3. Sync disabled and clear state based on parent Category value
                function syncSubCategoryState(forceClear = false) {
                    const catId = $catSelect.val();
                    const hasCategory = Boolean(catId);

                    $subSelect.prop('disabled', !hasCategory);

                    if (forceClear || !hasCategory) {
                        $subSelect.val(null).trigger('change');
                    }

                    const $container = $subSelect.next('.select2-container');
                    if ($container.length) {
                        if (hasCategory) {
                            $container.removeClass('select2-container--disabled');
                            $container.find('.select2-selection').attr('aria-disabled', 'false').removeAttr('tabindex');
                        } else {
                            $container.addClass('select2-container--disabled');
                            $container.find('.select2-selection').attr('aria-disabled', 'true').attr('tabindex', '-1');
                        }
                    }
                }

                syncSubCategoryState(false);

                // When category changes, reset and re-evaluate subcategory
                $catSelect.off('change.cat_sync').on('change.cat_sync', function() {
                    syncSubCategoryState(true);
                });
            }

            // Initialize for all existing showcase cards on page load
            document.querySelectorAll('.showcase-item').forEach(card => {
                setupShowcaseCategorySelects(card);
            });

            if (addShowcaseBtn) {
                addShowcaseBtn.addEventListener('click', function() {
                    if (noShowcasesMsg) noShowcasesMsg.style.display = 'none';

                    const template = document.getElementById('showcase-card-template').innerHTML;
                    const newHtml = template
                        .replace(/__INDEX__/g, showcaseCounter)
                        .replace(/__SORT_ORDER__/g, showcaseCounter);

                    showcasesContainer.insertAdjacentHTML('beforeend', newHtml);
                    const newCard = showcasesContainer.lastElementChild;
                    showcaseCounter++;
                    updateShowcaseBadge();

                    // Initialize category & dependent subcategory for new card
                    setupShowcaseCategorySelects(newCard);
                });
            }

            // Showcase image preview, title sync, and deletion delegation
            showcasesContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('showcase-file-input')) {
                    const file = e.target.files[0];
                    if (file) {
                        const card = e.target.closest('.showcase-item');
                        const img = card.querySelector('.showcase-preview-img');
                        if (img) {
                            img.src = URL.createObjectURL(file);
                        }
                    }
                }
            });

            // Dynamic header title update when typing in EN title
            showcasesContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('showcase-title-en')) {
                    const card = e.target.closest('.showcase-item');
                    const headerTitle = card.querySelector('.showcase-header-title');
                    if (headerTitle) {
                        headerTitle.textContent = e.target.value.trim() || 'New Category Showcase';
                    }
                }
            });

            showcasesContainer.addEventListener('click', function(e) {
                // Delete button
                const removeBtn = e.target.closest('.remove-showcase-btn');
                if (removeBtn) {
                    e.preventDefault();
                    const card = removeBtn.closest('.showcase-item');
                    const deleteFlag = card.querySelector('.showcase-delete-flag');
                    const scId = card.querySelector('input[name*="[id]"]').value;

                    if (scId) {
                        deleteFlag.value = '1';
                        card.style.display = 'none';
                    } else {
                        card.remove();
                    }
                    updateShowcaseBadge();
                    return;
                }

                // Showcase Auto-Translate Button
                const transBtn = e.target.closest('.btn-translate-showcase');
                if (transBtn) {
                    e.preventDefault();
                    const card = transBtn.closest('.showcase-item');
                    const titleEn = card.querySelector('.showcase-title-en');
                    const descEn = card.querySelector('.showcase-desc-en');

                    const titleVal = titleEn ? titleEn.value.trim() : '';
                    const descVal = descEn ? descEn.value.trim() : '';

                    if (!titleVal && !descVal) {
                        alert('Please fill in the English Title or Description before translating.');
                        return;
                    }

                    const originalBtnHtml = transBtn.innerHTML;
                    transBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Translating...';
                    transBtn.disabled = true;

                    const payload = {};
                    if (titleVal) payload.title = titleVal;
                    if (descVal) payload.description = descVal;

                    axios.post('{{ route("admin.translate.batch") }}', { fields: payload })
                        .then(function(res) {
                            if (res.data && res.data.success && res.data.translations) {
                                const trans = res.data.translations;
                                if (trans.title) {
                                    const titleFr = card.querySelector('.showcase-title-fr');
                                    const titleAr = card.querySelector('.showcase-title-ar');
                                    if (titleFr && trans.title.fr) titleFr.value = trans.title.fr;
                                    if (titleAr && trans.title.ar) titleAr.value = trans.title.ar;
                                }
                                if (trans.description) {
                                    const descFr = card.querySelector('.showcase-desc-fr');
                                    const descAr = card.querySelector('.showcase-desc-ar');
                                    if (descFr && trans.description.fr) descFr.value = trans.description.fr;
                                    if (descAr && trans.description.ar) descAr.value = trans.description.ar;
                                }
                                if (typeof showSuccessToast === 'function') {
                                    showSuccessToast();
                                }
                            }
                        })
                        .catch(function(err) {
                            console.error('Translation error:', err);
                            alert('Translation failed. Please try again.');
                        })
                        .finally(function() {
                            transBtn.innerHTML = originalBtnHtml;
                            transBtn.disabled = false;
                        });
                }
            });
        });
    </script>
@endpush
