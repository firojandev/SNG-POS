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
                    <li class="breadcrumb-item">
                        <a href="{{route('invoice.index')}}">Invoices</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ @$title }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-7 col-xl-8">
                <div class="theme-card pos-card h-100">
                    <div class="pos-card-header">
                        <div class="row gx-2">
                            <div class="col-9">
                                <div class="row gx-2">
                                    <div class="col-6">
                                        <div class="pos-input">
                                            <select id="customerSelect" class="form-control form-control-sm select2" aria-label="select" required>
                                                <option value="">Select Customer</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="pos-input">
                                            <input type="text" id="invoiceDate" name="date" class="form-control form-control-sm" placeholder="{{ get_option('date_format', 'Y-m-d') }}" required autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 text-right">
                                <a href="javascript:void(0);" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#customerModal"><span><i class="fa fa-plus"></i> </span> Add Customer</a>
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
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Discount">Disc</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="VAT Per Unit">V/U</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Unit Total">U/T</th>
                                        <th>-</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cartTableBody">
                                    <tr id="emptyCartMessage">
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fa fa-shopping-cart fa-2x mb-2"></i>
                                            <p class="mb-0">Cart is empty. Select products from the right panel to add them.</p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="pos-card-footer pt-3">
                            <form id="invoiceForm">
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
                                                <td class="ps-0 width-60-percentage"><strong>Unit Total:</strong></td>
                                                <td class="text-right pe-0" id="unitTotalAmount">{{ get_option('app_currency', '$') }}0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Total VAT:</strong></td>
                                                <td class="text-right pe-0" id="totalVatAmount">{{ get_option('app_currency', '$') }}0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 width-60-percentage"><strong>Total Amount:</strong></td>
                                                <td class="text-right pe-0" id="totalAmount">{{ get_option('app_currency', '$') }}0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Discount:</strong></td>
                                                <td class="text-right pe-0">
                                                    <div class="d-flex gap-1">
                                                        <select id="discountType" name="discount_type" class="form-control form-control-sm" style="max-width: 70px;">
                                                            <option value="flat">Flat</option>
                                                            <option value="percentage">%</option>
                                                        </select>
                                                        <input type="number" id="discountValue" name="discount_value" class="form-control form-control-sm text-center" placeholder="0" value="0" step="0.01" min="0" max="999999">
                                                    </div>
                                                    <small class="text-muted d-block mt-1" id="discountAmountDisplay">-{{ get_option('app_currency', '$') }}0.00</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 width-60-percentage"><strong>Payable Amount:</strong></td>
                                                <td class="text-right pe-0" id="payableAmount">{{ get_option('app_currency', '$') }}0.00</td>
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
                                                <a href="{{route('invoice.index')}}" class="btn btn-sm text-13 btn-danger">Cancel</a>
                                            </div>
                                            <div class="me-2">
                                                <a href="{{route('invoice.create')}}" class="btn btn-sm text-13 btn-secondary">Clear</a>
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
            <div class="col-lg-5 col-xl-4">
                <div class="theme-card pos-card h-100">
                    <div class="pos-card-header">
                        <div class="row gx-2">
                            <div class="col-12">
                                <div class="pos-input mb-2">
                                    <span class="pos-input-prepend text-light-muted"><i class="fa fa-search text-13"></i></span>
                                    <input type="text" id="productSearch" placeholder="Search by Name, SKU" class="form-control form-control-sm" aria-label="input">
                                </div>
                            </div>
                            <div class="col-12">
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


        <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="customerForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customerName" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customerName" name="name" placeholder="Enter customer name" required>
                                        <div class="invalid-feedback" id="nameError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customerPhone" class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customerPhone" name="phone" placeholder="Enter phone number" required>
                                        <div class="invalid-feedback" id="phoneError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customerEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="customerEmail" name="email" placeholder="Enter email address">
                                        <div class="invalid-feedback" id="emailError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="customerAddress" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="customerAddress" name="address" placeholder="Enter customer address">
                                        <div class="invalid-feedback" id="addressError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                <div class="invalid-feedback" id="photoError"></div>
                                <div class="form-text">Upload a photo (JPG, PNG, GIF - Max: 2MB)</div>
                                <div id="photoPreview" class="mt-2" style="display: none;">
                                    <small class="text-muted">Preview:</small><br>
                                    <img id="photoPreviewImg" src="" alt="Photo Preview" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isActiveCustomer" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="isActiveCustomer">Active</label>
                                </div>
                                <div class="invalid-feedback" id="is_activeError"></div>
                            </div>
                            <input type="hidden" id="customerId" name="customer_id">
                            <input type="hidden" id="customerFormMethod" value="POST">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="customerSaveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="customerSaveSpinner" role="status" aria-hidden="true"></span>
                                Save Customer
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
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/jquery-ui/jquery-ui.min.css')}}">
    <!--============== End Extra Plugin =================-->
@endpush

@push('js')
    <!--============== Extra Plugin =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/select2/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/jquery-ui/jquery-ui.js')}}"></script>

    <script>
        "use strict";
        // Pass configuration to JavaScript before loading invoice.js
        window.invoiceRoutes = {
            products: '{{ route('invoice.api.products') }}',
            calculateUnitTotal: '{{ route('invoice.api.calculate-unit-total') }}',
            store: '{{ route('invoice.store') }}',
            customersStore: '{{ route('customers.store') }}',
            customersGetData: '{{ route('customers.getData') }}'
        };

        window.invoiceConfig = {
            defaultImage: '{{ asset('admin/images/product/default.png') }}',
            currency: '{{ get_option('app_currency', '$') }}',
            dateFormatPhp: '{{ get_option('date_format', 'Y-m-d') }}'
        };
    </script>

    <script src="{{ asset('admin/partial/js/invoice.js') }}"></script>
    <!--============== End Plugin ===================-->
@endpush

