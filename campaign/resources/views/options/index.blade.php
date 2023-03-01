@extends('layouts.app', ["page_css" => [
    asset('assets/libs/switchery/switchery.min.css'),
    asset('assets/libs/multiselect/multi-select.css'),
     ] ])
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Options</h4>
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
                <form method="POST" action="{{ route('options.update', 1) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_name">Tracking Taboola</label>
                        <div class="col-md-10">
                            <textarea name="tracking_taboola" class="form-control">{{ $options->tracking_taboola ?? "" }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="geo_name">Tracking Outbrain</label>
                        <div class="col-md-10">
                            <textarea name="tracking_outbrain" class="form-control">{{ $options->tracking_outbrain ?? "" }}</textarea>
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
@endsection
