<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Coming soon | Jed Medical</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico')}}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css')}}" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css')}}" id="app-stylesheet" rel="stylesheet" type="text/css" />

</head>

<body class="authentication-bg">
<div class="mt-5 mb-5">
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="text-center">
                    <a href="{{ route('admin') }}" class="logo">
                        <img src="{{ asset('assets/images/logo-light.png')}}" alt="" height="22" class="logo-light mx-auto">
                        <img src="{{ asset('assets/images/logo-dark.png')}}" alt="" height="22" class="logo-dark mx-auto">
                    </a>

                    <h3 class="mt-4">Stay tunned, we're launching very soon</h3>
                    <p class="text-muted">We're making the system more awesome.</p>

                </div>
            </div>
        </div>
        <div class="row mt-5 justify-content-center">
            <div class="col-md-8 text-center">

                <a href="{{ route('login') }}" type="button" class="btn btn-primary btn-rounded width-md waves-effect waves-light">Login</a>
            </div> <!-- end col-->
        </div> <!-- end row-->

    </div>
</div>


<!-- Vendor js -->
<script src="{{ asset('assets/js/vendor.min.js')}}"></script>

<!-- Plugins js-->
<script src="{{ asset('assets/libs/jquery-countdown/jquery.countdown.min.js')}}"></script>

<!-- countdown init -->
<script src="{{ asset('assets/js/pages/countdown.init.js')}}"></script>

<!-- App js -->
<script src="{{ asset('assets/js/app.min.js')}}"></script>

</body>
</html>
