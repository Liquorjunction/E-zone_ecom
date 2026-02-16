<!-- Dispatch Create Popup Modal -->
<div class="modal fade" id="dispatchModal" tabindex="-1" aria-labelledby="dispatchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dispatchModalLabel">Create Dispatch</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="dispatchForm" method="POST" action="{{ route('admin.dispatch.store') }}">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="dispatch_order_id" name="order_id" value="">
          <div class="mb-3">
            <label class="form-label">Order Items to Dispatch</label>
            <div id="dispatch-items-table-wrapper">
              <table class="table table-bordered align-middle" id="dispatch-items-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Ordered Qty</th>
                    <th>Dispatch Qty</th>
                    <th>Inventory Location</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Rows will be populated by JS -->
                </tbody>
              </table>
            </div>
          </div>
          <div class="mb-3">
            <label for="dispatch_method" class="form-label">Dispatch Method</label>
            <select class="form-select" id="dispatch_method" name="dispatch_method">
              <option value="courier">Courier</option>
              <option value="pickup">Pickup</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create Dispatch</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('admin_assets/js/dispatch-create.js') }}"></script>
@endpush
