@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card-box">
                <h2>Data Mapping Scenarios</h2>
                @if(!$allow_mapping)
                <div class="row">
                    <div class="col-xl-12">
                        <div class="alert alert-warning">
                            <h2>Warning</h2>
                            <p> You can't do any imports while you not <a href="{{ route('companies') }}">select company</a>. No actions allowed</p>
                        </div>
                    </div>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Created on</th>
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
                                    <form action="{{ route('import.destroy', $item->id) }}" method="POST">
                                        @if($allow_mapping)
                                        <a href="{{ route('import.do_export', $item->id) }}" title="Make export">
                                            <i class="fas fa-eye text-success  fa-lg"></i> <span class="header-title">View</span>
                                        </a>
                                        @endif
                                        @csrf
                                        @method('DELETE')
                                        <!--button type="submit" title="Delete" style="border: none; background-color:transparent;">
                                            <i class="fas fa-trash fa-lg text-danger"></i>
                                        </button-->
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

@endsection
