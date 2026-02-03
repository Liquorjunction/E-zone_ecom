@extends('admin.layouts.app')
@section('title', 'Review Details')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Review Details</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td>{{ $review->id }}</td>
                </tr>
                <tr>
                    <th>Product</th>
                    <td>
                        @if($review->product)
                            {{ $review->product->getTranslation('name', 'en') }}
                            @if($review->productVariant)
                                - {{ $review->productVariant->getTranslation('variant_name', 'en') }}
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>User</th>
                    <td>
                        @if($review->user)
                            {{ $review->user->name }}
                        @else
                            {{ $review->reviewer_name ?? 'N/A' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Order</th>
                    <td>
                        @if($review->order)
                            {{ $review->order->order_number }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Rating</th>
                    <td>{{ $review->rating }} / 5</td>
                </tr>
                <tr>
                    <th>Review Text</th>
                    <td>{{ $review->review_text }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ ucfirst($review->is_approved) }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $review->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $review->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            </table>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary mt-3">Back to Reviews</a>
        </div>
    </div>
</div>
@endsection
