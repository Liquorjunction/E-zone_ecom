@extends('admin.layouts.app')

@section('title', 'NAPS API Log Details')

@section('content')
    <div class="container-fluid py-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h1 class="h4 mb-1">NAPS API Log Details</h1>
                <p class="text-muted mb-0">View the full API response data.</p>
            </div>
            <a href="{{ route('admin.naps-log.index') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
        </div>

        <div class="row g-3">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="text-uppercase text-muted small fw-semibold">Log ID</div>
                                <p class="mb-0 fw-semibold">{{ $napsLog->id }}</p>
                            </div>
                            <div class="col-md-4">
                                <div class="text-uppercase text-muted small fw-semibold">Order ID</div>
                                <p class="mb-0 fw-semibold">{{ $napsLog->order_id ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <div class="text-uppercase text-muted small fw-semibold">API Type</div>
                                <p class="mb-0 fw-semibold">{{ ucfirst($napsLog->api_type ?? 'N/A') }}</p>
                            </div>
                            <div class="col-md-4">
                                <div class="text-uppercase text-muted small fw-semibold">Created At</div>
                                <p class="mb-0 fw-semibold">
                                    {{ $napsLog->created_at ? $napsLog->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="text-uppercase text-muted small fw-semibold mb-2">Full API Response</div>
                            <div class="border rounded p-3 bg-dark">
                                <pre class="text-light mb-0" style="white-space: pre-wrap; word-wrap: break-word;">{{ json_encode($apiResponse, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
