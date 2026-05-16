@extends('admin.layouts.app')
@section('title', 'NAPS API Log Details')
@section('header-title', 'NAPS API Log Details')
@section('header-description', 'View and analyze the complete API response data for this log entry')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.naps-log.show', $napsLog) as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid pt-3">
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
            <div>
                <h4 class="fw-semibold mb-1">NAPS API Log Details</h4>
                <p class="text-secondary small mb-0">View and analyze the complete API response data for this log entry</p>
            </div>
            <a href="{{ route('admin.naps-log.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Logs
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3 p-md-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="text-uppercase text-muted small fw-semibold">Log ID</div>
                                        <p class="mb-0 fw-semibold">#{{ $napsLog->id }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-uppercase text-muted small fw-semibold">Order ID</div>
                                        <p class="mb-0 fw-semibold">{{ $napsLog->order_id ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-uppercase text-muted small fw-semibold">API Type</div>
                                        @php
                                            $apiType = strtolower($napsLog->api_type ?? 'unknown');
                                            $badgeClass = 'secondary';
                                            if (str_contains($apiType, 'create')) {
                                                $badgeClass = 'success';
                                            } elseif (str_contains($apiType, 'cancel')) {
                                                $badgeClass = 'danger';
                                            } elseif (str_contains($apiType, 'status')) {
                                                $badgeClass = 'primary';
                                            } elseif (str_contains($apiType, 'inquiry')) {
                                                $badgeClass = 'warning';
                                            }
                                        @endphp
                                        <span
                                            class="badge bg-{{ $badgeClass }}">{{ ucfirst($napsLog->api_type ?? 'N/A') }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-uppercase text-muted small fw-semibold">Created At</div>
                                        <p class="mb-0 fw-semibold">
                                            {{ $napsLog->created_at ? $napsLog->created_at->format('d M Y, h:i A') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm bg-light mb-3">
                                    <div
                                        class="card-header border-bottom d-flex justify-content-between align-items-center py-3" style="background-color: #606060!important;">
                                        <h5 class="mb-0">Full API Response</h5>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="copyJson()">
                                            <i class="bi bi-clipboard me-1"></i><span id="copyText">Copy</span>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <pre class="mb-0 bg-dark text-light rounded p-3 overflow-auto" style="max-height: 520px;"><code id="jsonCode">{{ json_encode($apiResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-3 p-md-4">
                                <h5 class="mb-3">Log Information</h5>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <th class="ps-0">Log ID</th>
                                        <td class="text-end pe-0">#{{ $napsLog->id }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Order ID</th>
                                        <td class="text-end pe-0">{{ $napsLog->order_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">API Type</th>
                                        <td class="text-end pe-0">{{ ucfirst($napsLog->api_type ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0">Recorded</th>
                                        <td class="text-end pe-0">
                                            {{ $napsLog->created_at ? $napsLog->created_at->format('d M Y, h:i A') : 'N/A' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-3 p-md-4">
                                <h5 class="mb-3">Quick Actions</h5>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.naps-log.index') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-list-ul me-1"></i>View All Logs
                                    </a>
                                    <a href="{{ route('admin.naps-log.show', $napsLog->id) }}"
                                        class="btn btn-outline-secondary btn-sm" onclick="location.reload(); return false;">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh Page
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 p-md-4">
                                <h5 class="mb-3">API Types Guide</h5>
                                <div class="d-grid gap-2 small">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-success">Create</span>
                                        <span class="text-muted ms-2 grow text-end">New payment request</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-danger">Cancel</span>
                                        <span class="text-muted ms-2 grow text-end">Cancel payment</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-primary">Status</span>
                                        <span class="text-muted ms-2 grow text-end">Check payment status</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-warning text-dark">Inquiry</span>
                                        <span class="text-muted ms-2 grow text-end">Transaction inquiry</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Copy JSON to clipboard
        function copyJson() {
            const jsonText = document.getElementById('jsonCode').textContent;
            navigator.clipboard.writeText(jsonText).then(function() {
                const copyText = document.getElementById('copyText');
                copyText.textContent = 'Copied!';

                setTimeout(function() {
                    copyText.textContent = 'Copy';
                }, 2000);
            });
        }
    </script>
@endpush
