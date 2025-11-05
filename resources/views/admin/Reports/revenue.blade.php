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

        <!-- Date Filter Section -->
        <div class="theme-card mb-4">
            <div class="theme-card-header">
                <h6 class="theme-card-title">Filter by Date Range</h6>
            </div>
            <div class="theme-card-body">
                <form action="{{ route('sales-report.revenue') }}" method="GET" class="row g-3">
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
                        <a href="{{ route('sales-report.export-revenue-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success w-100">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-secondary w-100" onclick="window.location.href='{{ route('sales-report.revenue') }}'">
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
                                <h6 class="text-muted mb-1">Total Revenue (Profit)</h6>
                                <h3 class="mb-0 text-success">{{ get_option('app_currency') }}{{ number_format($summary['total_revenue'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="fa fa-line-chart text-success"></i>
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
                                <h6 class="text-muted mb-1">Total Cost</h6>
                                <h3 class="mb-0 text-danger">{{ get_option('app_currency') }}{{ number_format($summary['total_cost'], 2) }}</h3>
                            </div>
                            <div class="icon-box bg-danger-light">
                                <i class="fa fa-money text-danger"></i>
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
                                <h6 class="text-muted mb-1">Revenue Margin</h6>
                                <h3 class="mb-0 text-info">{{ number_format($summary['revenue_margin'], 2) }}%</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="fa fa-percent text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Daily Revenue Trend</h6>
                    </div>
                    <div class="theme-card-body">
                        <canvas id="revenueChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Revenue by Category</h6>
                    </div>
                    <div class="theme-card-body">
                        <canvas id="categoryChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Breakdown Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Category Revenue Breakdown</h6>
                    </div>
                    <div class="theme-card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-center">Products</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Sales</th>
                                        <th class="text-end">Revenue (Profit)</th>
                                        <th class="text-end">Margin %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categoryRevenue as $category)
                                        <tr>
                                            <td><strong>{{ $category['category'] }}</strong></td>
                                            <td class="text-center">{{ $category['product_count'] }}</td>
                                            <td class="text-center">{{ number_format($category['total_quantity']) }}</td>
                                            <td class="text-end">{{ get_option('app_currency') }}{{ number_format($category['total_sales'], 2) }}</td>
                                            <td class="text-end text-success"><strong>{{ get_option('app_currency') }}{{ number_format($category['total_revenue'], 2) }}</strong></td>
                                            <td class="text-end">
                                                <span class="badge {{ $category['revenue_margin'] > 30 ? 'bg-success' : ($category['revenue_margin'] > 15 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ number_format($category['revenue_margin'], 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Profitable Products -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Top 10 Most Profitable Products</h6>
                    </div>
                    <div class="theme-card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th class="text-center">Qty Sold</th>
                                        <th class="text-end">Sales</th>
                                        <th class="text-end">Revenue (Profit)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProfitableProducts as $index => $product)
                                        <tr>
                                            <td>
                                                @if($index < 3)
                                                    <span class="badge bg-warning">{{ $index + 1 }}</span>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td><strong>{{ $product->name }}</strong></td>
                                            <td><code>{{ $product->sku }}</code></td>
                                            <td><span class="badge bg-secondary">{{ $product->category ?? 'Uncategorized' }}</span></td>
                                            <td class="text-center"><span class="badge bg-info">{{ number_format($product->total_quantity) }}</span></td>
                                            <td class="text-end">{{ get_option('app_currency') }}{{ number_format($product->total_sales, 2) }}</td>
                                            <td class="text-end text-success"><strong>{{ get_option('app_currency') }}{{ number_format($product->total_revenue, 2) }}</strong></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No products sold in this period</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Revenue Table -->
        <div class="theme-card">
            <div class="theme-card-header d-flex justify-content-between align-items-center">
                <h6 class="theme-card-title">Product Revenue Details</h6>
                <div class="text-muted">
                    <small>Total: {{ number_format($productRevenue->count()) }} products</small>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="revenueTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th class="text-center">Qty Sold</th>
                                <th class="text-end">Sales</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Margin %</th>
                                <th class="text-end">Avg Price</th>
                                <th class="text-center">Orders</th>
                                <th class="text-center">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productRevenue as $index => $item)
                                @php
                                    $revenueMargin = $item->total_sales > 0 ? ($item->total_revenue / $item->total_sales) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td><code>{{ $item->sku }}</code></td>
                                    <td><span class="badge bg-secondary">{{ $item->category ?? 'Uncategorized' }}</span></td>
                                    <td class="text-center"><span class="badge bg-info">{{ number_format($item->total_quantity) }}</span></td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($item->total_sales, 2) }}</td>
                                    <td class="text-end text-success"><strong>{{ get_option('app_currency') }}{{ number_format($item->total_revenue, 2) }}</strong></td>
                                    <td class="text-end">
                                        <span class="badge {{ $revenueMargin > 30 ? 'bg-success' : ($revenueMargin > 15 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($revenueMargin, 1) }}%
                                        </span>
                                    </td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($item->avg_price, 2) }}</td>
                                    <td class="text-center">{{ $item->order_count }}</td>
                                    <td class="text-center">
                                        @if(($item->stock_quantity ?? 0) > 10)
                                            <span class="badge bg-success">{{ number_format($item->stock_quantity) }}</span>
                                        @elseif(($item->stock_quantity ?? 0) > 0)
                                            <span class="badge bg-warning">{{ number_format($item->stock_quantity) }}</span>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No products sold in this period</td>
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

    <!--============== DataTables CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/datatable/css/dataTables.bootstrap5.min.css')}}">
    <!--============== End DataTables CSS =================-->

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
    </style>
@endpush

@push('js')
    <!--============== jQuery UI Datepicker JS =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/jquery-ui/jquery-ui.js')}}"></script>
    <!--============== End jQuery UI Datepicker JS =================-->

    <!--============== DataTables JS =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
    <!--============== End DataTables JS =================-->

    <!--============== Chart.js (Local) =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/chartjs/chart.umd.min.js')}}"></script>
    <!--============== End Chart.js =================-->

    <script>
        "use strict";

        /**
         * Revenue Report Configuration
         */
        window.revenueReportConfig = {
            currency: '{{ get_option('app_currency', '$') }}',
            dateFormatPhp: '{{ get_option('date_format', 'Y-m-d') }}'
        };

        // Revenue data for charts
        window.dailyRevenueData = @json($dailyRevenue);
        window.categoryRevenueData = @json($categoryRevenue);
    </script>

    <!--============== Revenue Report JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/revenue-report.js')}}"></script>
    <!--============== End Revenue Report JS =================-->
@endpush
