<div class="row">
    <div class="col-md-12 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0"><strong>Technical Specifications</strong></label>
            <ul class="nav nav-pills nav-pills-sm" id="langTabSpecs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active py-1 px-3" data-bs-toggle="tab" href="#specs-en" role="tab" aria-controls="specs-en" aria-selected="true">English</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#specs-fr" role="tab" aria-controls="specs-fr" aria-selected="false">French</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#specs-ar" role="tab" aria-controls="specs-ar" aria-selected="false">Arabic</a>
                </li>
            </ul>
        </div>

        <div class="tab-content border p-3 rounded">
            @foreach (['en', 'fr', 'ar'] as $lang)
                <div class="tab-pane fade {{ $lang === 'en' ? 'show active' : '' }}" id="specs-{{ $lang }}">

                    {{-- Batch Translate Button (English Only) --}}
                    @if($lang === 'en' && isset($product))
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-sm btn-primary btn-translate-all"
                            data-url="{{ route('admin.translate.batch') }}"
                            data-fields='["dimensions", "warranty"]'>
                            <i class="bi bi-translate me-2"></i> Translate Fields
                        </button>
                    </div>
                    @endif

                    <div class="row {{ $lang === 'ar' ? 'text-end' : '' }}" dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">

                        {{-- Static Fields: Dimensions & Warranty --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dimensions ({{ strtoupper($lang) }})</label>
                            <input type="text"
                                   name="dimensions[{{ $lang }}]"
                                   id="dimensions_{{ $lang }}"
                                   class="form-control"
                                   placeholder="e.g. 10x10x5 cm"
                                   value="{{ old('dimensions.'.$lang, isset($product) ? $product->getTranslation('dimensions', $lang, false) : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Warranty ({{ strtoupper($lang) }})</label>
                            <input type="text"
                                   name="warranty[{{ $lang }}]"
                                   id="warranty_{{ $lang }}"
                                   class="form-control"
                                   placeholder="e.g. 2 Years"
                                   value="{{ old('warranty.'.$lang, isset($product) ? $product->getTranslation('warranty', $lang, false) : '') }}">
                        </div>

                        {{-- Dynamic Key-Value Table --}}
                        <div class="col-md-12">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Detailed Specifications ({{ strtoupper($lang) }})</span>
                                <button type="button" class="btn btn-sm btn-info add-spec-btn" data-lang="{{ $lang }}">
                                    <i class="fas fa-plus-circle"></i> Add Field
                                </button>
                            </label>

                            <table class="table table-bordered table-sm" id="spec-table-{{ $lang }}">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Title / Feature</th>
                                        <th>Value / Description</th>
                                        <th style="width: 50px;" class="text-center"><i class="fas fa-trash-alt"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Retrieve existing specs if editing, or empty array if creating
                                        $existingSpecs = [];
                                        if (isset($product)) {
                                            $raw = $product->getTranslation('specifications', $lang, false);
                                            // Handle case where Spatie returns JSON string or Array
                                            $existingSpecs = is_array($raw) ? $raw : (json_decode($raw, true) ?? []);
                                        }

                                        // Handle "Old" input (validation failure) taking precedence over DB values
                                        $oldSpecs = old("specifications.$lang");
                                        $specsToDisplay = $oldSpecs ?? $existingSpecs;
                                    @endphp

                                    @foreach ($specsToDisplay as $key => $entry)
                                        <tr>
                                            <td>
                                                <input type="text"
                                                       name="specifications[{{ $lang }}][{{ $key }}][title]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $entry['title'] ?? '' }}"
                                                       placeholder="Feature">
                                            </td>
                                            <td>
                                                <input type="text"
                                                       name="specifications[{{ $lang }}][{{ $key }}][value]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $entry['value'] ?? '' }}"
                                                       placeholder="Value">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm py-0 px-2 remove-spec-row">&times;</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if(empty($specsToDisplay))
                                <div class="text-center text-muted small py-2 empty-msg">
                                    Click "Add Field" to add detailed specs.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
