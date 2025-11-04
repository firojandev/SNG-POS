"use strict";

/**
 * Payment from Customer Index Page Manager
 *
 * @package    SNG-POS
 * @subpackage Payment from Customer Module
 * @version    1.0.0
 */

class PaymentFromCustomerIndexManager {
    constructor() {
        // Configuration
        this.routes = window.paymentFromCustomerRoutes || {};
        this.config = window.paymentFromCustomerConfig || {};
        this.currency = this.config.currency || '$';

        // DataTable instance
        this.dataTable = null;

        // Date range
        this.startDate = null;
        this.endDate = null;

        // Initialize
        this.init();
    }

    /**
     * Initialize the page
     */
    init() {
        console.log('PaymentFromCustomerIndexManager: Initializing...');

        // Initialize jQuery UI datepickers
        this.initializeDatePickers();

        // Set default date range (last 15 days)
        this.setDefaultDateRange();

        // Initialize DataTable
        this.initDataTable();

        // Bind events
        this.bindEvents();
    }

    /**
     * Initialize jQuery UI datepickers
     */
    initializeDatePickers() {
        if (typeof $.fn.datepicker !== 'undefined') {
            const phpFmt = (window.paymentFromCustomerConfig && window.paymentFromCustomerConfig.dateFormatPhp)
                ? window.paymentFromCustomerConfig.dateFormatPhp
                : 'Y-m-d';
            const jqFmt = this.phpDateFormatToJqueryUI(phpFmt);

            $('#startDate, #endDate').datepicker({
                dateFormat: jqFmt,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true
            });
        }
    }

    /**
     * Map PHP date format to jQuery UI datepicker format
     */
    phpDateFormatToJqueryUI(phpFormat) {
        const map = {
            'Y': 'yy',
            'y': 'y',
            'm': 'mm',
            'n': 'm',
            'd': 'dd',
            'j': 'd',
            '/': '/',
            '-': '-',
            ' ': ' '
        };
        let result = '';
        for (let i = 0; i < phpFormat.length; i++) {
            const ch = phpFormat[i];
            result += (map[ch] !== undefined) ? map[ch] : ch;
        }
        return result;
    }

    /**
     * Set default date range to last 15 days
     */
    setDefaultDateRange() {
        const today = new Date();
        const fifteenDaysAgo = new Date();
        fifteenDaysAgo.setDate(today.getDate() - 15);

        this.startDate = this.formatDate(fifteenDaysAgo);
        this.endDate = this.formatDate(today);

        // Use datepicker's setDate method if available
        if (typeof $.fn.datepicker !== 'undefined') {
            $('#startDate').datepicker('setDate', fifteenDaysAgo);
            $('#endDate').datepicker('setDate', today);
        } else {
            $('#startDate').val(this.startDate);
            $('#endDate').val(this.endDate);
        }
    }

    /**
     * Format date to YYYY-MM-DD
     */
    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    /**
     * Initialize DataTable
     */
    initDataTable() {
        const self = this;

        this.dataTable = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: this.routes.getData,
                type: 'GET',
                data: function(d) {
                    d.start_date = self.startDate;
                    d.end_date = self.endDate;
                }
            },
            columns: [
                { data: 'payment_date', name: 'payment_date' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'invoice_number', name: 'invoice_number' },
                {
                    data: 'formatted_amount',
                    name: 'amount',
                    className: 'text-end'
                },
                { data: 'note', name: 'note' }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                emptyTable: 'No payment records found',
                zeroRecords: 'No matching records found'
            }
        });

        console.log('PaymentFromCustomerIndexManager: DataTable initialized');
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Filter button
        $('#filterBtn').on('click', () => this.applyFilter());

        // Reset button
        $('#resetBtn').on('click', () => this.resetFilter());

        // Export CSV button
        $('#exportCsvBtn').on('click', () => this.exportCsv());

        // Enter key on date inputs
        $('#startDate, #endDate').on('keypress', (e) => {
            if (e.which === 13) {
                this.applyFilter();
            }
        });
    }

    /**
     * Apply date range filter
     */
    applyFilter() {
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();

        if (!startDate || !endDate) {
            this.showToast('Please select both start and end dates', 'warning');
            return;
        }

        // Convert datepicker date to Date object
        const startDateObj = $('#startDate').datepicker('getDate');
        const endDateObj = $('#endDate').datepicker('getDate');

        if (!startDateObj || !endDateObj) {
            this.showToast('Invalid date format', 'danger');
            return;
        }

        if (startDateObj > endDateObj) {
            this.showToast('Start date cannot be greater than end date', 'danger');
            return;
        }

        // Convert to YYYY-MM-DD format for server
        this.startDate = this.formatDate(startDateObj);
        this.endDate = this.formatDate(endDateObj);

        // Reload DataTable with new date range
        this.refreshTable();

        this.showToast('Filter applied successfully', 'success');
    }

    /**
     * Reset filter to default (last 15 days)
     */
    resetFilter() {
        this.setDefaultDateRange();
        this.refreshTable();
        this.showToast('Filter reset to last 15 days', 'info');
    }

    /**
     * Refresh DataTable
     */
    refreshTable() {
        if (this.dataTable) {
            this.dataTable.ajax.reload(null, false);
        }
    }

    /**
     * Export data to CSV
     */
    exportCsv() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        if (!startDate || !endDate) {
            this.showToast('Please select date range first', 'warning');
            return;
        }

        // Get date objects from datepicker
        const startDateObj = $('#startDate').datepicker('getDate');
        const endDateObj = $('#endDate').datepicker('getDate');

        if (!startDateObj || !endDateObj) {
            this.showToast('Invalid date format', 'danger');
            return;
        }

        // Convert to YYYY-MM-DD format for server
        const formattedStartDate = this.formatDate(startDateObj);
        const formattedEndDate = this.formatDate(endDateObj);

        // Build export URL with date range
        const exportUrl = `${this.routes.exportCsv}?start_date=${formattedStartDate}&end_date=${formattedEndDate}`;

        // Trigger download
        window.location.href = exportUrl;

        this.showToast('Downloading CSV file...', 'info');
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        // Use toastr if available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
            return;
        }

        // Fallback to Bootstrap alert
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
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, (m) => map[m]);
    }
}

// Initialize when document is ready
$(document).ready(function() {
    window.paymentFromCustomerIndexManager = new PaymentFromCustomerIndexManager();
});
