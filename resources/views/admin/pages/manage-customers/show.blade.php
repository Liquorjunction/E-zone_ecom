@extends('admin.layouts.app')
@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Customer Details</h4>
                    <p class="text-muted mb-0">View customer information and status</p>
                </div>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-soft-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Customers
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-soft-primary border-0">
                    <h5 class="mb-0"><i class="bi bi-person text-primary me-2"></i>Customer Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold text-muted">Name</label>
                            <p class="h6 mb-0">{{ $customer->name }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold text-muted">Email</label>
                            <p class="h6 mb-0">
                                <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold text-muted">Phone</label>
                            <p class="h6 mb-0">{{ $customer->phone ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold text-muted">Status</label>
                            <p class="mb-0">
                                @if($customer->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-soft-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                    <a href="{{ route('admin.customers.edit', ['id' => $customer->id]) }}" class="btn btn-primary px-4">
                        <i class="bi bi-pencil me-2"></i>Edit Customer
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-soft-info border-0">
                    <h6 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>Account Info</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <small class="text-muted d-block fw-semibold">Registered</small>
                        <p class="mb-0">{{ $customer->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block fw-semibold">Last Updated</small>
                        <p class="mb-0">{{ $customer->updated_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-semibold">Last Login</small>
                        <p class="mb-0">{{ $customer->last_login_at ? $customer->last_login_at->format('M d, Y H:i') : 'Never' }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-soft-warning border-0">
                    <h6 class="mb-0"><i class="bi bi-shield-check text-warning me-2"></i>Verification</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-2">
                        <small class="text-muted d-block fw-semibold">Email Verified</small>
                        <p class="mb-0">
                            @if($customer->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                                <small class="text-muted">{{ $customer->email_verified_at->format('M d, Y') }}</small>
                            @else
                                <span class="badge bg-warning">Not Verified</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
