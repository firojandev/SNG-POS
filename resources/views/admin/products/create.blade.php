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
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8">
                            <div class="row">
                                <!-- Product Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- SKU -->
                                <div class="col-md-6 mb-3">
                                    <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                           id="sku" name="sku" value="{{ old('sku') }}" required>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Purchase Price -->
                                <div class="col-md-6 mb-3">
                                    <label for="purchase_price" class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{get_option('app_currency')}}</span>
                                        <input type="number" class="form-control @error('purchase_price') is-invalid @enderror"
                                               id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}"
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('purchase_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sell Price -->
                                <div class="col-md-6 mb-3">
                                    <label for="sell_price" class="form-label">Sell Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{get_option('app_currency')}}</span>
                                        <input type="number" class="form-control @error('sell_price') is-invalid @enderror"
                                               id="sell_price" name="sell_price" value="{{ old('sell_price') }}"
                                               step="0.01" min="0" required>
                                    </div>
                                    @error('sell_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Stock Quantity -->
                                <div class="col-md-6 mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                                           min="0" required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select select2-dropdown @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Unit -->
                                <div class="col-md-6 mb-3">
                                    <label for="unit_id" class="form-label">Unit</label>
                                    <select class="form-select select2-dropdown @error('unit_id') is-invalid @enderror"
                                            id="unit_id" name="unit_id">
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tax -->
                                <div class="col-md-6 mb-3">
                                    <label for="tax_id" class="form-label">Tax</label>
                                    <select class="form-select select2-dropdown @error('tax_id') is-invalid @enderror"
                                            id="tax_id" name="tax_id">
                                        <option value="">Select Tax</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{ $tax->id }}"
                                                {{ old('tax_id') == $tax->id ? 'selected' : '' }}>
                                                {{ $tax->name }} ({{ $tax->value }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tax_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- VAT -->
                                <div class="col-md-6 mb-3">
                                    <label for="vat_id" class="form-label">VAT</label>
                                    <select class="form-select select2-dropdown @error('vat_id') is-invalid @enderror"
                                            id="vat_id" name="vat_id">
                                        <option value="">Select VAT</option>
                                        @foreach($vats as $vat)
                                            <option value="{{ $vat->id }}"
                                                {{ old('vat_id') == $vat->id ? 'selected' : '' }}>
                                                {{ $vat->name }} ({{ $vat->value }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vat_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="4"
                                              placeholder="Enter product description...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Image Upload -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <div class="image-upload-container text-center">
                                    <div class="image-preview mb-3" id="imagePreview">
                                        <div class="upload-placeholder">
                                            <i class="fa fa-image fa-3x text-muted mb-2"></i>
                                            <p class="text-muted">Click to upload image</p>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                           id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active"
                                           name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('admin/partial/js/products.js') }}"></script>
@endpush
