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
                    <button type="button" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#assetModal" onclick="openCreateModal()">
                        <i class="fa fa-plus"></i>Add Asset
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th class="width-20-percentage text-center">Options</th>
                        </tr>
                        </thead>
                        <tbody id="assetTableBody">
                            <!-- Data will be populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Asset Modal -->
        <div class="modal fade" id="assetModal" tabindex="-1" aria-labelledby="assetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assetModalLabel">Add Asset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="assetForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="assetName" class="form-label">Asset Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="assetName" name="name" placeholder="e.g., Cash, Land, Equipment" required>
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="assetAmount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="assetAmount" name="amount" step="0.01" min="0" placeholder="e.g., 50000.00" required>
                                <div class="invalid-feedback" id="amountError"></div>
                                <div class="form-text">Enter the asset value/amount</div>
                            </div>
                            <div class="mb-3">
                                <label for="assetNote" class="form-label">Note</label>
                                <textarea class="form-control" id="assetNote" name="note" rows="3" placeholder="Additional notes about the asset (optional)"></textarea>
                                <div class="invalid-feedback" id="noteError"></div>
                            </div>
                            <input type="hidden" id="assetId" name="asset_id">
                            <input type="hidden" id="formMethod" value="POST">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span>
                                Save Asset
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
    <script type="text/javascript" src="{{asset('admin/partial/js/asset.js')}}"></script>
@endpush
