@extends('admin.layouts.app')

@section('title', 'Edit Discount')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Edit Discount</h4>
                    <p class="text-muted mb-0">{{ $discount->getTranslation('name', 'en') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-soft-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Discounts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.discounts.update') }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $discount->id }}">

        <div class="row">
            <!-- Discount Details Card -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-soft-primary border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-tag text-primary me-2"></i>Discount Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Discount Name -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Discount Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-soft-primary text-primary fw-semibold">EN</span>
                                <input type="text" name="name[en]"
                                       value="{{ $discount->getTranslation('name', 'en') }}"
                                       class="form-control" required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-soft-info text-info fw-semibold">AR</span>
                                <input type="text" name="name[ar]"
                                       value="{{ $discount->getTranslation('name', 'ar') }}"
                                       class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-soft-success text-success fw-semibold">FR</span>
                                <input type="text" name="name[fr]"
                                       value="{{ $discount->getTranslation('name', 'fr') }}"
                                       class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Type -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">
                                    Discount Type <span class="text-danger">*</span>
                                </label>
                                <select name="type" class="form-select" required>
                                    <option value="percentage" {{ $discount->type == 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)
                                    </option>
                                    <option value="flat" {{ $discount->type == 'flat' ? 'selected' : '' }}>
                                        Flat Amount
                                    </option>
                                </select>
                            </div>

                            <!-- Value -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">
                                    Discount Value <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="value"
                                       value="{{ $discount->value }}"
                                       class="form-control" required>
                            </div>

                            <!-- Max Discount (Visible only for percentage type) -->
                            <div class="col-md-12 mb-4" id="max-discount-cap-wrapper" style="display: none;">
                                <label class="form-label fw-semibold">
                                    Maximum Discount Cap
                                    <span class="badge bg-soft-warning text-warning ms-2">Optional</span>
                                </label>
                                <input type="number" step="0.01" name="max_discount"
                                       value="{{ $discount->max_discount }}"
                                       class="form-control" placeholder="e.g., 100">
                                <small class="text-muted mt-2 d-block">
                                    <i class="bi bi-lightbulb me-1"></i>Useful for percentage discounts (e.g., 50% off up to $100 max)
                                </small>
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Start Date & Time</label>
                                <input type="datetime-local" name="starts_at"
                                       value="{{ $discount->starts_at }}"
                                       class="form-control" id="starts-at-field"
                                       readonly style="background-color: #98a3ad!important; cursor: not-allowed;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">End Date & Time</label>
                                <input type="datetime-local" name="ends_at"
                                       value="{{ $discount->ends_at }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Info Card -->
            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-soft-info border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-gear text-info me-2"></i>Settings
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between p-3 bg-soft-light rounded-3">
                            <div>
                                <h6 class="mb-1">Discount Status</h6>
                                <small class="text-muted">
                                    {{ $discount->status ? 'Currently Active' : 'Currently Inactive' }}
                                </small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="status" id="statusSwitch"
                                       style="width: 3rem; height: 1.5rem;"
                                       {{ $discount->status ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discount Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-soft-success border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle text-success me-2"></i>Discount Details
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Discount ID</small>
                            <span class="badge bg-soft-dark text-dark">#{{ $discount->id }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Type</small>
                            <span class="badge bg-soft-primary text-primary text-capitalize">
                                {{ $discount->type }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Current Value</small>
                            <strong class="text-dark">
                                {{ $discount->type == 'percentage' ? $discount->value . '%' : '$' . $discount->value }}
                            </strong>
                        </div>
                        @if($discount->max_discount)
                        <div>
                            <small class="text-muted d-block mb-1">Max Cap</small>
                            <strong class="text-dark">${{ $discount->max_discount }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Date Info -->
                @if($discount->starts_at || $discount->ends_at)
                <div class="card shadow-sm border-0 border-start border-warning">
                    <div class="card-body p-4">
                        <h6 class="mb-3">
                            <i class="bi bi-calendar text-warning me-2"></i>Schedule
                        </h6>
                        @if($discount->starts_at)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Starts</small>
                            <span class="text-dark">{{ \Carbon\Carbon::parse($discount->starts_at)->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif
                        @if($discount->ends_at)
                        <div>
                            <small class="text-muted d-block mb-1">Ends</small>
                            <span class="text-dark">{{ \Carbon\Carbon::parse($discount->ends_at)->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!-- Targets Section (same as Create form) -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header bg-soft-warning border-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-bullseye text-warning me-2"></i>Discount Targets
                            </h5>
                            <small class="text-muted">Specify which products, categories, or brands this discount
                                applies to</small>
                        </div>
                        <button type="button" class="btn btn-primary" id="add-target-row">
                            <i class="bi bi-plus me-2"></i>Add Target
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="targets-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4" width="30%">Target Type</th>
                                        <th width="40%">Target ID</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="target-rows">
                                    @if (old('targets'))
                                        @foreach (old('targets') as $index => $oldTarget)
                                            <tr>
                                                <td class="ps-4">
                                                    <select name="targets[{{ $index }}][target_type]"
                                                        class="form-select target-type-select">
                                                        <option value="product"
                                                            {{ $oldTarget['target_type'] == 'product' ? 'selected' : '' }}>
                                                            Product
                                                        </option>
                                                        <option value="category"
                                                            {{ $oldTarget['target_type'] == 'category' ? 'selected' : '' }}>
                                                            Category
                                                        </option>
                                                        <option value="subcategory"
                                                            {{ $oldTarget['target_type'] == 'subcategory' ? 'selected' : '' }}>
                                                            Subcategory
                                                        </option>
                                                        <option value="brand"
                                                            {{ $oldTarget['target_type'] == 'brand' ? 'selected' : '' }}>
                                                            Brand
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    @if ($oldTarget['target_type'] == 'category')
                                                        <select name="targets[{{ $index }}][target_id]"
                                                            class="form-select target-id-select">
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories ?? [] as $cat)
                                                                <option value="{{ $cat->id }}"
                                                                    {{ $oldTarget['target_id'] == $cat->id ? 'selected' : '' }}>
                                                                    {{ $cat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($oldTarget['target_type'] == 'brand')
                                                        <select name="targets[{{ $index }}][target_id]"
                                                            class="form-select">
                                                            <option value="">Select Brand</option>
                                                            @foreach ($brands ?? [] as $br)
                                                                <option value="{{ $br->id }}"
                                                                    {{ $oldTarget['target_id'] == $br->id ? 'selected' : '' }}>
                                                                    {{ $br->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($oldTarget['target_type'] == 'subcategory')
                                                        <select name="targets[{{ $index }}][target_id]"
                                                            class="form-select">
                                                            <option value="">Select Subcategory</option>
                                                            @foreach ($subcategories ?? [] as $sc)
                                                                <option value="{{ $sc->id }}"
                                                                    {{ $oldTarget['target_id'] == $sc->id ? 'selected' : '' }}>
                                                                    {{ $sc->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($oldTarget['target_type'] == 'product')
                                                        <select name="targets[{{ $index }}][target_id]"
                                                            class="form-select">
                                                            <option value="">Select Variant</option>
                                                            @foreach ($productVariants ?? [] as $p)
                                                                <option value="{{ $p->id }}"
                                                                    {{ $oldTarget['target_id'] == $p->id ? 'selected' : '' }}>
                                                                    {{ $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="number" name="targets[{{ $index }}][target_id]"
                                                            class="form-control" placeholder="Enter ID (e.g., 105)"
                                                            value="{{ $oldTarget['target_id'] }}" required>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-soft-danger btn-sm remove-row">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif(isset($resolvedTargets) && count($resolvedTargets))
                                        @foreach($resolvedTargets as $index => $t)
                                            <tr>
                                                <td class="ps-4">
                                                    <select name="targets[{{ $index }}][target_type]" class="form-select target-type-select">
                                                        <option value="product" {{ $t['target_type'] == 'product' ? 'selected' : '' }}>Product</option>
                                                        <option value="category" {{ $t['target_type'] == 'category' ? 'selected' : '' }}>Category</option>
                                                        <option value="subcategory" {{ $t['target_type'] == 'subcategory' ? 'selected' : '' }}>Subcategory</option>
                                                        <option value="brand" {{ $t['target_type'] == 'brand' ? 'selected' : '' }}>Brand</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    @if($t['target_type'] == 'category')
                                                        <select name="targets[{{ $index }}][target_id]" class="form-select target-id-select">
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories ?? [] as $cat)
                                                                <option value="{{ $cat->id }}" {{ $t['target_id'] == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($t['target_type'] == 'brand')
                                                        <select name="targets[{{ $index }}][target_id]" class="form-select target-id-select">
                                                            <option value="">Select Brand</option>
                                                            @foreach ($brands ?? [] as $br)
                                                                <option value="{{ $br->id }}" {{ $t['target_id'] == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($t['target_type'] == 'subcategory')
                                                        <select name="targets[{{ $index }}][target_id]" class="form-select target-id-select">
                                                            <option value="">Select Subcategory</option>
                                                            @foreach ($subcategories ?? [] as $sc)
                                                                <option value="{{ $sc->id }}" {{ $t['target_id'] == $sc->id ? 'selected' : '' }}>{{ $sc->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($t['target_type'] == 'product')
                                                        @php
                                                            $variantIds = collect($productVariants ?? [])->pluck('id')->all();
                                                        @endphp
                                                        @if(in_array($t['target_id'], $variantIds))
                                                            <select name="targets[{{ $index }}][target_id]" class="form-select target-id-select">
                                                                <option value="">Select Variant</option>
                                                                @foreach ($productVariants ?? [] as $p)
                                                                    <option value="{{ $p->id }}" {{ $t['target_id'] == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <div class="d-flex">
                                                                <input type="number" name="targets[{{ $index }}][target_id]" class="form-control me-2" value="{{ $t['target_id'] }}" readonly>
                                                                <input type="text" class="form-control" value="{{ $t['name'] ?? 'Unknown' }}" readonly>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <input type="number" name="targets[{{ $index }}][target_id]" class="form-control" placeholder="Enter ID (e.g., 105)" value="{{ $t['target_id'] }}" required>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-soft-danger btn-sm remove-row">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="empty-message">
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="bi bi-inbox fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No targets added yet</p>
                                                    <small>Click "Add Target" to apply this discount to specific
                                                        items</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div
                        class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                        <div>
                            <a href="{{ route('admin.discounts.index') }}" class="btn btn-soft-secondary me-2">
                                <i class="bi bi-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check me-2"></i>Update Discount
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        window._discountData = {
            categories: @json($categories ?? []),
            brands: @json($brands ?? []),
            subcategories: @json($subcategories ?? []),
            productVariants: @json($productVariants ?? []),
            rowIdx: {{ old('targets') ? count(old('targets')) : (isset($discount) && isset($discount->targets) ? $discount->targets->count() : 0) }}
        };
    </script>
    <script src="{{ asset('admin_assets/js/discounts-create.js') }}"></script>
@endpush
