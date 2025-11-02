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
            deferRender: true,
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
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
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
                    if (!data) return '-';
                    const uuid = row.uuid || '';
                    const viewUrl = self.routes.view.replace(':uuid', uuid);
                    return '<a href="' + viewUrl + '" class="fw-bold text-primary">' + self.escapeHtml(data) + '</a>';
                }
            },
            {
                data: 'supplier',
                name: 'supplier.name',
                defaultContent: '-',
                render: function(data, type, row) {
                    if (data && data.name) {
                        const supplierId = data.id || '';
                        const supplierViewUrl = '/admin/suppliers/' + supplierId + '/view';
                        return '<a href="' + supplierViewUrl + '" class="text-primary" title="View Supplier Profile">' + self.escapeHtml(data.name) + '</a>';
                    }
                    return '-';
                }
            },
            {
                data: 'date',
                name: 'date',
                defaultContent: '-',
                className: 'text-center',
                render: function(data, type, row) {
                    return data ? data : '-';
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

        let buttons = `
            <div class="btn-group" role="group">
                <a href="${viewUrl}"
                   class="btn btn-sm btn-primary me-2"
                   title="View Purchase Details">
                    <i class="fa fa-eye"></i>
                </a>
        `;

        // Add payment button if there is due amount
        if (row.due_amount && parseFloat(row.due_amount) > 0) {
            buttons += `
                <button type="button"
                   class="btn btn-sm btn-success"
                   onclick="openPaymentModal('${uuid}', '${this.escapeHtml(row.invoice_number)}', ${row.due_amount}, ${row.supplier ? row.supplier.id : 0})"
                   title="Make Payment">
                    <i class="fa fa-credit-card"></i>
                </button>
            `;
        }

        buttons += `</div>`;

        return buttons;
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
        // Dispose of existing tooltips first to prevent duplicates
        const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        existingTooltips.forEach(function(tooltipEl) {
            const existingTooltip = bootstrap.Tooltip.getInstance(tooltipEl);
            if (existingTooltip) {
                existingTooltip.dispose();
            }
        });

        // Initialize new tooltips
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
let currentPurchaseUuid = null;
let currentDueAmount = 0;

$(document).ready(function() {
    // Check if routes are available
    if (typeof window.purchaseIndexRoutes === 'undefined') {
        console.error('PurchaseIndexManager: Routes not available. Make sure the Blade template includes the route configuration.');
        return;
    }

    // Initialize the manager
    purchaseIndexManager = new PurchaseIndexManager();

    // Set today's date as default
    $('#payment_date').val(new Date().toISOString().split('T')[0]);

    // Handle payment form submission
    $('#paymentForm').on('submit', handlePaymentFormSubmit);

    // Reset form when modal is closed
    $('#paymentModal').on('hidden.bs.modal', function() {
        resetPaymentForm();
    });

    // Validate payment amount
    $('#payment_amount').on('input', function() {
        validatePaymentAmount();
    });
});

// Cleanup on page unload
$(window).on('beforeunload', function() {
    if (purchaseIndexManager) {
        purchaseIndexManager.destroy();
    }
});

/**
 * Open payment modal
 * @param {string} uuid - Purchase UUID
 * @param {string} invoiceNumber - Invoice number
 * @param {number} dueAmount - Due amount
 * @param {number} supplierId - Supplier ID
 */
function openPaymentModal(uuid, invoiceNumber, dueAmount, supplierId) {
    currentPurchaseUuid = uuid;
    currentDueAmount = parseFloat(dueAmount);

    $('#purchase_uuid').val(uuid);
    $('#supplier_id').val(supplierId);
    $('#invoice_number_display').val(invoiceNumber);
    $('#due_amount_display').val(window.purchaseIndexConfig.currency + currentDueAmount.toFixed(2));
    $('#payment_amount').attr('max', currentDueAmount);
    $('#payment_amount').val(currentDueAmount.toFixed(2));

    // Clear validation errors
    clearValidationErrors();

    $('#paymentModal').modal('show');
}

/**
 * Handle payment form submission
 * @param {Event} e - Form submit event
 */
function handlePaymentFormSubmit(e) {
    e.preventDefault();

    if (!validatePaymentAmount()) {
        return;
    }

    const formData = new FormData(document.getElementById('paymentForm'));

    showLoadingSpinner('#paymentSaveSpinner', '#paymentSaveBtn');
    clearValidationErrors();

    $.ajax({
        url: '/admin/purchase/' + currentPurchaseUuid + '/make-payment',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                $('#paymentModal').modal('hide');
                // Reload the page after a short delay to show success message
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showToast(response.message || 'Payment failed', 'danger');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayValidationErrors(xhr.responseJSON.errors);
                showToast('Please correct the validation errors', 'danger');
            } else {
                showToast('An error occurred. Please try again.', 'danger');
            }
        },
        complete: function() {
            hideLoadingSpinner('#paymentSaveSpinner', '#paymentSaveBtn');
        }
    });
}

/**
 * Validate payment amount
 * @returns {boolean}
 */
function validatePaymentAmount() {
    const amount = parseFloat($('#payment_amount').val());
    const $amountInput = $('#payment_amount');
    const $amountError = $('#amountError');

    if (isNaN(amount) || amount <= 0) {
        $amountInput.addClass('is-invalid');
        $amountError.text('Payment amount must be greater than 0');
        return false;
    }

    if (amount > currentDueAmount) {
        $amountInput.addClass('is-invalid');
        $amountError.text('Payment amount cannot exceed due amount');
        return false;
    }

    $amountInput.removeClass('is-invalid');
    $amountError.text('');
    return true;
}

/**
 * Reset payment form
 */
function resetPaymentForm() {
    $('#paymentForm')[0].reset();
    $('#payment_date').val(new Date().toISOString().split('T')[0]);
    currentPurchaseUuid = null;
    currentDueAmount = 0;
    clearValidationErrors();
}

/**
 * Display validation errors
 * @param {Object} errors
 */
function displayValidationErrors(errors) {
    for (const field in errors) {
        if (errors.hasOwnProperty(field)) {
            const errorElement = '#' + field + 'Error';
            const inputElement = '[name="' + field + '"]';

            $(errorElement).text(errors[field][0]);
            $(inputElement).addClass('is-invalid');
        }
    }
}

/**
 * Clear validation errors
 */
function clearValidationErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

/**
 * Show loading spinner
 * @param {string} spinnerSelector
 * @param {string} buttonSelector
 */
function showLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).removeClass('d-none');
    $(buttonSelector).prop('disabled', true);
}

/**
 * Hide loading spinner
 * @param {string} spinnerSelector
 * @param {string} buttonSelector
 */
function hideLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).addClass('d-none');
    $(buttonSelector).prop('disabled', false);
}

/**
 * Show toast notification
 * @param {string} message
 * @param {string} type
 */
function showToast(message, type = 'info') {
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3"
                 role="alert"
                 style="z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        const $alert = $(alertHtml);
        $('body').append($alert);

        setTimeout(() => {
            $alert.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }
}
