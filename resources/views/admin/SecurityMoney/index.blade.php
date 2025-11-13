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
                    <button type="button" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#securityMoneyModal" onclick="openCreateModal()">
                        <i class="fa fa-plus"></i> Add Security Money
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                        <tr>
                            <th>Receiver</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Note</th>
                            <th class="width-20-percentage text-center">Options</th>
                        </tr>
                        </thead>
                        <tbody id="securityMoneyTableBody">
                            <!-- Data will be populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Security Money Modal -->
        <div class="modal fade" id="securityMoneyModal" tabindex="-1" aria-labelledby="securityMoneyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="securityMoneyModalLabel">Add Security Money</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="securityMoneyForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="securityMoneyReceiver" class="form-label">Receiver Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="securityMoneyReceiver" name="receiver" placeholder="e.g., John Doe, ABC Company" required>
                                <div class="invalid-feedback" id="receiverError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="securityMoneyDate" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="securityMoneyDate" name="date" required placeholder="{{ get_option('date_format', 'Y-m-d') }}" autocomplete="off">
                                <div class="invalid-feedback" id="dateError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="securityMoneyAmount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="securityMoneyAmount" name="amount" step="0.01" min="0" placeholder="e.g., 50000.00" required>
                                <div class="invalid-feedback" id="amountError"></div>
                                <div class="form-text">Enter the security money amount</div>
                            </div>
                            <div class="mb-3">
                                <label for="securityMoneyStatus" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="securityMoneyStatus" name="status" required>
                                    <option value="Paid" selected>Paid</option>
                                    <option value="Received">Received</option>
                                </select>
                                <div class="invalid-feedback" id="statusError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="securityMoneyNote" class="form-label">Note</label>
                                <textarea class="form-control" id="securityMoneyNote" name="note" rows="3" placeholder="Additional notes about the security money (optional)"></textarea>
                                <div class="invalid-feedback" id="noteError"></div>
                            </div>
                            <input type="hidden" id="securityMoneyId" name="security_money_id">
                            <input type="hidden" id="formMethod" value="POST">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span>
                                Save Security Money
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
    <script type="text/javascript" src="{{asset('admin/partial/js/security-money.js')}}"></script>
@endpush
