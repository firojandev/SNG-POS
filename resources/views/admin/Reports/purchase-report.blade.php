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
                        Purchase Reports
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
                <form action="{{ route('purchase-report.index') }}" method="GET" class="row g-3">
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
                        <a href="{{ route('purchase-report.export-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success w-100">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-secondary w-100" onclick="window.location.href='{{ route('purchase-report.index') }}'">
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
                                <h6 class="text-muted mb-1">Total Purchases</h6>
                                <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($summary['total_purchases'], 2) }}</h3>
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
                                <h6 class="text-muted mb-1">Total Orders</h6>
                                <h3 class="mb-0">{{ number_format($summary['total_count']) }}</h3>
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
                                <h6 class="text-muted mb-1">Items Purchased</h6>
                                <h3 class="mb-0">{{ number_format($summary['items_purchased']) }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="fa fa-cubes text-primary"></i>
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
                                <h6 class="text-muted mb-1">Unique Products</h6>
                                <h3 class="mb-0">{{ number_format($summary['unique_products']) }}</h3>
                            </div>
                            <div class="icon-box bg-warning-light">
                                <i class="fa fa-tag text-warning"></i>
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
                                <h6 class="text-muted mb-1">Unique Suppliers</h6>
                                <h3 class="mb-0">{{ number_format($summary['unique_suppliers']) }}</h3>
                            </div>
                            <div class="icon-box bg-secondary-light">
                                <i class="fa fa-users text-secondary"></i>
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
                                <h6 class="text-muted mb-1">Avg Purchase Value</h6>
                                <h3 class="mb-0 text-info">{{ get_option('app_currency') }}{{ number_format($summary['avg_purchase_value'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="fa fa-calculator text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Daily Purchase Chart -->
            <div class="col-lg-12 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Daily Purchase Trend</h6>
                    </div>
                    <div class="theme-card-body">
                        <canvas id="dailyPurchaseChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Suppliers and Products -->
        <div class="row mb-4">
            <!-- Top Suppliers -->
            <div class="col-lg-6 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Top 10 Suppliers</h6>
                    </div>
                    <div class="theme-card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Supplier</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Total Purchase</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSuppliers as $index => $supplierData)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($supplierData->supplier)
                                                    <a href="{{ route('suppliers.view', $supplierData->supplier->id) }}" target="_blank" class="text-decoration-none">
                                                        <strong>{{ $supplierData->supplier->name }}</strong>
                                                    </a>
                                                @else
                                                    <strong>N/A</strong>
                                                @endif
                                                <br><small class="text-muted">{{ $supplierData->supplier->phone ?? '' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $supplierData->purchase_count }}</span>
                                            </td>
                                            <td class="text-end">{{ get_option('app_currency') }}{{ number_format($supplierData->total_purchase, 2) }}</td>
                                            <td class="text-center">
                                                @if($supplierData->supplier)
                                                    <a href="{{ route('suppliers.view', $supplierData->supplier->id) }}" target="_blank" class="btn btn-sm btn-info" title="View Supplier">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No suppliers data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Purchased Products -->
            <div class="col-lg-6 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Top 10 Purchased Products</h6>
                    </div>
                    <div class="theme-card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
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
                                            <td colspan="4" class="text-center text-muted">No products purchased in this period</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product-wise Purchase Details -->
        <div class="theme-card mb-4">
            <div class="theme-card-header d-flex justify-content-between align-items-center">
                <h6 class="theme-card-title">Product-wise Purchase Details</h6>
                <a href="{{ route('purchase-report.export-product-wise-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success">
                    <i class="fa fa-download"></i> Export Product Details
                </a>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="productPurchaseTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th class="text-center">Qty Purchased</th>
                                <th class="text-end">Total Cost</th>
                                <th class="text-end">Avg Price</th>
                                <th class="text-center">Orders</th>
                                <th class="text-center">Current Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productPurchases as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $item->product->category->name ?? 'Uncategorized' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($item->total_quantity_purchased) }}</span>
                                    </td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($item->total_purchase_cost, 2) }}</td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($item->avg_purchase_price, 2) }}</td>
                                    <td class="text-center">{{ $item->order_count }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ number_format($item->product->stock_quantity ?? 0) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No product purchase data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Purchases -->
        <div class="theme-card">
            <div class="theme-card-header d-flex justify-content-between align-items-center">
                <h6 class="theme-card-title">Recent Purchases (Last 10)</h6>
                <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-brand-secondary">
                    <i class="fa fa-eye"></i> View All
                </a>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Supplier</th>
                                <th>Phone</th>
                                <th class="text-center">Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Due</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPurchases as $purchase)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchase.show', $purchase->uuid) }}" target="_blank" class="text-decoration-none">
                                            <strong>{{ $purchase->invoice_number }}</strong>
                                        </a>
                                    </td>
                                    <td>
                                        @if($purchase->supplier)
                                            <a href="{{ route('suppliers.view', $purchase->supplier->id) }}" target="_blank" class="text-decoration-none">
                                                {{ $purchase->supplier->name }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $purchase->supplier->phone ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $purchase->date->format('Y-m-d') }}</td>
                                    <td class="text-end">{{ $purchase->formatted_total_amount }}</td>
                                    <td class="text-end text-success">{{ $purchase->formatted_paid_amount }}</td>
                                    <td class="text-end text-danger">{{ $purchase->formatted_due_amount }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('purchase.show', $purchase->uuid) }}" target="_blank" class="btn btn-sm btn-info" title="View Purchase">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No purchases found for this period</td>
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

    <!--============== Purchase Report CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/partial/css/purchase-report.css')}}">
    <!--============== End Purchase Report CSS =================-->
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
         * Purchase Report Configuration
         * Pass configuration and data to JavaScript
         */
        window.purchaseReportConfig = {
            dateFormatPhp: '{{ get_option("date_format", "Y-m-d") }}',
            currency: '{{ get_option("app_currency") }}'
        };

        // Chart data
        window.purchaseReportData = {
            dailyPurchases: @json($dailyPurchases)
        };
    </script>

    <!--============== Purchase Report Custom JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/purchase-report.js')}}"></script>
    <!--============== End Purchase Report Custom JS =================-->
@endpush
