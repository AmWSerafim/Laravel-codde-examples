@extends('layouts.app', ["page_css" => [
    asset('assets/libs/switchery/switchery.min.css'),
    asset('assets/libs/multiselect/multi-select.css'),
     ] ])
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Update language</h4>
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
                <form method="POST" action="{{ route('languages.update', $language->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_name">Name*</label>
                        <div class="col-md-10">
                            <input name="name" type="text" id="geo_name" parsley-trigger="change" required="" class="form-control" value="{{$language->name}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_slug">Slug*</label>
                        <div class="col-md-10">
                            <input name="slug" type="text" id="geo_slug" parsley-trigger="change" required="" class="form-control" value="{{$language->slug}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_slug">Ad title prefix for Outbrain*</label>
                        <div class="col-md-10">
                            <input name="ad_title_prefix_outbrain" type="text" id="ad_title_prefix_outbrain" parsley-trigger="change" class="form-control" value="{{$language->ad_title_prefix_outbrain}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Geos*</label>
                        <div class="col-md-10">
                            <select multiple="multiple" class="multi-select" id="geos" name="geos[]" data-plugin="multiselect" data-parsley-required data-parsley-mincheck="1">
                                @foreach ($geos as  $geo)
                                    <option value="{{$geo->id}}" @if(in_array($geo->id, $selected_geos)) selected @endif >{{  $geo->name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="form-group text-right mb-0">
                        <button id="create" class="btn btn-primary waves-effect waves-light mr-1" type="submit">
                            Save
                        </button>

                    </div>
                </form>
            </div>

        </div>
    </div>
    <script src="/assets/libs/multiselect/jquery.multi-select.js"></script>
    <script>
        $('[data-plugin="multiselect"]').multiSelect($(this).data());
    </script>

@endsection
