"use strict";

// Global variables
let staffDataTable;
let isEditMode = false;
let stores = [];

$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();

    // Load stores from DOM (populated by controller)
    loadStoresFromDOM();

    // Load staff data
    loadStaff();

    // Form submission handler
    $('#staffForm').on('submit', handleFormSubmit);

    // Modal show handler - reinitialize Select2
    $('#staffModal').on('shown.bs.modal', function() {
        // Small delay to ensure modal is fully rendered
        setTimeout(function() {
            // Store current validation state before reinitializing Select2
            const validationState = {};
            $('.is-invalid').each(function() {
                const name = $(this).attr('name');
                const errorElement = '#' + name + 'Error';
                validationState[name] = {
                    element: this,
                    errorText: $(errorElement).text()
                };
            });
            
            // Destroy existing Select2 if it exists
            if ($('#storeSelect').hasClass('select2-hidden-accessible')) {
                $('#storeSelect').select2('destroy');
            }
            
            // Reinitialize Select2 for elements in the modal
            if (typeof window.reinitializeSelect2 === 'function') {
                window.reinitializeSelect2('#staffModal');
            } else {
                // Fallback: manually initialize Select2
                $('#storeSelect').select2({
                    width: '100%',
                    allowClear: false,
                    minimumResultsForSearch: 0,
                    placeholder: 'Select Store',
                    dropdownParent: $('#staffModal'), // Important: attach to modal
                    language: {
                        noResults: function() {
                            return "No results found";
                        },
                        searching: function() {
                            return "Searching...";
                        }
                    }
                });
            }
            
            // Restore validation state after Select2 initialization
            setTimeout(function() {
                for (const fieldName in validationState) {
                    const state = validationState[fieldName];
                    $(state.element).addClass('is-invalid');
                    $('#' + fieldName + 'Error').text(state.errorText);
                    
                    // For Select2 elements, also add invalid class to the container
                    if ($(state.element).hasClass('select2-hidden-accessible')) {
                        $(state.element).next('.select2-container').addClass('is-invalid');
                    }
                }
            }, 50);
        }, 100);
    });

    // Modal close handler
    $('#staffModal').on('hidden.bs.modal', function() {
        // Destroy Select2 to prevent memory leaks
        if ($('#storeSelect').hasClass('select2-hidden-accessible')) {
            $('#storeSelect').select2('destroy');
        }
        resetForm();
    });

    // Avatar preview handler
    $('#avatar').on('change', function() {
        previewAvatar(this);
    });
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    staffDataTable = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "columns": [
            { "title": "Avatar", "orderable": false },
            { "title": "Name" },
            { "title": "Email" },
            { "title": "Phone" },
            { "title": "Designation" },
            { "title": "Store" },
            { "title": "Options", "orderable": false }
        ],
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] } // Avatar and Options columns
        ],
        "language": {
            "emptyTable": "No staff found",
            "zeroRecords": "No matching staff found"
        }
    });
}

/**
 * Load stores from DOM (populated by controller)
 */
function loadStoresFromDOM() {
    const storeSelect = $('#storeSelect');
    stores = [];

    storeSelect.find('option').each(function() {
        const value = $(this).val();
        const text = $(this).text();

        if (value && value !== '') {
            stores.push({
                id: parseInt(value),
                name: text
            });
        }
    });
}

/**
 * Load stores for dropdown (AJAX method - kept as fallback)
 */
function loadStores() {
    $.ajax({
        url: '/admin/staff/get-stores',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                stores = response.data;
                populateStoreDropdown();
            } else {
                console.error('Failed to load stores:', response.message);
                showToastr('error', 'Failed to load stores');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading stores:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);

            if (xhr.status === 404) {
                showToastr('error', 'Route not found. Please check if the server is running correctly.');
            } else if (xhr.status === 401) {
                showToastr('error', 'Authentication required. Please log in again.');
            } else if (xhr.status === 403) {
                showToastr('error', 'Access denied. You do not have permission to access this resource.');
            } else {
                showToastr('error', 'Failed to load stores. Please try again.');
            }
        }
    });
}

/**
 * Populate store dropdown
 */
function populateStoreDropdown() {
    const storeSelect = $('#storeSelect');

    if (storeSelect.length === 0) {
        console.error('Store select element not found!');
        return;
    }

    storeSelect.empty();
    storeSelect.append('<option value="">Select Store</option>');

    if (stores && stores.length > 0) {
        stores.forEach(function(store) {
            storeSelect.append(`<option value="${store.id}">${store.name}</option>`);
        });
    } else {
        console.warn('No stores to populate');
    }
}

/**
 * Load staff data via AJAX
 */
function loadStaff() {
    $.ajax({
        url: '/admin/staff/get-data',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#dataTable_processing').show(); // built-in processing element
        },
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load staff');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading staff:', error);
            showToastr('error', 'Failed to load staff');
        },
        complete: function() {
            $('#dataTable_processing').hide();
        }
    });
}

/**
 * Populate DataTable with staff data
 */
