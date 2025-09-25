@extends('layouts.admin')
@section('content')
    <main class="main-content">

    <!--========== Wiz Card ==============-->
    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="ratio ratio-21x9">
                <div class="wiz-card p-3 d-flex justify-content-center flex-column h-100">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="icon-box box-40 rounded-circle">
                                <i class="fa fa-user-o text-muted"></i>
                            </div>
                        </div>
                        <div>
                            <span class="d-block text-center font-weight-strong text-muted m-0">1543 K</span>
                            <small class="d-block text-center text-muted">Customer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="ratio ratio-21x9">
                <div class="wiz-card p-3 d-flex justify-content-center flex-column h-100">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="icon-box box-40 rounded-circle">
                                <i class="fa fa-shopping-basket text-muted"></i>
                            </div>
                        </div>
                        <div>
                            <span class="d-block text-center font-weight-strong text-muted m-0">1543 K</span>
                            <small class="d-block text-center text-muted">Orders</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="ratio ratio-21x9">
                <div class="wiz-card p-3 d-flex justify-content-center flex-column h-100">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="icon-box box-40 rounded-circle">
                                <i class="fa fa-line-chart text-muted"></i>
                            </div>
                        </div>
                        <div>
                            <span class="d-block text-center font-weight-strong text-muted m-0">1543 K</span>
                            <small class="d-block text-center text-muted">Sales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="ratio ratio-21x9">
                <div class="wiz-card p-3 d-flex justify-content-center flex-column h-100">
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="icon-box box-40 rounded-circle">
                                <i class="fa fa-line-chart text-muted"></i>
                            </div>
                        </div>
                        <div>
                            <span class="d-block text-center font-weight-strong text-muted m-0">1543 K</span>
                            <small class="d-block text-center text-muted">Sales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!--=========== Chart =============-->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="theme-card px-0 h-100">
                <h5 class="text-muted text-center px-3">Sales Overview</h5>
                <small class="d-block text-center text-muted">July 2021</small>
                <div class="ratio ratio-16x9">
                    <div id="reviewChart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="theme-card h-100">
                <h5 class="text-muted mb-4">Total Sales</h5>
                <div id="chartRadial" class="legend-vertical"></div>
            </div>
        </div>
    </div>







</main>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/appexchart/dist/apexcharts.css')}}">
@endpush

@push('js')
    <!--==== ChartJs ========-->
    <script type="text/javascript" src="{{asset('admin/plugin/appexchart/dist/apexcharts.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/partial/js/dashboard.js')}}"></script>
@endpush
