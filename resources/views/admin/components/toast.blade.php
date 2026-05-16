{{-- Custom Toast Component --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1055;">
    <div id="globalToast"
        class="toast border-0 shadow-sm rounded-2"
        role="alert"
        aria-live="assertive"
        aria-atomic="true">

        <div class="toast-header bg-primary text-white border-0 rounded-top-2">
            <strong class="me-auto">Notification</strong>
            <button type="button"
                class="btn-close btn-close-white ms-2"
                data-bs-dismiss="toast"
                aria-label="Close">
            </button>
        </div>

        <div class="toast-body bg-white text-dark">
            <p class="mb-2" id="toastMessage">
                Hello, world! This is a toast message.
            </p>

            <div class="d-flex justify-content-end gap-2">
                <button type="button"
                    class="btn btn-primary btn-sm"
                    id="toastActionBtn"
                    style="display:none;">
                    Take action
                </button>
            </div>
        </div>
    </div>
</div>
