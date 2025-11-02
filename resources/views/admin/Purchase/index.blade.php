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
            <div class="theme-card-header d-flex justify-content-between align-items-center">
                <h6 class="theme-card-title">{{@$title}}</h6>
                <a href="{{ route('purchase.create') }}" class="btn btn-sm btn-brand-secondary">
                    <i class="fa fa-plus"></i> Add Purchase
                </a>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Supplier</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Paid Amount</th>
                                <th class="text-center">Due Amount</th>
                                <th class="width-20-percentage text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseTableBody">
                            <!-- Data will be populated via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Make Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="paymentForm">
                        <div class="modal-body">
                            <input type="hidden" id="purchase_uuid" name="purchase_uuid">
                            <input type="hidden" id="supplier_id" name="supplier_id">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_number_display" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Due Amount</label>
                                <input type="text" class="form-control text-danger fw-bold" id="due_amount_display" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="payment_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="payment_amount" name="amount" placeholder="Enter payment amount" required>
                                <div class="invalid-feedback" id="amountError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                                <div class="invalid-feedback" id="payment_dateError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="payment_note" class="form-label">Note</label>
                                <textarea class="form-control" id="payment_note" name="note" rows="3" placeholder="Enter payment note (optional)"></textarea>
                                <div class="invalid-feedback" id="noteError"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="paymentSaveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="paymentSaveSpinner" role="status" aria-hidden="true"></span>
                                Submit Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <!--============== DataTable CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/datatable/css/dataTables.bootstrap5.min.css')}}">
    <!--============== End DataTable CSS =================-->
@endpush

@push('js')
    <!--============== DataTable JS =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
    <!--============== End DataTable JS =================-->

    <script>
        "use strict";

        /**
         * Purchase Index Page Configuration
         * Pass routes and configuration to JavaScript before loading the main script
         */
        window.purchaseIndexRoutes = {
            getData: '{{ route('purchase.api.getData') }}',
            view: '/admin/purchase/:uuid',
            create: '{{ route('purchase.create') }}',
            edit: '/admin/purchase/:uuid/edit',
            destroy: '/admin/purchase/:uuid'
        };

        window.purchaseIndexConfig = {
            debug: {{ config('app.debug') ? 'true' : 'false' }},
            locale: '{{ app()->getLocale() }}',
            currency: '{{ get_option('app_currency', '$') }}'
        };
    </script>

    <!--============== Purchase Index Custom JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/purchase-index.js')}}"></script>
    <!--============== End Purchase Index Custom JS =================-->
@endpush
