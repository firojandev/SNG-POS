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
            <div class="theme-card-header d-flex justify-content-between">
                <h6 class="theme-card-title">{{@$title}}</h6>
                <div>
                    <button type="button" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#storeModal" onclick="openCreateModal()">
                        <i class="fa fa-plus"></i>Add Store
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="width-20-percentage text-center">Options</th>
                        </tr>
                        </thead>
                        <tbody id="storeTableBody">
                            <!-- Data will be populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Store Modal -->
        <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="storeModalLabel">Add Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="storeForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="storeName" class="form-label">Store Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="storeName" name="name" placeholder="e.g., Main Branch, Downtown Store" required>
                                        <div class="invalid-feedback" id="nameError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contactPerson" class="form-label">Contact Person <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contactPerson" name="contact_person" placeholder="e.g., John Doe" required>
                                        <div class="invalid-feedback" id="contact_personError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phoneNumber" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phoneNumber" name="phone_number" placeholder="e.g., +1234567890" required>
                                        <div class="invalid-feedback" id="phone_numberError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="e.g., store@example.com" required>
                                        <div class="invalid-feedback" id="emailError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter complete store address" required></textarea>
                                <div class="invalid-feedback" id="addressError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="details" class="form-label">Details</label>
                                <textarea class="form-control" id="details" name="details" rows="3" placeholder="Additional details about the store (optional)"></textarea>
                                <div class="invalid-feedback" id="detailsError"></div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="isActive">
                                        Active Store
                                    </label>
                                </div>
                                <div class="form-text">Inactive stores will not be available for selection</div>
                                <div class="invalid-feedback" id="is_activeError"></div>
                            </div>
                            <input type="hidden" id="storeId" name="store_id">
                            <input type="hidden" id="formMethod" value="POST">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span>
                                Save Store
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/datatable/css/dataTables.bootstrap5.min.css')}}">
@endpush

@push('js')
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/partial/js/store.js')}}"></script>
@endpush
