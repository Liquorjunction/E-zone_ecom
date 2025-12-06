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

    {{-- Import Modal --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Products (Excel/CSV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Added ID to form for JS targeting --}}
                <form id="importForm" action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Choose File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.products.template') }}"
                                class="text-primary text-decoration-underline">Download Sample Template</a>
                        </div>
                        <div class="alert alert-warning small">
                            <strong>Note:</strong> IDs for Brands and Categories must exist in the database.
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Buttons Container --}}
                        <div id="import-buttons" class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-upload"></i> Upload & Import
                            </button>
                        </div>

                        {{-- Progress Bar Container (Hidden by default) --}}
                        <div id="import-progress" class="w-100 d-none">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small" id="progress-label">Uploading...</span>
                                <span class="text-muted small" id="progress-percent">0%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div id="progress-bar-fill"
                                     class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                     role="progressbar"
                                     style="width: 0%"
                                     aria-valuenow="0"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-danger mt-1 d-none" id="import-error-msg"></small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Import Status Popup (Triggered by Session) --}}
    @if(session('import_status'))
        <div class="modal fade" id="importStatusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header {{ session('import_status')['failed'] > 0 ? 'bg-warning' : 'bg-success' }} text-white">
                        <h5 class="modal-title">Import Summary</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row text-center mb-4">
                            <div class="col-6 border-end">
                                <h3 class="text-success">{{ session('import_status')['success'] }}</h3>
                                <span class="text-muted">Successful</span>
                            </div>
                            <div class="col-6">
                                <h3 class="text-danger">{{ session('import_status')['failed'] }}</h3>
                                <span class="text-muted">Failed</span>
                            </div>
                        </div>

                        @if(count(session('import_status')['errors']) > 0)
                            <div class="alert alert-danger">
                                <h6>Error Log:</h6>
                                <ul class="mb-0 small" style="max-height: 200px; overflow-y: auto;">
                                    @foreach(session('import_status')['errors'] as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="text-center text-success">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <p>All products were imported successfully!</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script type="module">
            document.addEventListener('DOMContentLoaded', function () {
                var myModal = new bootstrap.Modal(document.getElementById('importStatusModal'));
                myModal.show();
            });
        </script>
    @endif

    @push('scripts')
        <script src="{{ asset('admin_assets/js/datatableJs/products.js') }}"></script>
    @endpush
@endsection
