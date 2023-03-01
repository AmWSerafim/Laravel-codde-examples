@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card-box">
                @if(!$allow_mapping)
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="alert alert-warning">
                                <h2>Warning</h2>
                                <p> You can't create any scenarios while you not <a href="{{ route('companies') }}">select company</a>. Just delete action is allowed</p>
                            </div>
                        </div>
                    </div>
                @else
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a href="{{ route('mapping.import') }}" type="button" class="btn btn-primary btn-rounded width-md waves-effect waves-light">
                                Create Scenario
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <h2>Last Saved Mappings</h2>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($mappings as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ date_format($item->created_at, 'M jS Y') }}</td>
                                <td>
                                    <form action="{{ route('mapping.destroy', $item->id) }}" method="POST">
                                        <!--a href="{{ route('mapping.reimport', $item->id) }}" title="Reimport">
                                            <i class="fas fa-eye text-success  fa-lg"></i>
                                        </a-->
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete" style="border: none; background-color:transparent;" class="delete_mapping">
                                            <i class="fas fa-trash fa-lg text-danger"></i>  <span class="header-title">Delete</span>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(".delete_mapping").click(function(){
           if(confirm("Are you sure want delete this mapping? This action cannot be undone.")){
               //console.log('true');
               return true;
           } else {
               //console.log('false');
               return false;
           }
        });
    </script>
@endsection
