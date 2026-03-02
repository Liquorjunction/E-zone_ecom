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

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function showLoading(title, text) {
        Swal.fire({
            title,
            text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
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
                    'X-CSRF-TOKEN': getCsrfToken(),
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

            showLoading('Updating...', 'Please wait while we update the settings');

            try {
                const response = await fetch(`/admin/site-settings?id=${siteId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
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

    function toggleDayInputs(row, isClosed) {
        const timeInputs = row.querySelectorAll('.day-time-input');
        timeInputs.forEach((input) => {
            input.disabled = isClosed;
            if (isClosed) {
                input.value = '';
            }
        });
    }

    function buildOpeningHoursJson(form) {
        const rows = form.querySelectorAll('tbody tr[data-day]');
        const openingHours = {};

        for (const row of rows) {
            const day = row.dataset.day;
            const openInput = row.querySelector('[data-field="open"]');
            const closeInput = row.querySelector('[data-field="close"]');
            const closedCheckbox = row.querySelector('.day-closed-checkbox');

            const isClosed = closedCheckbox ? closedCheckbox.checked : false;
            const open = openInput ? openInput.value : '';
            const close = closeInput ? closeInput.value : '';

            if (isClosed) {
                openingHours[day] = {
                    is_closed: true,
                    open: null,
                    close: null
                };
                continue;
            }

            if (!open || !close) {
                throw new Error(`Please set both open and close time for ${day}.`);
            }

            openingHours[day] = {
                is_closed: false,
                open,
                close
            };
        }

        return JSON.stringify(openingHours);
    }

    function initOpeningHoursForm() {
        const form = document.getElementById('openingHoursForm');
        if (!form) return;

        const rows = form.querySelectorAll('tbody tr[data-day]');
        rows.forEach((row) => {
            const checkbox = row.querySelector('.day-closed-checkbox');
            if (!checkbox) return;
            checkbox.addEventListener('change', function() {
                toggleDayInputs(row, this.checked);
            });
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const siteId = this.dataset.siteId;
            const hiddenOpeningHours = document.getElementById('opening_hours');

            let openingHoursJson = '';
            try {
                openingHoursJson = buildOpeningHoursJson(this);
            } catch (validationError) {
                showAlert('error', validationError.message);
                return;
            }

            if (hiddenOpeningHours) {
                hiddenOpeningHours.value = openingHoursJson;
            }

            showLoading('Updating...', 'Saving opening hours');

            try {
                const response = await fetch(`/admin/site-settings/opening-hours?id=${siteId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ opening_hours: openingHoursJson })
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
                        text: result.message || 'Failed to update opening hours'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update opening hours. Please try again.'
                });
            }
        });
    }

    function initMapIframeForm() {
        const form = document.getElementById('mapIframeForm');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const siteId = this.dataset.siteId;
            const mapIframeElement = document.getElementById('map_iframe');
            const mapIframe = mapIframeElement ? mapIframeElement.value.trim() : '';

            if (!mapIframe) {
                showAlert('error', 'Map iframe is required.');
                return;
            }

            showLoading('Updating...', 'Saving map iframe');

            try {
                const response = await fetch(`/admin/site-settings/add-mapIframe?id=${siteId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ map_iframe: mapIframe })
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
                        text: result.message || 'Failed to update map iframe'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update map iframe. Please try again.'
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

        showLoading('Creating...', 'Setting up initial site settings');

        try {
            const response = await fetch('/admin/site-settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
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
        document.addEventListener('DOMContentLoaded', function() {
            initSiteSettingsForm();
            initOpeningHoursForm();
            initMapIframeForm();
        });
    } else {
        initSiteSettingsForm();
        initOpeningHoursForm();
        initMapIframeForm();
    }

})();
