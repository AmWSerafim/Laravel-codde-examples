@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <a class="btn btn-primary btn-rounded" href="{{ route('companies') }}" title="Back to Companies list"><i class="fas fa-backward "></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>View Company</h2>
                    </div>
                </div>
            </div>
            <div class="row" style="height:20px"></div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a href="{{ route('company.users', $company->id) }}" type="button" class="btn btn-primary btn-rounded width-md waves-effect waves-light">
                            View company users
                        </a>
                    </div>
                </div>
            </div>
            <div class="row" style="height:20px"></div>
            <form action="{{ route('companies.show', $company->id) }}" method="GET">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Title:</strong>
                            <input disabled type="text" name="name" value="{{ $company->title }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Slug:</strong>
                            <input  disabled type="text" name="slug" value="{{ $company->slug }}" class="form-control" placeholder="Slug">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Address:</strong>
                            <input disabled type="text" name="address" value="{{ $company->address }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Description:</strong>
                            <textarea disabled name="description"  class="form-control">{{ $company->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Comment:</strong>
                            <textarea disabled name="comment" class="form-control">{{ $company->comment }}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
