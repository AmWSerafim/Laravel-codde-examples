@extends('layouts.app')

@section('content')

    <div class="col-xl-12">
        <div class="card-box">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Make import with new mapping</h2>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-primary" href="{{ route('excel') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('excel.import') }}" id="upload-files-form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-xl-6">
                        <label for="payment-file">Payment details file</label>
                        <input type="file" name="payment-file" class="form-control" id="payment-file">
                    </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>

        </div>
    </div>

@endsection
