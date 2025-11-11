@extends('layouts.admin')
@section('content')
    <main class="main-content">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Dashboard Overview</h4>
            <p class="text-muted mb-0">Welcome back! Here's what's happening with your store today.</p>
        </div>
        <div class="text-muted">
            <i class="fa fa-calendar"></i> {{ now()->format('F d, Y') }}
        </div>
    </div>

    <!-- Summary Statistics Cards - Row 1 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card card-stats">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($total_sales, 2) }}</h3>
                            <small class="text-muted">This Month</small>
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
                            <h6 class="text-muted mb-1">Net Revenue</h6>
                            <h3 class="mb-0 {{ $total_revenue >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ get_option('app_currency') }}{{ number_format($total_revenue, 2) }}
                            </h3>
                            <small class="text-muted">Profit This Month</small>
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
                            <h6 class="text-muted mb-1">Total Purchases</h6>
                            <h3 class="mb-0 text-danger">{{ get_option('app_currency') }}{{ number_format($total_purchases, 2) }}</h3>
                            <small class="text-muted">This Month</small>
                        </div>
                        <div class="icon-box bg-danger-light">
                            <i class="fa fa-shopping-cart text-danger"></i>
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
                            <h6 class="text-muted mb-1">Profit Margin</h6>
                            <h3 class="mb-0 text-info">{{ number_format($profit_margin, 1) }}%</h3>
                            <small class="text-muted">This Month</small>
                        </div>
                        <div class="icon-box bg-info-light">
                            <i class="fa fa-percent text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards - Row 2 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card card-stats-secondary">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Orders</h6>
                            <h4 class="mb-0">{{ number_format($total_orders) }}</h4>
                            <small class="text-muted">This Month</small>
                        </div>
                        <div class="icon-box-sm bg-primary-light">
                            <i class="fa fa-shopping-basket text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card card-stats-secondary">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Customers</h6>
                            <h4 class="mb-0">{{ number_format($total_customers) }}</h4>
                            <small class="text-muted">Active</small>
                        </div>
                        <div class="icon-box-sm bg-success-light">
                            <i class="fa fa-users text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card card-stats-secondary">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Products</h6>
                            <h4 class="mb-0">{{ number_format($total_products) }}</h4>
                            <small class="text-muted">In Inventory</small>
                        </div>
                        <div class="icon-box-sm bg-info-light">
                            <i class="fa fa-cubes text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card card-stats-secondary">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Stock Value</h6>
                            <h4 class="mb-0">{{ get_option('app_currency') }}{{ number_format($total_stock_value, 2) }}</h4>
                            <small class="text-muted">Inventory</small>
                        </div>
                        <div class="icon-box-sm bg-secondary-light">
                            <i class="fa fa-archive text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card border-start border-warning border-4">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Low Stock</h6>
                            <h4 class="mb-0 text-warning">{{ $low_stock_count }}</h4>
                            <small class="text-muted">Products</small>
                        </div>
                        <div class="icon-box-sm bg-warning-light">
                            <i class="fa fa-exclamation-triangle text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card border-start border-danger border-4">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Out of Stock</h6>
                            <h4 class="mb-0 text-danger">{{ $out_of_stock_count }}</h4>
                            <small class="text-muted">Products</small>
                        </div>
                        <div class="icon-box-sm bg-danger-light">
                            <i class="fa fa-times-circle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card border-start border-success border-4">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Due from Customers</h6>
                            <h4 class="mb-0 text-success">{{ get_option('app_currency') }}{{ number_format($total_due_from_customers, 2) }}</h4>
                            <small class="text-muted">Receivable</small>
                        </div>
                        <div class="icon-box-sm bg-success-light">
                            <i class="fa fa-arrow-down text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card border-start border-danger border-4">
                <div class="theme-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Due to Suppliers</h6>
                            <h4 class="mb-0 text-danger">{{ get_option('app_currency') }}{{ number_format($total_due_to_suppliers, 2) }}</h4>
                            <small class="text-muted">Payable</small>
                        </div>
                        <div class="icon-box-sm bg-danger-light">
                            <i class="fa fa-arrow-up text-danger"></i>
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
                    <h6 class="theme-card-title">Sales & Revenue Trend</h6>
                    <small class="text-muted">Last 12 Months - Sales vs Profit</small>
                </div>
                <div class="theme-card-body">
                    <div style="height: 300px;">
                        <canvas id="reviewChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h6 class="theme-card-title">Sales by Status</h6>
                </div>
                <div class="theme-card-body">
                    <div id="chartRadial" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Section -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="theme-card-header d-flex justify-content-between align-items-center">
                    <h6 class="theme-card-title mb-0">Top 10 Best Selling Products</h6>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
                <div class="theme-card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-end">Sales</th>
                                    <th class="text-center">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_selling_products as $index => $product)
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <span class="badge bg-warning">{{ $index + 1 }}</span>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $product->id) }}" class="text-decoration-none">
                                                <strong>{{ $product->name }}</strong>
                                            </a>
                                            <br><small class="text-muted">{{ $product->sku }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ number_format($product->total_quantity) }}</span>
                                        </td>
                                        <td class="text-end">{{ get_option('app_currency') }}{{ number_format($product->total_sales, 2) }}</td>
                                        <td class="text-center">
                                            @if($product->stock_quantity > 10)
                                                <span class="badge bg-success">{{ number_format($product->stock_quantity) }}</span>
                                            @elseif($product->stock_quantity > 0)
                                                <span class="badge bg-warning">{{ number_format($product->stock_quantity) }}</span>
                                            @else
                                                <span class="badge bg-danger">Out</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No sales data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="theme-card-header d-flex justify-content-between align-items-center">
                    <h6 class="theme-card-title mb-0">Top 10 Most Profitable Products</h6>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
                <div class="theme-card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Profit</th>
                                    <th class="text-end">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_revenue_products as $index => $product)
                                    @php
                                        $margin = $product->total_sales > 0 ? ($product->total_profit / $product->total_sales) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <span class="badge bg-warning">{{ $index + 1 }}</span>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $product->id) }}" class="text-decoration-none">
                                                <strong>{{ $product->name }}</strong>
                                            </a>
                                            <br><small class="text-muted">{{ $product->sku }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ number_format($product->total_quantity) }}</span>
                                        </td>
                                        <td class="text-end text-success"><strong>{{ get_option('app_currency') }}{{ number_format($product->total_profit, 2) }}</strong></td>
                                        <td class="text-end">
                                            <span class="badge {{ $margin > 30 ? 'bg-success' : ($margin > 15 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($margin, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No sales data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions and Low Stock -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="theme-card-header d-flex justify-content-between align-items-center">
                    <h6 class="theme-card-title mb-0">Recent Sales Invoices</h6>
                    <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
                <div class="theme-card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_invoices as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ route('invoice.show', $invoice->uuid) }}" class="text-decoration-none">
                                                <code>{{ $invoice->invoice_number }}</code>
                                            </a>
                                        </td>
                                        <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                                        <td><small>{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format(get_option('date_format', 'Y-m-d')) : 'N/A' }}</small></td>
                                        <td class="text-end">{{ get_option('app_currency') }}{{ number_format($invoice->payable_amount, 2) }}</td>
                                        <td class="text-center">
                                            @if($invoice->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($invoice->status == 'returned')
                                                <span class="badge bg-warning">Returned</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No recent invoices</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h6 class="theme-card-title">Low Stock Alert</h6>
                </div>
                <div class="theme-card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-end">Sell Price</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($low_stock_products as $product)
                                    <tr>
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td><code>{{ $product->sku }}</code></td>
                                        <td class="text-center">
                                            <span class="badge {{ $product->stock_quantity == 0 ? 'bg-danger' : 'bg-warning' }}">
                                                {{ number_format($product->stock_quantity) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ get_option('app_currency') }}{{ number_format($product->sell_price, 2) }}</td>
                                        <td class="text-center">
                                            @if($product->stock_quantity == 0)
                                                <span class="badge bg-danger">Out</span>
                                            @else
                                                <span class="badge bg-warning">Low</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">All products are well stocked</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header d-flex justify-content-between align-items-center">
                    <h6 class="theme-card-title mb-0">Recent Purchases</h6>
                    <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
                <div class="theme-card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                    <th class="text-center">Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_purchases as $purchase)
                                    <tr>
                                        <td>
                                            <a href="{{ route('purchase.show', $purchase->uuid) }}" class="text-decoration-none">
                                                <code>{{ $purchase->invoice_number }}</code>
                                            </a>
                                        </td>
                                        <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                                        <td>{{ $purchase->date ? \Carbon\Carbon::parse($purchase->date)->format(get_option('date_format', 'Y-m-d')) : 'N/A' }}</td>
                                        <td class="text-end">{{ get_option('app_currency') }}{{ number_format($purchase->total_amount, 2) }}</td>
                                        <td class="text-end">{{ get_option('app_currency') }}{{ number_format($purchase->paid_amount, 2) }}</td>
                                        <td class="text-end">{{ get_option('app_currency') }}{{ number_format($purchase->due_amount, 2) }}</td>
                                        <td class="text-center">
                                            @if($purchase->due_amount == 0)
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($purchase->paid_amount == 0)
                                                <span class="badge bg-danger">Unpaid</span>
                                            @else
                                                <span class="badge bg-warning">Partial</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No recent purchases</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/appexchart/dist/apexcharts.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/partial/css/dashboard.css')}}">
@endpush

@push('js')
    <!--==== Chart.js ========-->
    <script type="text/javascript" src="{{asset('admin/plugin/chartjs/chart.umd.min.js')}}"></script>
    <!--==== ApexCharts ========-->
    <script type="text/javascript" src="{{asset('admin/plugin/appexchart/dist/apexcharts.js')}}"></script>
    <script>
        // Pass data to JavaScript
        window.dashboardData = {
            monthlySales: @json($monthly_sales),
            monthlyRevenue: @json($monthly_revenue),
            activeSales: {{ $active_sales }},
            returnedSales: {{ $returned_sales }},
            cancelledSales: {{ $cancelled_sales }},
            currency: '{{ get_option("app_currency") }}'
        };
    </script>
    <script type="text/javascript" src="{{asset('admin/partial/js/dashboard.js')}}"></script>
@endpush
