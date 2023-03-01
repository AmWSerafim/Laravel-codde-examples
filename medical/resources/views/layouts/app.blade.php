<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Jed Medical</title>
    <!--title>{{ config('app.name', 'Laravel') }}</title-->

    <!--link href="{{ asset('assets/css/bootstrap.min.css')}}" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css')}}" id="app-stylesheet" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" /-->


    <!-- dropify -->
    <link href="{{ asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet" />
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
        <ul class="list-unstyled topnav-menu float-right mb-0">
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="pro-user-name ml-1">
                        {{ $share_data['user_name'] ?? "" }}
                        <i class="mdi mdi-chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                     <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="dropdown-item notify-item">
                            <i class="fe-log-out mr-1"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </a>

                </div>
            </li>
        </ul>

        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{ route('admin') }}" class="logo logo-dark text-center">
                <h4 class="page-title-main">JED Medical</h4>
            </a>
            <a href="{{ route('admin') }}" class="logo logo-light text-center">
                <h4 class="header-title mt-0 mb-4">JED Medical</h4>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
            <li>
                <button class="button-menu-mobile disable-btn waves-effect">
                    <i class="fe-menu"></i>
                </button>
            </li>

            <li>
                <h4 class="page-title-main">Admin panel</h4>
            </li>

        </ul>

    </div>
    <!-- end Topbar -->
@if (Auth::check())
    <!-- ========== Left Sidebar Start ========== -->
    <div class="left-side-menu">
        <div class="slimscroll-menu">
            <!-- User box -->
            <div class="user-box text-center">
                <!--h4 class="header-title mt-0 mb-4"></h4-->
                <h4 class="header-title mt-0 mb-4">
                    @if($share_data['browsing_from'] != "")
                        Browsing from <br>
                        {{$share_data['browsing_from']}}<br>
                        <a href="{{ route('companies.switch_reset') }}">{{ __("Reset") }}</a>
                    @else
                        {{ $share_data['company_name'] ?? ""}}
                    @endif
                </h4>
            </div>

            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <ul class="metismenu" id="side-menu">
                    <li class="menu-title">Navigation</li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-page-layout-sidebar-left"></i>
                            <span> {{ __('Admin Panel') }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            @role('master-admin')
                                <li><a href=" {{ route('roles') }} ">{{ __('Roles') }}</a></li>
                            @endrole
                            @role('master-admin')
                                <li><a href=" {{ route('permissions') }} ">{{ __('Permissions') }}</a></li>
                            @endrole
                            @user_can('users-all')
                                <li><a href=" {{ route('users') }} ">{{ __('Users') }}</a></li>
                            @end_user_can
                            @role('master-admin')
                                <li><a href=" {{ route('companies') }} ">{{ __('Companies') }}</a></li>
                            @endrole
                        </ul>
                    </li>
                    @user_can('create-mapping')
                    <li>
                        <a href=" {{ route('mapping') }} ">{{ __('Create Scenario') }}</a>
                    </li>
                    @end_user_can
                    @user_can('export-all')
                    <li>
                        <a href=" {{ route('import') }} ">{{ __('Apply Scenario') }}</a>
                    </li>
                    <li>
                        <a href=" {{ route('import-history') }} ">{{ __('Scenario History') }}</a>
                    </li>
                    @end_user_can
                </ul>
            </div>
            <!-- End Sidebar -->

            <div class="clearfix"></div>

        </div>
        <!-- Sidebar -left -->

    </div>
    <!-- Left Sidebar End -->
    <div class="content-page">
@else
    <div class="content-page" style="margin-left: 0px;">
@endif

        <div class="content">

            @yield('content')

        </div> <!-- content -->

        <!-- Footer Start -->
    @if (Auth::check())
        <footer class="footer">
    @else
        <footer class="footer" style="left: 0px;">
    @endif
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
<!-- dropify js -->
<script src="{{ asset('assets/libs/dropify/dropify.min.js')}}"></script>
<script src="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
<!-- form-upload init -->
<script src="{{asset('assets/js/combined.init.js')}}"></script>
<!-- App js -->
<script src="{{ asset('assets/js/app.min.js')}}"></script>


<!--script src="{{ asset('assets/js/vendor.min.js')}}"></script>
<script src="{{ asset('assets/libs/jquery-knob/jquery.knob.min.js')}}"></script>
<script src="{{ asset('assets/libs/morris-js/morris.min.js')}}"></script>
<script src="{{ asset('assets/libs/raphael/raphael.min.js')}}"></script>
<script src="{{ asset('assets/js/pages/dashboard.init.js')}}"></script>
<script src="{{ asset('assets/js/app.min.js')}}"></script-->

</body>
</html>
