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
                        {{@$title}}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header d-flex justify-content-between align-items-center">
                <h6 class="theme-card-title">{{@$title}}</h6>
                <a href="{{ route('invoice.create') }}" class="btn btn-sm btn-brand-secondary">
                    <i class="fa fa-plus"></i> Add Invoice
                </a>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Paid Amount</th>
                                <th class="text-center">Due Amount</th>
                                <th class="text-center">Status</th>
                                <th class="width-20-percentage text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody">
                            <!-- Data will be populated via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Include Payment from Customer Modal Component --}}
        @include('admin.components.payment-from-customer-modal')

    </main>
@endsection

@push('css')
    <!--============== DataTable CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/datatable/css/dataTables.bootstrap5.min.css')}}">
    <!--============== End DataTable CSS =================-->
@endpush

@push('js')
    <!--============== DataTable JS =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
    <!--============== End DataTable JS =================-->

    <script>
        "use strict";

        /**
         * Invoice Index Page Configuration
         * Pass routes and configuration to JavaScript before loading the main script
         */
        window.invoiceIndexRoutes = {
            getData: '{{ route('invoice.api.getData') }}',
            view: '/admin/invoice/:uuid',
            create: '{{ route('invoice.create') }}',
            edit: '/admin/invoice/:uuid/edit',
            destroy: '/admin/invoice/:uuid',
            return: '/admin/invoice/:uuid/return',
            cancel: '/admin/invoice/:uuid/cancel'
        };

        window.invoiceIndexConfig = {
            debug: {{ config('app.debug') ? 'true' : 'false' }},
            locale: '{{ app()->getLocale() }}',
            currency: '{{ get_option('app_currency', '$') }}'
        };
    </script>

    <!--============== Invoice Index Custom JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/invoice-index.js')}}"></script>
    <!--============== End Invoice Index Custom JS =================-->

    <!--============== Payment from Customer Modal JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/payment-from-customer-modal.js')}}"></script>
    <script>
        "use strict";
        $(document).ready(function() {
            // Initialize payment modal instance
            window.paymentFromCustomerModal = new PaymentFromCustomerModal({
                currency: '{{ get_option('app_currency', '$') }}',
                onSuccess: function() {
                    // Reload DataTable instead of full page reload
                    if (invoiceIndexManager && invoiceIndexManager.dataTable) {
                        invoiceIndexManager.refreshTable();
                    } else {
                        location.reload();
                    }
                }
            });
        });
    </script>
    <!--============== End Payment from Customer Modal JS =================-->
@endpush
