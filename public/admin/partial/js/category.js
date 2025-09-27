"use strict";

// Global variables
let categoryDataTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Load categories data
    loadCategories();

    // Form submission handler
    $('#categoryForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#categoryModal').on('hidden.bs.modal', function() {
        resetForm();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    categoryDataTable = $('#dataTable').DataTable({
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
            "emptyTable": "No categories found",
            "zeroRecords": "No matching categories found"
        }
    });
}

/**
 * Load categories data via AJAX
 */
function loadCategories() {
    $.ajax({
        url: '/admin/category/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load categories');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading categories:', error);
            showToastr('error', 'Failed to load categories');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with category data
 */
function populateTable(categories) {
    // Clear existing data
    categoryDataTable.clear();

    // Add new data
    categories.forEach(function(category) {
        const actions = `
            <button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${category.id}, '${category.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${category.id}, '${category.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-trash"></i> Delete
            </button>
        `;

        categoryDataTable.row.add([
            category.name,
            actions
        ]);
    });

    // Redraw table
    categoryDataTable.draw();
}

/**
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#categoryModalLabel').text('Add Category');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(categoryId, categoryName) {
    isEditMode = true;
    $('#categoryModalLabel').text('Edit Category');
    $('#categoryId').val(categoryId);
    $('#categoryName').val(categoryName);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Clear validation errors
    clearValidationErrors();

    // Show modal
    $('#categoryModal').modal('show');
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(categoryId, categoryName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${categoryName}" category? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(categoryId, categoryName);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const categoryId = $('#categoryId').val();
    const method = $('#formMethod').val();

    let url = '/admin/category';
    let ajaxMethod = 'POST';

    if (isEditMode && categoryId) {
        url += '/' + categoryId;
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
                $('#categoryModal').modal('hide');

                // Reload data without page refresh
                loadCategories();
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
function handleDelete(categoryId, categoryName) {
    if (!categoryId) {
        showToastr('error', 'Invalid category ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the category.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/category/' + categoryId,
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
                    loadCategories();
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
                text: 'Failed to delete category',
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
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}
