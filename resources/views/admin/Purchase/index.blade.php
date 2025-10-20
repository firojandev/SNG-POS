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
                <a href="{{ route('purchase.create') }}" class="btn btn-sm btn-brand-secondary">
                    <i class="fa fa-plus"></i> Add Purchase
                </a>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Supplier</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Paid Amount</th>
                                <th class="text-center">Due Amount</th>
                                <th class="width-20-percentage text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseTableBody">
                            <!-- Data will be populated via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
         * Purchase Index Page Configuration
         * Pass routes and configuration to JavaScript before loading the main script
         */
        window.purchaseIndexRoutes = {
            getData: '{{ route('purchase.api.getData') }}',
            view: '/admin/purchase/:uuid',
            create: '{{ route('purchase.create') }}',
            edit: '/admin/purchase/:uuid/edit',
            destroy: '/admin/purchase/:uuid'
        };

        window.purchaseIndexConfig = {
            debug: {{ config('app.debug') ? 'true' : 'false' }},
            locale: '{{ app()->getLocale() }}',
            currency: '{{ get_option('app_currency', '$') }}'
        };
    </script>

    <!--============== Purchase Index Custom JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/purchase-index.js')}}"></script>
    <!--============== End Purchase Index Custom JS =================-->
@endpush
