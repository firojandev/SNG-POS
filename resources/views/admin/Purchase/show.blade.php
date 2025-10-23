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
                    <li class="breadcrumb-item">
                        <a href="{{route('purchase.index')}}">Purchases</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $purchase->invoice_number }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="theme-card-title mb-0">Purchase Details</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-secondary me-2">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('purchase.show', $purchase->uuid) }}?download=pdf" class="btn btn-sm btn-danger me-2">
                            <i class="fa fa-file-pdf-o"></i> Download PDF
                        </a>
                        <a href="{{ route('purchase.create') }}" class="btn btn-sm btn-brand-secondary">
                            <i class="fa fa-plus"></i> Add Purchase
                        </a>
                    </div>
                </div>
            </div>

            <div class="theme-card-body">
                <!-- Purchase Header Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted mb-3">Purchase Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted"><strong>Invoice Number:</strong></td>
                                        <td><span class="badge bg-primary">{{ $purchase->invoice_number }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Purchase Date:</strong></td>
                                        <td>
                                            @if($purchase->date)
                                                {{ \Carbon\Carbon::parse($purchase->date)->format(get_option('date_format', 'Y-m-d')) }}
                                            @else
                                                {{ $purchase->created_at->format('M d, Y') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Created At:</strong></td>
                                        <td>{{ $purchase->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted mb-3">Supplier Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted"><strong>Name:</strong></td>
                                        <td>{{ $purchase->supplier->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Phone:</strong></td>
                                        <td>{{ $purchase->supplier->phone }}</td>
                                    </tr>
                                    @if($purchase->supplier->email)
                                    <tr>
                                        <td class="text-muted"><strong>Email:</strong></td>
                                        <td>{{ $purchase->supplier->email }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted mb-3">Payment Summary</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted"><strong>Total Amount:</strong></td>
                                        <td><strong>{{ $purchase->formatted_total_amount }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Paid Amount:</strong></td>
                                        <td class="text-success"><strong>{{ $purchase->formatted_paid_amount }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Due Amount:</strong></td>
                                        <td class="{{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            <strong>{{ $purchase->formatted_due_amount }}</strong>
                                        </td>
                                    </tr>
                                </table>
                                @if($purchase->due_amount > 0)
                                    <span class="badge bg-warning">Partial Payment</span>
                                @else
                                    <span class="badge bg-success">Fully Paid</span>
                                @endif
                            </div>
                        </div>

                        @if($purchase->note)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-sticky-note-o"></i> Note:</strong>
                                    <p class="mb-0 mt-2">{{ $purchase->note }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Purchase Items -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="fa fa-shopping-cart"></i> Purchase Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="35%">Product</th>
                                        <th width="10%" class="text-center">SKU</th>
                                        <th width="10%" class="text-center">Unit</th>
                                        <th width="10%" class="text-end">Unit Price</th>
                                        <th width="8%" class="text-center">Quantity</th>
                                        <th width="10%" class="text-end">Tax</th>
                                        <th width="12%" class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                         alt="{{ $item->product->name }}"
                                                         class="me-2 rounded"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product->name }}</strong>
                                                    @if($item->product->category)
                                                        <br><small class="text-muted"><i class="fa fa-tag"></i> {{ $item->product->category->name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><code>{{ $item->product->sku }}</code></td>
                                        <td class="text-center">
                                            @if($item->product->unit)
                                                {{ $item->product->unit->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $item->formatted_unit_price }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if($item->tax_amount > 0)
                                                {{ $item->formatted_tax_amount }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end"><strong>{{ $item->formatted_unit_total }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="7" class="text-end">Grand Total:</th>
                                        <th class="text-end text-primary">{{ $purchase->formatted_total_amount }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('css')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
</style>
@endpush
