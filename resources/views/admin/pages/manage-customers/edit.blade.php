@extends('admin.layouts.app')
@section('title', 'Edit Customer')
@section('header-title', 'Edit Customer')
@section('header-description', 'Update customer information and status')
@section('breadcrumbs')
    @foreach (Breadcrumbs::generate('admin.customers.edit', $customer) as $breadcrumb)
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
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Edit Customer</h4>
                    <p class="text-muted mb-0">Update customer information and status</p>
                </div>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-soft-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Customers
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the errors below.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-soft-primary border-0">
                        <h5 class="mb-0"><i class="bi bi-person text-primary me-2"></i>Customer Details</h5>
                    </div>
                    <div class="card-body p-4">
                        @include('admin.pages.manage-customers.form')
                    </div>
                    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-soft-secondary">
                            <i class="bi bi-x me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check me-2"></i>Update Customer
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                @if(isset($customer->created_at))
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-soft-secondary border-0">
                        <h6 class="mb-0"><i class="bi bi-info-circle text-secondary me-2"></i>Account Info</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-2">
                            <small class="text-muted d-block">Registered</small>
                            <span class="fw-semibold">{{ $customer->created_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Last Updated</small>
                            <span class="fw-semibold">{{ $customer->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
