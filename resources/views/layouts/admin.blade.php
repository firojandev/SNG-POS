<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Home</title>
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/icofont/icofont.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/simpleline/css/simple-line-icons.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/icon/icon-pe7/css/pe-icon-7-stroke.css')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap">
    <!--============== Extra Plugin =================-->
    @stack('css')
    <!--============== End Extra Plugin =================-->
    <!--======== Custom =============-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/custom-helper.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/main.css')}}">



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

<!--============== Extra Plugin ===================-->

@stack('js')

<!--============== End Plugin ===================-->

<!--============ Custom Main ================-->
<script type="text/javascript" src="{{asset('admin/js/main.js')}}"></script>
</body>
</html>
