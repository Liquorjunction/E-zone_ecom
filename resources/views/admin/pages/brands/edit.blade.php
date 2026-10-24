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
                    <i class="bi bi-arrow-left me-1"></i> Back to Brands
                </a>
                <button type="submit" form="brand-wizard-form" class="btn btn-sm btn-primary">
                    <i class="bi bi-save me-1"></i> Save All Changes
                </button>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.brands.update') }}" method="POST" enctype="multipart/form-data" id="brand-wizard-form" novalidate>
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
                                                <input type="text" name="name[en]" id="name_en" class="form-control @if($errors->has('name') || $errors->has('name.en')) is-invalid @endif"
                                                       value="{{ old('name.en', $brand->getTranslation('name', 'en', false)) }}"
                                                       placeholder="e.g. Sharp" required>
                                                @if($errors->has('name.en'))
                                                    <div class="invalid-feedback d-block">{{ $errors->first('name.en') }}</div>
                                                @elseif($errors->has('name'))
                                                    <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
                                                @endif
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
                                        <input id="brand-icon" type="file" name="image" class="form-control form-control-sm @error('image') is-invalid @enderror" accept="image/*">
                                        <div class="form-text mt-1 text-xs">Accepted: jpeg, png, webp, avif. Max 2MB.</div>
                                        <div class="form-text mt-0 text-xs text-muted">Leave empty to keep current logo.</div>
                                        <div id="iconFeedback" class="invalid-feedback @error('image') d-block @enderror" style="@error('image') display:block; @else display:none; @enderror">
                                            @error('image') {{ $message }} @enderror
                                        </div>
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
                                Next: Hero Banners <i class="bi bi-arrow-right ms-1"></i>
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
                            <div>
                                <h5 class="mb-0 fw-bold">Promotional Banner Slides <span class="text-danger">*</span></h5>
                                <p class="text-muted small mb-0">Minimum 1 banner slide is mandatory for the brand page hero carousel.</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-success" id="add-banner-btn">
                                <i class="bi bi-plus-circle me-1"></i> Add Banner Slide
                            </button>
                        </div>

                        <div id="banner-global-error" class="alert alert-danger py-2 px-3 mb-3 small" style="{{ $errors->has('banners') ? 'display: block;' : 'display: none;' }}">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <span class="error-text">{{ $errors->first('banners') ?: 'At least 1 hero banner slide is mandatory.' }}</span>
                        </div>

                        <div id="banners-container" class="space-y-3">
                            @forelse($brand->banners as $bIndex => $banner)
                                <div class="card border mb-3 banner-item shadow-2xs" data-index="{{ $bIndex }}">
                                    <input type="hidden" name="banners[{{ $bIndex }}][id]" value="{{ $banner->id }}">
                                    <input type="hidden" name="banners[{{ $bIndex }}][_delete]" value="0" class="banner-delete-flag">

                                    {{-- Card Header --}}
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary rounded-pill">Slide #{{ $loop->iteration }}</span>
                                            <span class="fw-bold banner-header-title text-dark">
                                                {{ $banner->getTranslation('alt_text', 'en', false) ?: 'Banner Slide #' . $loop->iteration }}
                                            </span>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-banner-btn py-0 px-2" title="Remove Banner">
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </div>

                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            {{-- Banner Preview --}}
                                            <div class="col-md-3 text-center">
                                                <label class="form-label small fw-bold mb-1">Banner Preview</label>
                                                <div class="bg-light rounded border p-2 d-flex align-items-center justify-content-center overflow-hidden mb-1 banner-preview-container" style="height: 95px;">
                                                    <div class="banner-empty-preview text-center text-muted {{ $banner->image ? 'd-none' : '' }}">
                                                        <i class="bi bi-image fs-2 text-secondary opacity-50 d-block"></i>
                                                        <span class="text-2xs text-secondary">No image selected</span>
                                                    </div>
                                                    <img src="{{ $banner->image ? asset('storage/' . $banner->image) : '' }}"
                                                         alt="Banner Preview"
                                                         class="img-fluid rounded banner-preview-img {{ $banner->image ? '' : 'd-none' }}"
                                                         data-has-existing="{{ $banner->image ? '1' : '0' }}"
                                                         style="max-height: 85px; max-width: 100%; width: auto; height: auto; object-fit: contain;">
                                                </div>
                                                <div class="form-text text-2xs text-muted">Recommended: 1600 × 562 px</div>
                                            </div>

                                            {{-- Inputs --}}
                                            <div class="col-md-9">
                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold mb-1">Replace Image (optional)</label>
                                                        <input type="file" name="banners[{{ $bIndex }}][image]" class="form-control form-control-sm banner-file-input @error("banners.{$bIndex}.image") is-invalid @enderror" accept="image/*">
                                                        @error("banners.{$bIndex}.image")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text text-2xs text-muted">Leave empty to keep existing banner.</div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-bold mb-1">Sort Order</label>
                                                        <input type="number" name="banners[{{ $bIndex }}][sort_order]" class="form-control form-control-sm"
                                                               value="{{ $banner->sort_order ?? $bIndex }}" min="0">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-bold mb-1">Status</label>
                                                        <select name="banners[{{ $bIndex }}][status]" class="form-select form-select-sm">
                                                            <option value="active" {{ $banner->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $banner->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="form-label small fw-bold mb-1">Alt Text / Title</label>
                                                    <input type="text" name="banners[{{ $bIndex }}][alt_text][en]" class="form-control form-control-sm banner-alt-text"
                                                           value="{{ $banner->getTranslation('alt_text', 'en', false) }}" placeholder="e.g. Sharp Home Appliances Range">
                                                </div>
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
                                <i class="bi bi-arrow-left me-1"></i> Previous: Brand Info
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="step3">
                                Next: Category Showcases <i class="bi bi-arrow-right ms-1"></i>
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
                                Add custom Button Text and Button URL (such as an anchor <code>#products-catalog</code> or a category page) to direct visitors anywhere you want.
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
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </div>

                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            {{-- Lifestyle Image Column --}}
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold mb-1">Lifestyle Photo <span class="text-danger">*</span></label>
                                                <div class="bg-light rounded border p-2 d-flex align-items-center justify-content-center overflow-hidden mb-2" style="height: 120px;">
                                                    <img src="{{ $showcase->image ? asset('storage/' . $showcase->image) : asset('images/no_image.jpg') }}"
                                                         alt="Showcase Preview"
                                                         class="img-fluid rounded showcase-preview-img"
                                                         style="max-height: 110px; max-width: 100%; width: auto; height: auto; object-fit: contain;{{ $showcase->image ? '' : ' opacity: 0.65;' }}">
                                                </div>
                                                <input type="file" name="showcases[{{ $sIndex }}][image]"
                                                       class="form-control form-control-sm showcase-file-input @error("showcases.{$sIndex}.image") is-invalid @enderror"
                                                       accept="image/*">
                                                @error("showcases.{$sIndex}.image")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text text-2xs text-muted">Recommended: 800×600 or 1:1 square.</div>
                                            </div>

                                            {{-- Target Link & Ordering Column --}}
                                            <div class="col-md-9">
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-7">
                                                        <label class="form-label small fw-bold mb-1">Button URL (Target Link)</label>
                                                        <input type="text" name="showcases[{{ $sIndex }}][button_url]"
                                                               class="form-control form-control-sm showcase-btn-url @error("showcases.{$sIndex}.button_url") is-invalid @enderror"
                                                               value="{{ old("showcases.{$sIndex}.button_url", $showcase->button_url) }}" placeholder="e.g. #products-catalog or /category/refrigerators">
                                                        @error("showcases.{$sIndex}.button_url")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text text-2xs text-muted">Page link or anchor ID for the card's CTA button. Mandatory if Button Text is entered.</div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-bold mb-1">Sort Order</label>
                                                        <input type="number" name="showcases[{{ $sIndex }}][sort_order]" class="form-control form-control-sm"
                                                               value="{{ $showcase->sort_order ?? $sIndex }}" min="0">
                                                    </div>
                                                    <div class="col-md-2">
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
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-bold mb-1">Card Name / Title (EN) <span class="text-danger">*</span></label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][title][en]"
                                                                           class="form-control form-control-sm showcase-title-en @error("showcases.{$sIndex}.title.en") is-invalid @enderror"
                                                                           value="{{ old("showcases.{$sIndex}.title.en", $showcase->getTranslation('title', 'en', false)) }}"
                                                                           placeholder="e.g. Laundry" required>
                                                                    @error("showcases.{$sIndex}.title.en")
                                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-bold mb-1">Button Text (EN)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][button_text][en]" class="form-control form-control-sm showcase-btn-text-en"
                                                                           value="{{ $showcase->getTranslation('button_text', 'en', false) }}" placeholder="e.g. View Laundry">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label small fw-bold mb-1">Description (EN)</label>
                                                                    <textarea name="showcases[{{ $sIndex }}][description][en]" rows="2" class="form-control form-control-sm showcase-desc-en"
                                                                              placeholder="With Sharp Washing Machines, enjoy clothes that are ultra-clean...">{{ $showcase->getTranslation('description', 'en', false) }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Showcase FR --}}
                                                        <div class="tab-pane fade" id="sc-fr-{{ $sIndex }}">
                                                            <div class="row g-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-bold mb-1">Title (FR)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][title][fr]" class="form-control form-control-sm showcase-title-fr"
                                                                           value="{{ $showcase->getTranslation('title', 'fr', false) }}" placeholder="e.g. Buanderie">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-bold mb-1">Button Text (FR)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][button_text][fr]" class="form-control form-control-sm showcase-btn-text-fr"
                                                                           value="{{ $showcase->getTranslation('button_text', 'fr', false) }}" placeholder="e.g. Voir la buanderie">
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label small fw-bold mb-1">Description (FR)</label>
                                                                    <textarea name="showcases[{{ $sIndex }}][description][fr]" rows="2" class="form-control form-control-sm showcase-desc-fr"
                                                                              placeholder="Description en français...">{{ $showcase->getTranslation('description', 'fr', false) }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Showcase AR --}}
                                                        <div class="tab-pane fade" id="sc-ar-{{ $sIndex }}">
                                                            <div class="row g-2" dir="rtl">
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-bold mb-1">Title (AR)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][title][ar]" class="form-control form-control-sm showcase-title-ar"
                                                                           value="{{ $showcase->getTranslation('title', 'ar', false) }}" placeholder="e.g. غسيل الملابس">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-bold mb-1">Button Text (AR)</label>
                                                                    <input type="text" name="showcases[{{ $sIndex }}][button_text][ar]" class="form-control form-control-sm showcase-btn-text-ar"
                                                                           value="{{ $showcase->getTranslation('button_text', 'ar', false) }}" placeholder="e.g. عرض منتجات الغسيل">
                                                                </div>
                                                                <div class="col-12">
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
                                <i class="bi bi-arrow-left me-1"></i> Previous: Hero Banners
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-1"></i> Save All Changes
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

        {{-- Card Header --}}
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-pill">New Slide</span>
                <span class="fw-bold banner-header-title text-dark">New Banner Slide</span>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm remove-banner-btn py-0 px-2" title="Remove Banner">
                <i class="bi bi-trash me-1"></i> Remove
            </button>
        </div>

        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Banner Preview Column --}}
                <div class="col-md-3 text-center">
                    <label class="form-label small fw-bold mb-1">Banner Preview</label>
                    <div class="bg-light rounded border p-2 d-flex align-items-center justify-content-center overflow-hidden mb-1 banner-preview-container" style="height: 95px;">
                        <div class="banner-empty-preview text-center text-muted">
                            <i class="bi bi-image fs-2 text-secondary opacity-50 d-block"></i>
                            <span class="text-2xs text-secondary">No image selected</span>
                        </div>
                        <img src=""
                             alt="Banner Preview"
                             class="img-fluid rounded banner-preview-img d-none"
                             style="max-height: 85px; max-width: 100%; width: auto; height: auto; object-fit: contain;">
                    </div>
                    <div class="form-text text-2xs text-muted">Recommended: 1600 × 562 px</div>
                </div>

                {{-- Fields Column --}}
                <div class="col-md-9">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-1">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" name="banners[__INDEX__][image]" class="form-control form-control-sm banner-file-input" accept="image/*" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">Sort Order</label>
                            <input type="number" name="banners[__INDEX__][sort_order]" class="form-control form-control-sm"
                                   value="__SORT_ORDER__" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">Status</label>
                            <select name="banners[__INDEX__][status]" class="form-select form-select-sm">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label small fw-bold mb-1">Alt Text / Title</label>
                        <input type="text" name="banners[__INDEX__][alt_text][en]" class="form-control form-control-sm banner-alt-text"
                               placeholder="e.g. Summer Appliances Promotion">
                    </div>
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
                <i class="bi bi-trash me-1"></i> Remove
            </button>
        </div>

        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Lifestyle Image Column --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Lifestyle Photo <span class="text-danger">*</span></label>
                    <div class="bg-light rounded border p-2 d-flex align-items-center justify-content-center overflow-hidden mb-2" style="height: 120px;">
                        <img src="{{ asset('images/no_image.jpg') }}"
                             alt="Showcase Preview"
                             class="img-fluid rounded showcase-preview-img"
                             style="max-height: 110px; max-width: 100%; width: auto; height: auto; object-fit: contain; opacity: 0.65;">
                    </div>
                    <input type="file" name="showcases[__INDEX__][image]" class="form-control form-control-sm showcase-file-input" accept="image/*" required>
                    <div class="form-text text-2xs text-muted">Recommended: 800×600 or 1:1 square.</div>
                </div>

                {{-- Linking & Ordering Column --}}
                <div class="col-md-9">
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold mb-1">Button URL (Target Link)</label>
                            <input type="text" name="showcases[__INDEX__][button_url]" class="form-control form-control-sm showcase-btn-url"
                                   placeholder="e.g. #products-catalog or /category/refrigerators">
                            <div class="form-text text-2xs text-muted">Page link or anchor ID for the card's CTA button. Mandatory if Button Text is entered.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">Sort Order</label>
                            <input type="number" name="showcases[__INDEX__][sort_order]" class="form-control form-control-sm"
                                   value="__SORT_ORDER__" min="0">
                        </div>
                        <div class="col-md-2">
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
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Card Name / Title (EN) <span class="text-danger">*</span></label>
                                        <input type="text" name="showcases[__INDEX__][title][en]" class="form-control form-control-sm showcase-title-en"
                                               placeholder="e.g. Laundry" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Button Text (EN)</label>
                                        <input type="text" name="showcases[__INDEX__][button_text][en]" class="form-control form-control-sm showcase-btn-text-en"
                                               placeholder="e.g. View Laundry">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold mb-1">Description (EN)</label>
                                        <textarea name="showcases[__INDEX__][description][en]" rows="2" class="form-control form-control-sm showcase-desc-en"
                                                  placeholder="With Sharp Washing Machines, enjoy clothes that are ultra-clean..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Showcase FR --}}
                            <div class="tab-pane fade" id="sc-fr-__INDEX__">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Title (FR)</label>
                                        <input type="text" name="showcases[__INDEX__][title][fr]" class="form-control form-control-sm showcase-title-fr"
                                               placeholder="e.g. Buanderie">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Button Text (FR)</label>
                                        <input type="text" name="showcases[__INDEX__][button_text][fr]" class="form-control form-control-sm showcase-btn-text-fr"
                                               placeholder="e.g. Voir la buanderie">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold mb-1">Description (FR)</label>
                                        <textarea name="showcases[__INDEX__][description][fr]" rows="2" class="form-control form-control-sm showcase-desc-fr"
                                                  placeholder="Description en français..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Showcase AR --}}
                            <div class="tab-pane fade" id="sc-ar-__INDEX__">
                                <div class="row g-2" dir="rtl">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Title (AR)</label>
                                        <input type="text" name="showcases[__INDEX__][title][ar]" class="form-control form-control-sm showcase-title-ar"
                                               placeholder="e.g. غسيل الملابس">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold mb-1">Button Text (AR)</label>
                                        <input type="text" name="showcases[__INDEX__][button_text][ar]" class="form-control form-control-sm showcase-btn-text-ar"
                                               placeholder="e.g. عرض منتجات الغسيل">
                                    </div>
                                    <div class="col-12">
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

            // -------------------------------------------------------------
            // Helper: Show/Clear Field-Level Validation Errors
            // -------------------------------------------------------------
            function setFieldError(inputElement, errorMessage) {
                if (!inputElement) return;
                inputElement.classList.add('is-invalid');

                if (inputElement.id === 'brand-icon') {
                    const iconFeedback = document.getElementById('iconFeedback');
                    if (iconFeedback) {
                        iconFeedback.textContent = errorMessage;
                        iconFeedback.classList.add('d-block');
                        iconFeedback.style.display = 'block';
                    }
                    return;
                }

                const container = inputElement.closest('.mb-2, .mb-3, .form-group, .col-md-5, .col-md-6, .col-md-3, .col-12') || inputElement.parentNode;
                let feedback = container.querySelector('.invalid-feedback.client-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback d-block client-feedback';
                    if (inputElement.nextElementSibling && inputElement.nextElementSibling.classList.contains('form-text')) {
                        inputElement.parentNode.insertBefore(feedback, inputElement.nextElementSibling);
                    } else if (inputElement.nextSibling) {
                        inputElement.parentNode.insertBefore(feedback, inputElement.nextSibling);
                    } else {
                        inputElement.parentNode.appendChild(feedback);
                    }
                }
                feedback.textContent = errorMessage;
                feedback.style.display = 'block';
            }

            function clearFieldError(inputElement) {
                if (!inputElement) return;
                inputElement.classList.remove('is-invalid');

                if (inputElement.id === 'brand-icon') {
                    const iconFeedback = document.getElementById('iconFeedback');
                    if (iconFeedback) {
                        iconFeedback.textContent = '';
                        iconFeedback.classList.remove('d-block');
                        iconFeedback.style.display = 'none';
                    }
                    return;
                }

                const container = inputElement.closest('.mb-2, .mb-3, .form-group, .col-md-5, .col-md-6, .col-md-3, .col-12') || inputElement.parentNode;
                if (container) {
                    const clientFeedback = container.querySelector('.invalid-feedback.client-feedback');
                    if (clientFeedback) clientFeedback.remove();
                    const serverFeedback = container.querySelector('.invalid-feedback:not(.client-feedback)');
                    if (serverFeedback) serverFeedback.style.display = 'none';
                }
            }

            // -------------------------------------------------------------
            // Step-by-Step Validators
            // -------------------------------------------------------------
            function validateStep(stepNum) {
                if (stepNum === 1) {
                    let isValid = true;
                    let firstInvalid = null;

                    // 1. Brand Name (EN)
                    const nameEn = document.getElementById('name_en');
                    if (nameEn) {
                        if (!nameEn.value.trim()) {
                            isValid = false;
                            setFieldError(nameEn, 'Brand Name (English) is required.');
                            const enTab = document.getElementById('en-tab');
                            if (enTab) {
                                bootstrap.Tab.getOrCreateInstance(enTab).show();
                            }
                            if (!firstInvalid) firstInvalid = nameEn;
                        } else {
                            clearFieldError(nameEn);
                        }
                    }

                    // 2. Brand Logo (image)
                    const brandIcon = document.getElementById('brand-icon');
                    const iconPreview = document.getElementById('iconPreview');
                    const hasExistingLogo = iconPreview && iconPreview.src && !iconPreview.src.includes('no_image.jpg');
                    const hasSelectedFile = brandIcon && brandIcon.files && brandIcon.files.length > 0;

                    if (brandIcon) {
                        if (!hasSelectedFile && !hasExistingLogo) {
                            isValid = false;
                            setFieldError(brandIcon, 'Brand logo image is required.');
                            if (!firstInvalid) firstInvalid = brandIcon;
                        } else if (hasSelectedFile) {
                            const file = brandIcon.files[0];
                            const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/bmp', 'image/x-icon', 'image/tiff', 'image/avif'];
                            const maxSize = 2 * 1024 * 1024; // 2MB

                            if (!allowed.includes(file.type)) {
                                isValid = false;
                                setFieldError(brandIcon, 'Invalid file type. Please upload a valid image (JPEG, PNG, WEBP, AVIF).');
                                if (!firstInvalid) firstInvalid = brandIcon;
                            } else if (file.size > maxSize) {
                                isValid = false;
                                setFieldError(brandIcon, 'File too large. Max allowed size is 2MB.');
                                if (!firstInvalid) firstInvalid = brandIcon;
                            } else {
                                clearFieldError(brandIcon);
                            }
                        } else {
                            clearFieldError(brandIcon);
                        }
                    }

                    if (!isValid && firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                    return true;
                }

                if (stepNum === 2) {
                    let isValid = true;
                    let firstInvalid = null;
                    let activeBannerCount = 0;

                    const bannerItems = document.querySelectorAll('.banner-item');
                    bannerItems.forEach(item => {
                        const deleteFlag = item.querySelector('.banner-delete-flag');
                        if ((deleteFlag && deleteFlag.value === '1') || item.style.display === 'none') {
                            return;
                        }
                        activeBannerCount++;

                        const fileInput = item.querySelector('.banner-file-input');
                        const previewImg = item.querySelector('.banner-preview-img');
                        const hasExisting = previewImg && previewImg.dataset.hasExisting === '1' && previewImg.getAttribute('src');
                        const hasSelected = fileInput && fileInput.files && fileInput.files.length > 0;

                        if (fileInput) {
                            if (!hasSelected && !hasExisting) {
                                isValid = false;
                                setFieldError(fileInput, 'Banner image is required for this slide.');
                                if (!firstInvalid) firstInvalid = fileInput;
                            } else if (hasSelected) {
                                const file = fileInput.files[0];
                                const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/bmp', 'image/x-icon', 'image/tiff', 'image/avif'];
                                const maxSize = 5 * 1024 * 1024; // 5MB

                                if (!allowed.includes(file.type)) {
                                    isValid = false;
                                    setFieldError(fileInput, 'Invalid file type. Please upload an image.');
                                    if (!firstInvalid) firstInvalid = fileInput;
                                } else if (file.size > maxSize) {
                                    isValid = false;
                                    setFieldError(fileInput, 'Banner image too large. Max allowed size is 5MB.');
                                    if (!firstInvalid) firstInvalid = fileInput;
                                } else {
                                    clearFieldError(fileInput);
                                }
                            } else {
                                clearFieldError(fileInput);
                            }
                        }
                    });

                    const bannerGlobalError = document.getElementById('banner-global-error');
                    if (activeBannerCount < 1) {
                        isValid = false;
                        if (bannerGlobalError) {
                            const errSpan = bannerGlobalError.querySelector('.error-text');
                            if (errSpan) errSpan.textContent = 'At least 1 hero banner slide is mandatory. Please add a banner slide.';
                            bannerGlobalError.style.display = 'block';
                            bannerGlobalError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return false;
                    } else {
                        if (bannerGlobalError) {
                            bannerGlobalError.style.display = 'none';
                        }
                    }

                    if (!isValid && firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                    return true;
                }

                if (stepNum === 3) {
                    let isValid = true;
                    let firstInvalid = null;

                    const showcaseItems = document.querySelectorAll('.showcase-item');
                    showcaseItems.forEach(card => {
                        const deleteFlag = card.querySelector('.showcase-delete-flag');
                        if ((deleteFlag && deleteFlag.value === '1') || card.style.display === 'none') {
                            return;
                        }

                        // 1. Card Name / Title (EN)
                        const titleInput = card.querySelector('.showcase-title-en');
                        if (titleInput) {
                            if (!titleInput.value.trim()) {
                                isValid = false;
                                setFieldError(titleInput, 'Card Name (English) is required.');
                                const cardIndex = card.getAttribute('data-index');
                                const enTabBtn = card.querySelector(`button[data-bs-target="#sc-en-${cardIndex}"]`);
                                if (enTabBtn) {
                                    bootstrap.Tab.getOrCreateInstance(enTabBtn).show();
                                }
                                if (!firstInvalid) firstInvalid = titleInput;
                            } else {
                                clearFieldError(titleInput);
                            }
                        }

                        // 2. Lifestyle Photo
                        const fileInput = card.querySelector('.showcase-file-input');
                        const previewImg = card.querySelector('.showcase-preview-img');
                        const hasExisting = previewImg && previewImg.src && !previewImg.src.includes('no_image.jpg');
                        const hasSelected = fileInput && fileInput.files && fileInput.files.length > 0;

                        if (fileInput) {
                            if (!hasSelected && !hasExisting) {
                                isValid = false;
                                setFieldError(fileInput, 'Lifestyle photo is required.');
                                if (!firstInvalid) firstInvalid = fileInput;
                            } else if (hasSelected) {
                                const file = fileInput.files[0];
                                const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/bmp', 'image/x-icon', 'image/tiff', 'image/avif'];
                                const maxSize = 5 * 1024 * 1024; // 5MB

                                if (!allowed.includes(file.type)) {
                                    isValid = false;
                                    setFieldError(fileInput, 'Invalid file type. Please upload an image.');
                                    if (!firstInvalid) firstInvalid = fileInput;
                                } else if (file.size > maxSize) {
                                    isValid = false;
                                    setFieldError(fileInput, 'Lifestyle photo too large. Max size is 5MB.');
                                    if (!firstInvalid) firstInvalid = fileInput;
                                } else {
                                    clearFieldError(fileInput);
                                }
                            } else {
                                clearFieldError(fileInput);
                            }
                        }

                        // 3. Button URL is mandatory if Button Text is entered
                        const btnUrlInput = card.querySelector('.showcase-btn-url');
                        const btnTextEn = card.querySelector('.showcase-btn-text-en');
                        const btnTextFr = card.querySelector('.showcase-btn-text-fr');
                        const btnTextAr = card.querySelector('.showcase-btn-text-ar');

                        const hasEnBtnText = btnTextEn && btnTextEn.value.trim() !== '';

                        // If primary English button text is empty, auto-clear secondary translations
                        if (!hasEnBtnText) {
                            if (btnTextFr) btnTextFr.value = '';
                            if (btnTextAr) btnTextAr.value = '';
                        }

                        const hasButtonText = hasEnBtnText ||
                            (btnTextFr && btnTextFr.value.trim() !== '') ||
                            (btnTextAr && btnTextAr.value.trim() !== '');

                        if (hasButtonText) {
                            if (!btnUrlInput || !btnUrlInput.value.trim()) {
                                isValid = false;
                                setFieldError(btnUrlInput, 'Button URL is mandatory when Button Text is provided.');
                                if (!firstInvalid) firstInvalid = btnUrlInput;
                            } else {
                                clearFieldError(btnUrlInput);
                            }
                        } else {
                            if (btnUrlInput) clearFieldError(btnUrlInput);
                        }
                    });

                    if (!isValid && firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                    return true;
                }

                return true;
            }

            // -------------------------------------------------------------
            // Programmatic Tab Switching with Gatekeeper Protection
            // -------------------------------------------------------------
            let isProgrammaticSwitching = false;

            function switchTab(stepId) {
                const cleanId = stepId.replace('#', '');
                const tabTrigger = document.querySelector(`button[data-bs-target="#${cleanId}"]`);
                if (tabTrigger) {
                    isProgrammaticSwitching = true;
                    bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                    isProgrammaticSwitching = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }

            // Intercept direct clicks on top wizard tab navigation pills
            const wizardTabs = document.querySelectorAll('#pills-tab-brand button[data-bs-toggle="pill"]');
            wizardTabs.forEach(tab => {
                tab.addEventListener('show.bs.tab', function(e) {
                    if (isProgrammaticSwitching) return;

                    const targetSelector = e.target.getAttribute('data-bs-target');
                    const activeTab = document.querySelector('#pills-tab-brand button.nav-link.active');
                    const activeId = activeTab ? activeTab.getAttribute('data-bs-target') : '#step1';

                    if (targetSelector === '#step2') {
                        if (!validateStep(1)) {
                            e.preventDefault();
                            return;
                        }
                    } else if (targetSelector === '#step3') {
                        if (!validateStep(1)) {
                            e.preventDefault();
                            if (activeId !== '#step1') switchTab('step1');
                            return;
                        }
                        if (!validateStep(2)) {
                            e.preventDefault();
                            if (activeId !== '#step2') switchTab('step2');
                            return;
                        }
                    }
                });
            });

            // 2. Wizard Tab Navigation (Next / Prev buttons)
            document.querySelectorAll('.next-step').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const nextId = this.getAttribute('data-next');
                    if (nextId === 'step2') {
                        if (!validateStep(1)) return;
                        switchTab('step2');
                    } else if (nextId === 'step3') {
                        if (!validateStep(1)) {
                            switchTab('step1');
                            return;
                        }
                        if (!validateStep(2)) {
                            switchTab('step2');
                            return;
                        }
                        switchTab('step3');
                    }
                });
            });

            document.querySelectorAll('.prev-step').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const prevId = this.getAttribute('data-prev');
                    switchTab(prevId);
                });
            });

            // 3. Dynamic Banners Repeater
            let bannerCounter = {{ $brand->banners->count() }};
            const bannersContainer = document.getElementById('banners-container');
            const addBannerBtn = document.getElementById('add-banner-btn');
            const noBannersMsg = document.getElementById('no-banners-msg');
            const bannerBadge = document.getElementById('banner-count-badge');

            function updateBannerBadge() {
                const activeItems = bannersContainer.querySelectorAll('.banner-item:not([style*="display: none"])');
                const activeCount = activeItems.length;
                if (bannerBadge) bannerBadge.textContent = activeCount;
                if (noBannersMsg) noBannersMsg.style.display = activeCount === 0 ? 'block' : 'none';

                activeItems.forEach(item => {
                    const btn = item.querySelector('.remove-banner-btn');
                    if (btn) {
                        if (activeCount <= 1) {
                            btn.setAttribute('disabled', 'disabled');
                            btn.classList.add('disabled');
                            btn.title = 'At least 1 banner slide is mandatory';
                        } else {
                            btn.removeAttribute('disabled');
                            btn.classList.remove('disabled');
                            btn.title = 'Remove Banner';
                        }
                    }
                });

                const bannerGlobalError = document.getElementById('banner-global-error');
                if (activeCount >= 1 && bannerGlobalError) {
                    bannerGlobalError.style.display = 'none';
                }
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
                    const card = e.target.closest('.banner-item');
                    const img = card.querySelector('.banner-preview-img');
                    const emptyState = card.querySelector('.banner-empty-preview');

                    if (file) {
                        if (img) {
                            img.src = URL.createObjectURL(file);
                            img.classList.remove('d-none');
                        }
                        if (emptyState) {
                            emptyState.classList.add('d-none');
                        }
                        clearFieldError(e.target);
                    } else {
                        const hasExisting = img && img.dataset.hasExisting === '1' && img.getAttribute('src');
                        if (!hasExisting) {
                            if (img) {
                                img.src = '';
                                img.classList.add('d-none');
                            }
                            if (emptyState) {
                                emptyState.classList.remove('d-none');
                            }
                        }
                    }
                }
            });

            bannersContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-banner-btn');
                if (removeBtn) {
                    e.preventDefault();
                    const activeCount = bannersContainer.querySelectorAll('.banner-item:not([style*="display: none"])').length;
                    if (activeCount <= 1) {
                        alert('At least 1 hero banner slide is mandatory. You cannot remove the only banner.');
                        return;
                    }
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

            bannersContainer.addEventListener('input', function(e) {
                if (e.target.classList.contains('banner-alt-text')) {
                    const card = e.target.closest('.banner-item');
                    const headerTitle = card.querySelector('.banner-header-title');
                    if (headerTitle) {
                        headerTitle.textContent = e.target.value.trim() || 'Banner Slide';
                    }
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

            if (addShowcaseBtn) {
                addShowcaseBtn.addEventListener('click', function() {
                    if (noShowcasesMsg) noShowcasesMsg.style.display = 'none';

                    const template = document.getElementById('showcase-card-template').innerHTML;
                    const newHtml = template
                        .replace(/__INDEX__/g, showcaseCounter)
                        .replace(/__SORT_ORDER__/g, showcaseCounter);

                    showcasesContainer.insertAdjacentHTML('beforeend', newHtml);
                    showcaseCounter++;
                    updateShowcaseBadge();
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
                            img.style.opacity = '1';
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
                    const btnTextEn = card.querySelector('.showcase-btn-text-en');

                    const titleVal = titleEn ? titleEn.value.trim() : '';
                    const descVal = descEn ? descEn.value.trim() : '';
                    const btnTextVal = btnTextEn ? btnTextEn.value.trim() : '';

                    if (!titleVal && !descVal && !btnTextVal) {
                        alert('Please fill in English Title, Description, or Button Text before translating.');
                        return;
                    }

                    const originalBtnHtml = transBtn.innerHTML;
                    transBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Translating...';
                    transBtn.disabled = true;

                    const payload = {};
                    if (titleVal) payload.title = titleVal;
                    if (descVal) payload.description = descVal;
                    if (btnTextVal) payload.button_text = btnTextVal;

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
                                if (trans.button_text) {
                                    const btnTextFr = card.querySelector('.showcase-btn-text-fr');
                                    const btnTextAr = card.querySelector('.showcase-btn-text-ar');
                                    if (btnTextFr && trans.button_text.fr) btnTextFr.value = trans.button_text.fr;
                                    if (btnTextAr && trans.button_text.ar) btnTextAr.value = trans.button_text.ar;
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

            // -------------------------------------------------------------
            // Form Submission Validation
            // -------------------------------------------------------------
            const brandForm = document.getElementById('brand-wizard-form');
            if (brandForm) {
                brandForm.addEventListener('submit', function(e) {
                    if (!validateStep(1)) {
                        e.preventDefault();
                        e.stopPropagation();
                        switchTab('step1');
                        return false;
                    }

                    if (!validateStep(2)) {
                        e.preventDefault();
                        e.stopPropagation();
                        switchTab('step2');
                        return false;
                    }

                    if (!validateStep(3)) {
                        e.preventDefault();
                        e.stopPropagation();
                        switchTab('step3');
                        return false;
                    }

                    // Prevent double submit race conditions
                    if (brandForm.dataset.submitting === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    brandForm.dataset.submitting = 'true';
                    setTimeout(function() {
                        const submitButtons = document.querySelectorAll('button[type="submit"][form="brand-wizard-form"], #brand-wizard-form button[type="submit"]');
                        submitButtons.forEach(btn => {
                            btn.disabled = true;
                        });
                    }, 50);
                });
            }

            // Real-time clearing as user types
            document.addEventListener('input', function(e) {
                if (e.target.id === 'name_en' && e.target.value.trim()) {
                    clearFieldError(e.target);
                }
                if (e.target.classList.contains('showcase-title-en') && e.target.value.trim()) {
                    clearFieldError(e.target);
                }
                if (e.target.classList.contains('showcase-btn-url')) {
                    const card = e.target.closest('.showcase-item');
                    if (card) {
                        const btnTextEn = card.querySelector('.showcase-btn-text-en');
                        const hasEnBtnText = btnTextEn && btnTextEn.value.trim() !== '';
                        if (!hasEnBtnText || e.target.value.trim()) {
                            clearFieldError(e.target);
                        }
                    }
                }
                if (e.target.name && e.target.name.includes('[button_text]')) {
                    const card = e.target.closest('.showcase-item');
                    if (card) {
                        const btnUrlInput = card.querySelector('.showcase-btn-url');
                        const btnTextEn = card.querySelector('.showcase-btn-text-en');
                        const btnTextFr = card.querySelector('.showcase-btn-text-fr');
                        const btnTextAr = card.querySelector('.showcase-btn-text-ar');

                        // If user clears English button text, automatically clear FR and AR button texts
                        if (e.target.classList.contains('showcase-btn-text-en') && !e.target.value.trim()) {
                            if (btnTextFr) btnTextFr.value = '';
                            if (btnTextAr) btnTextAr.value = '';
                            if (btnUrlInput) clearFieldError(btnUrlInput);
                        } else {
                            const hasAnyBtnText = (btnTextEn && btnTextEn.value.trim() !== '') ||
                                                  (btnTextFr && btnTextFr.value.trim() !== '') ||
                                                  (btnTextAr && btnTextAr.value.trim() !== '');
                            if (!hasAnyBtnText && btnUrlInput) {
                                clearFieldError(btnUrlInput);
                            } else if (hasAnyBtnText && btnUrlInput && btnUrlInput.value.trim()) {
                                clearFieldError(btnUrlInput);
                            }
                        }
                    }
                }
            });

            document.addEventListener('change', function(e) {
                if (e.target.id === 'brand-icon' && e.target.files.length > 0) {
                    clearFieldError(e.target);
                }
                if (e.target.classList.contains('banner-file-input') && e.target.files.length > 0) {
                    clearFieldError(e.target);
                }
                if (e.target.classList.contains('showcase-file-input') && e.target.files.length > 0) {
                    clearFieldError(e.target);
                }
            });

            // Auto-switch to tab containing server-side validation error on load
            const firstServerInvalid = document.querySelector('.is-invalid, .invalid-feedback.d-block');
            if (firstServerInvalid) {
                const wizardPane = firstServerInvalid.closest('#step1, #step2, #step3');
                if (wizardPane && wizardPane.id) {
                    switchTab(wizardPane.id);
                }
                const subPane = firstServerInvalid.closest('.tab-pane:not(#step1):not(#step2):not(#step3)');
                if (subPane && subPane.id) {
                    const subTabTrigger = document.querySelector(`button[data-bs-target="#${subPane.id}"]`);
                    if (subTabTrigger) {
                        bootstrap.Tab.getOrCreateInstance(subTabTrigger).show();
                    }
                }
                firstServerInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
@endpush
