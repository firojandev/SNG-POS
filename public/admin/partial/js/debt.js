"use strict";

// Global variables
let debtDataTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Initialize date picker
    initializeDatePicker();

    // Load debts data
    loadDebts();

    // Form submission handler
    $('#debtForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#debtModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Re-initialize date picker when modal is shown
    $('#debtModal').on('shown.bs.modal', function() {
        initializeDatePicker();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    debtDataTable = $('#dataTable').DataTable({
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
            "emptyTable": "No debts found",
            "zeroRecords": "No matching debts found"
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
        $('#debtDate').datepicker({
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
 * Load debts data via AJAX
 */
function loadDebts() {
    $.ajax({
        url: '/admin/debt/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load debts');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading debts:', error);
            showToastr('error', 'Failed to load debts');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with debt data
 */
function populateTable(debts) {
    // Clear existing data
    debtDataTable.clear();

    // Add new data
    debts.forEach(function(debt) {
        const noteDisplay = debt.note ? debt.note.substring(0, 50) + (debt.note.length > 50 ? '...' : '') : '-';
        const statusBadge = debt.status === 'Paid'
            ? '<span class="badge bg-success">Paid</span>'
            : '<span class="badge bg-danger">Unpaid</span>';

        const actions = `
            <div class="text-center"><button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${debt.id})">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${debt.id}, '${escapeHtml(debt.lender)}')">
                <i class="fa fa-trash"></i> Delete
            </button></div>
        `;

        debtDataTable.row.add([
            debt.lender,
            debt.date,
            (typeof formatCurrency === 'function') ? formatCurrency(debt.amount, (window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$', 2) : (((window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$') + parseFloat(debt.amount).toFixed(2)),
            statusBadge,
            noteDisplay,
            actions
        ]);
    });

    // Redraw table
    debtDataTable.draw();
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
    $('#debtModalLabel').text('Add Debt');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(debtId) {
    isEditMode = true;
    $('#debtModalLabel').text('Edit Debt');
    $('#debtId').val(debtId);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Fetch debt data via AJAX to get properly formatted date
    $.ajax({
        url: '/admin/debt/' + debtId + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const debt = response.data;
                $('#debtLender').val(debt.lender);
                $('#debtDate').val(debt.date);
                $('#debtAmount').val(debt.amount);
                $('#debtNote').val(debt.note || '');
                $('#debtStatus').val(debt.status);

                // Clear validation errors
                clearValidationErrors();

                // Show modal
                $('#debtModal').modal('show');
            } else {
                showToastr('error', 'Failed to load debt');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading debt:', error);
            showToastr('error', 'Failed to load debt');
        }
    });
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(debtId, debtLender) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete debt from "${debtLender}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(debtId, debtLender);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const debtId = $('#debtId').val();
    const method = $('#formMethod').val();

    let url = '/admin/debt';
    let ajaxMethod = 'POST';

    if (isEditMode && debtId) {
        url += '/' + debtId;
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
                $('#debtModal').modal('hide');

                // Reload data without page refresh
                loadDebts();
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
function handleDelete(debtId, debtLender) {
    if (!debtId) {
        showToastr('error', 'Invalid debt ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the debt.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/debt/' + debtId,
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
                    loadDebts();
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
                text: 'Failed to delete debt',
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
    $('#debtForm')[0].reset();
    $('#debtId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}
