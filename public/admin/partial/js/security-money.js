"use strict";

// Global variables
let securityMoneyDataTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Initialize date picker
    initializeDatePicker();

    // Load security money data
    loadSecurityMoney();

    // Form submission handler
    $('#securityMoneyForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#securityMoneyModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Re-initialize date picker when modal is shown
    $('#securityMoneyModal').on('shown.bs.modal', function() {
        initializeDatePicker();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    securityMoneyDataTable = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "columnDefs": [
            { "orderable": false, "targets": -1 }
        ],
        "language": {
            "emptyTable": "No security money found",
            "zeroRecords": "No matching security money found"
        }
    });
}

/**
 * Initialize jQuery UI datepicker
 */
function initializeDatePicker() {
    // Map PHP date format to jQuery UI datepicker format
    const phpFmt = (window.appConfig && window.appConfig.dateFormatPhp) ? window.appConfig.dateFormatPhp : 'Y-m-d';
    const jqFmt = phpDateFormatToJqueryUI(phpFmt);

    if (typeof $.fn.datepicker !== 'undefined') {
        $('#securityMoneyDate').datepicker({
            dateFormat: jqFmt,
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+10'
        });
    }
}

/**
 * Map PHP date format to jQuery UI datepicker format
 */
function phpDateFormatToJqueryUI(phpFormat) {
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
 * Load security money data via AJAX
 */
function loadSecurityMoney() {
    $.ajax({
        url: '/admin/security-money/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load security money');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading security money:', error);
            showToastr('error', 'Failed to load security money');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with security money data
 */
function populateTable(securityMoneyList) {
    // Clear existing data
    securityMoneyDataTable.clear();

    // Add new data
    securityMoneyList.forEach(function(item) {
        const noteDisplay = item.note ? item.note.substring(0, 50) + (item.note.length > 50 ? '...' : '') : '-';
        const statusBadge = item.status === 'Received'
            ? '<span class="badge bg-success">Received</span>'
            : '<span class="badge bg-warning">Paid</span>';

        const actions = `
            <div class="text-center"><button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${item.id})">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${item.id}, '${escapeHtml(item.receiver)}')">
                <i class="fa fa-trash"></i> Delete
            </button></div>
        `;

        securityMoneyDataTable.row.add([
            item.receiver,
            item.date,
            (typeof formatCurrency === 'function') ? formatCurrency(item.amount, (window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$', 2) : (((window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$') + parseFloat(item.amount).toFixed(2)),
            statusBadge,
            noteDisplay,
            actions
        ]);
    });

    // Redraw table
    securityMoneyDataTable.draw();
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
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
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#securityMoneyModalLabel').text('Add Security Money');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(securityMoneyId) {
    isEditMode = true;
    $('#securityMoneyModalLabel').text('Edit Security Money');
    $('#securityMoneyId').val(securityMoneyId);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Fetch security money data via AJAX to get properly formatted date
    $.ajax({
        url: '/admin/security-money/' + securityMoneyId + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const item = response.data;
                $('#securityMoneyReceiver').val(item.receiver);
                $('#securityMoneyDate').val(item.date);
                $('#securityMoneyAmount').val(item.amount);
                $('#securityMoneyNote').val(item.note || '');
                $('#securityMoneyStatus').val(item.status);

                // Clear validation errors
                clearValidationErrors();

                // Show modal
                $('#securityMoneyModal').modal('show');
            } else {
                showToastr('error', 'Failed to load security money');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading security money:', error);
            showToastr('error', 'Failed to load security money');
        }
    });
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(securityMoneyId, receiver) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete security money for "${receiver}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(securityMoneyId, receiver);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const securityMoneyId = $('#securityMoneyId').val();
    const method = $('#formMethod').val();

    let url = '/admin/security-money';
    let ajaxMethod = 'POST';

    if (isEditMode && securityMoneyId) {
        url += '/' + securityMoneyId;
        ajaxMethod = 'POST'; // Always use POST for AJAX
        formData.append('_method', 'PUT'); // Laravel method spoofing
    }

    // Show loading spinner
    showLoadingSpinner('#saveSpinner', '#saveBtn');

    // Clear previous validation errors
    clearValidationErrors();

    $.ajax({
        url: url,
        type: ajaxMethod,
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToastr('success', response.message);
                $('#securityMoneyModal').modal('hide');

                // Reload data without page refresh
                loadSecurityMoney();
            } else {
                showToastr('error', response.message || 'Operation failed');
            }
        },
        error: function(xhr, status, error) {
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                displayValidationErrors(errors);
                showToastr('error', 'Please correct the validation errors');
            } else {
                console.error('Error:', error);
                showToastr('error', 'An error occurred. Please try again.');
            }
        },
        complete: function() {
            // Hide loading spinner
            hideLoadingSpinner('#saveSpinner', '#saveBtn');
        }
    });
}

/**
 * Handle delete operation
 */
function handleDelete(securityMoneyId, receiver) {
    if (!securityMoneyId) {
        showToastr('error', 'Invalid security money ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the security money.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/security-money/' + securityMoneyId,
        type: 'POST',
        data: {
            _method: 'DELETE'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Deleted!',
                    text: response.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload data without page refresh
                    loadSecurityMoney();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message || 'Delete operation failed',
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to delete security money',
                icon: 'error'
            });
        }
    });
}

/**
 * Show loading spinner on button
 */
function showLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).removeClass('d-none');
    $(buttonSelector).prop('disabled', true);
}

/**
 * Hide loading spinner on button
 */
function hideLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).addClass('d-none');
    $(buttonSelector).prop('disabled', false);
}

/**
 * Display validation errors
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
 * Reset form to initial state
 */
function resetForm() {
    $('#securityMoneyForm')[0].reset();
    $('#securityMoneyId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}
