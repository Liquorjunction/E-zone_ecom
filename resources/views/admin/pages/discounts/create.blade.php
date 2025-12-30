@extends('admin.layouts.app')

@section('title', 'Create New Discount')

@section('content')
<form action="{{ route('admin.discounts.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tag me-2"></i> Discount Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Discount Name <span class="text-danger">*</span></label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">EN</span>
                                <input type="text" name="name[en]" class="form-control" placeholder="Summer Sale" required value="{{ old('name.en') }}">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">AR</span>
                                <input type="text" name="name[ar]" class="form-control" placeholder="Translation will be auto-generated if empty" value="{{ old('name.ar') }}">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">FR</span>
                                <input type="text" name="name[fr]" class="form-control" placeholder="Translation will be auto-generated if empty" value="{{ old('name.fr') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="flat" {{ old('type') == 'flat' ? 'selected' : '' }}>Flat Amount</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="value" class="form-control" required value="{{ old('value') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Discount Cap</label>
                            <input type="number" step="0.01" name="max_discount" class="form-control" placeholder="Optional" value="{{ old('max_discount') }}">
                            <small class="text-muted">Useful for percentage discounts (e.g. 50% off up to $100)</small>
                        </div>

                        <div class="col-md-6 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" checked value="1">
                                <label class="form-check-label" for="statusSwitch">Active</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i> Apply To Targets</h5>
                    <button type="button" class="btn btn-sm btn-light text-dark fw-bold" id="add-target-row">
                        <i class="fas fa-plus"></i> Add Target
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" id="targets-table">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Target Type</th>
                                    <th width="50%">Target ID / Name</th>
                                    <th width="10%" class="text-center">Active</th>
                                    <th width="10%" class="text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="target-rows">
                                @if(old('targets'))
                                    @foreach(old('targets') as $index => $oldTarget)
                                        <tr>
                                            <td>
                                                <select name="targets[{{$index}}][target_type]" class="form-select">
                                                    <option value="product" {{ $oldTarget['target_type'] == 'product' ? 'selected' : '' }}>Product</option>
                                                    <option value="category" {{ $oldTarget['target_type'] == 'category' ? 'selected' : '' }}>Category</option>
                                                    <option value="subcategory" {{ $oldTarget['target_type'] == 'subcategory' ? 'selected' : '' }}>Subcategory</option>
                                                    <option value="brand" {{ $oldTarget['target_type'] == 'brand' ? 'selected' : '' }}>Brand</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="targets[{{$index}}][target_id]" class="form-control" value="{{ $oldTarget['target_id'] }}" placeholder="ID" required>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" type="checkbox" name="targets[{{$index}}][status]" value="1" {{ isset($oldTarget['status']) ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="empty-message">
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No targets added yet. Click "Add Target" to apply this discount to specific products or categories.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-success btn-lg px-4"><i class="fas fa-save me-2"></i> Create Discount</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let rowIdx = {{ old('targets') ? count(old('targets')) : 0 }};

        $('#add-target-row').on('click', function() {
            // Remove empty message if it exists
            $('.empty-message').remove();

            let html = `
                <tr>
                    <td>
                        <select name="targets[${rowIdx}][target_type]" class="form-select">
                            <option value="product">Product</option>
                            <option value="category">Category</option>
                            <option value="subcategory">Subcategory</option>
                            <option value="brand">Brand</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="targets[${rowIdx}][target_id]" class="form-control" placeholder="Enter ID (e.g., 105)" required>
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input" type="checkbox" name="targets[${rowIdx}][status]" value="1" checked>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#target-rows').append(html);
            rowIdx++;
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();

            // If no rows left, show empty message
            if ($('#target-rows tr').length === 0) {
                $('#target-rows').html(`
                    <tr class="empty-message">
                        <td colspan="4" class="text-center text-muted py-4">
                            No targets added yet. Click "Add Target" to apply this discount to specific products or categories.
                        </td>
                    </tr>
                `);
            }
        });
    });
</script>
@endpush
