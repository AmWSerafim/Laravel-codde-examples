@extends('layouts.app', ["page_css" => [
    asset('assets/libs/switchery/switchery.min.css'),
     ] ])
<link href="/assets/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css">
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">New Website</h4>
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
                <form method="POST" action="{{ url('websites') }}">
                    @csrf
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
                        <label class="col-md-2 col-form-label" for="geo_slug">URL*</label>
                        <div class="col-md-10">
                            <input name="url" type="text" id="website_url" parsley-trigger="change" required="" class="form-control" value="">
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

@endsection
