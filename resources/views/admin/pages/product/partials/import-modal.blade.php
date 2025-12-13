<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="importForm" action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    {{-- File Input & Download Template --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="importFile" class="form-label mb-0 fw-bold">Choose File</label>
                            <a href="{{ route('admin.products.template') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Download Sample
                            </a>
                        </div>

                        <input class="form-control" type="file" id="importFile" name="file" accept=".csv, .xlsx, .xls" required>
                        <div class="form-text text-muted small mt-1">
                            Supported formats: CSV, XLSX. Max size: 10MB.
                        </div>
                    </div>

                    {{-- Options --}}
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="skipTranslation" name="skip_translation">
                            <label class="form-check-label" for="skipTranslation">
                                <strong>Skip Auto-Translation (Fast Mode)</strong>
                            </label>
                            <p class="text-muted small mb-0 mt-1">
                                If checked, products will NOT be translated to French/Arabic automatically.
                                This makes the import process <strong>5x - 10x faster</strong>.
                                Recommended for large files.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="btnStartImport">
                        <i class="fas fa-file-import me-1"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
