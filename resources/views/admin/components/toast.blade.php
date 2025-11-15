{{-- Custom Toast Component --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="globalToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body bg-dark text-white">
            <div id="toastMessage">Hello, world! This is a toast message.</div>
            <div class="mt-2 pt-2 border-top border-secondary">
                <button type="button" class="btn btn-primary btn-sm me-2" id="toastActionBtn" style="display: none;">Take action</button>
                <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="toast">Close</button>
            </div>
        </div>
    </div>
</div>
