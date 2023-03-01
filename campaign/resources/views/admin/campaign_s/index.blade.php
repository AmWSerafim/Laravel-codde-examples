@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <a href="{{ route('campaign_s.create') }}" class="btn btn-primary">{{ __('Send Curl')  }}</a>
        </div>
    </div><!-- end col -->

@endsection
