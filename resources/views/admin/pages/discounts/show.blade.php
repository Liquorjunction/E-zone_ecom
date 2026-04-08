@extends('admin.layouts.app')
@section('title', __('Discount Details'))
@section('header-title', __('Discount Details'))
@section('header-description', 'All information about this discount and its targets')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.discounts.show', $discount) as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Discount Details</h4>
                    <p class="text-muted mb-0">All information about this discount and its targets</p>
                </div>
                <a href="{{ route('admin.discounts.index') }}" class="btn btn-soft-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Discounts
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Discount Info Card -->
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-soft-primary border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-tag text-primary me-2"></i>Discount Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">ID</small>
                                <span class="badge bg-soft-dark text-dark">#{{ $discount->id }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Status</small>
                                @if ($discount->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Type</small>
                                <span
                                    class="badge bg-soft-primary text-primary text-capitalize">{{ $discount->type }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Name (EN)</small>
                                <strong>{{ $discount->getTranslation('name', 'en') }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Name (AR)</small>
                                <strong>{{ $discount->getTranslation('name', 'ar') }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Name (FR)</small>
                                <strong>{{ $discount->getTranslation('name', 'fr') }}</strong>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Value</small>
                                <strong>
                                    @if ($discount->type == 'percentage')
                                        {{ $discount->value }}%
                                    @else
                                        ${{ $discount->value }}
                                    @endif
                                </strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Max Discount Cap</small>
                                <strong>
                                    @if ($discount->max_discount)
                                        ${{ $discount->max_discount }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Created At</small>
                                <span>{{ $discount->created_at ? $discount->created_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Starts At</small>
                                <span>{{ $discount->starts_at ? $discount->starts_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Ends At</small>
                                <span>{{ $discount->ends_at ? $discount->ends_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Updated At</small>
                                <span>{{ $discount->updated_at ? $discount->updated_at->format('M d, Y h:i A') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Discount Targets Card -->
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-soft-warning border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-bullseye text-warning me-2"></i>Discount Targets
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @if (isset($resolvedTargets) && count($resolvedTargets))
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Name/Label</th>
                                            <th>ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($resolvedTargets as $target)
                                            <tr>
                                                <td class="text-capitalize">{{ $target['target_type'] }}</td>
                                                <td>{{ $target['name'] ?: 'N/A' }}</td>
                                                <td>{{ $target['target_id'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-muted text-center py-4">
                                <i class="bi bi-inbox fa-2x mb-2 opacity-50"></i>
                                <p class="mb-0">No targets assigned</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
