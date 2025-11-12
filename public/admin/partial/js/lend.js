"use strict";

// Global variables
let lendDataTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Initialize date picker
    initializeDatePicker();

    // Load lends data
    loadLends();

    // Form submission handler
    $('#lendForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#lendModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Re-initialize date picker when modal is shown
    $('#lendModal').on('shown.bs.modal', function() {
        initializeDatePicker();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    lendDataTable = $('#dataTable').DataTable({
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
            "emptyTable": "No lends found",
            "zeroRecords": "No matching lends found"
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
        $('#lendDate').datepicker({
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
 * Load lends data via AJAX
 */
function loadLends() {
    $.ajax({
        url: '/admin/lend/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load lends');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading lends:', error);
            showToastr('error', 'Failed to load lends');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with lend data
 */
function populateTable(lends) {
    // Clear existing data
    lendDataTable.clear();

    // Add new data
    lends.forEach(function(lend) {
        const noteDisplay = lend.note ? lend.note.substring(0, 50) + (lend.note.length > 50 ? '...' : '') : '-';
        const statusBadge = lend.status === 'Received'
            ? '<span class="badge bg-success">Received</span>'
            : '<span class="badge bg-warning">Due</span>';

        const actions = `
            <div class="text-center"><button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${lend.id})">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${lend.id}, '${escapeHtml(lend.borrower)}')">
                <i class="fa fa-trash"></i> Delete
            </button></div>
        `;

        lendDataTable.row.add([
            lend.borrower,
            lend.date,
            (typeof formatCurrency === 'function') ? formatCurrency(lend.amount, (window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$', 2) : (((window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$') + parseFloat(lend.amount).toFixed(2)),
            statusBadge,
            noteDisplay,
            actions
        ]);
    });

    // Redraw table
    lendDataTable.draw();
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
    $('#lendModalLabel').text('Add Lend');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(lendId) {
    isEditMode = true;
    $('#lendModalLabel').text('Edit Lend');
    $('#lendId').val(lendId);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Fetch lend data via AJAX to get properly formatted date
    $.ajax({
        url: '/admin/lend/' + lendId + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const lend = response.data;
                $('#lendBorrower').val(lend.borrower);
                $('#lendDate').val(lend.date);
                $('#lendAmount').val(lend.amount);
                $('#lendNote').val(lend.note || '');
                $('#lendStatus').val(lend.status);

                // Clear validation errors
                clearValidationErrors();

                // Show modal
                $('#lendModal').modal('show');
            } else {
                showToastr('error', 'Failed to load lend');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading lend:', error);
            showToastr('error', 'Failed to load lend');
        }
    });
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(lendId, lendBorrower) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete lend to "${lendBorrower}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(lendId, lendBorrower);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const lendId = $('#lendId').val();
    const method = $('#formMethod').val();

    let url = '/admin/lend';
    let ajaxMethod = 'POST';

    if (isEditMode && lendId) {
        url += '/' + lendId;
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
                $('#lendModal').modal('hide');

                // Reload data without page refresh
                loadLends();
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
function handleDelete(lendId, lendBorrower) {
    if (!lendId) {
        showToastr('error', 'Invalid lend ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the lend.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/lend/' + lendId,
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
                    loadLends();
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
                text: 'Failed to delete lend',
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
    $('#lendForm')[0].reset();
    $('#lendId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}
