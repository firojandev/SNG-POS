@extends('layouts.admin')
@section('content')
    <main class="main-content">

        <div class="row">
            <div class="col-lg-6 col-xl-7">
                <div class="theme-card pos-card h-100">
                    <div class="pos-card-header">
                        <div class="row gx-2">
                            <div class="col-6 col-md-8 col-lg-9">
                                <div class="pos-input">
                                    <select id="supplierSelect" class="form-control form-control-sm select2" aria-label="select" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 text-right">
                                <a href="javascript:void(0);" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#supplierModal"><span><i class="fa fa-plus"></i> </span> Add Supplier</a>
                            </div>
                        </div>
                    </div>
                    <div class="pos-card-body">
                        <div class="pos-card-body-content">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle pos-vendor-table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Unit Price">U/P</th>
                                        <th>QTY</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Tax Per Unit">T/U</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Unit Total">U/T</th>
                                        <th>-</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cartTableBody">
                                    <tr id="emptyCartMessage">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fa fa-shopping-cart fa-2x mb-2"></i>
                                            <p class="mb-0">Cart is empty. Select products from the right panel to add them.</p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="pos-card-footer pt-3">
                            <form id="purchaseForm">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <div>
                                            <label for="note" class="text-light-muted text-13"><strong>Note:</strong></label>
                                            <textarea placeholder="Additional note" id="note" name="note" rows="3" class="form-control form-control-sm pos-input"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <table class="table align-middle pos-vendor-table text-muted table-borderless mb-0">
                                            <tbody>
                                            <tr>
                                                <td class="ps-0 width-60-percentage"><strong>Total Amount:</strong></td>
                                                <td class="text-right pe-0" id="totalAmount">{{ get_option('app_currency', '$') }}0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Paid Amount:</strong></td>
                                                <td class="text-right pe-0"><input type="number" id="paidAmount" name="paid_amount" class="form-control form-control-sm text-center" placeholder="Paid Amount" value="0" step="0.01" min="0" max="999999"></td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Due Amount:</strong></td>
                                                <td class="text-right pe-0" id="dueAmount">{{ get_option('app_currency', '$') }}0.00</td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div class="d-flex justify-content-end pt-3">
                                            <div class="me-2">
                                                <a href="{{route('purchase.index')}}" class="btn btn-sm text-13 btn-danger">Cancel</a>
                                            </div>
                                            <div class="me-2">
                                                <a href="{{route('purchase.create')}}" class="btn btn-sm text-13 btn-secondary">Clear</a>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-sm text-13 btn-brand-secondary">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>




                </div>
            </div>
            <div class="col-lg-6 col-xl-5">
                <div class="theme-card pos-card h-100">
                    <div class="pos-card-header">
                        <div class="row gx-2">
                            <div class="col-6">
                                <div class="pos-input">
                                    <span class="pos-input-prepend text-light-muted"><i class="fa fa-search text-13"></i></span>
                                    <input type="text" id="productSearch" placeholder="Search by Name, SKU" class="form-control form-control-sm" aria-label="input">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="pos-input">
                                    <select id="categoryFilter" class="form-control form-control-sm select2" aria-label="select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pos-card-body">
                        <div class="pos-card-body-content">
                            <div class="row g-2" id="productsContainer">
                                <!-- Products will be loaded dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


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
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="supplierAddress" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="supplierAddress" name="address" placeholder="Enter supplier address">
                                        <div class="invalid-feedback" id="addressError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="balance" class="form-label">Balance</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="balance" name="balance" value="0" placeholder="Enter balance amount">
                                        <div class="invalid-feedback" id="balanceError"></div>
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
    <!--============== Extra Plugin =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/select2/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/select2/select2-bootstrap/select2-bootstrap-5-theme.css')}}">
    <!--============== End Extra Plugin =================-->
@endpush

@push('js')
    <!--============== Extra Plugin =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/select2/select2.min.js')}}"></script>

    <script>
        "use strict";
        // Pass configuration to JavaScript before loading purchase.js
        window.purchaseRoutes = {
            products: '{{ route('purchase.api.products') }}',
            calculateUnitTotal: '{{ route('purchase.api.calculate-unit-total') }}',
            store: '{{ route('purchase.store') }}',
            suppliersStore: '{{ route('suppliers.store') }}',
            suppliersGetData: '{{ route('suppliers.getData') }}'
        };

        window.purchaseConfig = {
            defaultImage: '{{ asset('admin/images/product/default.png') }}',
            currency: '{{ get_option('app_currency', '$') }}'
        };
    </script>

    <script src="{{ asset('admin/partial/js/purchase.js') }}"></script>
    <!--============== End Plugin ===================-->
@endpush


