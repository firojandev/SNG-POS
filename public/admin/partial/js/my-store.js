"use strict";

$(document).ready(function() {
    $('#myStoreForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        $('#saveSpinner').removeClass('d-none');
        $('#saveBtn').prop('disabled', true);

        $.ajax({
            url: $('#myStoreForm').data('action-url'),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#saveSpinner').addClass('d-none');
                $('#saveBtn').prop('disabled', false);

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                $('#saveSpinner').addClass('d-none');
                $('#saveBtn').prop('disabled', false);

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`#${field}Error`).text(errors[field][0]);
                        $(`[name="${field}"]`).addClass('is-invalid');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to update store'
                    });
                }
            }
        });
    });
});
