@extends('master')

@section('content')     
    <div class="row top30">
        <div class="col-md-6 col-md-offset-3 text-center">
            <div class="alert alert-warning">
                <h1>STOP!</h1>
                <p>This may not be a valid URL. Continue at your own risk!</p>
            </div>
        </div>
    </div>
    <div class="row top15">
        <div class="col-md-4 col-md-offset-4 text-center">
            <h3>Full URL</h3>
            <a href="{{ $url->fullUrl }}" >{{ $url->fullUrl }}</a>
        </div>
    </div>
@endsection