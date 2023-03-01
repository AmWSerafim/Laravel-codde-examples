@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Completed reports</h2>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Scenario Name</th>
                        <th>User name</th>
                        <th>User email</th>
                        <th>Expecetd Amount</th>
                        <th>Received</th>
                        <th>Difference</th>
                        <!--th>Addition Billed</th>
                        <th>Addition Received</th>
                        <th>Addition Difference</th-->
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($mappings as $item)
                        <tr>
                            <td>{{ $item['id'] }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['user_name'] }}</td>
                            <td><a href="mailto:{{ $item['user_email'] }}">{{ $item['user_email'] }}</a></td>
                            <td>{{ $item['billed'] }}</td>
                            <td>{{ $item['received'] }}</td>
                            <td>{{ $item['difference'] }}</td>
                            <!--td>{{ $item['addition_billed'] }}</td>
                            <td>{{ $item['addition_received'] }}</td>
                            <td>{{ $item['addition_difference'] }}</td-->
                            <td>{{ $item['created_at'] }}</td>
                            <td>
                                <a href="{{ route('import-history.preview', $item['result_id']) }}" title="View">
                                    <i class="fas fa-eye text-success  fa-lg"></i>
                                </a>
                                <a href="{{ route('mapping.download', $item['result_file']) }}" title="Download Export">
                                    <i class="fas fa-save  fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
