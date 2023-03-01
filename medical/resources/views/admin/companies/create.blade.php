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
                        <h2>Add New Company</h2>
                    </div>
                </div>
            </div>
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
            <form action="{{ route('companies.store') }}" method="POST" >
                @csrf

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Title:</strong>
                            <input type="text" name="title" class="form-control" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <strong>Address:</strong>
                            <input type="text" name="address" class="form-control">
                        </div>
                        <div class="form-group">
                            <strong>Description:</strong>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <strong>Comment:</strong>
                            <textarea name="comment" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-rounded">Create</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
