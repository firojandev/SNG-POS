@extends('layouts.admin')

@push('styles')
    <link href="{{ asset('admin/partial/css/products.css') }}" rel="stylesheet">
@endpush

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
                        {{ $title }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <h6 class="theme-card-title">{{ $title }}</h6>
            </div>
            <div class="theme-card-body">
                <!-- Simple Filter Section -->
                <div class="simple-filter-section mb-4">
                    <div class="row align-items-center ">
                        <!-- Left Side: Search and Filter -->
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.products.index') }}" id="searchForm" class="d-flex gap-2 align-items-center">
                                <!-- Search Input -->
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Name, Sku, Scan Barcode"
                                       value="{{ request('search') }}">

                                <!-- Category Filter -->
                                <select name="category_id" class="form-select form-select-sm select2-dropdown">
                                    <option value="">All Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Search Button -->
                                <button type="submit" class="btn btn-success btn-sm d-flex gap-1 align-items-center">
                                    <i class="fa fa-search"></i> Search
                                </button>

                                @if(request('search') || request('category_id'))
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm d-flex gap-1 align-items-center">
                                        <i class="fa fa-times"></i> Clear
                                    </a>
                                @endif
                            </form>
                        </div>

                        <!-- Right Side: Action Buttons -->
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Add Product
                                </a>
                                <a href="{{ route('admin.products.export') }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-download"></i>Export CSV
                                </a>
                                <a href="{{ route('admin.products.import.form') }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-upload"></i> Import CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="card-body text-center">
                                    <!-- Product Image -->
                                    <div class="product-image mb-3">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="rounded-circle"
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px; margin: 0 auto;">
                                                <i class="fa fa-image text-muted fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Name -->
                                    <h6 class="product-name mb-2">{{ $product->name }}</h6>

                                    <!-- SKU -->
                                    <p class="text-muted mb-2">Sku: {{ $product->sku }}</p>

                                    <!-- Prices -->
                                    <div class="row text-start mb-2">
                                        <div class="col-6">
                                            <small class="text-muted">Purchase:</small>
                                            <div class="fw-bold">{{ $product->formatted_purchase_price }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Sales:</small>
                                            <div class="fw-bold">{{ $product->formatted_sell_price }}</div>
                                        </div>
                                    </div>

                                    <!-- Stock -->
                                    <div class="text-start mb-3">
                                        <small class="text-muted">Stock Quantity:</small>
                                        <div class="fw-bold">
                                            {{ $product->stock_quantity }}
                                            {{ $product->unit ? $product->unit->name : 'Unit' }}
                                            @if($product->stock_quantity == 0)
                                                <span class="badge bg-danger ms-1">Out of Stock</span>
                                            @elseif($product->stock_quantity < 10)
                                                <span class="badge bg-warning ms-1">Low Stock</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.products.show', $product) }}"
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary generate-barcode"
                                                title="Barcode"
                                                data-product-id="{{ $product->id }}"
                                                data-sku="{{ $product->sku }}"
                                                data-name="{{ $product->name }}">
                                            <i class="fa fa-barcode"></i>
                                        </button>
                                        <form method="POST"
                                              action="{{ route('admin.products.destroy', $product) }}"
                                              class="d-inline delete-product"
                                              data-product-name="{{ $product->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No products found</h5>
                                <p class="text-muted">Start by adding your first product</p>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Product
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                        <div class="small text-muted">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                        </div>
                        <div>
                            {{ $products->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5-links-only') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Barcode Quantity Modal -->
    <div class="modal fade" id="barcodeQuantityModal" tabindex="-1" aria-labelledby="barcodeQuantityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeQuantityModalLabel">
                        Generate Barcode
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="barcodeForm">
                        <div class="mb-3">
                            <div class="product-info">
                                <div class="fw-bold" id="modalProductName"></div>
                                <small class="text-muted">SKU: <span id="modalProductSku"></span></small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="barcodeQuantity" class="form-label">Quantity</label>
                            <input type="number"
                                   class="form-control"
                                   id="barcodeQuantity"
                                   name="quantity"
                                   min="1"
                                   max="100"
                                   placeholder="Enter quantity"
                                   required>
                            <div class="form-text">
                                <i class="fa fa-info-circle me-1"></i>Enter quantity between 1 to 100
                            </div>
                        </div>

                        <input type="hidden" id="productId" name="product_id">
                        <input type="hidden" id="productSku" name="sku">
                        <input type="hidden" id="productName" name="name">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="downloadBarcodeBtn">
                        <i class="fa fa-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin/partial/js/products.js') }}"></script>
@endpush
