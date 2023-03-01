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
                            <span> {{ __('Admin features') }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href=" {{ route('campaign_s') }} ">{{ __('Camaign creation static') }}</a></li>
                            <li><a href=" {{ route('campaign_f') }} ">{{ __('Campaign creation form') }}</a></li>
                        </ul>
                    </li>
@if(0)
                    <li class="menu-title">Apps</li>

                    <li>
                        <a href="apps-chat.html">
                            <i class="mdi mdi-forum"></i>
                            <span class="badge badge-purple float-right">New</span>
                            <span> Chat </span>
                        </a>
                    </li>

                    <li>
                        <a href="calendar.html">
                            <i class="mdi mdi-calendar"></i>
                            <span> Calendar </span>
                        </a>
                    </li>

                    <li>
                        <a href="inbox.html">
                            <i class="mdi mdi-email"></i>
                            <span> Mail </span>
                        </a>
                    </li>

                    <li class="menu-title">Components</li>

                    <li>
                        <a href="ui-typography.html">
                            <i class="mdi mdi-format-font"></i>
                            <span> Typography </span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-invert-colors"></i>
                            <span> User Interface </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="ui-buttons.html">Buttons</a></li>
                            <li><a href="ui-cards.html">Cards</a></li>
                            <li><a href="ui-draggable-cards.html">Draggable Cards</a></li>
                            <li><a href="ui-checkbox-radio.html">Checkboxs-Radios</a></li>
                            <li><a href="ui-material-icons.html">Material Design</a></li>
                            <li><a href="ui-font-awesome-icons.html">Font Awesome 5</a></li>
                            <li><a href="ui-dripicons.html">Dripicons</a></li>
                            <li><a href="ui-feather-icons.html">Feather Icons</a></li>
                            <li><a href="ui-themify-icons.html">Themify Icons</a></li>
                            <li><a href="ui-modals.html">Modals</a></li>
                            <li><a href="ui-notification.html">Notification</a></li>
                            <li><a href="ui-range-slider.html">Range Slider</a></li>
                            <li><a href="ui-components.html">Components</a>
                            <li><a href="ui-sweetalert.html">Sweet Alert</a>
                            <li><a href="ui-treeview.html">Tree view</a>
                            <li><a href="ui-widgets.html">Widgets</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-texture"></i>
                            <span class="badge badge-warning float-right">7</span>
                            <span> Forms </span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="form-elements.html">General Elements</a></li>
                            <li><a href="form-advanced.html">Advanced Form</a></li>
                            <li><a href="form-validation.html">Form Validation</a></li>
                            <li><a href="form-wizard.html">Form Wizard</a></li>
                            <li><a href="form-fileupload.html">Form Uploads</a></li>
                            <li><a href="form-quilljs.html">Quilljs Editor</a></li>
                            <li><a href="form-xeditable.html">X-editable</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-view-list"></i>
                            <span> Tables </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="tables-basic.html">Basic Tables</a></li>
                            <li><a href="tables-datatable.html">Data Tables</a></li>
                            <li><a href="tables-responsive.html">Responsive Table</a></li>
                            <li><a href="tables-editable.html">Editable Table</a></li>
                            <li><a href="tables-tablesaw.html">Tablesaw Table</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-chart-donut-variant"></i>
                            <span> Charts </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="charts-flot.html">Flot Charts</a></li>
                            <li><a href="charts-morris.html">Morris Charts</a></li>
                            <li><a href="charts-chartist.html">Chartist Charts</a></li>
                            <li><a href="charts-chartjs.html">Chartjs Charts</a></li>
                            <li><a href="charts-other.html">Other Charts</a></li>
                        </ul>
                    </li>


                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-file-multiple"></i>
                            <span> Pages </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="pages-starter.html">Starter Page</a></li>
                            <li><a href="pages-login.html">Login</a></li>
                            <li><a href="pages-register.html">Register</a></li>
                            <li><a href="pages-recoverpw.html">Recover Password</a></li>
                            <li><a href="pages-lock-screen.html">Lock Screen</a></li>
                            <li><a href="pages-confirm-mail.html">Confirm Mail</a></li>
                            <li><a href="pages-404.html">Error 404</a></li>
                            <li><a href="pages-500.html">Error 500</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-layers"></i>
                            <span> Extra Pages </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="extras-projects.html">Projects</a></li>
                            <li><a href="extras-tour.html">Tour</a></li>
                            <li><a href="extras-taskboard.html">Taskboard</a></li>
                            <li><a href="extras-taskdetail.html">Task Detail</a></li>
                            <li><a href="extras-profile.html">Profile</a></li>
                            <li><a href="extras-maps.html">Maps</a></li>
                            <li><a href="extras-contact.html">Contact list</a></li>
                            <li><a href="extras-pricing.html">Pricing</a></li>
                            <li><a href="extras-timeline.html">Timeline</a></li>
                            <li><a href="extras-invoice.html">Invoice</a></li>
                            <li><a href="extras-faq.html">FAQs</a></li>
                            <li><a href="extras-gallery.html">Gallery</a></li>
                            <li><a href="extras-email-templates.html">Email Templates</a></li>
                            <li><a href="extras-maintenance.html">Maintenance</a></li>
                            <li><a href="extras-comingsoon.html">Coming Soon</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-share-variant"></i>
                            <span> Multi Level </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level nav" aria-expanded="false">
                            <li>
                                <a href="javascript: void(0);">Level 1.1</a>
                            </li>
                            <li>
                                <a href="javascript: void(0);" aria-expanded="false">Level 1.2
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-third-level nav" aria-expanded="false">
                                    <li>
                                        <a href="javascript: void(0);">Level 2.1</a>
                                    </li>
                                    <li>
                                        <a href="javascript: void(0);">Level 2.2</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
@endif
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

@if(0)
<!-- Right Sidebar -->
<div class="right-bar">
    <div class="rightbar-title">
        <a href="javascript:void(0);" class="right-bar-toggle float-right">
            <i class="mdi mdi-close"></i>
        </a>
        <h4 class="font-16 m-0 text-white">Theme Customizer</h4>
    </div>
    <div class="slimscroll-menu">

        <div class="p-3">
            <div class="alert alert-warning" role="alert">
                <strong>Customize </strong> the overall color scheme, layout, etc.
            </div>
            <div class="mb-2">
                <img src="assets/images/layouts/light.png" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input theme-choice" id="light-mode-switch" checked />
                <label class="custom-control-label" for="light-mode-switch">Light Mode</label>
            </div>

            <div class="mb-2">
                <img src="assets/images/layouts/dark.png" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input theme-choice" id="dark-mode-switch" data-bsStyle="assets/css/bootstrap-dark.min.css"
                       data-appStyle="assets/css/app-dark.min.css" />
                <label class="custom-control-label" for="dark-mode-switch">Dark Mode</label>
            </div>

            <div class="mb-2">
                <img src="assets/images/layouts/rtl.png" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input theme-choice" id="rtl-mode-switch" data-appStyle="assets/css/app-rtl.min.css" />
                <label class="custom-control-label" for="rtl-mode-switch">RTL Mode</label>
            </div>

            <div class="mb-2">
                <img src="assets/images/layouts/dark-rtl.png" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="custom-control custom-switch mb-5">
                <input type="checkbox" class="custom-control-input theme-choice" id="dark-rtl-mode-switch" data-bsStyle="assets/css/bootstrap-dark.min.css"
                       data-appStyle="assets/css/app-dark-rtl.min.css" />
                <label class="custom-control-label" for="dark-rtl-mode-switch">Dark RTL Mode</label>
            </div>

            <a href="https://1.envato.market/k0YEM" class="btn btn-danger btn-block mt-3" target="_blank"><i class="mdi mdi-download mr-1"></i> Download Now</a>
        </div>
    </div> <!-- end slimscroll-menu-->
</div>
<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<a href="javascript:void(0);" class="right-bar-toggle demos-show-btn">
    <i class="mdi mdi-cog-outline mdi-spin"></i> &nbsp;Choose Demos
</a>
@endif
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

</body>
@if(0)
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
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
</body>
@endif
</html>
