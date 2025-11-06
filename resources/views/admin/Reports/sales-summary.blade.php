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
                        Sales Summary Report
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Date Filter Section -->
        <div class="theme-card mb-4">
            <div class="theme-card-header">
                <h6 class="theme-card-title">Filter by Date Range</h6>
            </div>
            <div class="theme-card-body">
                <form action="{{ route('sales-report.summary') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ $startDate }}" placeholder="{{ get_option('date_format', 'Y-m-d') }}" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="text" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ $endDate }}" placeholder="{{ get_option('date_format', 'Y-m-d') }}" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('sales-report.export-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success w-100">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-secondary w-100" onclick="window.location.href='{{ route('sales-report.summary') }}'">
                            <i class="fa fa-refresh"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Sales</h6>
                                <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($summary['total_sales'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="fa fa-dollar text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Paid</h6>
                                <h3 class="mb-0 text-success">{{ get_option('app_currency') }}{{ number_format($summary['total_paid'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="fa fa-check-circle text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Due</h6>
                                <h3 class="mb-0 text-danger">{{ get_option('app_currency') }}{{ number_format($summary['total_due'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-danger-light">
                                <i class="fa fa-exclamation-circle text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Invoices</h6>
                                <h3 class="mb-0">{{ number_format($summary['total_invoices']) }}</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="fa fa-file-text text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total VAT</h6>
                                <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($summary['total_vat'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="fa fa-percent text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Discount</h6>
                                <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($summary['total_discount'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-secondary-light">
                                <i class="fa fa-tag text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Items Sold</h6>
                                <h3 class="mb-0">{{ number_format($summary['items_sold']) }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="fa fa-shopping-cart text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card card-stats">
                    <div class="theme-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Active Invoices</h6>
                                <h3 class="mb-0 text-primary">{{ number_format($summary['active_invoices']) }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="fa fa-check text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Daily Sales Chart -->
            <div class="col-lg-8 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Daily Sales Trend</h6>
                    </div>
                    <div class="theme-card-body">
                        <canvas id="dailySalesChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Sales by Status Chart -->
            <div class="col-lg-4 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Sales by Status</h6>
                    </div>
                    <div class="theme-card-body">
                        <canvas id="salesByStatusChart"></canvas>
                        <div class="mt-3">
                            @foreach($salesByStatus as $status)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge
                                        @if($status->status == 'active') bg-success
                                        @elseif($status->status == 'returned') bg-warning
                                        @else bg-danger
                                        @endif">
                                        {{ ucfirst($status->status) }}
                                    </span>
                                    <span>{{ $status->count }} ({{ get_option('app_currency') }}{{ number_format($status->total_amount, 2) }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products and Customers -->
        <div class="row mb-4">
            <!-- Top Selling Products -->
            <div class="col-lg-6 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Top 10 Selling Products</h6>
                    </div>
                    <div class="theme-card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="text-center">Qty Sold</th>
                                        <th class="text-end">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                                <br><small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ number_format($item->total_quantity) }}</span>
                                            </td>
                                            <td class="text-end">{{ get_option('app_currency') }}{{ number_format($item->total_amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No products sold in this period</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="col-lg-6 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Top 10 Customers</h6>
                    </div>
                    <div class="theme-card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Total Purchase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCustomers as $index => $customerData)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($customerData->customer)
                                                    <a href="{{ route('customers.view', $customerData->customer->id) }}" target="_blank" class="text-decoration-none">
                                                        <strong>{{ $customerData->customer->name }}</strong>
                                                    </a>
                                                    <br><small class="text-muted">{{ $customerData->customer->phone ?? '' }}</small>
                                                @else
                                                    <strong>N/A</strong>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $customerData->invoice_count }}</span>
                                            </td>
                                            <td class="text-end">{{ get_option('app_currency') }}{{ number_format($customerData->total_purchase, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No customers data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="theme-card">
            <div class="theme-card-header d-flex justify-content-between align-items-center">
                <h6 class="theme-card-title">Recent Invoices (Last 10)</h6>
                <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-brand-secondary">
                    <i class="fa fa-eye"></i> View All
                </a>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th class="text-center">Date</th>
                                <th class="text-end">Payable</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Due</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('invoice.show', $invoice->uuid) }}" target="_blank" class="text-decoration-none">
                                            <strong>{{ $invoice->invoice_number }}</strong>
                                        </a>
                                    </td>
                                    <td>
                                        @if($invoice->customer)
                                            <a href="{{ route('customers.view', $invoice->customer->id) }}" target="_blank" class="text-decoration-none">
                                                {{ $invoice->customer->name }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $invoice->customer->phone ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $invoice->date->format('Y-m-d') }}</td>
                                    <td class="text-end">{{ $invoice->formatted_payable_amount }}</td>
                                    <td class="text-end text-success">{{ $invoice->formatted_paid_amount }}</td>
                                    <td class="text-end text-danger">{{ $invoice->formatted_due_amount }}</td>
                                    <td class="text-center">
                                        <span class="badge
                                            @if($invoice->status == 'active') bg-success
                                            @elseif($invoice->status == 'returned') bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('invoice.show', $invoice->uuid) }}" target="_blank" class="btn btn-sm btn-info" title="View Invoice">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No invoices found for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <!--============== jQuery UI Datepicker CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/jquery-ui/jquery-ui.min.css')}}">
    <!--============== End jQuery UI Datepicker CSS =================-->

    <style>
        .card-stats {
            border-left: 4px solid #007bff;
        }
        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .icon-box i {
            font-size: 24px;
        }
        .bg-primary-light { background-color: rgba(0, 123, 255, 0.1); }
        .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
        .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
        .bg-secondary-light { background-color: rgba(108, 117, 125, 0.1); }

        /* Invoice number link styling */
        .table tbody td a.text-decoration-none {
            color: #007bff;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .table tbody td a.text-decoration-none:hover {
            color: #0056b3;
            text-decoration: underline !important;
        }
    </style>
@endpush

@push('js')
    <!--============== jQuery UI Datepicker JS =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/jquery-ui/jquery-ui.js')}}"></script>
    <!--============== End jQuery UI Datepicker JS =================-->

    <!--============== Chart.js (Local) =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/chartjs/chart.umd.min.js')}}"></script>
    <!--============== End Chart.js =================-->

    <script>
        "use strict";

        /**
         * Sales Report Configuration
         * Pass routes and configuration to JavaScript before loading the main script
         */
        window.salesReportRoutes = {
            summary: '{{ route('sales-report.summary') }}',
            exportCsv: '{{ route('sales-report.export-csv') }}',
            getData: '{{ route('sales-report.getData') }}'
        };

        window.salesReportConfig = {
            debug: {{ config('app.debug') ? 'true' : 'false' }},
            locale: '{{ app()->getLocale() }}',
            currency: '{{ get_option('app_currency', '$') }}',
            dateFormatPhp: '{{ get_option('date_format', 'Y-m-d') }}'
        };

        // Chart data
        window.salesReportData = {
            dailySales: @json($dailySales),
            salesByStatus: @json($salesByStatus)
        };
    </script>

    <!--============== Sales Report Custom JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/sales-report.js')}}"></script>
    <!--============== End Sales Report Custom JS =================-->
@endpush
