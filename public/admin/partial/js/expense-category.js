"use strict";

// Global variables
let expenseCategoryDataTable;
let isEditMode = false;

// Test function to verify JS is loaded
window.testExpenseCategoryJS = function() {
    alert('Expense Category JS is loaded!');
    console.log('ExpenseCategoryDataTable:', expenseCategoryDataTable);
    console.log('DataTable element:', $('#dataTable').length);
};

$(document).ready(function() {
    console.log('Expense Category JS loaded');
    console.log('DataTable element exists:', $('#dataTable').length > 0);
    
    // Initialize DataTable
    initializeDataTable();

    // Load expense categories data
    loadExpenseCategories();

    // Form submission handler
    $('#expenseCategoryForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#expenseCategoryModal').on('hidden.bs.modal', function() {
        resetForm();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    console.log('Initializing DataTable...');
    try {
        expenseCategoryDataTable = $('#dataTable').DataTable({
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
                "emptyTable": "No expense categories found",
                "zeroRecords": "No matching expense categories found"
            }
        });
        console.log('DataTable initialized successfully');
    } catch (error) {
        console.error('DataTable initialization error:', error);
    }
}

/**
 * Load expense categories data via AJAX
 */
function loadExpenseCategories() {
    console.log('Loading expense categories...');
    $.ajax({
        url: '/admin/expense-category/get-data',
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
                showToastr('error', 'Failed to load expense categories');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading expense categories:', error);
            console.error('XHR:', xhr);
            showToastr('error', 'Failed to load expense categories');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}


/**
 * Populate DataTable with expense category data
 */
function populateTable(expenseCategories) {
    // Clear existing data
    expenseCategoryDataTable.clear();

    // Add new data
    expenseCategories.forEach(function(expenseCategory) {
        const actions = `
            <div class="text-center"><button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${expenseCategory.id}, '${expenseCategory.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${expenseCategory.id}, '${expenseCategory.name.replace(/'/g, "\\'")}')">
                <i class="fa fa-trash"></i> Delete
            </button></div>
        `;

        expenseCategoryDataTable.row.add([
            expenseCategory.name,
            actions
        ]);
    });

    // Redraw table
    expenseCategoryDataTable.draw();
}

/**
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#expenseCategoryModalLabel').text('Add Expense Category');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(expenseCategoryId, expenseCategoryName) {
    isEditMode = true;
    $('#expenseCategoryModalLabel').text('Edit Expense Category');
    $('#expenseCategoryId').val(expenseCategoryId);
    $('#expenseCategoryName').val(expenseCategoryName);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Clear validation errors
    clearValidationErrors();

    // Show modal
    $('#expenseCategoryModal').modal('show');
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(expenseCategoryId, expenseCategoryName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${expenseCategoryName}" expense category? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(expenseCategoryId, expenseCategoryName);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const expenseCategoryId = $('#expenseCategoryId').val();
    const method = $('#formMethod').val();

    let url = '/admin/expense-category';
    let ajaxMethod = 'POST';

    if (isEditMode && expenseCategoryId) {
        url += '/' + expenseCategoryId;
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
                $('#expenseCategoryModal').modal('hide');

                // Reload data without page refresh
                loadExpenseCategories();
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
function handleDelete(expenseCategoryId, expenseCategoryName) {
    if (!expenseCategoryId) {
        showToastr('error', 'Invalid expense category ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the expense category.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/expense-category/' + expenseCategoryId,
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
                    loadExpenseCategories();
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
                text: 'Failed to delete expense category',
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
    $('#expenseCategoryForm')[0].reset();
    $('#expenseCategoryId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
}