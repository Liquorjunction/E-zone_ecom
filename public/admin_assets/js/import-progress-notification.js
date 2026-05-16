/**
 * Advanced Import Progress Notification System
 * Features:
 * - Pop-in notification in bottom-right corner
 * - Non-blocking (users can continue working)
 * - Multiple concurrent imports support
 * - Real-time progress updates
 * - Collapsible/expandable interface
 * - Auto-hide on completion
 * - Persistent storage of import history
 */

class ImportProgressNotification {
    constructor(batchId, fileName = 'Import', isRestored = false) {
        this.batchId = batchId;
        this.fileName = fileName;
        this.isPolling = false;
        this.pollingInterval = null;
        this.isCollapsed = false;
        this.container = null;
        this.lastProgressData = null;
        this.isRestored = isRestored;

        this.init();
    }

    init() {
        // Create main container
        this.container = this.createContainer();
        document.body.appendChild(this.container);

        // Animate in
        setTimeout(() => {
            this.container.classList.add('show');
        }, 100);

        // Restore previous progress if this is a restored notification
        if (this.isRestored) {
            const savedData = this.getSavedProgress();
            if (savedData) {
                this.updateProgress(savedData);
            }
        }

        // Start polling
        this.startPolling();
    }

    // Save progress to localStorage for persistence
    saveProgress(data) {
        try {
            const storageKey = `import_progress_${this.batchId}`;
            const progressData = {
                batchId: this.batchId,
                fileName: this.fileName,
                timestamp: Date.now(),
                ...data
            };
            localStorage.setItem(storageKey, JSON.stringify(progressData));
            this.lastProgressData = progressData;
        } catch (e) {
            console.warn('Failed to save progress to localStorage:', e);
        }
    }

    // Retrieve saved progress from localStorage
    getSavedProgress() {
        try {
            const storageKey = `import_progress_${this.batchId}`;
            const saved = localStorage.getItem(storageKey);
            return saved ? JSON.parse(saved) : null;
        } catch (e) {
            console.warn('Failed to retrieve progress from localStorage:', e);
            return null;
        }
    }

    // Clean up saved progress after completion or cancellation
    clearSavedProgress() {
        try {
            const storageKey = `import_progress_${this.batchId}`;
            localStorage.removeItem(storageKey);
            this.lastProgressData = null;
        } catch (e) {
            console.warn('Failed to clear saved progress:', e);
        }
    }

