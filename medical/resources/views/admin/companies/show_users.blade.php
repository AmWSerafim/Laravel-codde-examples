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
                <a href="{{ route('users.create', $company_id) }}" class="dropdown-item">{{ __('Create User')  }}</a>
            </div>
        </div>

        <h4 class="header-title mt-0 mb-3">Company Users</h4>

        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ date_format($user->created_at, 'M jS Y') }}</td>
                    <td>
                        <form action="{{ route('users.destroy', ['id'=> $user->id, 'company_id' => $company_id]) }}" method="POST">
                            <a href="{{ route('users.edit', $user->id) }}" title="Edit">
                                <i class="fas fa-edit  fa-lg"></i>
                            </a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" style="border: none; background-color:transparent;" class="confirm_delete">
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
            {!! $users->links() !!}
        </div>
        @endif
    </div>
</div><!-- end col -->
<script type="text/javascript">
    jQuery(".confirm_delete").click(function(){
        if(confirm("Are you sure want delete this user? This action cannot be undone.")){
            //console.log('true');
            return true;
        } else {
            //console.log('false');
            return false;
        }
    });
</script>
@endsection
