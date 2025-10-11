"use strict";

let customerDataTable;
let customerEditMode = false;

$(document).ready(function() {
    initializeCustomerDataTable();
    loadCustomers();

    $('#customerForm').on('submit', handleCustomerFormSubmit);

    $('#customerModal').on('hidden.bs.modal', function() {
        resetCustomerForm();
    });

    // Photo preview handler
    $('#photo').on('change', function() {
        previewCustomerPhoto(this);
    });
});

function initializeCustomerDataTable() {
    customerDataTable = $('#customerTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        columns: [
            { title: 'Photo', orderable: false },
            { title: 'Name' },
            { title: 'Phone' },
            { title: 'Email' },
            { title: 'Address' },
            { title: 'Options', orderable: false }
        ],
        columnDefs: [
            { orderable: false, targets: [0, 5] }
        ],
        language: {
            emptyTable: 'No customers found',
            zeroRecords: 'No matching customers found'
        }
    });
}

function loadCustomers() {
    $.ajax({
        url: '/admin/customers/get-data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateCustomerTable(response.data);
            } else {
                showToastr('error', 'Failed to load customers');
            }
        },
        error: function() {
            showToastr('error', 'Failed to load customers');
        }
    });
}

function populateCustomerTable(customers) {
    customerDataTable.clear();
    customers.forEach(function(customer) {
        const photoHtml = customer.photo
            ? `<div class="text-center"><img src="/storage/${customer.photo}" alt="${customer.name}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"></div>`
            : `<div class="text-center"><div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; font-weight: bold;">${customer.name.charAt(0).toUpperCase()}</div></div>`;

        const actions = `
            <div class="text-center">
                <button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditCustomerModal(${customer.id})">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteCustomerModal(${customer.id}, '${customer.name.replace(/'/g, "\\'")}')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>`;

        customerDataTable.row.add([
            photoHtml,
            customer.name,
            customer.phone,
            customer.email || 'N/A',
            customer.address || 'N/A',
            actions
        ]);
    });
    customerDataTable.draw();
}

function openCreateCustomerModal() {
    resetCustomerForm();
    $('#customerModalLabel').text('Add Customer');
    $('#customerSaveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="customerSaveSpinner" role="status" aria-hidden="true"></span> Save');
}

function openEditCustomerModal(id) {
    $.ajax({
        url: '/admin/customers/' + id + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const c = response.data;
                customerEditMode = true;
                $('#customerModalLabel').text('Edit Customer');
                $('#customerId').val(c.id);
                $('#customerName').val(c.name);
                $('#customerPhone').val(c.phone);
                $('#customerEmail').val(c.email || '');
                $('#customerAddress').val(c.address || '');
                $('#isActiveCustomer').prop('checked', !!c.is_active);
                if (c.photo) {
                    $('#currentPhotoImg').attr('src', '/storage/' + c.photo);
                    $('#currentPhoto').show();
                } else {
                    $('#currentPhoto').hide();
                }
                $('#customerFormMethod').val('PUT');
                $('#customerModal').modal('show');
            } else {
                showToastr('error', response.message || 'Failed to fetch customer');
            }
        },
        error: function() {
            showToastr('error', 'Failed to fetch customer');
        }
    });
}

function openDeleteCustomerModal(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${name}" customer? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleCustomerDelete(id, name);
        }
    });
}

function handleCustomerFormSubmit(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const customerId = $('#customerId').val();
    const isEdit = customerEditMode && customerId;
    let url = '/admin/customers';
    let method = 'POST';
    if (isEdit) {
        url += '/' + customerId;
        method = 'POST';
        formData.append('_method', 'PUT');
    }

    showLoadingSpinner('#customerSaveSpinner', '#customerSaveBtn');
    clearValidationErrors();

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToastr('success', response.message);
                $('#customerModal').modal('hide');
                loadCustomers();
            } else {
                showToastr('error', response.message || 'Operation failed');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayValidationErrors(xhr.responseJSON.errors);
                showToastr('error', 'Please correct the validation errors');
            } else {
                showToastr('error', 'An error occurred. Please try again.');
            }
        },
        complete: function() {
            hideLoadingSpinner('#customerSaveSpinner', '#customerSaveBtn');
        }
    });
}

function handleCustomerDelete(id, name) {
    $.ajax({
        url: '/admin/customers/' + id,
        type: 'POST',
        data: { _method: 'DELETE' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({ title: 'Deleted!', text: response.message, icon: 'success', timer: 2000, showConfirmButton: false }).then(() => loadCustomers());
            } else {
                Swal.fire({ title: 'Error!', text: response.message || 'Delete operation failed', icon: 'error' });
            }
        },
        error: function() {
            Swal.fire({ title: 'Error!', text: 'Failed to delete customer', icon: 'error' });
        }
    });
}

function resetCustomerForm() {
    $('#customerForm')[0].reset();
    $('#customerId').val('');
    $('#customerFormMethod').val('POST');
    customerEditMode = false;
    clearValidationErrors();
    $('#currentPhoto').hide();
    $('#photoPreview').hide();
}

// Utility functions
function showLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).removeClass('d-none');
    $(buttonSelector).prop('disabled', true);
}

function hideLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).addClass('d-none');
    $(buttonSelector).prop('disabled', false);
}

function showToastr(type, message) {
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        alert(message);
    }
}

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

function clearValidationErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

function previewCustomerPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            $('#photoPreviewImg').attr('src', e.target.result);
            $('#photoPreview').show();
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        $('#photoPreview').hide();
    }
}
