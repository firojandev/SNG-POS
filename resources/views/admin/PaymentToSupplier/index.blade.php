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
                    <button type="button" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="openCreateModal()">
                        <i class="fa fa-plus"></i>Add
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Note</th>
                            <th class="width-20-percentage text-center">Options</th>
                        </tr>
                        </thead>
                        <tbody id="paymentTableBody">
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
                        <h5 class="modal-title" id="paymentModalLabel">Add Payment to Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="paymentForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select id="supplier_id" name="supplier_id" class="form-select select2-dropdown" required>
                                    <option value="">Select supplier...</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="supplier_idError"></div>
                            </div>

                            <div class="mb-3" id="balanceContainer" style="display: none;">
                                <label for="supplier_balance" class="form-label">Supplier Balance</label>
                                <input type="text" class="form-control" id="supplier_balance" readonly>
                                <small class="text-muted">Available balance for this supplier</small>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required placeholder="0.00" autocomplete="off">
                                <div class="invalid-feedback" id="amountError"></div>
                                <small class="text-muted" id="amountHelp"></small>
                            </div>

                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="payment_date" name="payment_date" required placeholder="{{ get_option('date_format', 'Y-m-d') }}" autocomplete="off">
                                <div class="invalid-feedback" id="payment_dateError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Optional note..."></textarea>
                                <div class="invalid-feedback" id="noteError"></div>
                            </div>

                            <input type="hidden" id="paymentId" name="payment_id">
                            <input type="hidden" id="formMethod" value="POST">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span>
                                Save
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
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/jquery-ui/jquery-ui.min.css')}}">
@endpush

@push('js')
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/jquery-ui/jquery-ui.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/partial/js/payment-to-supplier.js')}}"></script>
@endpush

