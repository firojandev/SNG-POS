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
                        Import Products
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <h6 class="theme-card-title">Import Products from CSV</h6>
            </div>
            <div class="theme-card-body">
                <div class="row">
                    <!-- Instructions Column -->
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fa fa-info-circle text-info"></i> Import Instructions
                                </h6>
                            </div>
                            <div class="card-body">
                                <h6>CSV Format Requirements:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fa fa-check text-success"></i> First row should contain headers</li>
                                    <li><i class="fa fa-check text-success"></i> Required columns: Name, SKU, Purchase Price, Sell Price, Stock Quantity</li>
                                    <li><i class="fa fa-check text-success"></i> Optional columns: Category, Unit, Tax, VAT, Description</li>
                                    <li><i class="fa fa-check text-success"></i> Maximum file size: 2MB</li>
                                    <li><i class="fa fa-check text-success"></i> Supported formats: CSV, TXT</li>
                                </ul>

                                <h6 class="mt-4">Column Order:</h6>
                                <ol class="small">
                                    <li>Name <span class="text-danger">*</span></li>
                                    <li>SKU <span class="text-danger">*</span></li>
                                    <li>Purchase Price <span class="text-danger">*</span></li>
                                    <li>Sell Price <span class="text-danger">*</span></li>
                                    <li>Stock Quantity <span class="text-danger">*</span></li>
                                    <li>Category (optional)</li>
                                    <li>Unit (optional)</li>
                                    <li>Tax (optional)</li>
                                    <li>VAT (optional)</li>
                                    <li>Description (optional)</li>
                                </ol>

                                <div class="alert alert-warning mt-3">
                                    <small>
                                        <strong>Note:</strong> Category, Unit, Tax, and VAT should match existing names in your system. 
                                        If not found, they will be left empty.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Sample CSV Download -->
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-info w-100" onclick="downloadSampleCSV()">
                                <i class="fa fa-download"></i> Download Sample CSV Template
                            </button>
                        </div>
                    </div>

                    <!-- Upload Form Column -->
                    <div class="col-md-6">
                        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fa fa-upload text-primary"></i> Upload CSV File
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- File Upload -->
                                    <div class="mb-4">
                                        <label for="csv_file" class="form-label">Select CSV File <span class="text-danger">*</span></label>
                                        <div class="file-upload-container">
                                            <input type="file" class="form-control @error('csv_file') is-invalid @enderror" 
                                                   id="csv_file" name="csv_file" accept=".csv,.txt" required 
                                                   onchange="previewFile(this)">
                                            @error('csv_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- File Preview -->
                                        <div id="filePreview" class="mt-3" style="display: none;">
                                            <div class="alert alert-info">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-file-csv fa-2x me-3"></i>
                                                    <div>
                                                        <strong id="fileName"></strong><br>
                                                        <small id="fileSize" class="text-muted"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Import Options -->
                                    <div class="mb-4">
                                        <h6>Import Options</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="skip_duplicates" 
                                                   name="skip_duplicates" value="1" checked>
                                            <label class="form-check-label" for="skip_duplicates">
                                                Skip duplicate SKUs
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="update_existing" 
                                                   name="update_existing" value="1">
                                            <label class="form-check-label" for="update_existing">
                                                Update existing products (if SKU matches)
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-upload"></i> Import Products
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Recent Imports -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fa fa-history text-secondary"></i> Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.products.export') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fa fa-download"></i> Export Current Products
                                    </a>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa fa-list"></i> View All Products
                                    </a>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-plus"></i> Add Single Product
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('admin/partial/js/products.js') }}"></script>
@endpush
