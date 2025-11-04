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
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-success" id="exportCsvBtn">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <!-- Date Range Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="text" class="form-control form-control-sm" id="startDate" name="start_date" placeholder="{{ get_option('date_format', 'Y-m-d') }}" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="text" class="form-control form-control-sm" id="endDate" name="end_date" placeholder="{{ get_option('date_format', 'Y-m-d') }}" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-primary w-100" id="filterBtn">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-secondary w-100" id="resetBtn">
                            <i class="fa fa-refresh"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Supplier Name</th>
                                <th class="text-end">Amount</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="paymentTableBody">
                            <!-- Data will be populated via DataTables AJAX -->
                        </tbody>
                    </table>
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

    <script>
        "use strict";

        /**
         * Payment to Supplier Index Page Configuration
         */
        window.paymentToSupplierRoutes = {
            getData: '{{ route('payment-to-supplier.getData') }}',
            exportCsv: '{{ route('payment-to-supplier.exportCsv') }}'
        };

        window.paymentToSupplierConfig = {
            debug: {{ config('app.debug') ? 'true' : 'false' }},
            locale: '{{ app()->getLocale() }}',
            currency: '{{ get_option('app_currency', '$') }}',
            dateFormatPhp: '{{ get_option('date_format', 'Y-m-d') }}'
        };
    </script>

    <script type="text/javascript" src="{{asset('admin/partial/js/payment-to-supplier-index.js')}}"></script>
@endpush

