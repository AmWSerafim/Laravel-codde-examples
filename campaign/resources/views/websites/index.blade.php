@extends('layouts.app', ["page_css" => [
    asset('assets/libs/switchery/switchery.min.css'),
    asset('assets/libs/multiselect/multi-select.css'),
     ] ])
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <h4 class="mt-0 header-title">Websites list</h4>
                <p class="text-muted font-14 mb-3">

                </p>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>URL</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($websites as $key => $value)
                            <tr>
                                <th scope="row">{{ $value->id }}</th>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->slug }}</td>
                                <td>{{ $value->url }}</td>
                                <td>
                                    <a class="btn btn-small btn-info" href="{{ url('websites/' . $value->id . '/edit') }}">Edit</a>
                                    <form action="{{ route('websites.destroy', $value->id) }}" method="POST" style="    display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="delete" class="btn btn-danger waves-effect width-md waves-light">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
                <a class="btn btn-small btn-info" href="{{ url('websites/create') }}">Add new</a>
            </div>

        </div>
    </div>


@endsection
