@extends('layouts.admin')
@section('content')
    <main class="main-content">

        <div class="breadcrumb-professional">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.dashboard')}}">
                            <i class="icon-home me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{@$title}}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <h6 class="theme-card-title">{{@$title}}</h6>
            </div>
            <div class="theme-card-body">
                <form id="myStoreForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="storeName" class="form-label">Store Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="storeName" name="name" value="{{ $store->name }}" placeholder="e.g., Main Branch, Downtown Store" required>
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contactPerson" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactPerson" name="contact_person" value="{{ $store->contact_person }}" placeholder="e.g., John Doe" required>
                                <div class="invalid-feedback" id="contact_personError"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phoneNumber" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phoneNumber" name="phone_number" value="{{ $store->phone_number }}" placeholder="e.g., +1234567890" required>
                                <div class="invalid-feedback" id="phone_numberError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $store->email }}" placeholder="e.g., store@example.com" required>
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter complete store address" required>{{ $store->address }}</textarea>
                        <div class="invalid-feedback" id="addressError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="details" class="form-label">Details</label>
                        <textarea class="form-control" id="details" name="details" rows="3" placeholder="Additional details about the store (optional)">{{ $store->details }}</textarea>
                        <div class="invalid-feedback" id="detailsError"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" {{ $store->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                        <div class="invalid-feedback" id="is_activeError"></div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#myStoreForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                $('#saveSpinner').removeClass('d-none');
                $('#saveBtn').prop('disabled', true);

                $.ajax({
                    url: '{{ route('store.update-my-store') }}',
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
    </script>
@endpush
