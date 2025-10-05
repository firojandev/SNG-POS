"use strict";

/**
 * Currency Management JavaScript
 * This file contains all JavaScript functionality for currency management
 */

$(document).ready(function() {
    initializeCurrencyModule();
});

/**
 * Initialize currency module
 */
function initializeCurrencyModule() {
    setupDeleteCurrencyHandlers();
    setupCurrencyFormValidation();
    handleValidationErrors();
}

/**
 * Setup delete currency button handlers
 */
function setupDeleteCurrencyHandlers() {
    $(document).on('click', '.delete-currency', function() {
        const currencyId = $(this).data('id');
        const currencyName = $(this).data('name');
        const currencySymbol = $(this).data('symbol');
        
        deleteCurrency(currencyId, currencyName, currencySymbol);
    });
}

/**
 * Delete currency with confirmation
 * @param {number} currencyId - Currency ID
 * @param {string} currencyName - Currency name
 * @param {string} currencySymbol - Currency symbol
 */
function deleteCurrency(currencyId, currencyName, currencySymbol) {
    const title = 'Are you sure?';
    const text = `You want to delete currency "${currencyName} (${currencySymbol})"? This action cannot be undone!`;
    const confirmText = 'Yes, delete it!';
    const cancelText = 'Cancel';
    
    showConfirmDialog(
        title,
        text,
        confirmText,
        cancelText,
        function() {
            // Create and submit delete form
            submitDeleteCurrencyForm(currencyId);
        },
        'warning'
    );
}

/**
 * Submit delete currency form
 * @param {number} currencyId - Currency ID to delete
 */
function submitDeleteCurrencyForm(currencyId) {
    // Create form element
    const form = $('<form>', {
        'method': 'POST',
        'action': `/admin/currency/${currencyId}`
    });
    
    // Add CSRF token
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': $('meta[name="csrf-token"]').attr('content')
    }));
    
    // Add DELETE method
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_method',
        'value': 'DELETE'
    }));
    
    // Append to body and submit
    $('body').append(form);
    form.submit();
}

/**
 * Setup currency form validation
 */
function setupCurrencyFormValidation() {
    $('#currencyForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const symbol = $('#symbol').val().trim();
        
        if (!name || !symbol) {
            e.preventDefault();
            showToastr('error', 'Please fill in all required fields.');
            return false;
        }
        
        // Additional validation can be added here
        return true;
    });
}

/**
 * Reset currency form
 */
function resetCurrencyForm() {
    $('#currencyForm')[0].reset();
    $('#currencyForm .is-invalid').removeClass('is-invalid');
    $('#currencyForm .invalid-feedback').remove();
}

/**
 * Show currency modal
 */
function showCurrencyModal() {
    resetCurrencyForm();
    $('#currencyModal').modal('show');
}

/**
 * Hide currency modal
 */
function hideCurrencyModal() {
    $('#currencyModal').modal('hide');
}

/**
 * Refresh currency list (if using AJAX)
 */
function refreshCurrencyList() {
    // This function can be implemented if you want to refresh the list via AJAX
    // For now, we'll just reload the page
    window.location.reload();
}

/**
 * Handle currency selection change
 */
function handleCurrencySelectionChange() {
    $('#currency_symbol').on('change', function() {
        const selectedCurrency = $(this).find('option:selected').text();
        console.log('Currency changed to:', selectedCurrency);
        
        // You can add additional logic here when currency selection changes
    });
}

/**
 * Handle validation errors - show modal if there are errors
 */
function handleValidationErrors() {
    // Check if there are validation errors by looking for error elements
    if ($('.alert-danger').length > 0 || $('.is-invalid').length > 0) {
        $('#currencyModal').modal('show');
    }
}

// Initialize currency selection handler
$(document).ready(function() {
    handleCurrencySelectionChange();
});
