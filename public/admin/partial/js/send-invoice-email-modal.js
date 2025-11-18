"use strict";
/**
 * Send Invoice Email Modal JavaScript
 * Handles email sending functionality for invoices
 */

class SendInvoiceEmailModal {
    constructor() {
        this.init();
    }

    init() {
        this.initializeSummernote();
        this.bindEvents();
    }

    initializeSummernote() {
        // Initialize Summernote when modal is shown
        $('#sendInvoiceEmailModal').on('shown.bs.modal', function() {
            $('#email_body').summernote({
                height: 300,
                placeholder: 'Enter email message...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });

        // Destroy Summernote when modal is hidden
        $('#sendInvoiceEmailModal').on('hidden.bs.modal', function() {
            $('#email_body').summernote('destroy');
        });
    }

    bindEvents() {
        $('#sendInvoiceEmailForm').on('submit', (e) => this.handleEmailSubmit(e));
    }

    handleEmailSubmit(e) {
        e.preventDefault();

        const invoiceUuid = $('#invoice_uuid').val();
        const emailSubject = $('#email_subject').val();
        const emailBody = $('#email_body').summernote('code'); // Get HTML content from Summernote
        const customerEmail = $('#customer_email').val();
        const sendBtn = $('#sendEmailBtn');
        const originalText = sendBtn.html();

        // Validation
        if (!invoiceUuid) {
            this.showError('Invoice UUID is missing');
            return;
        }

        if (!emailSubject || !emailBody) {
            this.showError('Please fill in all required fields');
            return;
        }

        if (!customerEmail) {
            this.showError('Customer email is not available');
            return;
        }

        // Disable button and show loading
        sendBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Sending...');

        // Clear previous alerts
        $('#emailAlertContainer').html('');

        $.ajax({
            url: window.invoiceShowConfig.baseUrl + '/' + invoiceUuid + '/send-email',
            method: 'POST',
            data: {
                _token: window.invoiceShowConfig.csrfToken,
                email_subject: emailSubject,
                email_body: emailBody,
                customer_email: customerEmail
            },
            success: (response) => {
                this.showSuccess(response.message);

                // Close modal after 2 seconds
                setTimeout(() => {
                    $('#sendInvoiceEmailModal').modal('hide');
                    // Clear alert when modal is hidden
                    $('#emailAlertContainer').html('');
                }, 2000);
            },
            error: (xhr) => {
                const errorMessage = xhr.responseJSON?.message || 'Failed to send email. Please try again.';
                this.showError(errorMessage);
            },
            complete: () => {
                // Re-enable button
                sendBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    showSuccess(message) {
        $('#emailAlertContainer').html(
            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            '<i class="fa fa-check-circle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>'
        );
    }

    showError(message) {
        $('#emailAlertContainer').html(
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            '<i class="fa fa-exclamation-circle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>'
        );
    }
}

/**
 * Open send invoice email modal
 */
function openSendInvoiceModal(invoiceUuid) {
    // Set the invoice UUID in the hidden field
    $('#invoice_uuid').val(invoiceUuid);

    // Show the modal
    $('#sendInvoiceEmailModal').modal('show');

    // Clear any previous alerts
    $('#emailAlertContainer').html('');
}

// Initialize when document is ready
$(document).ready(function() {
    new SendInvoiceEmailModal();
});
