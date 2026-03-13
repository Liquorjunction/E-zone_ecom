
<script src="{{ asset('admin_assets/js/core/libs.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/core/external.min.js') }}"></script>
<script src="{{ asset('admin_assets/js/charts/widgetcharts.js') }}"></script>
<script src="{{ asset('admin_assets/js/charts/vectore-chart.js') }}"></script>
<script src="{{ asset('admin_assets/js/plugins/fslightbox.js') }}"></script>
<script src="{{ asset('admin_assets/js/plugins/setting.js') }}"></script>
<script src="{{ asset('admin_assets/js/plugins/slider-tabs.js') }}"></script>
<script src="{{ asset('admin_assets/js/plugins/form-wizard.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/aos/dist/aos.js') }}"></script>
<script src="{{ asset('admin_assets/js/hope-ui.js') }}" defer></script>
<script src="{{ asset('admin_assets/js/translationHandler.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('admin_assets/js/datatable-status-toggle.js') }}"></script>
<script src="{{ asset('admin_assets/js/datatable-action.js') }}"></script>
<script src="{{ asset('admin_assets/js/datatable-orderStatus.js') }}"></script>
<script src="{{ asset('admin_assets/js/sidebar.js') }}"></script>
<script src="{{ asset('admin_assets/js/datatableJs/batchOperations.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery and DataTables JS (jQuery must come before Select2) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="{{ asset('admin_assets/js/toast-handler.js') }}"></script>
<script src="{{ asset('admin_assets/js/delete-handler.js') }}"></script>

{{-- Session Messages Handler --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show session flash messages if they exist
        @if (session('success'))
            if (window.showToast) {
                window.showToast("{{ session('success') }}", 'success', 'Success');
            }
        @endif
        @if (session('error'))
            if (window.showToast) {
                window.showToast("{{ session('error') }}", 'error', 'Error');
            }
        @endif
        @if (session('warning'))
            if (window.showToast) {
                window.showToast("{{ session('warning') }}", 'warning', 'Warning');
            }
        @endif
        @if (session('info'))
            if (window.showToast) {
                window.showToast("{{ session('info') }}", 'info', 'Info');
            }
        @endif
    });
</script>
