@extends('admin.layouts.app')

@section('title', 'Edit Discount')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Edit Discount: {{ $discount->getTranslation('name', 'en') }}</h5>
        <a href="{{ route('admin.discounts.targets.create', $discount->id) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-bullseye"></i> Manage Targets
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.discounts.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $discount->id }}">

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Discount Name</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text">EN</span>
                        <input type="text" name="name[en]" value="{{ $discount->getTranslation('name', 'en') }}" class="form-control" required>
                    </div>
                    <div class="input-group mb-2">
                        <span class="input-group-text">AR</span>
                        <input type="text" name="name[ar]" value="{{ $discount->getTranslation('name', 'ar') }}" class="form-control">
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">FR</span>
                        <input type="text" name="name[fr]" value="{{ $discount->getTranslation('name', 'fr') }}" class="form-control">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="percentage" {{ $discount->type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="flat" {{ $discount->type == 'flat' ? 'selected' : '' }}>Flat Amount</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Value</label>
                    <input type="number" step="0.01" name="value" value="{{ $discount->value }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Max Discount Amount</label>
                    <input type="number" step="0.01" name="max_discount" value="{{ $discount->max_discount }}" class="form-control">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Start Date</label>
                    <input type="datetime-local" name="starts_at" value="{{ $discount->starts_at }}" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>End Date</label>
                    <input type="datetime-local" name="ends_at" value="{{ $discount->ends_at }}" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" {{ $discount->status ? 'checked' : '' }}>
                        <label class="form-check-label" for="statusSwitch">Active</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Discount</button>
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
