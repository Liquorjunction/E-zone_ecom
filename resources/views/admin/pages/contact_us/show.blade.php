@extends('admin.layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="h3 mb-0">Contact Us Enquiry Details</h1>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Ticket Number</dt>
                <dd class="col-sm-9">{{ $enquiry->ticket_number ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $enquiry->name ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $enquiry->email ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Subject</dt>
                <dd class="col-sm-9">{{ $enquiry->subject ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @include('admin.partials.datatable-enquiryStatus', [
                        'enquiry' => $enquiry,
                        'statusLabels' => [
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            'closed' => 'Closed',
                        ],
                        'availableStatuses' => ['open', 'in_progress', 'resolved', 'closed'],
                        'canUpdate' => auth()->user()->can('contact_us.update'),
                    ])
                </dd>
                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $enquiry->created_at ? $enquiry->created_at->format('Y-m-d H:i:s') : 'N/A' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
