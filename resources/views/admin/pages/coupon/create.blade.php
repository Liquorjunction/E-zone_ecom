@extends('admin.layouts.app')
@section('title', 'Create Coupon')
@section('header-title', 'Create New Coupon')
@section('header-description', 'Configure coupon code, limits, and active dates')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.coupons.create') as $breadcrumb)
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    @can('coupon.create')
                        <div>
                            <h4 class="mb-1">Create Coupon</h4>
                            <p class="text-muted mb-0">Configure coupon code, limits, and active dates</p>
                        </div>
                    @endcan
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-soft-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Coupons
                    </a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the errors below.
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-soft-primary border-0">
                            <h5 class="mb-0"><i class="bi bi-ticket-alt text-primary me-2"></i>Coupon Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., Welcome10"
                                    required value="{{ old('name') }}">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" placeholder="e.g., SAVE10"
                                        required value="{{ old('code') }}">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                                    <select name="coupon_type" class="form-select" required>
                                        <option value="discount" {{ old('coupon_type') == 'discount' ? 'selected' : '' }}>
                                            Discount (%)</option>
                                        <option value="flat_amount"
                                            {{ old('coupon_type') == 'flat_amount' ? 'selected' : '' }}>Flat Amount</option>
                                        {{-- <option value="free_shipping"
                                            {{ old('coupon_type') == 'free_shipping' ? 'selected' : '' }}>Free Shipping
                                        </option> --}}
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Value <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="value" class="form-control"
                                        placeholder="e.g., 10" required value="{{ old('value') }}">
                                    <small class="text-muted">For percentage, enter the percent number (e.g., 15)</small>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Minimum Order Amount</label>
                                    <input type="number" step="0.01" name="min_order_amount" class="form-control"
                                        placeholder="e.g., 50" value="{{ old('min_order_amount') }}">
                                </div>
                            </div>

                            <div class="row">
                                {{-- <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Maximum Discount</label>
                                    <input type="number" step="0.01" name="max_discount" class="form-control"
                                        placeholder="e.g., 100" value="{{ old('max_discount') }}">
                                </div> --}}
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Usage Limit</label>
                                    <input type="number" min="1" name="usage_limit" class="form-control"
                                        placeholder="Unlimited" value="{{ old('usage_limit') }}">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Usage Limit Per User</label>
                                    <input type="number" min="1" name="usage_limit_per_user" class="form-control"
                                        placeholder="Unlimited" value="{{ old('usage_limit_per_user') }}">
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Status</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" value="1"
                                            id="statusSwitch" style="width: 3rem; height: 1.5rem;"
                                            {{ old('status', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label ms-2" for="statusSwitch">Active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Starts At</label>
                                    <input type="datetime-local" name="starts_at" id="starts-at-field" class="form-control"
                                        value="{{ old('starts_at', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}">
                                    <input type="hidden" name="starts_at" id="starts-at-hidden" value="{{ old('starts_at', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">Ends At</label>
                                    <input type="datetime-local" name="ends_at" class="form-control"
                                        value="{{ old('ends_at') }}">
                                </div>
                            </div>
                        </div>
                        <div
                            class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-soft-secondary">
                                <i class="bi bi-x me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check me-2"></i>Create Coupon
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-soft-info border-0">
                            <h6 class="mb-0"><i class="bi bi-lightbulb text-info me-2"></i>Tips</h6>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-unstyled mb-0 small text-muted">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use short,
                                    memorable codes</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Set limits to
                                    prevent misuse</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use date range for
                                    seasonal sales</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Enable when ready to launch</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/coupons-create.js') }}"></script>
@endpush
