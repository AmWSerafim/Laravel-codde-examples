@extends('layouts.app')

@section('content')

<div class="col-xl-12">
    <div class="card-box">
        <div class="dropdown float-right">
            <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-dots-vertical"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <!-- item-->
                <a href="{{ route('roles.create') }}" class="dropdown-item">{{ __('Create Role')  }}</a>
            </div>
        </div>

        <h4 class="header-title mt-0 mb-3">Roles</h4>

        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Created date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->slug }}</td>
                    <td>{{ date_format($role->created_at, 'jS M Y') }}</td>
                    <td>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                            <a href="{{ route('roles.show', $role->id) }}" title="show">
                                <i class="fas fa-eye text-success  fa-lg"></i>
                            </a>
                            <a href="{{ route('roles.edit', $role->id) }}">
                                <i class="fas fa-edit  fa-lg"></i>
                            </a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="delete" style="border: none; background-color:transparent;">
                                <i class="fas fa-trash fa-lg text-danger"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if(0)
        <div>
            {!! $roles->links() !!}
        </div>
        @endif
    </div>
</div><!-- end col -->

@endsection
