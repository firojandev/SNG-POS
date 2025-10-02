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
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.products.index') }}">Products</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $product->name }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header d-flex justify-content-between align-items-center">
                <h6 class="theme-card-title">Product Details</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit Product
                    </a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                          class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="row">
                    <!-- Left Column - Product Image -->
                    <div class="col-md-4">
                        <div class="text-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="img-fluid rounded shadow-sm mb-3"
                                     style="max-height: 300px; width: 100%; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                                     style="height: 300px;">
                                    <div class="text-center">
                                        <i class="fa fa-image fa-4x text-muted mb-2"></i>
                                        <p class="text-muted">No Image Available</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Status Badge -->
                            <div class="mb-3">
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <!-- Stock Status -->
                            <div class="alert {{ $product->stock_quantity == 0 ? 'alert-danger' : ($product->stock_quantity < 10 ? 'alert-warning' : 'alert-success') }}">
                                <strong>Stock Status:</strong> {{ $product->stock_status }}
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Product Information -->
                    <div class="col-md-8">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-12 mb-4">
                                <h4 class="mb-3">{{ $product->name }}</h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="fw-bold text-muted">SKU:</td>
                                                <td>{{ $product->sku }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Category:</td>
                                                <td>
                                                    @if($product->category)
                                                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                                                    @else
                                                        <span class="text-muted">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Unit:</td>
                                                <td>
                                                    @if($product->unit)
                                                        <span class="badge bg-info">{{ $product->unit->name }}</span>
                                                    @else
                                                        <span class="text-muted">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Tax:</td>
                                                <td>
                                                    @if($product->tax)
                                                        <span class="badge bg-warning">{{ $product->tax->name }} ({{ $product->tax->rate }}%)</span>
                                                    @else
                                                        <span class="text-muted">No tax</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="fw-bold text-muted">Purchase Price:</td>
                                                <td class="fw-bold text-success">{{ $product->formatted_purchase_price }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-muted">Sell Price:</td>
                                                <td class="fw-bold text-primary">{{ $product->formatted_sell_price }}</td>
                                            </tr>

                                            <tr>
                                                <td class="fw-bold text-muted">Stock Quantity:</td>
                                                <td class="fw-bold">
                                                    {{ $product->stock_quantity }}
                                                    {{ $product->unit ? $product->unit->name : 'Unit' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($product->description)
                                <div class="col-12 mb-4">
                                    <h6 class="fw-bold text-muted">Description</h6>
                                    <div class="bg-light p-3 rounded">
                                        {{ $product->description }}
                                    </div>
                                </div>
                            @endif

                            <!-- Timestamps -->
                            <div class="col-12">
                                <h6 class="fw-bold text-muted">Record Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Created:</strong> {{ $product->created_at->format('M d, Y \a\t h:i A') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Last Updated:</strong> {{ $product->updated_at->format('M d, Y \a\t h:i A') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Products
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fa fa-print"></i> Print
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="generateBarcode()">
                                    <i class="fa fa-barcode"></i> Generate Barcode
                                </button>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> Edit Product
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barcode Modal -->
        <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="barcodeModalLabel">Product Barcode</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="barcodeContainer">
                            <!-- Barcode will be generated here -->
                        </div>
                        <p class="mt-3 mb-0">{{ $product->sku }}</p>
                        <small class="text-muted">{{ $product->name }}</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="printBarcode()">Print Barcode</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('admin/partial/js/products.js') }}"></script>
@endpush
