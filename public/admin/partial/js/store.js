"use strict";

// Global variables
let storeDataTable;
let isEditMode = false;

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Load stores data
    loadStores();

    // Form submission handler
    $('#storeForm').on('submit', handleFormSubmit);

    // Modal close handler
    $('#storeModal').on('hidden.bs.modal', function() {
        resetForm();
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    storeDataTable = $('#dataTable').DataTable({
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
            "emptyTable": "No stores found",
            "zeroRecords": "No matching stores found"
        }
    });
}

/**
 * Load stores data via AJAX
 */
function loadStores() {
    $.ajax({
        url: '/admin/store/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load stores');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading stores:', error);
            showToastr('error', 'Failed to load stores');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}

/**
 * Populate DataTable with store data
 */
function populateTable(stores) {
    // Clear existing data
    storeDataTable.clear();

    // Add new data
    stores.forEach(function(store) {
        const statusBadge = store.is_active 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-danger">Inactive</span>';

        const actions = `
            <div class="text-center">
                <button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${store.id})">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${store.id}, '${store.name.replace(/'/g, "\\'")}')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        `;

        storeDataTable.row.add([
            store.name,
            store.contact_person,
            store.phone_number,
            store.email,
            statusBadge,
            actions
        ]);
    });

    // Redraw table
    storeDataTable.draw();
}

/**
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#storeModalLabel').text('Add Store');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

/**
 * Open edit modal
 */
function openEditModal(storeId) {
    if (!storeId) {
        showToastr('error', 'Invalid store ID');
        return;
    }

    // Fetch store data
    $.ajax({
        url: '/admin/store/' + storeId + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const store = response.data;
                
                isEditMode = true;
                $('#storeModalLabel').text('Edit Store');
                $('#storeId').val(store.id);
                $('#storeName').val(store.name);
                $('#contactPerson').val(store.contact_person);
                $('#phoneNumber').val(store.phone_number);
                $('#email').val(store.email);
                $('#address').val(store.address);
                $('#details').val(store.details || '');
                $('#isActive').prop('checked', store.is_active);
                $('#formMethod').val('PUT');
                $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

                // Clear validation errors
                clearValidationErrors();

                // Show modal
                $('#storeModal').modal('show');
            } else {
                showToastr('error', response.message || 'Failed to fetch store data');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching store:', error);
            showToastr('error', 'Failed to fetch store data');
        }
    });
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(storeId, storeName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${storeName}" store? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(storeId, storeName);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const storeId = $('#storeId').val();
    const method = $('#formMethod').val();

    // Handle checkbox value
    if (!$('#isActive').is(':checked')) {
        formData.set('is_active', '0');
    }

    let url = '/admin/store';
    let ajaxMethod = 'POST';

    if (isEditMode && storeId) {
        url += '/' + storeId;
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
                $('#storeModal').modal('hide');

                // Reload data without page refresh
                loadStores();
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
function handleDelete(storeId, storeName) {
    if (!storeId) {
        showToastr('error', 'Invalid store ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the store.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/store/' + storeId,
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
                    loadStores();
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
                text: 'Failed to delete store',
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
    $('#storeForm')[0].reset();
    $('#storeId').val('');
    $('#formMethod').val('POST');
    $('#isActive').prop('checked', true);
    isEditMode = false;
    clearValidationErrors();
}
