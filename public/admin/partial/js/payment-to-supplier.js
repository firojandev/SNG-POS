"use strict";

let paymentDataTable;
let isEditMode = false;
let currentSupplierBalance = 0;

$(document).ready(function() {
    initializeDataTable();
    initializeWidgets();
    loadPayments();

    $('#paymentForm').on('submit', handleFormSubmit);
    $('#paymentModal').on('shown.bs.modal', function() {
        initializeWidgets();
    });
    $('#paymentModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Fetch supplier balance when supplier is selected
    $('#supplier_id').on('change', function() {
        const supplierId = $(this).val();
        if (supplierId) {
            fetchSupplierBalance(supplierId);
        } else {
            $('#balanceContainer').hide();
            $('#supplier_balance').val('');
            currentSupplierBalance = 0;
            $('#amountHelp').text('');
        }
    });

    // Validate amount on input
    $('#amount').on('input', function() {
        validateAmount();
    });
});

function initializeWidgets() {
    // Initialize select2 for supplier
    if (typeof window.initializeSelect2 === 'function') {
        window.initializeSelect2();
    }
    // Initialize jQuery UI datepicker
    if (typeof $.fn.datepicker !== 'undefined') {
        const phpFmt = (window.appConfig && window.appConfig.dateFormatPhp) ? window.appConfig.dateFormatPhp : 'Y-m-d';
        const jqFmt = phpDateFormatToJqueryUI(phpFmt);
        $('#payment_date').datepicker({
            dateFormat: jqFmt
        });
    }
}

// Map PHP date format to jQuery UI datepicker format
function phpDateFormatToJqueryUI(phpFormat) {
    // Basic mapping for common tokens used in settings
    const map = {
        'Y': 'yy',
        'y': 'y',
        'm': 'mm',
        'n': 'm',
        'd': 'dd',
        'j': 'd',
        '/': '/',
        '-': '-',
        ' ': ' '
    };
    let result = '';
    for (let i = 0; i < phpFormat.length; i++) {
        const ch = phpFormat[i];
        result += (map[ch] !== undefined) ? map[ch] : ch;
    }
    return result;
}

function initializeDataTable() {
    paymentDataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: -1 }
        ],
        language: {
            emptyTable: 'No payments found',
            zeroRecords: 'No matching payments found'
        }
    });
}

function loadPayments() {
    // Show loading overlay
    if (typeof showPageLoader === 'function') {
        showPageLoader();
    }

    $.ajax({
        url: '/admin/payment-to-supplier/get-data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load payments');
            }
        },
        error: function() {
            showToastr('error', 'Failed to load payments');
        },
        complete: function() {
            if (typeof hidePageLoader === 'function') {
                hidePageLoader();
            }
        }
    });
}

function populateTable(payments) {
    paymentDataTable.clear();
    payments.forEach(function(payment) {
        const actions = `
            <div class="text-center">
                <button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${payment.id})">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${payment.id})">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        `;

        paymentDataTable.row.add([
            payment.supplier ? payment.supplier.name : '',
            (typeof formatCurrency === 'function') ? formatCurrency(payment.amount, (window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$', 2) : (((window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$') + parseFloat(payment.amount).toFixed(2)),
            payment.payment_date,
            payment.note || '',
            actions
        ]);
    });
    paymentDataTable.draw();
}

function fetchSupplierBalance(supplierId) {
    const paymentId = $('#paymentId').val();

    $.ajax({
        url: '/admin/payment-to-supplier/get-supplier-balance',
        type: 'GET',
        data: {
            supplier_id: supplierId,
            payment_id: paymentId || null
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                currentSupplierBalance = parseFloat(response.balance);
                $('#supplier_balance').val(response.formatted_balance);
                $('#balanceContainer').show();
                $('#amountHelp').text('Maximum allowed: ' + response.formatted_balance);
                validateAmount();
            } else {
                showToastr('error', 'Failed to fetch supplier balance');
            }
        },
        error: function() {
            showToastr('error', 'Failed to fetch supplier balance');
        }
    });
}

