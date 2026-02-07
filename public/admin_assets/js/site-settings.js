(function() {
    'use strict';

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    function showAlert(type, message) {
        Toast.fire({
            icon: type === 'error' ? 'error' : type,
            title: message
        });
    }

    window.toggleSiteStatus = async function(newStatus) {
        const form = document.getElementById('siteSettingsForm');
        if (!form) {
            showAlert('error', 'Form not found');
            return;
        }

        const siteId = form.dataset.siteId;
        const statusText = newStatus === 'inactive' ? 'disable' : 'enable';
        const modeText = newStatus === 'inactive' ? 'maintenance mode' : 'active';

        const result = await Swal.fire({
            title: 'Are you sure?',
            html: `Do you want to ${statusText} the site?<br><small class="text-muted">Site will be set to ${modeText}</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: newStatus === 'inactive' ? '#f39c12' : '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${statusText} it!`,
            cancelButtonText: 'Cancel',
            reverseButtons: true
        });

        if (!result.isConfirmed) {
            return;
        }

        Swal.fire({
            title: 'Processing...',
            text: 'Updating site status',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(`/admin/site-settings/toggle-status?id=${siteId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                window.location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to update site status'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to update site status. Please try again.'
            });
        }
    };

    function initSiteSettingsForm() {
        const form = document.getElementById('siteSettingsForm');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const siteId = this.dataset.siteId;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            delete data._method;
            delete data._token;

            Swal.fire({
                title: 'Updating...',
                text: 'Please wait while we update the settings',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`/admin/site-settings?id=${siteId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message || 'Failed to update settings'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update settings. Please try again.'
                });
            }
        });
    }

    window.createInitialSettings = async function() {
        const result = await Swal.fire({
            title: 'Create Site Settings',
            text: 'This will create initial site settings with default values.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, create!',
            cancelButtonText: 'Cancel'
        });

        if (!result.isConfirmed) {
            return;
        }

        const data = {
            name: 'E-Zone',
            email: 'info@e-zone.com',
            phone: '',
            address: '',
            facebook: '',
            twitter: '',
            instagram: '',
            infotext: '',
            site_status: 'active'
        };

        Swal.fire({
            title: 'Creating...',
            text: 'Setting up initial site settings',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch('/admin/site-settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                window.location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: result.message || 'Failed to create settings'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to create settings. Please try again.'
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSiteSettingsForm);
    } else {
        initSiteSettingsForm();
    }

})();
