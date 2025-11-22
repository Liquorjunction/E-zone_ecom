@extends('admin.layouts.app')

@section('title', 'Cards')

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
                            <table id="cards-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.cards.data') }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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
