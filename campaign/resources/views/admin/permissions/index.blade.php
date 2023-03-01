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
                    <a href="{{ route('permissions.create') }}" class="dropdown-item">{{ __('Create Permission')  }}</a>
                </div>
            </div>

            <h4 class="header-title mt-0 mb-3">Permissions</h4>

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
                    @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->slug }}</td>
                            <td>{{ date_format($permission->created_at, 'jS M Y') }}</td>
                            <td>
                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST">
                                    <a href="{{ route('permissions.show', $permission->id) }}" title="show">
                                        <i class="fas fa-eye text-success  fa-lg"></i>
                                    </a>
                                    <a href="{{ route('permissions.edit', $permission->id) }}">
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
                {!! $permissions->links() !!}
            </div>
            @endif
        </div>
    </div><!-- end col -->

@endsection
