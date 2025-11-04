"use strict";

/**
 * Invoice Show Page Manager
 *
 * @package    SNG-POS
 * @subpackage Invoice Module
 * @version    1.0.0
 */

class InvoiceShowManager {
    constructor(config = {}) {
        // Configuration
        this.config = {
            currency: config.currency || '$',
            csrfToken: config.csrfToken || '',
            baseUrl: config.baseUrl || '/admin/invoice',
            ...config
        };

        // Payment from customer modal instance
        this.paymentFromCustomerModal = null;

        // Initialize
        this.init();
    }

    /**
     * Initialize the page
     */
    init() {
        console.log('InvoiceShowManager: Initializing...');

        // Initialize payment from customer modal
        this.initializePaymentModal();

        // Expose functions globally for inline onclick handlers
        window.returnInvoice = (uuid) => this.returnInvoice(uuid);
        window.cancelInvoice = (uuid) => this.cancelInvoice(uuid);
        window.openPaymentFromCustomerModal = (invoiceUuid, invoiceNumber, dueAmount, customerId) => {
            this.openPaymentFromCustomerModal(invoiceUuid, invoiceNumber, dueAmount, customerId);
        };
    }

    /**
     * Initialize payment from customer modal
     */
    initializePaymentModal() {
        if (typeof PaymentFromCustomerModal !== 'undefined') {
            this.paymentFromCustomerModal = new PaymentFromCustomerModal({
                currency: this.config.currency,
                onSuccess: () => {
                    location.reload();
                }
            });
            console.log('InvoiceShowManager: Payment modal initialized');
        }
    }

    /**
     * Open payment from customer modal
     */
    openPaymentFromCustomerModal(invoiceUuid, invoiceNumber, dueAmount, customerId) {
        if (this.paymentFromCustomerModal) {
            this.paymentFromCustomerModal.open(invoiceUuid, invoiceNumber, dueAmount, customerId);
        } else {
            console.error('Payment from customer modal not initialized');
        }
    }

    /**
     * Return an invoice
     */
    returnInvoice(uuid) {
        Swal.fire({
            title: 'Return Invoice?',
            text: 'Are you sure you want to return this invoice? This will restore product stock.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, return it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeReturn(uuid);
            }
        });
    }

    /**
     * Execute the return operation
     */
    executeReturn(uuid) {
        fetch(`${this.config.baseUrl}/${uuid}/return`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Invoice returned successfully',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    location.reload();
                });
            } else {
                this.showError(data.message || 'Failed to return invoice');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showError('An error occurred while returning the invoice');
        });
    }

    /**
     * Cancel an invoice
     */
    cancelInvoice(uuid) {
        Swal.fire({
            title: 'Cancel Invoice?',
            text: 'Are you sure you want to cancel this invoice? This will restore product stock.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeCancel(uuid);
            }
        });
    }

    /**
     * Execute the cancel operation
     */
    executeCancel(uuid) {
        fetch(`${this.config.baseUrl}/${uuid}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Invoice cancelled successfully',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    location.reload();
                });
            } else {
                this.showError(data.message || 'Failed to cancel invoice');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showError('An error occurred while cancelling the invoice');
        });
    }

    /**
     * Show error message
     */
    showError(message) {
        Swal.fire({
            title: 'Error!',
            text: message,
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    }
}

// Initialize when document is ready
$(document).ready(function() {
    // Get configuration from window object (set in blade template)
    const config = window.invoiceShowConfig || {};
    window.invoiceShowManager = new InvoiceShowManager(config);
});
