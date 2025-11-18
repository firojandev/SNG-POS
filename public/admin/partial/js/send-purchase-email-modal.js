"use strict";
/**
 * Send Purchase Email Modal JavaScript
 * Handles email sending functionality for purchase orders
 */

class SendPurchaseEmailModal {
    constructor() {
        this.init();
    }

    init() {
        this.initializeSummernote();
        this.bindEvents();
    }

    initializeSummernote() {
        // Initialize Summernote when modal is shown
        $('#sendPurchaseEmailModal').on('shown.bs.modal', function() {
            $('#purchase_email_body').summernote({
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
        $('#sendPurchaseEmailModal').on('hidden.bs.modal', function() {
            $('#purchase_email_body').summernote('destroy');
        });
    }

    bindEvents() {
        $('#sendPurchaseEmailForm').on('submit', (e) => this.handleEmailSubmit(e));
    }

    handleEmailSubmit(e) {
        e.preventDefault();

        const purchaseUuid = $('#purchase_uuid').val();
        const emailSubject = $('#purchase_email_subject').val();
        const emailBody = $('#purchase_email_body').summernote('code'); // Get HTML content from Summernote
        const supplierEmail = $('#supplier_email').val();
        const sendBtn = $('#sendPurchaseEmailBtn');
        const originalText = sendBtn.html();

        // Validation
        if (!purchaseUuid) {
            this.showError('Purchase UUID is missing');
            return;
        }

        if (!emailSubject || !emailBody) {
            this.showError('Please fill in all required fields');
            return;
        }

        if (!supplierEmail) {
            this.showError('Supplier email is not available');
            return;
        }

        // Disable button and show loading
        sendBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Sending...');

        // Clear previous alerts
        $('#purchaseEmailAlertContainer').html('');

        $.ajax({
            url: window.purchaseShowConfig.baseUrl + '/' + purchaseUuid + '/send-email',
            method: 'POST',
            data: {
                _token: window.purchaseShowConfig.csrfToken,
                email_subject: emailSubject,
                email_body: emailBody,
                supplier_email: supplierEmail
            },
            success: (response) => {
                this.showSuccess(response.message);

                // Close modal after 2 seconds
                setTimeout(() => {
                    $('#sendPurchaseEmailModal').modal('hide');
                    // Clear alert when modal is hidden
                    $('#purchaseEmailAlertContainer').html('');
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
        $('#purchaseEmailAlertContainer').html(
            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            '<i class="fa fa-check-circle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>'
        );
    }

    showError(message) {
        $('#purchaseEmailAlertContainer').html(
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            '<i class="fa fa-exclamation-circle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>'
        );
    }
}

/**
 * Open send purchase email modal
 */
function openSendPurchaseModal(purchaseUuid) {
    // Set the purchase UUID in the hidden field
    $('#purchase_uuid').val(purchaseUuid);

    // Show the modal
    $('#sendPurchaseEmailModal').modal('show');

    // Clear any previous alerts
    $('#purchaseEmailAlertContainer').html('');
}

// Initialize when document is ready
$(document).ready(function() {
    new SendPurchaseEmailModal();
});
