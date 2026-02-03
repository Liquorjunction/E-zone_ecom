@extends('admin.layouts.app')

@section('title', 'Reviews')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Reviews</h3>
        </div>
        <div class="card-body">
            <table id="reviews-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>User</th>
                        <th>Order</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Approval Actions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const reviewDataUrl = '{{ route('admin.reviews.data') }}';
    const csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/admin/reviews.js') }}"></script>
@endpush
