"use strict";

let expenseDataTable;
let isEditMode = false;

$(document).ready(function() {
    initializeDataTable();
    initializeWidgets();
    loadExpenses();

    $('#expenseForm').on('submit', handleFormSubmit);
    $('#expenseModal').on('shown.bs.modal', function() {
        initializeWidgets();
    });
    $('#expenseModal').on('hidden.bs.modal', function() {
        resetForm();
    });
});

function initializeWidgets() {
    // Initialize select2 for category
    if (typeof window.initializeSelect2 === 'function') {
        window.initializeSelect2();
    }
    // Initialize jQuery UI datepicker
    if (typeof $.fn.datepicker !== 'undefined') {
        const phpFmt = (window.appConfig && window.appConfig.dateFormatPhp) ? window.appConfig.dateFormatPhp : 'Y-m-d';
        const jqFmt = phpDateFormatToJqueryUI(phpFmt);
        $('#date').datepicker({
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
    expenseDataTable = $('#dataTable').DataTable({
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
            emptyTable: 'No expenses found',
            zeroRecords: 'No matching expenses found'
        }
    });
}

function loadExpenses() {
    $.ajax({
        url: '/admin/expenses/get-data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateTable(response.data);
            } else {
                showToastr('error', 'Failed to load expenses');
            }
        },
        error: function() {
            showToastr('error', 'Failed to load expenses');
        }
    });
}

function populateTable(expenses) {
    expenseDataTable.clear();
    expenses.forEach(function(expense) {
        const actions = `
            <div class="text-center">
                <button type="button" class="btn btn-sm text-13 btn-brand-secondary me-2" onclick="openEditModal(${expense.id})">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm text-13 btn-danger" onclick="openDeleteModal(${expense.id})">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
        `;

        expenseDataTable.row.add([
            expense.category ? expense.category.name : '',
            (typeof formatCurrency === 'function') ? formatCurrency(expense.amount, (window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$', 2) : (((window.appConfig && window.appConfig.currency) ? window.appConfig.currency : '$') + parseFloat(expense.amount).toFixed(2)),
            expense.date,
            expense.note || '',
            actions
        ]);
    });
    expenseDataTable.draw();
}

function openCreateModal() {
    resetForm();
    $('#expenseModalLabel').text('Add Expense');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Save');
}

function openEditModal(id) {
    isEditMode = true;
    $('#expenseModalLabel').text('Edit Expense');
    $('#expenseId').val(id);
    $('#formMethod').val('PUT');
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span> Update');

    $.ajax({
        url: '/admin/expenses/' + id + '/edit',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const e = response.data;
                $('#expense_category_id').val(e.expense_category_id).trigger('change');
                $('#amount').val(e.amount);
                $('#date').val(e.date);
                $('#note').val(e.note || '');
                $('#expenseModal').modal('show');
            } else {
                showToastr('error', 'Failed to load expense');
            }
        },
        error: function() {
            showToastr('error', 'Failed to load expense');
        }
    });
}

function openDeleteModal(id) {
    showConfirmDialog('Are you sure?', 'You want to delete this expense?', 'Yes, delete it!', 'Cancel', function() {
        $.ajax({
            url: '/admin/expenses/' + id,
            type: 'POST',
            data: { _method: 'DELETE' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccessDialog('Deleted!', response.message, 1500, function() {
                        loadExpenses();
                    });
                } else {
                    showErrorDialog('Error!', response.message || 'Delete operation failed');
                }
            },
            error: function() {
                showErrorDialog('Error!', 'Failed to delete expense');
            }
        });
    });
}

function handleFormSubmit(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const expenseId = $('#expenseId').val();
    let url = '/admin/expenses';
    let method = 'POST';
    if (isEditMode && expenseId) {
        url += '/' + expenseId;
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
                $('#expenseModal').modal('hide');
                loadExpenses();
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
    $('#expenseForm')[0].reset();
    $('#expenseId').val('');
    $('#formMethod').val('POST');
    isEditMode = false;
    clearValidationErrors();
    if ($('#expense_category_id').hasClass('select2-hidden-accessible')) {
        $('#expense_category_id').val('').trigger('change');
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


