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
                        Balance Sheet
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Date Filter Section -->
        <div class="theme-card mb-4">
            <div class="theme-card-header">
                <h6 class="theme-card-title">Balance Sheet As Of Date</h6>
            </div>
            <div class="theme-card-body">
                <form action="{{ route('balance-sheet.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="as_of_date" class="form-label">As of Date</label>
                        <input type="text" class="form-control form-control-sm" id="as_of_date" name="as_of_date" value="{{ $asOfDate }}" placeholder="{{ get_option('date_format', 'Y-m-d') }}" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('balance-sheet.export-csv', ['as_of_date' => $asOfDate]) }}" class="btn btn-sm btn-success w-100">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-secondary w-100" onclick="window.location.href='{{ route('balance-sheet.index') }}'">
                            <i class="fa fa-refresh"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Balance Sheet Display -->
        <div class="row">
            <!-- Assets Column -->
            <div class="col-md-6 mb-4">
                <div class="theme-card h-100">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title text-primary">Incomes</h6>
                    </div>
                    <div class="theme-card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $asset)
                                <tr>
                                    <td>
                                        <div>{{ $asset['name'] }}</div>
                                        @if(isset($asset['note']))
                                            <small class="text-muted">{{ $asset['note'] }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($asset['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary fw-bold">
                                    <td>Total=></td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($totalAssets, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Liabilities Column -->
            <div class="col-md-6 mb-4">
                <div class="theme-card h-100">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title text-danger">Liabilities</h6>
                    </div>
                    <div class="theme-card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($liabilities as $liability)
                                <tr>
                                    <td>
                                        <div>{{ $liability['name'] }}</div>
                                        @if(isset($liability['note']))
                                            <small class="text-muted">{{ $liability['note'] }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($liability['amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-danger fw-bold">
                                    <td>Total=></td>
                                    <td class="text-end">{{ get_option('app_currency') }}{{ number_format($totalLiabilities, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Balance Breakdown -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h6 class="theme-card-title">Bank Balance Breakdown</h6>
                    </div>
                    <div class="theme-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success mb-3">Inflows (+)</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>Payments Received from Customers</td>
                                        <td class="text-end text-success fw-bold">{{ get_option('app_currency') }}{{ number_format($bankBalanceBreakdown['payments_received'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Other Income</td>
                                        <td class="text-end text-success fw-bold">{{ get_option('app_currency') }}{{ number_format($bankBalanceBreakdown['other_income'], 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Total Inflows</strong></td>
                                        <td class="text-end text-success fw-bold">{{ get_option('app_currency') }}{{ number_format($bankBalanceBreakdown['payments_received'] + $bankBalanceBreakdown['other_income'], 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-danger mb-3">Outflows (-)</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>Payments Made to Suppliers</td>
                                        <td class="text-end text-danger fw-bold">{{ get_option('app_currency') }}{{ number_format($bankBalanceBreakdown['payments_made'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Expenses</td>
                                        <td class="text-end text-danger fw-bold">{{ get_option('app_currency') }}{{ number_format($bankBalanceBreakdown['expenses'], 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Total Outflows</strong></td>
                                        <td class="text-end text-danger fw-bold">{{ get_option('app_currency') }}{{ number_format($bankBalanceBreakdown['payments_made'] + $bankBalanceBreakdown['expenses'], 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Calculation:</strong> Bank Balance = (Payments Received + Other Income) - (Payments Made + Expenses) =
                                    <strong>{{ get_option('app_currency') }}{{ number_format($bankBalanceBreakdown['total'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Assets Summary -->
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-body text-center">
                        <h5 class="mb-3">(Income - Liabilities)</h5>
                        <h3 class="text-success">
                            Total Asset: {{ get_option('app_currency') }}{{ number_format($netAssets, 2) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <!--============== jQuery UI Datepicker CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/jquery-ui/jquery-ui.min.css')}}">
    <!--============== End jQuery UI Datepicker CSS =================-->
@endpush

@push('js')
    <!--============== jQuery UI Datepicker JS =================-->
    <script type="text/javascript" src="{{asset('admin/plugin/jquery-ui/jquery-ui.js')}}"></script>
    <!--============== End jQuery UI Datepicker JS =================-->

    <script>
        "use strict";

        /**
         * Balance Sheet Configuration
         */
        window.balanceSheetConfig = {
            currency: '{{ get_option('app_currency', '$') }}',
            dateFormatPhp: '{{ get_option('date_format', 'Y-m-d') }}'
        };
    </script>

    <!--============== Balance Sheet JS =================-->
    <script type="text/javascript" src="{{asset('admin/partial/js/balance-sheet.js')}}"></script>
    <!--============== End Balance Sheet JS =================-->
@endpush
