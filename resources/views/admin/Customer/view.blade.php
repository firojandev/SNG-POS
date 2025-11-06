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
                        <a href="{{route('customers.index')}}">Customers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Customer Profile
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Customer Profile Header -->
        <div class="theme-card mb-4">
            <div class="theme-card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($customer->photo)
                            <img src="{{ asset('storage/' . $customer->photo) }}" alt="{{ $customer->name }}"
                                 class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                                 style="width: 120px; height: 120px; color: white; font-weight: bold; font-size: 48px;">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <h3 class="mb-2">{{ $customer->name }}</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fa fa-phone text-muted me-2"></i><strong>Phone:</strong> {{ $customer->phone }}</p>
                                <p class="mb-1"><i class="fa fa-envelope text-muted me-2"></i><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fa fa-map-marker text-muted me-2"></i><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</p>
                                <p class="mb-1"><i class="fa fa-circle text-muted me-2" style="font-size: 8px;"></i><strong>Status:</strong>
                                    <span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Current Balance</h6>
                                <h2 class="mb-0 {{ $totalBalance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ get_option('app_currency') }}{{ number_format(abs($totalBalance), 2) }}
                                </h2>
                                <small class="text-muted">{{ $totalBalance > 0 ? 'Receivable' : 'Paid' }}</small>
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
                        <i class="fa fa-shopping-bag fa-3x text-info mb-3"></i>
                        <h6 class="text-muted mb-2">Total Sales</h6>
                        <h3 class="mb-0">{{ $totalInvoices }}</h3>
                        <small class="text-muted">Invoices</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fa fa-money fa-3x text-primary mb-3"></i>
                        <h6 class="text-muted mb-2">Total Sales Amount</h6>
                        <h3 class="mb-0">{{ get_option('app_currency') }}{{ number_format($totalSalesAmount, 2) }}</h3>
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

        <!-- Tabs for Invoice and Payment History -->
        <div class="theme-card">
            <div class="theme-card-body">
                <ul class="nav nav-tabs mb-3" id="customerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice"
                                type="button" role="tab" aria-controls="invoice" aria-selected="true">
                            <i class="fa fa-shopping-bag me-2"></i>Invoice History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment"
                                type="button" role="tab" aria-controls="payment" aria-selected="false">
                            <i class="fa fa-credit-card me-2"></i>Payment History
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="customerTabContent">
                    <!-- Invoice History Tab -->
                    <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
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
                                    @forelse($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoice.show', $invoice->uuid) }}" class="fw-bold text-primary" target="_blank">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->date->format(get_option('date_format', 'd M Y')) }}</td>
                                            <td>{{ get_option('app_currency') }}{{ number_format($invoice->total_amount, 2) }}</td>
                                            <td class="text-success">{{ get_option('app_currency') }}{{ number_format($invoice->paid_amount, 2) }}</td>
                                            <td class="text-danger">{{ get_option('app_currency') }}{{ number_format($invoice->due_amount, 2) }}</td>
                                            <td>
                                                @if($invoice->due_amount <= 0)
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($invoice->paid_amount > 0)
                                                    <span class="badge bg-warning">Partial</span>
                                                @else
                                                    <span class="badge bg-danger">Unpaid</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('invoice.show', $invoice->uuid) }}"
                                                       class="btn btn-sm btn-primary me-2" target="_blank"
                                                       title="View Invoice Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    @if($invoice->due_amount > 0)
                                                        <button type="button"
                                                           class="btn btn-sm btn-success"
                                                           onclick="openPaymentFromCustomerModal('{{ $invoice->uuid }}', '{{ $invoice->invoice_number }}', {{ $invoice->due_amount }}, {{ $customer->id }})"
                                                           title="Receive Payment">
                                                            <i class="fa fa-credit-card"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                                No invoice history found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination for Invoices -->
                        @if($invoices->hasPages())
                            <div class="mt-3">
                                {{ $invoices->links() }}
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
                                                @if($payment->invoice)
                                                    <a href="{{ route('invoice.show', $payment->invoice->uuid) }}" target="_blank">
                                                        {{ $payment->invoice->invoice_number }}
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

        {{-- Include Payment from Customer Modal Component --}}
        @include('admin.components.payment-from-customer-modal')

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

@push('js')
    <!--============== Invoice Payment Modal JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/payment-from-customer-modal.js')}}"></script>
    <script>
        "use strict";
        $(document).ready(function() {
            // Initialize payment modal instance
            window.paymentFromCustomerModal = new PaymentFromCustomerModal({
                currency: '{{ get_option('app_currency', '$') }}',
                onSuccess: function() {
                    // Reload the page to show updated balances and payment history
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            });
        });
    </script>
    <!--============== End Invoice Payment Modal JS =================-->
@endpush
