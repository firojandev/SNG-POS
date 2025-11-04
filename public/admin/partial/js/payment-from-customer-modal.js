"use strict";

/**
 * Payment from Customer Modal Manager (for Invoice Payments)
 *
 * A reusable class for handling payment modals for sales invoices.
 * Used in Invoice List, Customer View, Invoice Details, and other pages.
 *
 * @package    SNG-POS
 * @subpackage Payment Module
 * @version    1.0.0
 *
 * @example
 * // Initialize the modal
 * const paymentFromCustomerModal = new PaymentFromCustomerModal({
 *     currency: '$',
 *     modalId: 'paymentFromCustomerModal',
 *     formId: 'paymentFromCustomerForm',
 *     onSuccess: function() {
 *         console.log('Payment successful!');
 *         location.reload();
 *     }
 * });
 *
 * // Open the modal
 * paymentFromCustomerModal.open(uuid, invoiceNumber, dueAmount, customerId);
 */

class PaymentFromCustomerModal {
    /**
     * Constructor
     * @param {Object} options - Configuration options
     * @param {string} options.currency - Currency symbol (default: '$')
     * @param {string} options.modalId - Modal element ID (default: 'paymentFromCustomerModal')
     * @param {string} options.formId - Form element ID (default: 'paymentFromCustomerForm')
     * @param {Function} options.onSuccess - Success callback (default: page reload)
     * @param {Function} options.onError - Error callback (optional)
     * @param {string} options.apiEndpoint - API endpoint template (default: '/admin/invoice/{uuid}/receive-payment')
     */
    constructor(options = {}) {
        // Configuration
        this.currency = options.currency || '$';
        this.modalId = options.modalId || 'paymentFromCustomerModal';
        this.formId = options.formId || 'paymentFromCustomerForm';
        this.apiEndpoint = options.apiEndpoint || '/admin/invoice/{uuid}/receive-payment';
        this.onSuccess = options.onSuccess || this.defaultSuccessHandler.bind(this);
        this.onError = options.onError || null;

        // State
        this.currentInvoiceUuid = null;
        this.currentDueAmount = 0;
        this.currentInvoiceNumber = null;
        this.currentCustomerId = null;

        // DOM Elements (cached)
        this.$modal = null;
        this.$form = null;
        this.$invoiceUuidInput = null;
        this.$customerIdInput = null;
        this.$invoiceNumberDisplay = null;
        this.$dueAmountDisplay = null;
        this.$paymentAmount = null;
        this.$paymentDate = null;
        this.$paymentNote = null;
        this.$saveBtn = null;
        this.$saveSpinner = null;

        // Initialize
        this.init();
    }

