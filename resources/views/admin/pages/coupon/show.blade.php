@extends('admin.layouts.app')
@section('title', 'Coupon Details')
@section('header-title', 'Coupon Details')
@section('header-description', 'View all information about this coupon')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.coupons.show', $coupon) as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="fw-bold mb-1"><i class="bi bi-ticket-perforated text-primary me-2"></i>Coupon Details</h2>
                        <p class="text-muted mb-0">View all information about this coupon</p>
                    </div>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
                <div class="rounded-4 shadow-sm p-4 mb-4" style="background: linear-gradient(90deg, #f8fafc 60%, #e9ecef 100%);">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div>
                            <h3 class="fw-bold mb-1">{{ $coupon->name }}</h3>
                            <span class="badge bg-primary fs-6 px-3 py-2"><i class="bi bi-upc-scan me-1"></i>{{ $coupon->code }}</span>
                        </div>
                        <div class="mt-3 mt-md-0">
                            @if ($coupon->status)
                                <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Active</span>
                            @else
                                <span class="badge bg-secondary fs-6 px-3 py-2"><i class="bi bi-x-circle me-1"></i>Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow rounded-4">
                    <div class="card-header bg-white border-0 rounded-top-4 pb-0">
                        <h5 class="fw-semibold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Coupon Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-type-bold text-primary me-2 fs-5"></i>
                                    <span class="fw-semibold">Type</span>
                                </div>
                                <div class="ms-4">
                                    <span class="text-dark">
                                        @if ($coupon->coupon_type == 'discount')
                                            Discount (%)
                                        @elseif ($coupon->coupon_type == 'flat_amount')
                                            Flat Amount
                                        @elseif ($coupon->coupon_type == 'free_shipping')
                                            Free Shipping
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-cash-coin text-success me-2 fs-5"></i>
                                    <span class="fw-semibold">Value</span>
                                </div>
                                <div class="ms-4">
                                    <span class="text-dark">
                                        @if(isset($coupon->value))
                                            {{ config('app.CURRENCY_SYMBOL') }} {{ $coupon->value }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-people text-info me-2 fs-5"></i>
                                    <span class="fw-semibold">Usage Limit</span>
                                </div>
                                <div class="ms-4">
                                    <span class="text-dark">{{ $coupon->usage_limit ?? 'No Limit' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-calendar-event text-warning me-2 fs-5"></i>
                                    <span class="fw-semibold">Start Date</span>
                                </div>
                                <div class="ms-4">
                                    <span class="text-dark">{{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-calendar-x text-danger me-2 fs-5"></i>
                                    <span class="fw-semibold">End Date</span>
                                </div>
                                <div class="ms-4">
                                    <span class="text-dark">{{ $coupon->ends_at ? $coupon->ends_at->format('Y-m-d') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
