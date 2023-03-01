@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <a class="btn btn-primary btn-rounded" href="{{ route('permissions') }}" title="Back to permissions list"><i class="fas fa-backward "></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>View Permission</h2>
                    </div>
                </div>
            </div>
            <form action="{{ route('permissions.show', $permission->id) }}" method="GET">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input disabled type="text" name="name" value="{{ $permission->name }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Slug:</strong>
                            <input  disabled type="text" name="slug" value="{{ $permission->slug }}" class="form-control" placeholder="Slug">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
