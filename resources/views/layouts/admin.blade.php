<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{@$title}} :: SNG POS</title>
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/icofont/icofont.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/simpleline/css/simple-line-icons.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/icon-pe7/css/pe-icon-7-stroke.css')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap">
    <!--============== Global Libraries CSS =================-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/toastr.min.css')}}">
    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/select2/select2.min.css')}}">
    <!--============== Extra Plugin =================-->
    @stack('css')
    @stack('styles')
    <!--============== End Extra Plugin =================-->
    <!--======== Custom =============-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/custom-helper.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/modal-select2-fix.css')}}">

</head>
<body>
<!--========== Navbar ==========-->
@include('admin.Common.header')

<div class="panel-wrapper">

    <!--======= Aside =========-->
    @include('admin.Common.aside')

    <div class="wrapping-content" id="wrappingBody">
        @yield('content')

        @include('admin.Common.footer')
    </div>
</div>

<script type="text/javascript" src="{{asset('admin/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/popper.min.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/js/bootstrap.min.js')}}"></script>

<!--============== Global Libraries ===================-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="{{asset('admin/js/toastr.min.js')}}"></script>
<!-- Select2 JS -->
<script type="text/javascript" src="{{asset('admin/plugin/select2/select2.min.js')}}"></script>
<!-- Select2 Global Initialization -->
<script type="text/javascript" src="{{asset('admin/partial/js/select2-init.js')}}"></script>
<script type="text/javascript" src="{{asset('admin/partial/js/global.js')}}"></script>

<!--============== Extra Plugin ===================-->

@stack('js')
@stack('scripts')

<!--============== End Plugin ===================-->

<!--============ Custom Main ================-->
<script type="text/javascript" src="{{asset('admin/js/main.js')}}"></script>
</body>
</html>
