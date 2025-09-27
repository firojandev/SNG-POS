"use strict";

/**
 * Global JavaScript Configuration and Utilities
 * This file contains global configurations and utility functions used across the admin panel
 */

$(document).ready(function() {
    // Initialize global configurations
    initializeGlobalConfigs();
});

/**
 * Initialize global configurations
 */
function initializeGlobalConfigs() {
    // Configure CSRF token for all AJAX requests
    setupCSRFToken();
    
    // Configure toastr options
    setupToastrConfig();
    
    // Configure SweetAlert defaults
    setupSweetAlertConfig();
}

/**
 * Setup CSRF token for all AJAX requests
 */
function setupCSRFToken() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

/**
 * Configure toastr global options
 */
function setupToastrConfig() {
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    }
}

/**
 * Configure SweetAlert global defaults
 */
function setupSweetAlertConfig() {
    if (typeof Swal !== 'undefined') {
        // Set default SweetAlert options
        Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });
    }
}

/**
 * Global toastr wrapper function
 * @param {string} type - success, error, warning, info
 * @param {string} message - Message to display
 * @param {string} title - Optional title
 */
function showToastr(type, message, title = '') {
    if (typeof toastr !== 'undefined') {
        switch (type.toLowerCase()) {
            case 'success':
                toastr.success(message, title);
                break;
            case 'error':
                toastr.error(message, title);
                break;
            case 'warning':
                toastr.warning(message, title);
                break;
            case 'info':
                toastr.info(message, title);
                break;
            default:
                toastr.info(message, title);
        }
    } else {
        console.log(`${type.toUpperCase()}: ${message}`);
    }
}

/**
 * Global SweetAlert confirmation dialog
 * @param {string} title - Dialog title
 * @param {string} text - Dialog text
 * @param {string} confirmText - Confirm button text
 * @param {string} cancelText - Cancel button text
 * @param {function} onConfirm - Callback function when confirmed
 * @param {string} icon - Icon type (warning, error, success, info, question)
 */
function showConfirmDialog(title, text, confirmText = 'Yes', cancelText = 'Cancel', onConfirm = null, icon = 'warning') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    } else {
        if (confirm(`${title}\n${text}`)) {
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        }
    }
}

/**
 * Show loading dialog with SweetAlert
 * @param {string} title - Loading title
 * @param {string} text - Loading text
 */
function showLoadingDialog(title = 'Processing...', text = 'Please wait while we process your request.') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
}

/**
 * Hide loading dialog
 */
function hideLoadingDialog() {
    if (typeof Swal !== 'undefined') {
        Swal.close();
    }
}

/**
 * Show success dialog with SweetAlert
 * @param {string} title - Success title
 * @param {string} text - Success text
 * @param {number} timer - Auto close timer in milliseconds
 * @param {function} callback - Callback function after dialog closes
 */
function showSuccessDialog(title, text, timer = 2000, callback = null) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: 'success',
            timer: timer,
            showConfirmButton: false
        }).then(() => {
            if (typeof callback === 'function') {
                callback();
            }
        });
    } else {
        alert(`${title}\n${text}`);
        if (typeof callback === 'function') {
            callback();
        }
    }
}

/**
 * Show error dialog with SweetAlert
 * @param {string} title - Error title
 * @param {string} text - Error text
 * @param {function} callback - Callback function after dialog closes
 */
function showErrorDialog(title, text, callback = null) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: 'error'
        }).then(() => {
            if (typeof callback === 'function') {
                callback();
            }
        });
    } else {
        alert(`${title}\n${text}`);
        if (typeof callback === 'function') {
            callback();
        }
    }
}

/**
 * Utility function to format currency
 * @param {number} amount - Amount to format
 * @param {string} currency - Currency symbol
 * @param {number} decimals - Number of decimal places
 */
function formatCurrency(amount, currency = '$', decimals = 2) {
    return currency + parseFloat(amount).toFixed(decimals);
}

/**
 * Utility function to format date
 * @param {string|Date} date - Date to format
 * @param {string} format - Date format (default: 'DD/MM/YYYY')
 */
function formatDate(date, format = 'DD/MM/YYYY') {
    if (typeof moment !== 'undefined') {
        return moment(date).format(format);
    }
    return new Date(date).toLocaleDateString();
}

/**
 * Utility function to validate email
 * @param {string} email - Email to validate
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Utility function to validate phone number
 * @param {string} phone - Phone number to validate
 */
function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

/**
 * Debounce function for search inputs
 * @param {function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @param {boolean} immediate - Execute immediately
 */
function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}
