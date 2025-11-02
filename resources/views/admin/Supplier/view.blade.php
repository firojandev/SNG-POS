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
                        <a href="{{route('suppliers.index')}}">Suppliers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Supplier Profile
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Supplier Profile Header -->
        <div class="theme-card mb-4">
            <div class="theme-card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($supplier->photo)
                            <img src="{{ asset('storage/' . $supplier->photo) }}" alt="{{ $supplier->name }}"
                                 class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                                 style="width: 120px; height: 120px; color: white; font-weight: bold; font-size: 48px;">
                                {{ strtoupper(substr($supplier->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <h3 class="mb-2">{{ $supplier->name }}</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fa fa-user text-muted me-2"></i><strong>Contact Person:</strong> {{ $supplier->contact_person ?? 'N/A' }}</p>
                                <p class="mb-1"><i class="fa fa-phone text-muted me-2"></i><strong>Phone:</strong> {{ $supplier->phone }}</p>
                                <p class="mb-1"><i class="fa fa-envelope text-muted me-2"></i><strong>Email:</strong> {{ $supplier->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fa fa-map-marker text-muted me-2"></i><strong>Address:</strong> {{ $supplier->address ?? 'N/A' }}</p>
                                <p class="mb-1"><i class="fa fa-circle text-muted me-2" style="font-size: 8px;"></i><strong>Status:</strong>
                                    <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        @if($supplier->about)
                            <p class="mb-0 mt-2"><i class="fa fa-info-circle text-muted me-2"></i><strong>About:</strong> {{ $supplier->about }}</p>
                        @endif
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Current Balance</h6>
                                <h2 class="mb-0 {{ $totalBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ get_option('app_currency') }}{{ number_format(abs($totalBalance), 2) }}
                                </h2>
                                <small class="text-muted">{{ $totalBalance >= 0 ? 'Payable' : 'Receivable' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fa fa-shopping-cart fa-3x text-info mb-3"></i>
                        <h6 class="text-muted mb-2">Total Purchases</h6>
                        <h3 class="mb-0">{{ $totalPurchases }}</h3>
                        <small class="text-muted">Invoices</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fa fa-money fa-3x text-primary mb-3"></i>
                        <h6 class="text-muted mb-2">Total Purchase Amount</h6>
                        <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($totalPurchaseAmount, 2) }}</h3>
                        <small class="text-muted">All time</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fa fa-credit-card fa-3x text-success mb-3"></i>
                        <h6 class="text-muted mb-2">Total Payments</h6>
                        <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($totalPayments, 2) }}</h3>
                        <small class="text-muted">{{ $paymentsCount }} transactions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fa fa-clock-o fa-3x text-warning mb-3"></i>
                        <h6 class="text-muted mb-2">Due Amount</h6>
                        <h3 class="mb-0 text-danger">{{ get_option('app_currency') }}{{ number_format($totalDueAmount, 2) }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for Purchase and Payment History -->
        <div class="theme-card">
            <div class="theme-card-body">
                <ul class="nav nav-tabs mb-3" id="supplierTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase"
                                type="button" role="tab" aria-controls="purchase" aria-selected="true">
                            <i class="fa fa-shopping-cart me-2"></i>Purchase History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment"
                                type="button" role="tab" aria-controls="payment" aria-selected="false">
                            <i class="fa fa-credit-card me-2"></i>Payment History
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="supplierTabContent">
                    <!-- Purchase History Tab -->
                    <div class="tab-pane fade show active" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Due Amount</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $purchase)
                                        <tr>
                                            <td><strong>{{ $purchase->invoice_number }}</strong></td>
                                            <td>{{ $purchase->date->format(get_option('date_format', 'd M Y')) }}</td>
                                            <td>{{ get_option('app_currency') }}{{ number_format($purchase->total_amount, 2) }}</td>
                                            <td class="text-success">{{ get_option('app_currency') }}{{ number_format($purchase->paid_amount, 2) }}</td>
                                            <td class="text-danger">{{ get_option('app_currency') }}{{ number_format($purchase->due_amount, 2) }}</td>
                                            <td>
                                                @if($purchase->due_amount <= 0)
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($purchase->paid_amount > 0)
                                                    <span class="badge bg-warning">Partial</span>
                                                @else
                                                    <span class="badge bg-danger">Unpaid</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('purchase.show', $purchase->uuid) }}"
                                                   class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                                No purchase history found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination for Purchases -->
                        @if($purchases->hasPages())
                            <div class="mt-3">
                                {{ $purchases->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Payment History Tab -->
                    <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>Invoice No.</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format(get_option('date_format', 'd M Y')) }}</td>
                                            <td>
                                                @if($payment->purchase)
                                                    <a href="{{ route('purchase.show', $payment->purchase->uuid) }}" target="_blank">
                                                        {{ $payment->purchase->invoice_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-success">
                                                <strong>{{ get_option('app_currency') }}{{ number_format($payment->amount, 2) }}</strong>
                                            </td>
                                            <td>{{ $payment->note ?? '-' }}</td>
                                            <td>{{ $payment->created_at->format(get_option('date_format', 'd M Y') . ' H:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                                No payment history found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination for Payments -->
                        @if($payments->hasPages())
                            <div class="mt-3">
                                {{ $payments->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <style>
        .card {
            transition: transform 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
    </style>
@endpush
