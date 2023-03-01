@extends('layouts.app', ["page_css" => [
    asset('assets/libs/switchery/switchery.min.css'),
     ] ])
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">New Account</h4>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ url('accounts') }}">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Platform*</label>
                        <div class="col-md-10">
                            <select class="form-control" id="platform" name="platform" data-parsley-required>
                                <option value="taboola" selected="selected">Taboola</option>
                                <option value="outbrain">Outbrain</option>
                            </select>
                        </div>
                    </div>
                    <div id="accounts_select" class="form-group row">
                        @include('accounts.accountsSelect', ['accounts' => $accounts, "platform" => "taboola", "selected" => ""])

                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_name">Name*</label>
                        <div class="col-md-10">
                            <input name="name" type="text" id="website_name" parsley-trigger="change" required="" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_slug">Slug*</label>
                        <div class="col-md-10">
                            <input name="slug" type="text" id="website_slug" parsley-trigger="change" required="" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Website*</label>
                        <div class="col-md-10">
                            <select class="form-control" id="website" name="website_id" data-parsley-required>
                                <option value="">{{ __('Please select') }}</option>
                                @foreach ($websites as  $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group text-right mb-0">
                        <button id="create" class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                            Create
                        </button>

                    </div>
                </form>
            </div>

        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#platform').on('change', function () {
                $("#account").prop('disabled', 'disabled');
                $.ajax({
                    type: "GET",
                    url: '/accounts/generateAccountsSelectAjax?platform=' + this.value,
                    success: function (msg) {
                        $("#accounts_select").html(msg);
                        $("#account").prop('disabled', false);
                    }
                });

            });

            $("body").on("change", ".account_selector", function () {
                //console.log($( "#account option:selected" ).text());
                $("#website_name").val($( "#account option:selected" ).text());
            });

            function validate() {
                var instance = $('#campaignForm').parsley();
                return instance.validate();

            }
        });
    </script>
@endsection
