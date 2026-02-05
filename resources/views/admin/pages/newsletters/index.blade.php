@extends('admin.layouts.app')
@section('title', 'Manage Newsletters')

@section('header-title', 'Newsletter Management')
@section('header-description', 'Manage the newsletters shown across the site')

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.newsletters.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Newsletter List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @php
                    $hasAction = auth()->user()->can('newsletter.update') || auth()->user()->can('newsletter.delete');
                @endphp
                <table id="newsletters-table" class="table table-striped" role="grid"
                    data-url="{{ route('admin.newsletter-subscribers.data') }}" data-has-action="{{ $hasAction ? 1 : 0 }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Subscribed At</th>
                            <th>Status</th>
                            @if ($hasAction)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/newsletters.js') }}"></script>
    @endpush
@endsection
