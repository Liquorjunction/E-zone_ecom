<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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
<script src="{{ asset('admin_assets/js/datatable-status-toggle.js') }}"></script>
<script src="{{ asset('admin_assets/js/sidebar-hover.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

{{-- Toast Functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global function to show custom toast
    window.showToast = function(message, type = 'info', title = 'Notification', actionCallback = null) {
        const toastElement = document.getElementById('globalToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastActionBtn = document.getElementById('toastActionBtn');

        // Set message
        toastMessage.textContent = message;

        // Set background color based on type
        toastElement.className = 'toast fade show';
        if (type === 'success') {
            toastElement.classList.add('bg-success', 'text-white');
        } else if (type === 'error') {
            toastElement.classList.add('bg-danger', 'text-white');
        } else if (type === 'warning') {
            toastElement.classList.add('bg-warning');
        } else {
            toastElement.classList.add('bg-info', 'text-white');
        }

        // Handle action button
        if (actionCallback) {
            toastActionBtn.style.display = 'inline-block';
            toastActionBtn.textContent = 'Take action';
            toastActionBtn.onclick = actionCallback;
        } else {
            toastActionBtn.style.display = 'none';
        }

        // Show the toast
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    };

    // Automatically show toasts for flashed session messages
    @if(session('success'))
        showToast("{{ session('success') }}", 'success', 'Success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error', 'Error');
    @endif
    @if(session('warning'))
        showToast("{{ session('warning') }}", 'warning', 'Warning');
    @endif
    @if(session('info'))
        showToast("{{ session('info') }}", 'info', 'Info');
    @endif
});
</script>
