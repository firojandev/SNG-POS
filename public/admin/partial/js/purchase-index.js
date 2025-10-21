"use strict";

/**
 * Purchase Index Page JavaScript
 * Handles DataTable initialization and management for purchase listing
 *
 * @package    SNG-POS
 * @subpackage Purchase Module
 * @author     Your Name
 * @version    1.0.0
 */

class PurchaseIndexManager {
    constructor() {
        this.dataTable = null;
        this.routes = window.purchaseIndexRoutes || {};
        this.init();
    }

    /**
     * Initialize the purchase index page
     */
    init() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables library is not loaded');
            return;
        }

        this.initDataTable();
        this.bindEvents();
    }

    /**
     * Initialize DataTable with configuration
     */
    initDataTable() {
        const self = this;

        this.dataTable = $('#dataTable').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            ajax: {
                url: this.routes.getData,
                dataSrc: function(json) {
                    if (self.isDebugMode()) {
                        console.log('Purchase API Response:', json);
                        if (json.success && json.data.length > 0) {
                            console.log('Data count:', json.data.length);
                            console.log('First row sample:', json.data[0]);
                        }
                    }

                    if (json.success) {
                        return json.data;
                    }

                    console.error('API returned success: false', json);
                    self.showError('Failed to load purchase data');
                    return [];
                },
                error: function(xhr, error, code) {
                    console.error('DataTable AJAX error:', {
                        error: error,
                        code: code,
                        status: xhr.status,
                        statusText: xhr.statusText
                    });
                    self.showError('Failed to load purchase data. Please check your connection and try again.');
                }
            },
            columns: this.getTableColumns(),
            order: [[2, 'desc']], // Sort by date descending
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: this.getTableLanguage(),
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            drawCallback: function(settings) {
                // Initialize tooltips after table draw
                self.initializeTooltips();
            }
        });
    }

    /**
     * Get DataTable column configuration
     * @returns {Array}
     */
    getTableColumns() {
        const self = this;

        return [
            {
                data: 'invoice_number',
                name: 'invoice_number',
                defaultContent: '-',
                render: function(data, type, row) {
                    return data ? '<strong>' + self.escapeHtml(data) + '</strong>' : '-';
                }
            },
            {
                data: 'supplier',
                name: 'supplier.name',
                defaultContent: '-',
                render: function(data, type, row) {
                    return (data && data.name) ? self.escapeHtml(data.name) : '-';
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                defaultContent: '-',
                className: 'text-center',
                render: function(data, type, row) {
                    if (!data) return '-';

                    if (type === 'sort' || type === 'type') {
                        return data; // Return raw data for sorting
                    }

                    return self.formatDate(data);
                }
            },
            {
                data: 'formatted_total_amount',
                name: 'total_amount',
                defaultContent: '-',
                className: 'text-center',
                render: function(data, type, row) {
                    return data ? data : '-';
                }
            },
            {
                data: 'formatted_paid_amount',
                name: 'paid_amount',
                defaultContent: '-',
                className: 'text-center',
                render: function(data, type, row) {
                    return data ? data : '-';
                }
            },
            {
                data: 'formatted_due_amount',
                name: 'due_amount',
                defaultContent: '-',
                className: 'text-center',
                render: function(data, type, row) {
                    if (!data) return '-';
                    const className = row.due_amount > 0 ? 'text-danger' : 'text-success';
                    return '<span class="' + className + ' fw-bold">' + data + '</span>';
                }
            },
            {
                data: null,
                name: 'actions',
                defaultContent: '',
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return self.renderActionButtons(row);
                }
            }
        ];
    }

    /**
     * Render action buttons for each row
     * @param {Object} row - The row data
     * @returns {string}
     */
    renderActionButtons(row) {
        const uuid = row.uuid || '';
        const viewUrl = this.routes.view.replace(':uuid', uuid);

        return `
            <div class="btn-group" role="group">
                <a href="${viewUrl}"
                   class="btn btn-sm btn-primary me-2"
                   title="View Purchase Details"
                   data-bs-toggle="tooltip">
                    <i class="fa fa-eye"></i>
                </a>
<!--                <button type="button"-->
<!--                        class="btn btn-sm btn-warning"-->
<!--                        title="Edit (Coming Soon)"-->
<!--                        data-bs-toggle="tooltip"-->
<!--                        disabled>-->
<!--                    <i class="fa fa-edit"></i>-->
<!--                </button>-->
<!--                <button type="button"-->
<!--                        class="btn btn-sm btn-danger"-->
<!--                        title="Delete (Coming Soon)"-->
<!--                        data-bs-toggle="tooltip"-->
<!--                        disabled>-->
<!--                    <i class="fa fa-trash"></i>-->
<!--                </button>-->
            </div>
        `;
    }

    /**
     * Get DataTable language configuration
     * @returns {Object}
     */
    getTableLanguage() {
        return {
            emptyTable: "No purchases found. <a href='" + this.routes.create + "' class='fw-bold'>Create your first purchase</a>",
            zeroRecords: "No matching purchases found",
            loadingRecords: "Loading purchases...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            search: "_INPUT_",
            searchPlaceholder: "Search purchases...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ purchases",
            infoEmpty: "Showing 0 to 0 of 0 purchases",
            infoFiltered: "(filtered from _MAX_ total purchases)",
            paginate: {
                first: '<i class="fa fa-angle-double-left"></i>',
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>',
                last: '<i class="fa fa-angle-double-right"></i>'
            }
        };
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Refresh button event
        $(document).on('click', '[data-action="refresh-table"]', () => {
            this.refreshTable();
        });

        // Export functionality (if needed in future)
        $(document).on('click', '[data-action="export-table"]', () => {
            this.exportTable();
        });
    }

    /**
     * Initialize Bootstrap tooltips
     */
    initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Format date for display
     * @param {string} dateString - ISO date string
     * @returns {string}
     */
    formatDate(dateString) {
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return '-';
            }

            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (error) {
            console.error('Date formatting error:', error);
            return '-';
        }
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string}
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Refresh the DataTable
     */
    refreshTable() {
        if (this.dataTable) {
            this.dataTable.ajax.reload(null, false); // false = stay on current page
            this.showSuccess('Table refreshed successfully');
        }
    }

    /**
     * Export table data (placeholder for future implementation)
     */
    exportTable() {
        console.log('Export functionality - to be implemented');
        this.showInfo('Export functionality will be available soon');
    }

    /**
     * Show success message
     * @param {string} message
     */
    showSuccess(message) {
        this.showToast(message, 'success');
    }

    /**
     * Show error message
     * @param {string} message
     */
    showError(message) {
        this.showToast(message, 'danger');
    }

    /**
     * Show info message
     * @param {string} message
     */
    showInfo(message) {
        this.showToast(message, 'info');
    }

    /**
     * Show toast notification
     * @param {string} message
     * @param {string} type - success, danger, info, warning
     */
    showToast(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3"
                 role="alert"
                 style="z-index: 9999; min-width: 300px;">
                ${this.escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        const $alert = $(alertHtml);
        $('body').append($alert);

        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            $alert.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }

    /**
     * Check if debug mode is enabled
     * @returns {boolean}
     */
    isDebugMode() {
        return window.purchaseIndexConfig && window.purchaseIndexConfig.debug === true;
    }

    /**
     * Destroy the DataTable instance
     */
    destroy() {
        if (this.dataTable) {
            this.dataTable.destroy();
            this.dataTable = null;
        }
    }
}

// Initialize when document is ready
let purchaseIndexManager;

$(document).ready(function() {
    // Check if routes are available
    if (typeof window.purchaseIndexRoutes === 'undefined') {
        console.error('PurchaseIndexManager: Routes not available. Make sure the Blade template includes the route configuration.');
        return;
    }

    // Initialize the manager
    purchaseIndexManager = new PurchaseIndexManager();
});

// Cleanup on page unload
$(window).on('beforeunload', function() {
    if (purchaseIndexManager) {
        purchaseIndexManager.destroy();
    }
});
