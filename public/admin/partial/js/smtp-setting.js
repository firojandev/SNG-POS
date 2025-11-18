"use strict";
/**
 * SMTP Setting JavaScript
 * Handles test email functionality for SMTP configuration
 */

class SmtpSettingManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        $('#testEmailForm').on('submit', (e) => this.handleTestEmail(e));
    }

    handleTestEmail(e) {
        e.preventDefault();

        const testEmail = $('#test_email').val();
        const sendBtn = $('#sendTestEmailBtn');
        const originalText = sendBtn.html();

        // Disable button and show loading
        sendBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Sending...');

        // Clear previous response
        $('#test-email-response').html('');

        $.ajax({
            url: window.smtpSettingRoutes.testConnection,
            method: 'POST',
            data: {
                _token: window.smtpSettingConfig.csrfToken,
                test_email: testEmail
            },
            success: (response) => {
                this.showSuccess(response.message);
                $('#test_email').val('');
            },
            error: (xhr) => {
                const errorMessage = xhr.responseJSON?.message || 'Failed to send test email';
                this.showError(errorMessage);
            },
            complete: () => {
                // Re-enable button
                sendBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    showSuccess(message) {
        $('#test-email-response').html(
            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            '<i class="fa fa-check-circle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>'
        );
    }

    showError(message) {
        $('#test-email-response').html(
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            '<i class="fa fa-exclamation-circle me-2"></i>' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>'
        );
    }
}

// Initialize when document is ready
$(document).ready(function() {
    new SmtpSettingManager();
});
