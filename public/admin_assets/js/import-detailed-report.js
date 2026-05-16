/**
 * Import Detailed Report Module
 * Provides comprehensive product-wise reporting for import process
 * Integrates with ImportProgressNotification class
 */

// Add methods to ImportProgressNotification prototype
if (typeof ImportProgressNotification !== 'undefined') {
    ImportProgressNotification.prototype.showDetailedReport = function(data) {
        // Fetch detailed product-wise report from backend
        axios.get(`/admin/products/import/report/${this.batchId}`)
            .then(res => {
                this.renderDetailedReportModal(res.data, data);
            })
            .catch(err => {
                console.error('Failed to fetch report:', err);
                this.showToast('Failed to load detailed report', 'error');
            });
    };

    ImportProgressNotification.prototype.renderDetailedReportModal = function(reportData, summaryData) {
        // Create a large modal for detailed report
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = `report-modal-${this.batchId}`;
        modal.setAttribute('tabindex', '-1');
        modal.innerHTML = `
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-check-circle"></i> Import Report - ${this.escapeHtml(this.fileName)}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <!-- Summary Stats -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="text-primary">${(reportData.successful?.length || 0) + (reportData.failed?.length || 0)}</h3>
                                        <small class="text-muted">Total Products</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="text-success">${reportData.successful?.length || 0}</h3>
                                        <small class="text-muted">Successfully Imported</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="text-danger">${reportData.failed?.length || 0}</h3>
                                        <small class="text-muted">Failed / Skipped</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs for different views -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="successful-tab" data-bs-toggle="tab" data-bs-target="#successful-content" type="button" role="tab">
                                    <i class="bi bi-check text-success"></i> Successful (${reportData.successful?.length || 0})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="failed-tab" data-bs-toggle="tab" data-bs-target="#failed-content" type="button" role="tab">
                                    <i class="bi bi-times text-danger"></i> Failed / Skipped (${reportData.failed?.length || 0})
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Successful Products -->
                            <div class="tab-pane fade show active" id="successful-content">
                                ${this.renderProductTable(reportData.successful || [], 'success')}
                            </div>

                            <!-- Failed Products (including skipped) -->
                            <div class="tab-pane fade" id="failed-content">
                                ${this.renderProductTable(reportData.failed || [], 'danger')}
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Export button functionality
        const exportBtn = modal.querySelector('#export-report-btn');
        exportBtn?.addEventListener('click', () => this.exportReportAsCSV(reportData, summaryData));

        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    };

    ImportProgressNotification.prototype.renderProductTable = function(products, type) {
        if (!products || products.length === 0) {
            return `<div class="alert alert-info text-center">No products in this category</div>`;
        }

        const rows = products.map((product, index) => {
            const errorMsg = product.error || product.reason || '';
            const badgeClass = type === 'success' ? 'bg-success' : 'bg-danger';
            const badgeText = type === 'success' ? 'Imported' : 'Failed / Skipped';

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <strong>${this.escapeHtml(product.name_en || 'N/A')}</strong>
                        <br>
                        <small class="text-muted">${this.escapeHtml(product.sku || 'No SKU')}</small>
                    </td>
                    <td>${this.escapeHtml(product.brand || 'N/A')}</td>
                    <td>${this.escapeHtml(product.category || 'N/A')}</td>
                    <td class="text-end">
                        <span class="badge ${badgeClass}">
                            ${badgeText}
                        </span>
                        ${errorMsg ? `<br><small class="text-danger">${this.escapeHtml(errorMsg)}</small>` : ''}
                    </td>
                </tr>
            `;
        }).join('');

        return `
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Product Name</th>
                            <th width="15%">Brand</th>
                            <th width="15%">Category</th>
                            <th width="20%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows}
                    </tbody>
                </table>
            </div>
        `;
    };

    ImportProgressNotification.prototype.exportReportAsCSV = function(reportData, summaryData) {
        // Prepare CSV data
        const timestamp = new Date().toLocaleString();
        let csvContent = 'data:text/csv;charset=utf-8,';

        // Add header with summary
        csvContent += `Import Report\n`;
        csvContent += `File: ${this.fileName}\n`;
        csvContent += `Date: ${timestamp}\n`;
        csvContent += `Batch ID: ${this.batchId}\n`;
        csvContent += `\n`;
        const totalCount = (reportData.successful?.length || 0) + (reportData.failed?.length || 0);
        const successCount = reportData.successful?.length || 0;
        const failedCount = reportData.failed?.length || 0;

        csvContent += `Summary\n`;
        csvContent += `Total Products,${totalCount}\n`;
        csvContent += `Successfully Imported,${successCount}\n`;
        csvContent += `Failed / Skipped,${failedCount}\n`;
        csvContent += `\n\n`;

        // Add successful products
        if (reportData.successful && reportData.successful.length > 0) {
            csvContent += `SUCCESSFULLY IMPORTED (${reportData.successful.length})\n`;
            csvContent += `Product Name,SKU,Brand,Category,Status\n`;
            reportData.successful.forEach(product => {
                csvContent += `"${this.escapeCsv(product.name_en || 'N/A')}","${this.escapeCsv(product.sku || 'N/A')}","${this.escapeCsv(product.brand || 'N/A')}","${this.escapeCsv(product.category || 'N/A')}","Imported"\n`;
            });
            csvContent += `\n`;
        }

        // Add failed products (includes skipped reasons)
        if (reportData.failed && reportData.failed.length > 0) {
            csvContent += `FAILED / SKIPPED (${reportData.failed.length})\n`;
            csvContent += `Product Name,SKU,Brand,Category,Error/Reason\n`;
            reportData.failed.forEach(product => {
                csvContent += `"${this.escapeCsv(product.name_en || 'N/A')}","${this.escapeCsv(product.sku || 'N/A')}","${this.escapeCsv(product.brand || 'N/A')}","${this.escapeCsv(product.category || 'N/A')}","${this.escapeCsv(product.error || product.reason || 'Unknown error')}"\n`;
            });
            csvContent += `\n`;
        }

        // Download CSV
        const link = document.createElement('a');
        link.setAttribute('href', encodeURI(csvContent));
        link.setAttribute('download', `import-report-${this.batchId}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        this.showToast('Report exported successfully!', 'success');
    };

    ImportProgressNotification.prototype.escapeCsv = function(value) {
        if (value === null || value === undefined) return '';
        return String(value).replace(/"/g, '""');
    };
}