function populateTable(staff) {
    // Clear existing data
    staffDataTable.clear();

    // Add new data
    staff.forEach(function(member) {
        const storeName = member.store ? member.store.name : 'No Store';
        const phone = member.phone || 'N/A';
        const designation = member.designation || 'N/A';

        // Avatar display
        let avatarHtml = '<div class="text-center">';
        if (member.avatar) {
            avatarHtml += `<img src="/storage/${member.avatar}" alt="${member.name}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">`;
        } else {
            avatarHtml += `<div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; font-weight: bold;">${member.name.charAt(0).toUpperCase()}</div>`;
        }
        avatarHtml += '</div>';

        const actions = `
            <div class="text-center">
                <button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${member.id})">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${member.id}, '${member.name.replace(/'/g, "\\'")}')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        `;

        staffDataTable.row.add([
            avatarHtml,
            member.name,
            member.email,
            phone,
            designation,
            storeName,
            actions
        ]);
    });

    // Redraw table
    staffDataTable.draw();
}

/**
 * Open create modal
 */
function openCreateModal() {
    resetForm();
    $('#staffModalLabel').text('Add Staff');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');

    // Make password required for create
    $('#password').prop('required', true);
    $('#passwordConfirmation').prop('required', true);
    $('#passwordRequired').show();
    $('#passwordConfirmationRequired').show();
    $('#passwordHelp').text('Minimum 8 characters required');

    // Stores should already be loaded from DOM
}

/**
 * Open edit modal
 */
function openEditModal(staffId) {
    if (!staffId) {
        showToastr('error', 'Invalid staff ID');
        return;
    }

    // Fetch staff data
    $.ajax({
        url: '/admin/staff/' + staffId + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const staff = response.data;

                isEditMode = true;
                $('#staffModalLabel').text('Edit Staff');
                $('#staffId').val(staff.id);
                $('#staffName').val(staff.name);
                $('#email').val(staff.email);
                $('#phoneNumber').val(staff.phone || '');
                $('#designation').val(staff.designation || '');
                $('#address').val(staff.address || '');
                $('#storeSelect').val(staff.store_id || '');
                $('#formMethod').val('PUT');

                // Handle current avatar display
                if (staff.avatar) {
                    $('#currentAvatarImg').attr('src', '/storage/' + staff.avatar);
                    $('#currentAvatar').show();
                } else {
                    $('#currentAvatar').hide();
                }
                $('#avatarPreview').hide();
                $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

                // Make password optional for edit
                $('#password').prop('required', false);
                $('#passwordConfirmation').prop('required', false);
                $('#passwordRequired').hide();
                $('#passwordConfirmationRequired').hide();
                $('#passwordHelp').text('Leave blank to keep current password');

                // Clear validation errors
                clearValidationErrors();

                // Show modal
                $('#staffModal').modal('show');
            } else {
                showToastr('error', response.message || 'Failed to fetch staff data');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching staff:', error);
            showToastr('error', 'Failed to fetch staff data');
        }
    });
}

/**
 * Open delete confirmation with SweetAlert
 */
function openDeleteModal(staffId, staffName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${staffName}" staff member? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(staffId, staffName);
        }
    });
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const staffId = $('#staffId').val();
    const method = $('#formMethod').val();

    let url = '/admin/staff';
    let ajaxMethod = 'POST';

    if (isEditMode && staffId) {
        url += '/' + staffId;
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
                $('#staffModal').modal('hide');

                // Reload data without page refresh
                loadStaff();
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
function handleDelete(staffId, staffName) {
    if (!staffId) {
        showToastr('error', 'Invalid staff ID');
        return;
    }

    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the staff member.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '/admin/staff/' + staffId,
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
                    loadStaff();
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
                text: 'Failed to delete staff member',
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

            // Set error message
            $(errorElement).text(errors[field][0]);
            
            // Add invalid class to input
            $(inputElement).addClass('is-invalid');
            
            // For Select2 elements, also add invalid class to the container
            if ($(inputElement).hasClass('select2-hidden-accessible')) {
                $(inputElement).next('.select2-container').addClass('is-invalid');
            }
        }
    }
    
    // Ensure modal stays open to show errors
    if (!$('#staffModal').hasClass('show')) {
        $('#staffModal').modal('show');
    }
}

/**
 * Clear validation errors
 */
function clearValidationErrors() {
    // Remove invalid class from all inputs
    $('.is-invalid').removeClass('is-invalid');
    
    // Clear all error messages
    $('.invalid-feedback').text('');
    
    // Also clear Select2 container invalid classes
    $('.select2-container.is-invalid').removeClass('is-invalid');
}

/**
 * Preview avatar image
 */
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            $('#avatarPreviewImg').attr('src', e.target.result);
            $('#avatarPreview').show();
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        $('#avatarPreview').hide();
    }
}

/**
 * Reset form to initial state
 */
function resetForm() {
    $('#staffForm')[0].reset();
    $('#staffId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();

    // Reset password requirements for create mode
    $('#password').prop('required', true);
    $('#passwordConfirmation').prop('required', true);
    $('#passwordRequired').show();
    $('#passwordConfirmationRequired').show();
    $('#passwordHelp').text('Minimum 8 characters required');

    // Reset avatar displays
    $('#currentAvatar').hide();
    $('#avatarPreview').hide();

    // Store dropdown is already populated from DOM, no need to reset
}
