"use strict";

// Global variables
let vatDataTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Load VATs data
    loadVats();

    // Form submission handler
    $('#vatForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#vatModal').on('hidden.bs.modal', function() {
        resetForm();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    vatDataTable = $('#dataTable').DataTable({
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
            "emptyTable": "No VATs found",
            "zeroRecords": "No matching VATs found"
        }
    });
}

/**
 * Load VATs data via AJAX
 */
function loadVats() {
    $.ajax({
        url: '/admin/vat/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load VATs');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading VATs:', error);
            showToastr('error', 'Failed to load VATs');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with VAT data
 */
function populateTable(vats) {
    // Clear existing data
    vatDataTable.clear();

    // Add new data
    vats.forEach(function(vat) {
        const actions = `
            <div class="text-center"><button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${vat.id}, '${vat.name.replace(/'/g, "\\'")}', '${vat.value}')">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${vat.id}, '${vat.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-trash"></i> Delete
            </button></div>
        `;

        vatDataTable.row.add([
            vat.name,
            vat.value + '%',
            actions
        ]);
    });

    // Redraw table
    vatDataTable.draw();
}

/**
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#vatModalLabel').text('Add VAT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(vatId, vatName, vatValue) {
    isEditMode = true;
    $('#vatModalLabel').text('Edit VAT');
    $('#vatId').val(vatId);
    $('#vatName').val(vatName);
    $('#vatValue').val(vatValue);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Clear validation errors
    clearValidationErrors();

    // Show modal
    $('#vatModal').modal('show');
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(vatId, vatName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${vatName}" VAT? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(vatId, vatName);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const vatId = $('#vatId').val();
    const method = $('#formMethod').val();

    let url = '/admin/vat';
    let ajaxMethod = 'POST';

    if (isEditMode && vatId) {
        url += '/' + vatId;
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
                $('#vatModal').modal('hide');

                // Reload data without page refresh
                loadVats();
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
function handleDelete(vatId, vatName) {
    if (!vatId) {
        showToastr('error', 'Invalid VAT ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the VAT.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/vat/' + vatId,
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
                    loadVats();
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
                text: 'Failed to delete VAT',
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
    $('#vatForm')[0].reset();
    $('#vatId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}
