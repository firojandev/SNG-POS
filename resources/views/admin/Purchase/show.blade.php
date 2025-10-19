@extends('layouts.admin')
@section('content')
    <main class="main-content">
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h4 class="theme-card-title">Purchase Details</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('purchase.index') }}" class="btn btn-secondary me-2">
                                    <i class="fa fa-arrow-left"></i> Back to Purchases
                                </a>
                                <a href="{{ route('purchase.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> New Purchase
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Purchase Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Invoice Number:</strong></td>
                                                        <td>{{ $purchase->invoice_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Supplier:</strong></td>
                                                        <td>{{ $purchase->supplier->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Purchase Date:</strong></td>
                                                        <td>{{ $purchase->created_at->format('M d, Y h:i A') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Total Amount:</strong></td>
                                                        <td class="text-end">{{ $purchase->formatted_total_amount }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Paid Amount:</strong></td>
                                                        <td class="text-end">{{ $purchase->formatted_paid_amount }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Due Amount:</strong></td>
                                                        <td class="text-end {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                            {{ $purchase->formatted_due_amount }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        @if($purchase->note)
                                        <div class="mt-3">
                                            <strong>Note:</strong>
                                            <p class="mt-1">{{ $purchase->note }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Purchase Items</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>SKU</th>
                                                        <th class="text-center">Unit Price</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-center">Tax</th>
                                                        <th class="text-end">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchase->items as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($item->product->image)
                                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                                @endif
                                                                <div>
                                                                    <strong>{{ $item->product->name }}</strong>
                                                                    @if($item->product->category)
                                                                        <br><small class="text-muted">{{ $item->product->category->name }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $item->product->sku }}</td>
                                                        <td class="text-center">{{ $item->formatted_unit_price }}</td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-center">
                                                            @if($item->tax_amount > 0)
                                                                {{ $item->formatted_tax_amount }}
                                                            @else
                                                                <span class="text-muted">No Tax</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ $item->formatted_unit_total }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-active">
                                                        <th colspan="5" class="text-end">Grand Total:</th>
                                                        <th class="text-end">{{ $purchase->formatted_total_amount }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Supplier Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>{{ $purchase->supplier->name }}</td>
                                            </tr>
                                            @if($purchase->supplier->contact_person)
                                            <tr>
                                                <td><strong>Contact Person:</strong></td>
                                                <td>{{ $purchase->supplier->contact_person }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Phone:</strong></td>
                                                <td>{{ $purchase->supplier->phone }}</td>
                                            </tr>
                                            @if($purchase->supplier->email)
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>{{ $purchase->supplier->email }}</td>
                                            </tr>
                                            @endif
                                            @if($purchase->supplier->address)
                                            <tr>
                                                <td><strong>Address:</strong></td>
                                                <td>{{ $purchase->supplier->address }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                        
                                        @if($purchase->supplier->about)
                                        <div class="mt-3">
                                            <strong>About:</strong>
                                            <p class="mt-1 text-muted">{{ $purchase->supplier->about }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Payment Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Amount:</span>
                                            <strong>{{ $purchase->formatted_total_amount }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Paid Amount:</span>
                                            <strong class="text-success">{{ $purchase->formatted_paid_amount }}</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span>Due Amount:</span>
                                            <strong class="{{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $purchase->formatted_due_amount }}
                                            </strong>
                                        </div>
                                        
                                        @if($purchase->due_amount > 0)
                                        <div class="mt-3">
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                This purchase has outstanding balance.
                                            </div>
                                        </div>
                                        @else
                                        <div class="mt-3">
                                            <div class="alert alert-success">
                                                <i class="fa fa-check-circle"></i>
                                                This purchase is fully paid.
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

