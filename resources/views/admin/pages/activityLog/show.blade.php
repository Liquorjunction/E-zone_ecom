@extends('admin.layouts.app')

@section('title', 'NAPS API Log Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/viewLog.css') }}">
@endpush

@section('content')
    <div class="container-fluid py-3">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.naps-log.index') }}">NAPS Payment Logs</a></li>
                <li class="breadcrumb-item active">Log Details #{{ $napsLog->id }}</li>
            </ol>
        </nav>

        <!-- Header Section -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h4 mb-1 fw-bold">NAPS API Log Details</h1>
                <p class="text-muted mb-0">View and analyze the complete API response data.</p>
            </div>
            <a href="{{ route('admin.naps-log.index') }}" class="action-btn btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Back to Logs
            </a>
        </div>

        <div class="row g-4">
            <!-- Main Content - Left Side -->
            <div class="col-lg-8">
                <!-- Key Information Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="info-icon log">
                                    <i class="bi bi-hash"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Log ID</div>
                                    <p class="mb-0 fw-bold fs-5">#{{ $napsLog->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="info-icon order">
                                    <i class="bi bi-bag-check"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Order ID</div>
                                    <p class="mb-0 fw-bold fs-5">{{ $napsLog->order_id ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="info-icon api">
                                    <i class="bi bi-plug"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">API Type</div>
                                    @php
                                        $apiType = strtolower($napsLog->api_type ?? 'unknown');
                                        $badgeClass = 'default';
                                        if (str_contains($apiType, 'create')) {
                                            $badgeClass = 'create';
                                        } elseif (str_contains($apiType, 'cancel')) {
                                            $badgeClass = 'cancel';
                                        } elseif (str_contains($apiType, 'status')) {
                                            $badgeClass = 'status';
                                        } elseif (str_contains($apiType, 'inquiry')) {
                                            $badgeClass = 'inquiry';
                                        }
                                    @endphp
                                    <span class="api-badge {{ $badgeClass }}">
                                        @if ($badgeClass === 'create')
                                            <i class="bi bi-plus-circle"></i>
                                        @elseif($badgeClass === 'cancel')
                                            <i class="bi bi-x-circle"></i>
                                        @elseif($badgeClass === 'status')
                                            <i class="bi bi-info-circle"></i>
                                        @elseif($badgeClass === 'inquiry')
                                            <i class="bi bi-question-circle"></i>
                                        @else
                                            <i class="bi bi-gear"></i>
                                        @endif
                                        {{ ucfirst($napsLog->api_type ?? 'N/A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="d-flex align-items-center gap-3">
                                <div class="info-icon time">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Created At</div>
                                    <p class="mb-0 fw-semibold">
                                        {{ $napsLog->created_at ? $napsLog->created_at->format('d M Y') : 'N/A' }}
                                        <span
                                            class="text-muted small">({{ $napsLog->created_at ? $napsLog->created_at->format('h:i A') : '' }})</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Full API Response Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-code-slash me-2 text-primary"></i>
                                Full API Response
                            </h5>
                            <button class="copy-btn" onclick="copyJson()">
                                <i class="bi bi-clipboard me-1"></i>
                                <span id="copyText">Copy</span>
                            </button>
                        </div>
                        <div class="json-viewer">
                            <div class="json-window-controls">
                                <span class="control-close"></span>
                                <span class="control-minimize"></span>
                                <span class="control-maximize"></span>
                            </div>
                            <div class="json-content" id="jsonContent">
                                <pre class="mb-0"><code id="jsonCode">{{ json_encode($apiResponse, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Right Side -->
            <div class="col-lg-4">
                <!-- Quick Info Sidebar -->
                <div class="sidebar-card mb-4">
                    <h6><i class="bi bi-info-circle me-2"></i>Log Information</h6>

                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-label">Log ID</div>
                            <div class="timeline-value">#{{ $napsLog->id }}</div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #f5576c;"></div>
                        <div class="timeline-content">
                            <div class="timeline-label">Order ID</div>
                            <div class="timeline-value">{{ $napsLog->order_id ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #4facfe;"></div>
                        <div class="timeline-content">
                            <div class="timeline-label">API Type</div>
                            <div class="timeline-value">{{ ucfirst($napsLog->api_type ?? 'N/A') }}</div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #43e97b;"></div>
                        <div class="timeline-content">
                            <div class="timeline-label">Recorded</div>
                            <div class="timeline-value">
                                {{ $napsLog->created_at ? $napsLog->created_at->format('d M Y, h:i A') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Sidebar -->
                <div class="sidebar-card mb-4">
                    <h6><i class="bi bi-lightning me-2"></i>Quick Actions</h6>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.naps-log.index') }}" class="btn btn-primary">
                            <i class="bi bi-list-ul me-2"></i>View All Logs
                        </a>
                        <a href="{{ route('admin.naps-log.show', $napsLog->id) }}" class="btn btn-outline-secondary"
                            onclick="location.reload(); return false;">
                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh Page
                        </a>
                    </div>
                </div>

                <!-- Help Sidebar -->
                <div class="sidebar-card" style="background: #f8f9fa;">
                    <h6><i class="bi bi-question-circle me-2"></i>API Types Guide</h6>

                    <div class="small">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="api-badge create py-1 px-2"><i class="bi bi-plus-circle"></i> Create</span>
                            <span class="text-muted">New payment request</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="api-badge cancel py-1 px-2"><i class="bi bi-x-circle"></i> Cancel</span>
                            <span class="text-muted">Cancel payment</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="api-badge status py-1 px-2"><i class="bi bi-info-circle"></i> Status</span>
                            <span class="text-muted">Check payment status</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="api-badge inquiry py-1 px-2"><i class="bi bi-question-circle"></i> Inquiry</span>
                            <span class="text-muted">Transaction inquiry</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // JSON Syntax Highlighting
        function highlightJson() {
            const jsonContent = document.getElementById('jsonCode');
            let json = jsonContent.textContent;

            // Escape HTML
            json = json.replace(/&/g, '&amp;').replace(/</g, '<').replace(/>/g, '>');

            // Apply syntax highlighting
            json = json.replace(
                /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                function(match) {
                    let cls = 'json-number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'json-key';
                        } else {
                            cls = 'json-string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'json-boolean';
                    } else if (/null/.test(match)) {
                        cls = 'json-null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                });

            jsonContent.innerHTML = json;
        }

        // Copy JSON to clipboard
        function copyJson() {
            const jsonText = document.getElementById('jsonCode').textContent;
            navigator.clipboard.writeText(jsonText).then(function() {
                const copyBtn = document.querySelector('.copy-btn');
                const copyText = document.getElementById('copyText');
                copyBtn.classList.add('copied');
                copyText.textContent = 'Copied!';

                setTimeout(function() {
                    copyBtn.classList.remove('copied');
                    copyText.textContent = 'Copy';
                }, 2000);
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            highlightJson();
        });
    </script>
@endpush
