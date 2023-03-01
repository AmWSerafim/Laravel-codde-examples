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
                        <h2>Edit User</h2>
                    </div>
                </div>
            </div>
            @if($error)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                </div>
            @endif
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
            <form action="{{ route('users.update', $company->id) }}" method="POST">
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                @method('PATCH')
                @csrf
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                </div>
                <div class="form-group">
                    <strong>Email:</strong>
                    <input type="hidden" name="old_email" class="form-control" value="{{ $user->email }}">
                    <input type="text" name="email" class="form-control" value="{{ $user->email }}">
                </div>
                <div class="form-group">
                    <label for="select_company_id" class="control-label">Company:</label>
                    <select name="company_id" id="select_company_id" class="form-control select2">
                        <option value="0"> --Please select-- </option>
                        @foreach($companies as $item)
                            @if($item->id == $company->id)
                                <option value="{{ $item->id }}" selected="selected">{{ $item->title }}</option>
                            @else
                                <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endif;
                        @endforeach;
                    </select>
                </div>
                <div class="form-group">
                    <label for="select_role_id" class="control-label">Role:</label>
                    <select name="role_id" id="select_role_id" class="form-control select2">
                        <option value=""> --Please select-- </option>
                        @foreach($roles as $item)
                            @if($item->id == $role->id)
                                <option value="{{ $item->id }}" selected="selected">{{ $item->name }}</option>
                            @else
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endif;
                        @endforeach;
                    </select>
                </div>
                <div class="form-group">
                    <strong>Password:</strong>
                    <input type="password" name="pass" class="form-control">
                </div>
                <div class="form-group">
                    <strong>Password Confirm:</strong>
                    <input type="password" name="pass_conf" class="form-control">
                </div>
                <div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-primary btn-rounded">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
