"use strict";

let supplierDataTable;
let supplierEditMode = false;

$(document).ready(function() {
    initializeSupplierDataTable();
    loadSuppliers();

    $('#supplierForm').on('submit', handleSupplierFormSubmit);

    $('#supplierModal').on('hidden.bs.modal', function() {
        resetSupplierForm();
    });

    // Photo preview handler
    $('#photo').on('change', function() {
        previewSupplierPhoto(this);
    });
});

function initializeSupplierDataTable() {
    supplierDataTable = $('#supplierTable').DataTable({
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
            { title: 'Contact Person' },
            { title: 'Phone' },
            { title: 'Email' },
            { title: 'Balance' },
            { title: 'Options', orderable: false }
        ],
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ],
        language: {
            emptyTable: 'No suppliers found',
            zeroRecords: 'No matching suppliers found'
        }
    });
}

function loadSuppliers() {
    $.ajax({
        url: '/admin/suppliers/get-data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateSupplierTable(response.data);
            } else {
                showToastr('error', 'Failed to load suppliers');
            }
        },
        error: function() {
            showToastr('error', 'Failed to load suppliers');
        }
    });
}

function populateSupplierTable(suppliers) {
    supplierDataTable.clear();
    suppliers.forEach(function(supplier) {
        const photoHtml = supplier.photo
            ? `<div class="text-center"><a href="/admin/suppliers/${supplier.id}/view" target="_blank"><img src="/storage/${supplier.photo}" alt="${supplier.name}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"></a></div>`
            : `<div class="text-center"><a href="/admin/suppliers/${supplier.id}/view" target="_blank"><div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: white; font-weight: bold;">${supplier.name.charAt(0).toUpperCase()}</div></a></div>`;

        const nameHtml = `<a href="/admin/suppliers/${supplier.id}/view" target="_blank" class="text-decoration-none fw-bold text-primary">${supplier.name}</a>`;

        const actions = `
            <div class="text-center">
                <a href="/admin/suppliers/${supplier.id}/view" class="btn btn-sm text-13 btn-info me-2" target="_blank" title="View Supplier Details">
                    <i class="fa fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditSupplierModal(${supplier.id})">
                    <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteSupplierModal(${supplier.id}, '${supplier.name.replace(/'/g, "\\'")}')">
                    <i class="fa fa-trash"></i>
                </button>
            </div>`;

        supplierDataTable.row.add([
            photoHtml,
            nameHtml,
            supplier.contact_person || 'N/A',
            supplier.phone,
            supplier.email || 'N/A',
            formatCurrency(supplier.balance || 0, window.appConfig.currency),
            actions
        ]);
    });
    supplierDataTable.draw();
}

function openCreateSupplierModal() {
    resetSupplierForm();
    $('#supplierModalLabel').text('Add Supplier');
    $('#supplierSaveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="supplierSaveSpinner" role="status" aria-hidden="true"></span> Save');
}

function openEditSupplierModal(id) {
    $.ajax({
        url: '/admin/suppliers/' + id + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const s = response.data;
                supplierEditMode = true;
                $('#supplierModalLabel').text('Edit Supplier');
                $('#supplierId').val(s.id);
                $('#supplierName').val(s.name);
                $('#contactPerson').val(s.contact_person || '');
                $('#supplierPhone').val(s.phone);
                $('#supplierEmail').val(s.email || '');
                $('#supplierAddress').val(s.address || '');
                $('#about').val(s.about || '');
                $('#balance').val(s.balance || 0);
                $('#isActiveSupplier').prop('checked', !!s.is_active);
                if (s.photo) {
                    $('#currentPhotoImg').attr('src', '/storage/' + s.photo);
                    $('#currentPhoto').show();
                } else {
                    $('#currentPhoto').hide();
                }
                $('#supplierFormMethod').val('PUT');
                $('#supplierModal').modal('show');
            } else {
                showToastr('error', response.message || 'Failed to fetch supplier');
            }
        },
        error: function() {
            showToastr('error', 'Failed to fetch supplier');
        }
    });
}

function openDeleteSupplierModal(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${name}" supplier? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            handleSupplierDelete(id, name);
        }
    });
}

function handleSupplierFormSubmit(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const supplierId = $('#supplierId').val();
    const isEdit = supplierEditMode && supplierId;
    let url = '/admin/suppliers';
    let method = 'POST';
    if (isEdit) {
        url += '/' + supplierId;
        method = 'POST';
        formData.append('_method', 'PUT');
    }

    showLoadingSpinner('#supplierSaveSpinner', '#supplierSaveBtn');
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
                $('#supplierModal').modal('hide');
                loadSuppliers();
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
            hideLoadingSpinner('#supplierSaveSpinner', '#supplierSaveBtn');
        }
    });
}

function handleSupplierDelete(id, name) {
    $.ajax({
        url: '/admin/suppliers/' + id,
        type: 'POST',
        data: { _method: 'DELETE' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({ title: 'Deleted!', text: response.message, icon: 'success', timer: 2000, showConfirmButton: false }).then(() => loadSuppliers());
            } else {
                Swal.fire({ title: 'Error!', text: response.message || 'Delete operation failed', icon: 'error' });
            }
        },
        error: function() {
            Swal.fire({ title: 'Error!', text: 'Failed to delete supplier', icon: 'error' });
        }
    });
}

function resetSupplierForm() {
    $('#supplierForm')[0].reset();
    $('#supplierId').val('');
    $('#supplierFormMethod').val('POST');
    supplierEditMode = false;
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

function previewSupplierPhoto(input) {
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



