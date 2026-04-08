@extends('admin.layouts.app')
@section('title', 'Contact Us Enquiries')
@section('header-title', 'Contact Us Enquiries Management')
@section('header-description', 'View and manage contact us enquiries submitted by users')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.contact-us.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 bg-white rounded-3 p-3">
            <div>
                <h4 class="fw-semibold mb-1">Contact Us Enquiries</h4>
                <p class="text-secondary small mb-0">View and manage contact us enquiries submitted by users</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                    data-bs-target="#filter-panel" aria-expanded="false" aria-controls="filter-panel">
                    <i class="fas fa-filter me-1"></i> Filters
                </button>
            </div>
        </div>

        {{-- Collapsible Filter Panel --}}
        <div class="collapse mb-4" id="filter-panel">
            <div class="card border-0 shadow-sm rounded-3">
                <div
                    class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4 rounded-top-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h text-primary"></i>
                        <span class="fw-semibold text-dark small">Filter Contact Us Enquiries</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#filter-panel"
                        aria-label="Close filters">
                    </button>
                </div>
                <div class="card-body p-4">
                    <form id="contact-us-filter-bar" method="GET">
                        <div class="d-flex flex-wrap align-items-start" style="gap:3rem;">
                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Status</div>
                                <div class="d-flex gap-3">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="status" value="all" id="status_all" checked>
                                        <label class="form-check-label small text-dark" for="status_all">All</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="status" value="open" id="status_open">
                                        <label class="form-check-label small text-dark" for="status_open">Open</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="status" value="in_progress" id="status_in_progress">
                                        <label class="form-check-label small text-dark" for="status_in_progress">In Progress</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="status" value="resolved" id="status_resolved">
                                        <label class="form-check-label small text-dark" for="status_resolved">Resolved</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="radio" name="status" value="closed" id="status_closed">
                                        <label class="form-check-label small text-dark" for="status_closed">Closed</label>
                                    </div>
                                </div>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Subject</div>
                                <select class="form-select form-select-sm" id="subject" name="subject"
                                    data-list-url="{{ route('admin.contact-us.subject-list') }}" data-loaded="0" style="width:220px;">
                                    <option value="">All Subjects</option>
                                </select>
                            </div>

                            <div class="vr opacity-25 d-none d-md-block align-self-stretch"></div>

                            <div>
                                <div class="text-uppercase text-muted fw-semibold mb-2"
                                    style="font-size:0.65rem;letter-spacing:0.08em;">Date Range</div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" style="width:145px;">
                                    <span class="text-muted small">—</span>
                                    <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" style="width:145px;">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-sm btn-primary" id="apply-filters-btn">
                                <i class="fas fa-search me-1"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="reset-filters-btn">
                                <i class="fas fa-eraser me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                <table id="contactUsTable" class="table table-striped table-hover w-100"
                    data-source="{{ route('admin.contact-us.data') }}">
                    <thead class="bg-light text-uppercase small">
                        <tr>
                            <th>#</th>
                            <th>Ticket Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/contactUs.js') }}"></script>
        <script src="{{ asset('admin_assets/js/contactUsFilterBar.js') }}"></script>
    @endpush
