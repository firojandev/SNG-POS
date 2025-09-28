"use strict";

// Global variables
let taxDataTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Load taxes data
    loadTaxes();

    // Form submission handler
    $('#taxForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#taxModal').on('hidden.bs.modal', function() {
        resetForm();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    taxDataTable = $('#dataTable').DataTable({
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
            "emptyTable": "No taxes found",
            "zeroRecords": "No matching taxes found"
        }
    });
}

/**
 * Load taxes data via AJAX
 */
function loadTaxes() {
    $.ajax({
        url: '/admin/tax/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load taxes');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading taxes:', error);
            showToastr('error', 'Failed to load taxes');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with tax data
 */
function populateTable(taxes) {
    // Clear existing data
    taxDataTable.clear();

    // Add new data
    taxes.forEach(function(tax) {
        const actions = `
            <div class="text-center"><button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${tax.id}, '${tax.name.replace(/'/g, "\\'")}', '${tax.value}')">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${tax.id}, '${tax.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-trash"></i> Delete
            </button></div>
        `;

        taxDataTable.row.add([
            tax.name,
            tax.value + '%',
            actions
        ]);
    });

    // Redraw table
    taxDataTable.draw();
}

/**
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#taxModalLabel').text('Add Tax');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(taxId, taxName, taxValue) {
    isEditMode = true;
    $('#taxModalLabel').text('Edit Tax');
    $('#taxId').val(taxId);
    $('#taxName').val(taxName);
    $('#taxValue').val(taxValue);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Clear validation errors
    clearValidationErrors();

    // Show modal
    $('#taxModal').modal('show');
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(taxId, taxName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${taxName}" tax? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(taxId, taxName);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const taxId = $('#taxId').val();
    const method = $('#formMethod').val();

    let url = '/admin/tax';
    let ajaxMethod = 'POST';

    if (isEditMode && taxId) {
        url += '/' + taxId;
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
                $('#taxModal').modal('hide');

                // Reload data without page refresh
                loadTaxes();
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
function handleDelete(taxId, taxName) {
    if (!taxId) {
        showToastr('error', 'Invalid tax ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the tax.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/tax/' + taxId,
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
                    loadTaxes();
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
                text: 'Failed to delete tax',
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
    $('#taxForm')[0].reset();
    $('#taxId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}