    /**
     * Initialize the modal and bind events
     */
    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupModal());
        } else {
            this.setupModal();
        }
    }

    /**
     * Setup modal after DOM is ready
     */
    setupModal() {
        // Cache DOM elements
        this.cacheElements();

        // Set today's date as default
        this.setDefaultDate();

        // Bind events
        this.bindEvents();

        if (this.isDebugMode()) {
            console.log('PaymentFromCustomerModal initialized', {
                modalId: this.modalId,
                formId: this.formId,
                currency: this.currency
            });
        }
    }

    /**
     * Cache DOM elements for better performance
     */
    cacheElements() {
        this.$modal = $('#' + this.modalId);
        this.$form = $('#' + this.formId);
        this.$invoiceUuidInput = $('#invoice_uuid');
        this.$customerIdInput = $('#customer_id');
        this.$invoiceNumberDisplay = $('#invoice_number_display');
        this.$dueAmountDisplay = $('#due_amount_display');
        this.$paymentAmount = $('#payment_amount');
        this.$paymentDate = $('#payment_date');
        this.$paymentNote = $('#payment_note');
        this.$saveBtn = $('#paymentFromCustomerSaveBtn');
        this.$saveSpinner = $('#paymentFromCustomerSaveSpinner');
    }

    /**
     * Set default payment date to today
     */
    setDefaultDate() {
        if (this.$paymentDate && this.$paymentDate.length) {
            const today = new Date().toISOString().split('T')[0];
            this.$paymentDate.val(today);
        }
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Form submission
        this.$form.on('submit', (e) => this.handleSubmit(e));

        // Modal events
        this.$modal.on('hidden.bs.modal', () => this.resetForm());
        this.$modal.on('shown.bs.modal', () => this.$paymentAmount.focus());

        // Amount validation on input
        this.$paymentAmount.on('input', () => this.validateAmount());
    }

    /**
     * Open the payment modal
     * @param {string} uuid - Invoice UUID
     * @param {string} invoiceNumber - Invoice number
     * @param {number} dueAmount - Due amount
     * @param {number} customerId - Customer ID
     */
    open(uuid, invoiceNumber, dueAmount, customerId) {
        // Store current data
        this.currentInvoiceUuid = uuid;
        this.currentInvoiceNumber = invoiceNumber;
        this.currentDueAmount = parseFloat(dueAmount);
        this.currentCustomerId = customerId;

        // Populate form fields
        this.$invoiceUuidInput.val(uuid);
        this.$customerIdInput.val(customerId);
        this.$invoiceNumberDisplay.val(invoiceNumber);
        this.$dueAmountDisplay.val(this.currency + this.currentDueAmount.toFixed(2));
        this.$paymentAmount.attr('max', this.currentDueAmount);
        this.$paymentAmount.val(this.currentDueAmount.toFixed(2));

        // Clear validation errors
        this.clearValidationErrors();

        // Show modal
        this.$modal.modal('show');

        if (this.isDebugMode()) {
            console.log('Payment from customer modal opened', {
                uuid,
                invoiceNumber,
                dueAmount,
                customerId
            });
        }
    }

    /**
     * Handle form submission
     * @param {Event} e - Form submit event
     */
    handleSubmit(e) {
        e.preventDefault();

        // Validate amount
        if (!this.validateAmount()) {
            return;
        }

        // Prepare form data
        const formData = new FormData(this.$form[0]);

        // Show loading state
        this.showLoadingSpinner();
        this.clearValidationErrors();

        // Build API URL
        const apiUrl = this.apiEndpoint.replace('{uuid}', this.currentInvoiceUuid);

        // Submit via AJAX
        $.ajax({
            url: apiUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => this.handleSuccess(response),
            error: (xhr) => this.handleError(xhr),
            complete: () => this.hideLoadingSpinner()
        });
    }

    /**
     * Handle successful payment submission
     * @param {Object} response - Server response
     */
    handleSuccess(response) {
        if (response.success) {
            this.showToast(response.message || 'Payment received successfully!', 'success');
            this.$modal.modal('hide');

            // Call success callback
            if (typeof this.onSuccess === 'function') {
                this.onSuccess(response);
            }
        } else {
            this.showToast(response.message || 'Payment failed', 'danger');
        }
    }

    /**
     * Handle payment submission error
     * @param {Object} xhr - XMLHttpRequest object
     */
    handleError(xhr) {
        if (xhr.status === 422) {
            // Validation errors
            const errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
            this.displayValidationErrors(errors);
            this.showToast('Please correct the validation errors', 'danger');
        } else if (xhr.status === 404) {
            this.showToast('Invoice not found', 'danger');
        } else if (xhr.status === 403) {
            this.showToast('You do not have permission to perform this action', 'danger');
        } else {
            this.showToast('An error occurred. Please try again.', 'danger');
        }

        // Call custom error callback if provided
        if (typeof this.onError === 'function') {
            this.onError(xhr);
        }

        if (this.isDebugMode()) {
            console.error('Payment submission error', {
                status: xhr.status,
                statusText: xhr.statusText,
                response: xhr.responseJSON
            });
        }
    }

    /**
     * Default success handler
     * @param {Object} response - Server response
     */
    defaultSuccessHandler(response) {
        // Reload page after a short delay
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    /**
     * Validate payment amount
     * @returns {boolean} - True if valid, false otherwise
     */
    validateAmount() {
        const amount = parseFloat(this.$paymentAmount.val());
        const $amountError = $('#amountError');

        // Check if amount is a valid number
        if (isNaN(amount) || amount <= 0) {
            this.$paymentAmount.addClass('is-invalid');
            $amountError.text('Payment amount must be greater than 0');
            return false;
        }

        // Check if amount exceeds due amount
        if (amount > this.currentDueAmount) {
            this.$paymentAmount.addClass('is-invalid');
            $amountError.text('Payment amount cannot exceed due amount (' + this.currency + this.currentDueAmount.toFixed(2) + ')');
            return false;
        }

        // Valid
        this.$paymentAmount.removeClass('is-invalid');
        $amountError.text('');
        return true;
    }

    /**
     * Reset form to initial state
     */
    resetForm() {
        // Reset form fields
        this.$form[0].reset();

        // Reset state
        this.currentInvoiceUuid = null;
        this.currentDueAmount = 0;
        this.currentInvoiceNumber = null;
        this.currentCustomerId = null;

        // Set default date
        this.setDefaultDate();

        // Clear validation errors
        this.clearValidationErrors();
    }

    /**
     * Display validation errors from server
     * @param {Object} errors - Validation errors object
     */
    displayValidationErrors(errors) {
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
     * Clear all validation errors
     */
    clearValidationErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    /**
     * Show loading spinner on submit button
     */
    showLoadingSpinner() {
        this.$saveSpinner.removeClass('d-none');
        this.$saveBtn.prop('disabled', true);
    }

    /**
     * Hide loading spinner on submit button
     */
    hideLoadingSpinner() {
        this.$saveSpinner.addClass('d-none');
        this.$saveBtn.prop('disabled', false);
    }

    /**
     * Show toast notification
     * @param {string} message - Message to display
     * @param {string} type - Type of toast (success, danger, info, warning)
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
        return String(text).replace(/[&<>"']/g, (m) => map[m]);
    }

    /**
     * Check if debug mode is enabled
     * @returns {boolean}
     */
    isDebugMode() {
        return window.invoiceIndexConfig && window.invoiceIndexConfig.debug === true;
    }

    /**
     * Destroy the modal instance
     */
    destroy() {
        // Unbind events
        this.$form.off('submit');
        this.$modal.off('hidden.bs.modal shown.bs.modal');
        this.$paymentAmount.off('input');

        // Clear state
        this.currentInvoiceUuid = null;
        this.currentDueAmount = 0;
        this.currentInvoiceNumber = null;
        this.currentCustomerId = null;

        if (this.isDebugMode()) {
            console.log('PaymentFromCustomerModal destroyed');
        }
    }
}

/**
 * Global function for backward compatibility
 * Opens the payment modal using the global instance
 *
 * @param {string} uuid - Invoice UUID
 * @param {string} invoiceNumber - Invoice number
 * @param {number} dueAmount - Due amount
 * @param {number} customerId - Customer ID
 */
function openPaymentFromCustomerModal(uuid, invoiceNumber, dueAmount, customerId) {
    if (window.paymentFromCustomerModal && typeof window.paymentFromCustomerModal.open === 'function') {
        window.paymentFromCustomerModal.open(uuid, invoiceNumber, dueAmount, customerId);
    } else {
        console.error('PaymentFromCustomerModal instance not found. Please initialize window.paymentFromCustomerModal first.');
    }
}
