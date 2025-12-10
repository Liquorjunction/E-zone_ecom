@extends('admin.layouts.app')
@section('title', 'Products Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">All Products</h4>
                        @can('products.create')
                            <div>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fa fa-file-excel"></i> Import
                                </button>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Product
                                </a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="product-table" class="table table-striped table-bordered w-100"
                                data-route="{{ route('admin.products.data') }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Bestseller</th>
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

    {{-- 1. IMPORT MODAL --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="btn-close-modal"></button>
                </div>

                <form id="importForm" action="{{ route('admin.products.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {{-- File Input Section --}}
                        <div class="mb-3">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Choose File (CSV/Excel)</label>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-secondary"
                                        onclick="window.location.href='{{ route('admin.products.template') }}'">
                                        <i class="fa fa-download"></i> Download Sample
                                    </button>
                                </div>
                            </div>

                            <input type="file" name="file" class="form-control" id="importFile" required>
                        </div>

                        {{-- Translation Option --}}
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="skip_translation" id="skipTranslation" value="1">
                                <label class="form-check-label" for="skipTranslation">
                                    <strong>Skip Auto-Translation (Faster Import)</strong>
                                    <br>
                                    <small class="text-muted">If checked, products will not be auto-translated to French/Arabic. This speeds up import by 5-10x.</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="btnStartImport">
                            <i class="fa fa-upload"></i> Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Include new advanced import progress notification styles and script --}}
        <link rel="stylesheet" href="{{ asset('admin_assets/css/import-progress-notification.css') }}">
        <script src="{{ asset('admin_assets/js/import-progress-notification.js') }}"></script>
        <script src="{{ asset('admin_assets/js/import-detailed-report.js') }}"></script>
        {{-- DataTable and form submission handler --}}
        <script src="{{ asset('admin_assets/js/datatableJs/products.js') }}"></script>
    @endpush
@endsection
