"use strict";

// Global variables
let roleDataTable;
let currentPage = null; // 'index', 'create', or 'edit'

/**
 * Initialize based on current page
 */
$(document).ready(function() {
    // Detect current page
    if ($('#roleTableBody').length) {
        currentPage = 'index';
        initializeIndexPage();
    } else if ($('#roleForm').length) {
        if ($('input[name="_method"]').val() === 'PUT') {
            currentPage = 'edit';
        } else {
            currentPage = 'create';
        }
        initializeFormPage();
    }
});

/**
 * ========================================
 * INDEX PAGE FUNCTIONALITY
 * ========================================
 */

/**
 * Initialize index page
 */
function initializeIndexPage() {
    // Initialize DataTable
    initializeDataTable();

    // Load roles data
    loadRoles();
}

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    roleDataTable = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "language": {
            "emptyTable": "No roles found",
            "zeroRecords": "No matching roles found"
        }
    });
}

/**
 * Load roles data via AJAX
 */
function loadRoles() {
    $.ajax({
        url: '/admin/roles/get-data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayRoles(response.data);
            } else {
                notyf.error('Failed to load roles');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading roles:', error);
            notyf.error('Failed to load roles');
        }
    });
}

/**
 * Display roles in table
 */
function displayRoles(roles) {
    // Clear existing data
    roleDataTable.clear();

    if (roles.length === 0) {
        roleDataTable.row.add([
            '<td colspan="3" class="text-center">No roles found</td>',
            '',
            ''
        ]);
    } else {
        roles.forEach(function(role) {
            const deleteButton = role.name !== 'Admin' ? `
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteRole(${role.id}, '${role.name.replace(/'/g, "\\'")}')">
                    <i class="fa fa-trash"></i>
                </button>
            ` : '';

            const actions = `
                <div class="text-center">
                    <a href="/admin/roles/${role.id}/edit" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                    </a>
                    ${deleteButton}
                </div>
            `;

            roleDataTable.row.add([
                role.name,
                `<span class="badge bg-info">${role.permissions.length} Permissions</span>`,
                actions
            ]);
        });
    }

    // Redraw table
    roleDataTable.draw();
}

/**
 * Delete role with confirmation
 */
function deleteRole(roleId, roleName) {
    if (!roleId) {
        notyf.error('Invalid role ID');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${roleName}" role? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(roleId, roleName);
        }
    });
}

/**
 * Handle delete operation
 */
function handleDelete(roleId, roleName) {
    // Show loading with SweetAlert
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the role.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: `/admin/roles/${roleId}`,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                    // Reload roles data
                    loadRoles();
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
                text: xhr.responseJSON?.message || 'Failed to delete role',
                icon: 'error'
            });
        }
    });
}

/**
 * ========================================
 * FORM PAGE FUNCTIONALITY (CREATE/EDIT)
 * ========================================
 */

/**
 * Initialize form page (both create and edit)
 */
function initializeFormPage() {
    // Handle global "Select All" checkbox
    $('#selectAll').on('click', toggleAllPermissions);

    // Handle category-level "Select All" checkboxes
    $('.category-select-all').on('change', handleCategorySelectAll);

    // Handle individual permission checkboxes
    $('.permission-checkbox').on('change', handlePermissionChange);

    // Handle form submission
    $('#roleForm').on('submit', handleFormSubmit);

    // Remove validation errors on input
    $('#roleName').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#nameError').text('');
    });

    // Initialize category select all states (for edit page)
    if (currentPage === 'edit') {
        initializeCategorySelectAllStates();
        initializeGlobalSelectAllState();
    }
}

/**
 * Toggle all permissions (Global Select All)
 */
function toggleAllPermissions() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const categoryCheckboxes = document.querySelectorAll('.category-select-all');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    // Also update category select all checkboxes
    categoryCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

/**
 * Handle category-level "Select All" checkbox
 */
function handleCategorySelectAll() {
    const category = $(this).data('category');
    const isChecked = $(this).is(':checked');

    // Check/uncheck all permissions in this category
    $(`.category-${category}`).prop('checked', isChecked);

    // Update global "Select All" checkbox state
    updateGlobalSelectAll();
}

/**
 * Handle individual permission checkbox change
 */
function handlePermissionChange() {
    const category = $(this).data('category');

    // Check if all permissions in this category are selected
    const totalInCategory = $(`.category-${category}`).length;
    const checkedInCategory = $(`.category-${category}:checked`).length;

    // Update category "Select All" checkbox
    $(`#select_all_${category}`).prop('checked', totalInCategory === checkedInCategory);

    // Update global "Select All" checkbox state
    updateGlobalSelectAll();
}

/**
 * Update global "Select All" checkbox state
 */
function updateGlobalSelectAll() {
    const totalPermissions = $('.permission-checkbox').length;
    const checkedPermissions = $('.permission-checkbox:checked').length;
    $('#selectAll').prop('checked', totalPermissions === checkedPermissions);
}

/**
 * Initialize category select all states on page load (for edit page)
 */
function initializeCategorySelectAllStates() {
    $('.category-select-all').each(function() {
        const category = $(this).data('category');
        const totalInCategory = $(`.category-${category}`).length;
        const checkedInCategory = $(`.category-${category}:checked`).length;

        if (totalInCategory === checkedInCategory && totalInCategory > 0) {
            $(this).prop('checked', true);
        }
    });
}

/**
 * Initialize global select all state on page load (for edit page)
 */
function initializeGlobalSelectAllState() {
    const totalCheckboxes = $('.permission-checkbox').length;
    const checkedCheckboxes = $('.permission-checkbox:checked').length;

    if (totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0) {
        $('#selectAll').prop('checked', true);
    }
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
    e.preventDefault();

    const saveBtn = $('#saveBtn');
    const saveSpinner = $('#saveSpinner');

    // Show spinner
    saveBtn.prop('disabled', true);
    saveSpinner.removeClass('d-none');

    // Clear previous validation errors
    clearValidationErrors();

    // Get form data
    const formData = $(this).serialize();

    // Determine URL and method based on page
    let url = '/admin/roles';
    let method = 'POST';

    if (currentPage === 'edit') {
        // Get role ID from URL path: /admin/roles/{id}/edit
        const pathParts = window.location.pathname.split('/');
        const roleId = pathParts[pathParts.length - 2]; // Get second to last part
        url = `/admin/roles/${roleId}`;
        method = 'PUT';
    }

    $.ajax({
        url: url,
        type: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Show SweetAlert success message
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Redirect to roles index page
                    window.location.href = '/admin/roles';
                });
            } else {
                notyf.error(response.message || 'Operation failed');
                saveBtn.prop('disabled', false);
                saveSpinner.addClass('d-none');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Failed to save role';

            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    displayValidationErrors(errors);
                    // Get first error message for alert
                    const firstError = Object.values(errors)[0];
                    if (firstError && firstError[0]) {
                        errorMessage = firstError[0];
                    }
                }
            } else if (xhr.responseJSON?.message) {
                errorMessage = xhr.responseJSON.message;
            }

            // Show error with SweetAlert
            Swal.fire({
                title: 'Error!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });

            saveBtn.prop('disabled', false);
            saveSpinner.addClass('d-none');
        }
    });
}

/**
 * ========================================
 * HELPER FUNCTIONS
 * ========================================
 */

/**
 * Display validation errors
 */
function displayValidationErrors(errors) {
    for (const field in errors) {
        if (errors.hasOwnProperty(field)) {
            const inputElement = $(`#${field}`);
            const errorElement = $(`#${field}Error`);

            inputElement.addClass('is-invalid');
            errorElement.text(errors[field][0]);
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
