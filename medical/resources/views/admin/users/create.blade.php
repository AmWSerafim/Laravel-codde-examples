@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <a class="btn btn-primary btn-rounded" href="{{ route('users') }}" title="Back to users list"><i class="fas fa-backward "></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Add New User</h2>
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
            <form action="{{ route('users.store') }}" method="POST" >
                @csrf

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" class="form-control" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <strong>Email:</strong>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="select_company_id" class="control-label">Company:</label>
                            <select name="company_id" id="select_company_id" class="form-control select2">
                                <option value="0"> --Please select-- </option>
                                @foreach($companies as $company)
                                    @if($company->id == $company_id)
                                    <option value="{{ $company->id }}" selected="selected">{{ $company->title }}</option>
                                    @else
                                    <option value="{{ $company->id }}">{{ $company->title }}</option>
                                    @endif;
                                @endforeach;
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select_role_id" class="control-label">Role:</label>
                            <select name="role_id" id="select_role_id" class="form-control select2">
                                <option value=""> --Please select-- </option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach;
                            </select>
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