    // Get all active import sessions from storage
    static getActiveImportSessions() {
        try {
            const sessions = [];
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key && key.startsWith('import_progress_')) {
                    const data = JSON.parse(localStorage.getItem(key));
                    // Only include active sessions (not completed/failed)
                    if (data && data.status !== 'completed' && data.status !== 'failed') {
                        sessions.push(data);
                    }
                }
            }
            return sessions;
        } catch (e) {
            console.warn('Failed to retrieve active import sessions:', e);
            return [];
        }
    }

    createContainer() {
        const container = document.createElement('div');
        container.className = 'import-notification-wrapper';
        container.id = `import-notification-${this.batchId}`;
        container.innerHTML = `
            <!-- Floating Notification Container -->
            <div class="import-notification-box">
                <!-- Header with controls -->
                <div class="notification-header">
                    <div class="header-left">
                        <button class="btn-collapse" title="Toggle">
                            <i class="bi bi-dash"></i>
                        </button>
                        <div class="header-title">
                            <h6 class="mb-0">
                                <i class="bi bi-file-import text-primary"></i>
                                ${this.escapeHtml(this.fileName)}
                            </h6>
                            <small class="text-muted">Batch ID: ${this.batchId.substring(0, 8)}</small>
                        </div>
                    </div>
                    <button class="btn-close" title="Close">
                        <i class="bi bi-x"></i>
                    </button>
                </div>

                <!-- Progress Section (shown when expanded) -->
                <div class="notification-body">
                    <!-- Status Badge -->
                    <div class="status-badge">
                        <span class="badge-text">
                            <i class="bi bi-spinner bi-spin"></i>
                            <span class="status-label">Processing</span>
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress-container">
                        <div class="progress-info">
                            <span class="progress-label">Progress</span>
                            <span class="progress-percent">0%</span>
                        </div>
                        <div class="progress-bar-wrapper">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                     style="width: 0%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="details-section">
                        <div class="detail-row">
                            <span class="detail-label">Processed:</span>
                            <span class="detail-value processed">0/0</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Success:</span>
                            <span class="detail-value success" style="color: #28a745;">0</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Failed:</span>
                            <span class="detail-value failed" style="color: #dc3545;">0</span>
                        </div>
                    </div>

                    <!-- Estimated Time -->
                    <div class="estimated-time">
                        <small class="text-muted">
                            <i class="bi bi-clock"></i>
                            <span class="time-text">Calculating...</span>
                        </small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="notification-actions">
                        <button class="btn btn-sm btn-outline-secondary btn-view-details" title="View Details">
                            <i class="bi bi-list"></i> Details
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-cancel" title="Cancel Import">
                            <i class="bi bi-ban"></i> Cancel
                        </button>
                    </div>
                </div>

                <!-- Collapsed View -->
                <div class="notification-collapsed">
                    <div class="collapse-progress">
                        <div class="mini-progress-bar">
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: 0%"></div>
                            </div>
                        </div>
                        <span class="collapse-percent">0%</span>
                    </div>
                </div>
            </div>

            <!-- Floating Action Menu -->
            <div class="floating-menu">
                <button class="btn-expand" title="Expand">
                    <i class="bi bi-chevron-up"></i>
                </button>
            </div>
        `;

        // Add event listeners
        this.attachEventListeners(container);

        return container;
    }

    attachEventListeners(container) {
        const collapseBtn = container.querySelector('.btn-collapse');
        const closeBtn = container.querySelector('.btn-close');
        const expandBtn = container.querySelector('.btn-expand');
        const cancelBtn = container.querySelector('.btn-cancel');
        const detailsBtn = container.querySelector('.btn-view-details');

        collapseBtn?.addEventListener('click', () => this.toggle());
        expandBtn?.addEventListener('click', () => this.expand());
        closeBtn?.addEventListener('click', () => this.close());
        cancelBtn?.addEventListener('click', () => this.cancel());
        detailsBtn?.addEventListener('click', () => this.showDetailsModal());
    }

    startPolling() {
        if (this.isPolling) return;

        this.isPolling = true;
        this.pollingInterval = setInterval(() => {
            this.fetchProgress();
        }, 1500); // Poll every 1.5 seconds
    }

    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        this.isPolling = false;
    }

    fetchProgress() {
        axios.get(`/admin/products/import/progress/${this.batchId}`)
            .then(res => {
                const data = res.data;
                // Save progress to localStorage
                this.saveProgress(data);
                this.updateProgress(data);

                if (data.status === 'completed') {
                    this.onComplete(data);
                } else if (data.status === 'failed') {
                    this.onFailed(data);
                }
            })
            .catch(err => {
                console.error('Progress polling error:', err);
            });
    }

    updateProgress(data) {
        const progressBar = this.container.querySelector('.progress-bar');
        const miniProgressBar = this.container.querySelector('.mini-progress-bar .progress-bar');
        const progressPercent = this.container.querySelector('.progress-percent');
        const collapsePercent = this.container.querySelector('.collapse-percent');
        const processedText = this.container.querySelector('.detail-value.processed');
        const successText = this.container.querySelector('.detail-value.success');
        const failedText = this.container.querySelector('.detail-value.failed');
        const timeText = this.container.querySelector('.time-text');
        const statusLabel = this.container.querySelector('.status-label');

        // Update progress bar (prefer percent, fallback to progress)
        const percentage = (typeof data.percent === 'number' ? data.percent : data.progress) || 0;
        progressBar.style.width = percentage + '%';
        miniProgressBar.style.width = percentage + '%';
        progressPercent.textContent = percentage + '%';
        collapsePercent.textContent = percentage + '%';

        // Update counters
        processedText.textContent = `${data.processed || 0}/${data.total || 0}`;
        successText.textContent = data.success || 0;
        failedText.textContent = data.failed || 0;

        // Update status
        if (data.status === 'processing') {
            statusLabel.textContent = 'Processing';
            const statusBadge = this.container.querySelector('.badge-text');
            if (statusBadge) {
                statusBadge.className = 'badge-text processing';
            }
        }

        // Estimate time remaining
        if (data.total > 0 && data.processed > 0) {
            const itemsPerSecond = data.processed / 1.5; // Rough estimate
            const remaining = data.total - data.processed;
            const secondsLeft = Math.ceil(remaining / itemsPerSecond);
            timeText.textContent = this.formatSeconds(secondsLeft) + ' remaining';
        }
    }

    onComplete(data) {
        this.stopPolling();
        // Clear saved progress after completion
        this.clearSavedProgress();

        const progressBar = this.container.querySelector('.progress-bar');
        const statusBadge = this.container.querySelector('.badge-text');
        const statusLabel = this.container.querySelector('.status-label');
        const progressPercent = this.container.querySelector('.progress-percent');
        const timeText = this.container.querySelector('.time-text');
        const actionsDiv = this.container.querySelector('.notification-actions');

        // Update UI
        progressBar.style.width = '100%';
        progressBar.classList.remove('progress-bar-animated');
        progressPercent.textContent = '100%';

        if (statusBadge) {
            statusBadge.className = 'badge-text completed';
            statusBadge.innerHTML = '<i class="bi bi-check"></i><span class="status-label">Completed</span>';
        }

        statusLabel.textContent = 'Completed';
        timeText.textContent = 'Import finished';

        // Replace action buttons with report button
        if (actionsDiv) {
            actionsDiv.innerHTML = `
                <button class="btn btn-sm btn-success btn-view-report" title="View Detailed Report">
                    <i class="bi bi-file-pdf"></i> View Report
                </button>
                <button class="btn btn-sm btn-outline-secondary btn-close-notif" title="Close">
                    <i class="bi bi-x"></i> Close
                </button>
            `;

            const reportBtn = actionsDiv.querySelector('.btn-view-report');
            const closeBtn = actionsDiv.querySelector('.btn-close-notif');

            reportBtn?.addEventListener('click', () => this.showDetailedReport(data));
            closeBtn?.addEventListener('click', () => this.close());
        }

        // Show notification toast
        this.showToast('Import completed successfully!', 'success');

        // Don't auto-close, keep notification visible with report button
        // User can click report or close button
    }

    onFailed(data) {
        this.stopPolling();
        // Clear saved progress after failure
        this.clearSavedProgress();

        const progressBar = this.container.querySelector('.progress-bar');
        const statusBadge = this.container.querySelector('.badge-text');
        const statusLabel = this.container.querySelector('.status-label');

        // Update UI to show error
        progressBar.classList.remove('bg-primary', 'progress-bar-animated');
        progressBar.classList.add('bg-danger');

        if (statusBadge) {
            statusBadge.className = 'badge-text failed';
            statusBadge.innerHTML = '<i class="bi bi-exclamation-circle"></i><span class="status-label">Failed</span>';
        }

        statusLabel.textContent = 'Failed';

        // Show error notification
        const errorMsg = data.errors && data.errors.length > 0 ? data.errors[0] : 'Unknown error';
        this.showToast('Import failed: ' + errorMsg, 'error');
    }

    toggle() {
        if (this.isCollapsed) {
            this.expand();
        } else {
            this.collapse();
        }
    }

    collapse() {
        this.isCollapsed = true;
        const body = this.container.querySelector('.notification-body');
        const collapsed = this.container.querySelector('.notification-collapsed');
        const collapseBtn = this.container.querySelector('.btn-collapse i');

        body.style.display = 'none';
        collapsed.style.display = 'flex';
        collapseBtn.className = 'bi bi-plus';

        this.container.classList.add('collapsed');
    }

    expand() {
        this.isCollapsed = false;
        const body = this.container.querySelector('.notification-body');
        const collapsed = this.container.querySelector('.notification-collapsed');
        const collapseBtn = this.container.querySelector('.btn-collapse i');

        body.style.display = 'block';
        collapsed.style.display = 'none';
        collapseBtn.className = 'bi bi-minus';

        this.container.classList.remove('collapsed');
    }

    close() {
        this.stopPolling();
        this.container.classList.remove('show');

        setTimeout(() => {
            if (this.container && this.container.parentNode) {
                this.container.parentNode.removeChild(this.container);
            }
        }, 300);
    }

    cancel() {
        if (confirm('Are you sure you want to cancel this import?')) {
            this.stopPolling();
            // Clear saved progress on cancel
            this.clearSavedProgress();

            axios.post(`/admin/products/import/cancel/${this.batchId}`)
                .then(() => {
                    this.showToast('Import cancelled', 'warning');
                    this.close();
                })
                .catch(err => {
                    this.showToast('Failed to cancel import', 'error');
                });
        }
    }

    showDetailsModal() {
        // Get current data
        const processed = this.container.querySelector('.detail-value.processed').textContent;
        const success = this.container.querySelector('.detail-value.success').textContent;
        const failed = this.container.querySelector('.detail-value.failed').textContent;
        const progress = this.container.querySelector('.progress-percent').textContent;

        // Create and show modal
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = `details-modal-${this.batchId}`;
        modal.setAttribute('tabindex', '-1');
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Import Details - ${this.escapeHtml(this.fileName)}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row text-center mb-4">
                            <div class="col-3">
                                <h4 class="text-primary">${progress}</h4>
                                <small class="text-muted">Overall Progress</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-success">${success}</h4>
                                <small class="text-muted">Successful</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-danger">${failed}</h4>
                                <small class="text-muted">Failed</small>
                            </div>
                            <div class="col-3">
                                <h4>${processed}</h4>
                                <small class="text-muted">Total Processed</small>
                            </div>
                        </div>
                        <hr>
                        <div id="error-log" style="max-height: 300px; overflow-y: auto;">
                            <p class="text-muted text-center">Loading error details...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Fetch and display errors
        axios.get(`/admin/products/import/errors/${this.batchId}`)
            .then(res => {
                const errorLog = modal.querySelector('#error-log');
                if (res.data.errors && res.data.errors.length > 0) {
                    errorLog.innerHTML = `
                        <ul class="list-unstyled">
                            ${res.data.errors.map(err => `<li class="mb-2"><small class="text-danger">• ${this.escapeHtml(err)}</small></li>`).join('')}
                        </ul>
                    `;
                } else {
                    errorLog.innerHTML = '<p class="text-success text-center">No errors reported!</p>';
                }
            })
            .catch(err => {
                console.error('Failed to fetch errors:', err);
            });

        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    showToast(message, type = 'info') {
        if (window.showToast) {
            window.showToast(message, type);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

    formatSeconds(seconds) {
        if (seconds < 60) {
            return `${seconds}s`;
        } else if (seconds < 3600) {
            return `${Math.floor(seconds / 60)}m ${seconds % 60}s`;
        } else {
            return `${Math.floor(seconds / 3600)}h ${Math.floor((seconds % 3600) / 60)}m`;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global tracking of active imports
window.activeImports = {};

window.createImportNotification = function(batchId, fileName = 'Import') {
    const notification = new ImportProgressNotification(batchId, fileName, false);
    window.activeImports[batchId] = notification;
    return notification;
};

window.closeImportNotification = function(batchId) {
    if (window.activeImports[batchId]) {
        window.activeImports[batchId].close();
        delete window.activeImports[batchId];
    }
};

// Restore active imports from localStorage on page load
window.restoreActiveImports = function() {
    try {
        const activeSessions = ImportProgressNotification.getActiveImportSessions();
        activeSessions.forEach(session => {
            // Only restore if not already active
            if (!window.activeImports[session.batchId]) {
                const notification = new ImportProgressNotification(session.batchId, session.fileName, true);
                window.activeImports[session.batchId] = notification;
            }
        });
    } catch (e) {
        console.warn('Failed to restore active imports:', e);
    }
};

// Restore on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    window.restoreActiveImports();
});

// Also restore if page is already loaded (for dynamic loading)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.restoreActiveImports);
} else {
    // Page already loaded, restore immediately
    setTimeout(window.restoreActiveImports, 100);
}
