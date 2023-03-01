@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Update Role</h2>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-primary" href="{{ route('roles') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
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
            <form action="{{ route('roles.update', $role->id) }}" method="POST" >
                @method('PATCH')
                @csrf

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" class="form-control" placeholder="Name" value="{{ $role->name }}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Slug:</strong>
                            <input type="text" name="slug" class="form-control" placeholder="Slug"  value="{{ $role->slug }}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <strong>Select permissions:</strong>
                        @foreach ($permissions as $permission)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="permission_{{ $permission->id }}"
                                       name="permissions[]"
                                       @if(in_array($permission->id, $role_permissions))
                                           checked="checked"
                                       @endif
                                       value="{{ $permission->id }}">
                                <label class="custom-control-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
