<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Campaign Tool</title>
    <!--title>{{ config('app.name', 'Laravel') }}</title-->

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css')}}" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
    @if (isset($page_css) && count($page_css) >= 1 )
        @foreach ($page_css as $css)
            <link href="{{ $css }}" rel="stylesheet">
        @endforeach
    @endif
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css')}}" id="app-stylesheet" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js')}}"></script>
    <!--script src="https://code.jquery.com/jquery-3.3.1.min.js"></script-->

</head>
<body>

<!-- Begin page -->
<div id="wrapper">
    <!-- Topbar Start -->
    <div class="navbar-custom">
        <ul class="list-unstyled topnav-menu float-right mb-0">

            <li class="d-none d-sm-block">
                <form class="app-search">
                    <div class="app-search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search...">
                            <div class="input-group-append">
                                <button class="btn" type="submit">
                                    <i class="fe-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </li>



            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('assets/images/users/user-1.jpg')}}" alt="user-image" class="rounded-circle">
                    <span class="pro-user-name ml-1">
                                Nowak <i class="mdi mdi-chevron-down"></i>
                            </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-settings"></i>
                        <span>Settings</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-lock"></i>
                        <span>Lock Screen</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-log-out"></i>
                        <span>Logout</span>
                    </a>

                </div>
            </li>



        </ul>

        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{ route('admin') }}" class="logo logo-dark text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-dark.png')}}" alt="" height="16">
                        </span>
                <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png')}}" alt="" height="24">
                        </span>
            </a>
            <a href="{{ route('admin') }}" class="logo logo-light text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-light.png')}}" alt="" height="16">
                        </span>
                <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png')}}" alt="" height="24">
                        </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
            <li>
                <button class="button-menu-mobile disable-btn waves-effect">
                    <i class="fe-menu"></i>
                </button>
            </li>

            <li>
                <h4 class="page-title-main">Dashboard</h4>
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
                <img src="{{ asset('assets/images/users/user-1.jpg') }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail avatar-md">
                <div class="dropdown">
                    <a href="#" class="user-name dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown"  aria-expanded="false">User menu</a>
                    <div class="dropdown-menu user-pro-dropdown">
                        <!-- item-->
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="dropdown-item notify-item">
                            <i class="fe-log-out mr-1"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <ul class="metismenu" id="side-menu">
                    <li class="menu-title">Navigation</li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-page-layout-sidebar-left"></i>
                            <span> {{ __('Campaign') }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href=" {{ route('campaign_f') }} ">{{ __('Generate campaigns') }}</a></li>
                        </ul>
                    </li>
                    @role('admin')
                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-page-layout-sidebar-left"></i>
                            <span> {{ __('Settings') }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href=" {{ route('geos.index') }} ">{{ __('Geos') }}</a></li>
                            <li><a href=" {{ route('languages.index') }} ">{{ __('Languages') }}</a></li>
                            <li><a href=" {{ route('websites.index') }} ">{{ __('Websites') }}</a></li>
                            <li><a href=" {{ route('accounts.index') }} ">{{ __('Accounts') }}</a></li>
                            <li><a href=" {{ route('options.index') }} ">{{ __('Options') }}</a></li>
                        </ul>
                    </li>
                    @endrole
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
                        2016 - 2020 &copy; Adminto theme by <a href="">Coderthemes</a>
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
    <script src="assets/libs/multiselect/jquery.multi-select.js"></script>
<!--Morris Chart-->
{{--<script src="{{ asset('assets/libs/morris-js/morris.min.js')}}"></script>--}}
{{--<script src="{{ asset('assets/libs/raphael/raphael.min.js')}}"></script>--}}
        <script src="http://parsleyjs.org/dist/parsley.js"></script>
<!-- Dashboard init js-->
{{--<script src="{{ asset('assets/js/pages/dashboard.init.js')}}"></script>--}}

<!-- App js -->
<script src="{{ asset('assets/js/app.min.js')}}"></script>

</body>

</html>
