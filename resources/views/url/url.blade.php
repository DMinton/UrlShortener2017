@extends('master')

@section('content')
    <div class="row top30">
            <div class="col-md-4 col-md-offset-4 text-center">
                <h1>URL Shortner</h1>
            </div>
    </div>        
    <div class="row top10">
        <div class="col-md-6 col-md-offset-3">
            <div class="form-group">
                <input v-model="inputUrl" type="input" class="form-control" id="urladdress" placeholder="URL Address">
            </div>
            <button :disabled="urlIsEmptyAndNotValid" v-on:click="submitUrl" type="submit" class="btn btn-default btn-block">Submit</button>
        </div>
    </div>
    @include('url/create')
@endsection