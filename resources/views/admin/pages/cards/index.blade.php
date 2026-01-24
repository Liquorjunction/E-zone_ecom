@extends('admin.layouts.app')

@section('title', 'Cards')
@section('header-title', 'Cards Management')
@section('header-description', 'Create and manage promotional cards displayed on the homepage')
@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.about_us.create') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.cards.index') as $breadcrumb)
        @if ($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Cards</h4>
                        @can('card.create')
                            <div>
                                <a href="{{ route('admin.cards.create') }}" class="btn btn-primary">Add Card</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @php
                                $hasActions = auth()->user()->can('card.update') || auth()->user()->can('card.delete');
                            @endphp
                            <table id="cards-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.cards.data') }}"
                                data-has-actions="{{ $hasActions ? 1 : 0 }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        @canany(['card.update', 'card.delete'])
                                            <th>Action</th>
                                        @endcanany
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/datatableJs/cards.js') }}"></script>
@endpush
