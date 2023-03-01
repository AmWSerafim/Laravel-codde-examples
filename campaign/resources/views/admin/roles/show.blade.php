@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>View Role</h2>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-primary" href="{{ route('roles') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
                    </div>
                </div>
            </div>
            <form action="{{ route('roles.show', $role->id) }}" method="GET" >
                @csrf

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input disabled type="text" name="name" class="form-control" placeholder="Name" value="{{ $role->name }}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Slug:</strong>
                            <input disabled type="text" name="slug" class="form-control" placeholder="Slug"  value="{{ $role->slug }}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <strong>Select permissions:</strong>
                        @foreach ($permissions as $permission)
                            @if(in_array($permission->id, $role_permissions))
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       disabled
                                       class="custom-control-input"
                                       id="permission_{{ $permission->id }}"
                                       name="permissions[]"
                                       checked="checked"
                                       value="{{ $permission->id }}">
                                <label class="custom-control-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
