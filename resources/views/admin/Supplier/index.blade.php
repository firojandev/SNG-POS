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
                    <button type="button" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="openCreateSupplierModal()">
                        <i class="fa fa-plus"></i>Add Supplier
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="supplierTable" class="table data-table">
                        <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Balance ({{ get_option('app_currency') }})</th>
                            <th class="width-20-percentage text-center">Options</th>
                        </tr>
                        </thead>
                        <tbody id="supplierTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supplier Modal -->
        <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="supplierModalLabel">Add Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="supplierForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="supplierName" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="supplierName" name="name" placeholder="Enter supplier name" required>
                                        <div class="invalid-feedback" id="nameError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contactPerson" class="form-label">Contact Person</label>
                                        <input type="text" class="form-control" id="contactPerson" name="contact_person" placeholder="Enter contact person name">
                                        <div class="invalid-feedback" id="contact_personError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="supplierPhone" class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="supplierPhone" name="phone" placeholder="Enter phone number" required>
                                        <div class="invalid-feedback" id="phoneError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="supplierEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="supplierEmail" name="email" placeholder="Enter email address">
                                        <div class="invalid-feedback" id="emailError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="supplierAddress" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="supplierAddress" name="address" placeholder="Enter supplier address">
                                        <div class="invalid-feedback" id="addressError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="about" class="form-label">About</label>
                                <textarea class="form-control" id="about" name="about" rows="3" placeholder="Enter supplier description or additional information"></textarea>
                                <div class="invalid-feedback" id="aboutError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="invalid-feedback" id="photoError"></div>
                                <div class="form-text">Upload a photo (JPG, PNG, GIF - Max: 2MB)</div>
                                <div id="currentPhoto" class="mt-2" style="display: none;">
                                    <small class="text-muted">Current Image:</small><br>
                                    <img id="currentPhotoImg" src="" alt="Current Photo" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </div>
                                <div id="photoPreview" class="mt-2" style="display: none;">
                                    <small class="text-muted">Preview:</small><br>
                                    <img id="photoPreviewImg" src="" alt="Photo Preview" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isActiveSupplier" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="isActiveSupplier">Active</label>
                                </div>
                                <div class="invalid-feedback" id="is_activeError"></div>
                            </div>
                            <input type="hidden" id="supplierId" name="supplier_id">
                            <input type="hidden" id="supplierFormMethod" value="POST">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="supplierSaveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="supplierSaveSpinner" role="status" aria-hidden="true"></span>
                                Save Supplier
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
    <script type="text/javascript" src="{{asset('admin/partial/js/supplier.js')}}"></script>
@endpush


