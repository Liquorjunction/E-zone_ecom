@extends('admin.layouts.app')

@section('title', 'Contact Enquiry')

@section('content')
@php
    $images = is_array($enquiry->images) ? $enquiry->images : [];
    $attachments = is_array($enquiry->attachments) ? $enquiry->attachments : [];
    $statusLabels = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];
@endphp

<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Contact Us Enquiry</h1>
            <p class="text-muted mb-0">Review user details, message, status, and uploaded files in one place.</p>
        </div>
        <a href="{{ route('admin.contact-us.index') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Ticket Number</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->ticket_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Created At</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->created_at ? $enquiry->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Name</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Email</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Mobile</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->mobile ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Subject</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->subject_label ?? ($enquiry->subject ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Order Number</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->order_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase text-muted small fw-semibold">Product SKU</div>
                            <p class="mb-0 fw-semibold">{{ $enquiry->product_sku ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <div class="text-uppercase text-muted small fw-semibold mb-1">Message</div>
                            <div class="border rounded p-3 bg-light">{{ $enquiry->message ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <h5 class="mb-3">Enquiry Status</h5>
                    <div class="mb-3">
                        <div class="text-uppercase text-muted small fw-semibold mb-1">Current Status</div>
                        <span class="badge text-bg-primary">{{ $statusLabels[$enquiry->status] ?? ucfirst(str_replace('_', ' ', $enquiry->status ?? 'unknown')) }}</span>
                    </div>

                    <div class="list-group list-group-flush border rounded overflow-hidden">
                        @foreach($statusLabels as $key => $label)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $label }}</span>
                                @if($enquiry->status === $key)
                                    <span class="badge text-bg-success">Current</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-light border mt-3 mb-0 py-2 px-3 small text-muted">
                        Status is read-only on this page.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <h5 class="mb-3">Uploaded Images</h5>
                    @if(!empty($images))
                        <div class="row g-2">
                            @foreach($images as $index => $image)
                                @php
                                    $path = is_array($image) ? ($image['path'] ?? null) : (is_object($image) ? ($image->path ?? null) : $image);
                                    $name = is_array($image) ? ($image['original_name'] ?? ('Image ' . ($index + 1))) : (is_object($image) ? ($image->original_name ?? ('Image ' . ($index + 1))) : ('Image ' . ($index + 1)));
                                    $url = null;

                                    if (!empty($path)) {
                                        $url = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
                                            ? $path
                                            : asset('storage/' . ltrim($path, '/'));
                                    }
                                @endphp

                                @if($url)
                                    <div class="col-6 col-md-4 col-xl-3">
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="card border text-decoration-none h-100">
                                            <img src="{{ $url }}" alt="{{ $name }}" class="card-img-top img-fluid">
                                            <div class="card-body p-2">
                                                <small class="text-dark">{{ $name }}</small>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">No images uploaded for this enquiry.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <h5 class="mb-3">Attachments</h5>
                    @if(!empty($attachments))
                        <div class="d-grid gap-2">
                            @foreach($attachments as $index => $file)
                                @php
                                    $path = is_array($file) ? ($file['path'] ?? null) : (is_object($file) ? ($file->path ?? null) : $file);
                                    $name = is_array($file) ? ($file['original_name'] ?? ('Attachment ' . ($index + 1))) : (is_object($file) ? ($file->original_name ?? ('Attachment ' . ($index + 1))) : ('Attachment ' . ($index + 1)));
                                    $size = is_array($file) ? ($file['size'] ?? null) : (is_object($file) ? ($file->size ?? null) : null);
                                    $url = null;

                                    if (!empty($path)) {
                                        $url = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
                                            ? $path
                                            : asset('storage/' . ltrim($path, '/'));
                                    }
                                @endphp

                                @if($url)
                                    <div class="border rounded p-2 d-flex justify-content-between align-items-center gap-2 bg-body">
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-break">
                                            {{ $name }}
                                        </a>
                                        @if(is_numeric($size))
                                            <span class="badge text-bg-light">{{ number_format($size / 1024, 1) }} KB</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">No attachments uploaded for this enquiry.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