function validateAmount() {
    const amount = parseFloat($('#amount').val()) || 0;
    const amountInput = $('#amount');

    if (amount > currentSupplierBalance) {
        amountInput.addClass('is-invalid');
        $('#amountError').text('Amount cannot exceed supplier balance of ' + (window.appConfig && window.appConfig.currency ? window.appConfig.currency : '$') + currentSupplierBalance.toFixed(2));
        return false;
    } else {
        amountInput.removeClass('is-invalid');
        $('#amountError').text('');
        return true;
    }
}

function openCreateModal() {
    resetForm();
    $('#paymentModalLabel').text('Add Payment to Supplier');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

function openEditModal(id) {
    isEditMode = true;
    $('#paymentModalLabel').text('Edit Payment to Supplier');
    $('#paymentId').val(id);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    // Show loading overlay
    if (typeof showPageLoader === 'function') {
        showPageLoader();
    }

    $.ajax({
        url: '/admin/payment-to-supplier/' + id + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const payment = response.data;
                $('#supplier_id').val(payment.supplier_id).trigger('change');

                // Wait for balance to load before setting amount
                setTimeout(function() {
                    $('#amount').val(payment.amount);
                    $('#payment_date').val(payment.payment_date);
                    $('#note').val(payment.note || '');
                    $('#paymentModal').modal('show');
                }, 500);
            } else {
                showToastr('error', 'Failed to load payment');
            }
        },
        error: function() {
            showToastr('error', 'Failed to load payment');
        },
        complete: function() {
            if (typeof hidePageLoader === 'function') {
                hidePageLoader();
            }
        }
    });
}

function openDeleteModal(id) {
    showConfirmDialog('Are you sure?', 'You want to delete this payment?', 'Yes, delete it!', 'Cancel', function() {
        // Show loading overlay
        if (typeof showPageLoader === 'function') {
            showPageLoader();
        }

        $.ajax({
            url: '/admin/payment-to-supplier/' + id,
            type: 'POST',
            data: { _method: 'DELETE' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccessDialog('Deleted!', response.message, 1500, function() {
                        loadPayments();
                    });
                } else {
                    showErrorDialog('Error!', response.message || 'Delete operation failed');
                }
            },
            error: function() {
                showErrorDialog('Error!', 'Failed to delete payment');
            },
            complete: function() {
                if (typeof hidePageLoader === 'function') {
                    hidePageLoader();
                }
            }
        });
    });
}

function handleFormSubmit(e) {
    e.preventDefault();

    // Validate amount before submitting
    if (!validateAmount()) {
        showToastr('error', 'Please enter a valid amount within the supplier balance');
        return false;
    }

    const formData = new FormData(this);
    const paymentId = $('#paymentId').val();
    let url = '/admin/payment-to-supplier';
    let method = 'POST';
    if (isEditMode && paymentId) {
        url += '/' + paymentId;
        method = 'POST';
        formData.append('_method', 'PUT');
    }

    showLoadingSpinner('#saveSpinner', '#saveBtn');
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
                $('#paymentModal').modal('hide');
                loadPayments();
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
            hideLoadingSpinner('#saveSpinner', '#saveBtn');
        }
    });
}

function clearValidationErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
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

function resetForm() {
    $('#paymentForm')[0].reset();
    $('#paymentId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    currentSupplierBalance = 0;
    $('#balanceContainer').hide();
    $('#supplier_balance').val('');
    $('#amountHelp').text('');
    clearValidationErrors();
    if ($('#supplier_id').hasClass('select2-hidden-accessible')) {
        $('#supplier_id').val('').trigger('change');
    }
}

// Local helpers for button spinner state
function showLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).removeClass('d-none');
    $(buttonSelector).prop('disabled', true);
}

function hideLoadingSpinner(spinnerSelector, buttonSelector) {
    $(spinnerSelector).addClass('d-none');
    $(buttonSelector).prop('disabled', false);
}
