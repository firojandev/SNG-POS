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
                        Stock Report
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <div class="theme-card stock-card border-start-primary">
                    <div class="theme-card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Products</p>
                                <h2 class="mb-0 fw-bold">{{ number_format($summary['total_products']) }}</h2>
                            </div>
                            <div class="stat-icon bg-primary">
                                <i class="fa fa-cubes"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <div class="theme-card stock-card border-start-info">
                    <div class="theme-card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Stock Quantity</p>
                                <h2 class="mb-0 fw-bold">{{ number_format($summary['total_stock_quantity']) }}</h2>
                            </div>
                            <div class="stat-icon bg-info">
                                <i class="fa fa-database"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <div class="theme-card stock-card border-start-success">
                    <div class="theme-card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Stock Value</p>
                                <h2 class="mb-0 fw-bold">{{ get_option('app_currency') }}{{ number_format($summary['total_stock_value'], 2) }}</h2>
                            </div>
                            <div class="stat-icon bg-success">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <div class="theme-card stock-card border-start-success">
                    <div class="theme-card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">In Stock</p>
                                <h2 class="mb-0 fw-bold text-success">{{ number_format($summary['in_stock']) }}</h2>
                            </div>
                            <div class="stat-icon bg-success">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <div class="theme-card stock-card border-start-warning">
                    <div class="theme-card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Low Stock</p>
                                <h2 class="mb-0 fw-bold text-warning">{{ number_format($summary['low_stock']) }}</h2>
                            </div>
                            <div class="stat-icon bg-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <div class="theme-card stock-card border-start-danger">
                    <div class="theme-card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Out of Stock</p>
                                <h2 class="mb-0 fw-bold text-danger">{{ number_format($summary['out_of_stock']) }}</h2>
                            </div>
                            <div class="stat-icon bg-danger">
                                <i class="fa fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="theme-card mb-4">
            <div class="theme-card-body">
                <form action="{{ route('stock-report.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Product</label>
                        <input type="text" class="form-control form-control-sm search-product" id="search" name="search" value="{{ $searchTerm ?? '' }}" placeholder="Product name or SKU">
                    </div>
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select form-select-sm" id="category_id" name="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="stock_status" class="form-label">Stock Status</label>
                        <select class="form-select form-select-sm" id="stock_status" name="stock_status">
                            <option value="">All Status</option>
                            <option value="in" {{ $stockStatus == 'in' ? 'selected' : '' }}>In Stock</option>
                            <option value="low" {{ $stockStatus == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ $stockStatus == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="window.location.href='{{ route('stock-report.index') }}'">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stock Report Table -->
        <div class="theme-card">
            <div class="theme-card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa fa-table me-2 text-primary"></i>Stock Report
                            <span class="badge bg-primary ms-2">{{ number_format($products->total()) }} Products</span>
                        </h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <label class="mb-0 text-muted small me-2">Show:</label>
                            <select class="form-select form-select-sm" id="perPageSelect" style="width: 120px;">
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                            </select>
                            <a href="{{ route('stock-report.export-csv', ['category_id' => $categoryId, 'stock_status' => $stockStatus, 'search' => $searchTerm]) }}" class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                <i class="fa fa-download"></i>
                                <span>Export CSV</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="stockReportTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th class="text-center">Stock Qty</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Purchase Price</th>
                                <th class="text-end">Sell Price</th>
                                <th class="text-end">Stock Value</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $product)
                                @php
                                    $stockValue = $product->stock_quantity * $product->purchase_price;

                                    // Determine stock status (Low stock threshold: 10)
                                    if ($product->stock_quantity <= 0) {
                                        $statusClass = 'danger';
                                        $statusText = 'Out of Stock';
                                    } elseif ($product->stock_quantity <= 10) {
                                        $statusClass = 'warning';
                                        $statusText = 'Low Stock';
                                    } else {
                                        $statusClass = 'success';
                                        $statusText = 'In Stock';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                    </td>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                    <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusClass }}">{{ number_format($product->stock_quantity ?? 0) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($product->purchase_price ?? 0, 2) }}</td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($product->sell_price ?? 0, 2) }}</td>
                                    <td class="text-end">
                                        <strong>{{ get_option('app_currency') }}{{ number_format($stockValue, 2) }}</strong>
                                    </td>
                                    <td>{{ $product->unit->name ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No products found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="theme-card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ number_format($products->total()) }} products
                    </div>
                    <div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <!--============== Stock Report CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/partial/css/stock-report.css')}}">
    <!--============== End Stock Report CSS =================-->
@endpush

@push('js')
    <!--============== Stock Report Custom JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/stock-report.js')}}"></script>
    <!--============== End Stock Report Custom JS =================-->
@endpush
