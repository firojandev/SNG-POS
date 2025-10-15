"use strict";

// Global variables
let incomeCategoryDataTable;
let isEditMode = false;

// Test function to verify JS is loaded
window.testIncomeCategoryJS = function() {
    alert('Income Category JS is loaded!');
    console.log('IncomeCategoryDataTable:', incomeCategoryDataTable);
    console.log('DataTable element:', $('#dataTable').length);
};

$(document).ready(function() {
    console.log('Income Category JS loaded');
    console.log('DataTable element exists:', $('#dataTable').length > 0);
    
    // Initialize DataTable
    initializeDataTable();

    // Load income categories data
    loadIncomeCategories();

    // Form submission handler
    $('#incomeCategoryForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#incomeCategoryModal').on('hidden.bs.modal', function() {
        resetForm();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    console.log('Initializing DataTable...');
    try {
        incomeCategoryDataTable = $('#dataTable').DataTable({
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
                "emptyTable": "No income categories found",
                "zeroRecords": "No matching income categories found"
            }
        });
        console.log('DataTable initialized successfully');
    } catch (error) {
        console.error('DataTable initialization error:', error);
    }
}

/**
 * Load income categories data via AJAX
 */
function loadIncomeCategories() {
    console.log('Loading income categories...');
    $.ajax({
        url: '/admin/income-category/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            console.log('AJAX response:', response);
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load income categories');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading income categories:', error);
            console.error('XHR:', xhr);
            showToastr('error', 'Failed to load income categories');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with income category data
 */
function populateTable(incomeCategories) {
    // Clear existing data
    incomeCategoryDataTable.clear();

    // Add new data
    incomeCategories.forEach(function(incomeCategory) {
        const actions = `
            <div class="text-center"><button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${incomeCategory.id}, '${incomeCategory.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${incomeCategory.id}, '${incomeCategory.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-trash"></i> Delete
            </button></div>
        `;

        incomeCategoryDataTable.row.add([
            incomeCategory.name,
            actions
        ]);
    });

    // Redraw table
    incomeCategoryDataTable.draw();
}

/**
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#incomeCategoryModalLabel').text('Add Income Category');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(incomeCategoryId, incomeCategoryName) {
    isEditMode = true;
    $('#incomeCategoryModalLabel').text('Edit Income Category');
    $('#incomeCategoryId').val(incomeCategoryId);
    $('#incomeCategoryName').val(incomeCategoryName);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Clear validation errors
    clearValidationErrors();

    // Show modal
    $('#incomeCategoryModal').modal('show');
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(incomeCategoryId, incomeCategoryName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${incomeCategoryName}" income category? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(incomeCategoryId, incomeCategoryName);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const incomeCategoryId = $('#incomeCategoryId').val();
    const method = $('#formMethod').val();

    let url = '/admin/income-category';
    let ajaxMethod = 'POST';

    if (isEditMode && incomeCategoryId) {
        url += '/' + incomeCategoryId;
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
                $('#incomeCategoryModal').modal('hide');

                // Reload data without page refresh
                loadIncomeCategories();
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
function handleDelete(incomeCategoryId, incomeCategoryName) {
    if (!incomeCategoryId) {
        showToastr('error', 'Invalid income category ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the income category.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/income-category/' + incomeCategoryId,
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
                    loadIncomeCategories();
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
                text: 'Failed to delete income category',
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
    $('#incomeCategoryForm')[0].reset();
    $('#incomeCategoryId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}
