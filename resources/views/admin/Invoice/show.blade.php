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
                        <a href="{{route('invoice.index')}}">Invoices</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $invoice->invoice_number }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="theme-card-title mb-0">Invoice Details</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-secondary me-2">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('invoice.show', $invoice->uuid) }}?download=pdf" class="btn btn-sm btn-danger me-2">
                            <i class="fa fa-file-pdf-o"></i> Download PDF
                        </a>
                        @if($invoice->status === 'active')
                            <button type="button" class="btn btn-sm btn-warning me-2" onclick="returnInvoice('{{ $invoice->uuid }}')">
                                <i class="fa fa-undo"></i> Return
                            </button>
                            <button type="button" class="btn btn-sm btn-dark me-2" onclick="cancelInvoice('{{ $invoice->uuid }}')">
                                <i class="fa fa-ban"></i> Cancel
                            </button>
                        @endif
                        <a href="{{ route('invoice.create') }}" class="btn btn-sm btn-brand-secondary">
                            <i class="fa fa-plus"></i> Add Invoice
                        </a>
                    </div>
                </div>
            </div>

            <div class="theme-card-body">
                <!-- Invoice Header Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted mb-3">Invoice Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted"><strong>Invoice Number:</strong></td>
                                        <td><span class="badge bg-primary">{{ $invoice->invoice_number }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Invoice Date:</strong></td>
                                        <td>
                                            @if($invoice->date)
                                                {{ \Carbon\Carbon::parse($invoice->date)->format(get_option('date_format', 'Y-m-d')) }}
                                            @else
                                                {{ $invoice->created_at->format('M d, Y') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Status:</strong></td>
                                        <td>
                                            @if($invoice->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($invoice->status === 'returned')
                                                <span class="badge bg-warning">Returned</span>
                                            @else
                                                <span class="badge bg-dark">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Created At:</strong></td>
                                        <td>{{ $invoice->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted mb-3">Customer Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted"><strong>Name:</strong></td>
                                        <td>{{ $invoice->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Phone:</strong></td>
                                        <td>{{ $invoice->customer->phone }}</td>
                                    </tr>
                                    @if($invoice->customer->email)
                                    <tr>
                                        <td class="text-muted"><strong>Email:</strong></td>
                                        <td>{{ $invoice->customer->email }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->customer->address)
                                    <tr>
                                        <td class="text-muted"><strong>Address:</strong></td>
                                        <td>{{ $invoice->customer->address }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <div class="col-md-4">
                                <h6 class="text-muted mb-3">Payment Summary</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted"><strong>Subtotal:</strong></td>
                                        <td>{{ $invoice->formatted_subtotal }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Discount:</strong></td>
                                        <td class="text-danger">{{ $invoice->formatted_discount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Total Amount:</strong></td>
                                        <td><strong>{{ $invoice->formatted_total_amount }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Paid Amount:</strong></td>
                                        <td class="text-success"><strong>{{ $invoice->formatted_paid_amount }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Due Amount:</strong></td>
                                        <td class="{{ $invoice->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            <strong>{{ $invoice->formatted_due_amount }}</strong>
                                        </td>
                                    </tr>
                                </table>
                                @if($invoice->due_amount > 0)
                                    <span class="badge bg-warning">Partial Payment</span>
                                @else
                                    <span class="badge bg-success">Fully Paid</span>
                                @endif
                            </div>
                        </div>

                        @if($invoice->note)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-sticky-note-o"></i> Note:</strong>
                                    <p class="mb-0 mt-2">{{ $invoice->note }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="fa fa-shopping-cart"></i> Invoice Items</h5>
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
                                        <th width="10%" class="text-end">VAT</th>
                                        <th width="12%" class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $index => $item)
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
                                            @if($item->vat_amount > 0)
                                                {{ $item->formatted_vat_amount }}
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
                                        <th class="text-end text-primary">{{ $invoice->formatted_total_amount }}</th>
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

@push('js')
<script>
    function returnInvoice(uuid) {
        if (confirm('Are you sure you want to return this invoice? This will restore product stock.')) {
            fetch(`/admin/invoice/${uuid}/return`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Invoice returned successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to return invoice');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while returning the invoice');
            });
        }
    }

    function cancelInvoice(uuid) {
        if (confirm('Are you sure you want to cancel this invoice? This will restore product stock.')) {
            fetch(`/admin/invoice/${uuid}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Invoice cancelled successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to cancel invoice');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while cancelling the invoice');
            });
        }
    }
</script>
@endpush
