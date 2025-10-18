@extends('layouts.admin')
@section('content')
    <main class="main-content">

        <div class="row">
            <div class="col-lg-6 col-xl-7">
                <div class="theme-card pos-card h-100">
                    <div class="pos-card-header">
                        <div class="row gx-2">
                            <div class="col-6 col-md-8 col-lg-9">
                                <div class="pos-input">
                                    <select class="form-control form-control-sm select2" aria-label="select">
                                        <option>Select Supplier</option>
                                        <option>Jakir Hossain</option>
                                        <option>Selim Reza</option>
                                        <option>Rakib Uddin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 text-right">
                                <a href="#" class="btn btn-sm w-100 btn-brand-secondary"><span><i class="fa fa-plus"></i> </span> Ad New</a>
                            </div>
                        </div>
                    </div>
                    <div class="pos-card-body">
                        <div class="pos-card-body-content">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle pos-vendor-table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Unit Price">U/P</th>
                                        <th>QTY</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Tax Per Unit">T/U</th>
                                        <th data-bs-toggle="tooltip" data-bs-placement="top" title="Unit Total">U/T</th>
                                        <th>-</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @for($i = 0; $i < 20; $i++)
                                    <tr>
                                        <td>5TH Generation, CoreI7 Laptop</td>
                                        <td>$12</td>
                                        <td>
                                            <div class="qty-counter-group qty-group-slim">
                                                <a href="#" class="btn btn-sm no-focus qty-decrement">-</a>
                                                <input type="number" min="1" value="1" class="form-control form-control-sm no-focus qty-count">
                                                <a href="#" class="btn btn-sm no-focus  qty-increment">+</a>
                                            </div>
                                        </td>
                                        <td>$10</td>
                                        <td>$4589</td>
                                        <td><a href="#" class="btn btn-sm btn-danger text-12 py-0 px-1"><i class="fa fa-minus"></i></a></td>
                                    </tr>
                                    @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="pos-card-footer pt-3">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div>
                                        <label for="note" class="text-light-muted text-13"><strong>Note:</strong></label>
                                        <textarea placeholder="Additional note" id="note" rows="3" class="form-control form-control-sm pos-input"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <table class="table align-middle pos-vendor-table text-muted table-borderless mb-0">
                                        <tbody>
                                        <tr>
                                            <td class="ps-0 width-60-percentage"><strong>Total Amount:</strong></td>
                                            <td class="text-right pe-0">$4353</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0"><strong>Paid Amount:</strong></td>
                                            <td class="text-right pe-0"><input type="number" name="paid_amount" class="form-control form-control-sm text-center" placeholder="Paid Amount" value="0"></td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0"><strong>Due Amount:</strong></td>
                                            <td class="text-right pe-0">$4353</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-end pt-3">
                                        <div class="me-2">
                                            <a href="{{route('purchase.index')}}" class="btn btn-sm text-13 btn-danger">Cancel</a>
                                        </div>
                                        <div class="me-2">
                                            <a href="{{route('purchase.create')}}" class="btn btn-sm text-13 btn-secondary">Clear</a>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-sm text-13 btn-brand-secondary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                </div>
            </div>
            <div class="col-lg-6 col-xl-5">
                <div class="theme-card pos-card h-100">
                    <div class="pos-card-header">
                        <div class="row gx-2">
                            <div class="col-6">
                                <div class="pos-input">
                                    <span class="pos-input-prepend text-light-muted"><i class="fa fa-search text-13"></i></span>
                                    <input type="text" placeholder="Search by Name, SKU" class="form-control form-control-sm" aria-label="input">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="pos-input">
                                    <select class="form-control form-control-sm select2" aria-label="select">
                                        <option>All</option>
                                        <option>Laptop</option>
                                        <option>Desktop</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pos-card-body">
                        <div class="pos-card-body-content">
                            <div class="row g-2">
                                @for($p=0; $p < 30; $p++)
                                <div class="col-sm-6">
                                    <div class="wiz-card pos-product-item @if($p==1 || $p==4 || $p==10) selected @endif">
                                        <div class="me-1">
                                            <div class="pos-product-fig">
                                                <img src="{{asset('admin/images/product/3.png')}}">
                                            </div>
                                        </div>
                                        <div class="pos-product-content">
                                            <table class="pos-product-table">
                                                <tbody>
                                                <tr>
                                                    <td colspan="3">5TH Generation, CoreI7 Laptop</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Price</strong></td>
                                                    <td>:</td>
                                                    <td>$12</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>SKU</strong></td>
                                                    <td>:</td>
                                                    <td>P20251020{{$p}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>

                            <div class="text-center py-3">
                                <a href="#" class="btn btn-sm btn-brand-secondary"><span><i class="fa fa-spinner fa-spin"></i></span> Load More</a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>







    </main>
@endsection

@push('css')
    <!--============== Extra Plugin =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/select2/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/select2/select2-bootstrap/select2-bootstrap-5-theme.css')}}">
    <!--============== End Extra Plugin =================-->
@endpush

@push('js')
    <!--==== Chart Js ========-->
    <script type="text/javascript" src="{{asset('admin/plugin/select2/select2.min.js')}}"></script>
    <script>
        $(".select2").select2({
            theme: "bootstrap-5",
            containerCssClass: "select2--small", // For Select2 v4.0
            selectionCssClass: "select2--small", // For Select2 v4.1
            dropdownCssClass: "select2--small",
        });

        $('.pos-card-body-content').each(function () {
            var initHeight = 0;
            $(this).children().each(function () {
                initHeight = parseFloat(initHeight + $(this).outerHeight());
            });

            if ($(this).outerHeight() < initHeight)
            {
                $(this).css({
                    'padding-right': 4+'px'
                });
            } else {
                $(this).css({
                    'padding-right': 0
                });
            }
        });


        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });


    </script>

    <!--============== End Plugin ===================-->
@endpush


