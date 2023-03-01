@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-md-12">
                @if(!isset($error))
                    <div class="alert alert-success" role="alert">
                        Mapping '{{ $mapping_name }}' done successful.
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <p>{{ $error }}</p>
                    </div>
                    <p>Please proceed with new mapping creation <a href="{{ route('mapping.import') }}">process</a>.</p>
                @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <a class="btn btn-primary btn-rounded" href="{{ route('mapping.import') }}" title="Back to scenarios"><i class="fas fa-backward "></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Merge files result:</h2>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12">
                    <a href="{{ route('mapping.download', $file_path) }}" class="btn btn-primary btn-rounded">
                        Download Export file
                    </a>
                </div>
            </div>

@if(!isset($error))
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        @foreach ($total_headers as $key => $value)
                            <th>{{ $value  }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($result as $r_key => $r_value)
                        @php
                            $counter = 0;
                        @endphp
                        <tr>
                            <td>{{ $r_key }}</td>
                            @foreach($r_value as $row_key => $row_value)
                                @if($counter == ($cols_count-3))
                                    <td style="background-color: #B6DCFD;">{{ $row_value }}</td>
                                @elseif($counter == $cols_count-2)
                                    <td style="background-color: #98E5E8;">{{ $row_value }}</td>
                                @elseif($counter == $cols_count-1)
                                    @if($row_value < 0)
                                        <td style="background-color: #FD7741;">{{ $row_value }}</td>
                                    @elseif($row_value > 0)
                                        <td style="background-color: #FDF557;">{{ $row_value }}</td>
                                    @else
                                        <td>{{ $row_value }}</td>
                                    @endif
                                @else
                                    <td>{{ $row_value }}</td>
                                @endif
                                @php
                                    $counter++
                                @endphp
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row" style="height: 80px"></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        @foreach ($total_headers as $key => $value)
                            <th>{{ $value  }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($addition_result as $r_key => $r_value)
                        @php
                            $counter = 0;
                        @endphp
                        <tr>
                            <td>{{ $r_key }}</td>
                            @foreach($r_value as $row_key => $row_value)
                                @if($counter == ($cols_count-3))
                                    <td style="background-color: #B6DCFD;">{{ $row_value }}</td>
                                @elseif($counter == $cols_count-2)
                                    <td style="background-color: #98E5E8;">{{ $row_value }}</td>
                                @elseif($counter == $cols_count-1)
                                    @if($row_value < 0)
                                        <td style="background-color: #FD7741;">{{ $row_value }}</td>
                                    @elseif($row_value > 0)
                                        <td style="background-color: #FDF557;">{{ $row_value }}</td>
                                    @else
                                        <td>{{ $row_value }}</td>
                                    @endif
                                @else
                                    <td>{{ $row_value }}</td>
                                @endif
                                @php
                                    $counter++
                                @endphp
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
@endif
        </div>
    </div>
@endsection
