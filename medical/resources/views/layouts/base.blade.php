<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Jed Medical</title>
<!--title>{{ config('app.name', 'Laravel') }}</title-->

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css')}}" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css')}}" id="app-stylesheet" rel="stylesheet" type="text/css" />

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>

<!-- Begin page -->
<div id="wrapper">
    <!-- Topbar Start -->
    <div class="navbar-custom">
        <ul class="list-unstyled topnav-menu float-right mb-0"></ul>
        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{ route('admin') }}" class="logo logo-dark text-center">
                <h4 class="page-title-main">JED Medical</h4>
            </a>
            <a href="{{ route('admin') }}" class="logo logo-light text-center">
                <h4 class="header-title mt-0 mb-4">JED Medical</h4>
             </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left mb-0"></ul>
    </div>
    <!-- end Topbar -->

    <div class="content-page">
        <div class="content-page" style="margin-left: 0px;">

            <div class="content">

                @yield('content')

            </div> <!-- content -->

            <!-- Footer Start -->
                <footer class="footer" style="left: 0px;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                2020, JED Medical
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-right footer-links d-none d-sm-block">
                                    <a href="javascript:void(0);">About Us</a>
                                    <a href="javascript:void(0);">Help</a>
                                    <a href="javascript:void(0);">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->
        </div>
    </div>
        <!-- END wrapper -->
    <!-- Vendor js -->
        <script src="{{ asset('assets/js/vendor.min.js')}}"></script>

        <!-- knob plugin -->
        <script src="{{ asset('assets/libs/jquery-knob/jquery.knob.min.js')}}"></script>

        <!--Morris Chart-->
        <script src="{{ asset('assets/libs/morris-js/morris.min.js')}}"></script>
        <script src="{{ asset('assets/libs/raphael/raphael.min.js')}}"></script>

        <!-- Dashboard init js-->
        <script src="{{ asset('assets/js/pages/dashboard.init.js')}}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.min.js')}}"></script>
</div>
</body>
</html>
