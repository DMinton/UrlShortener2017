@extends('master')

@section('content')
    <div class="row top7">
        <div class="col-md-4 col-md-offset-8">
            <table v-if="sites" class="table-condensed table-hover well" style="width: 350px;">
                <tbody>
                    <thead>
                        <tr>
                            <th>Full URL</th>
                            <th>Visits</th>
                            <th>Last Visit</th>
                        </tr>
                    </thead>
                    <tr v-on:click="populateUrl(site)" v-for="site in sites" class="info looksClickable">
                        <td class="truncate" v-text="site.fullUrl">This is a long amount of text I am wanting to test</td>
                        <td v-text="site.visits"></td>
                        <td v-text="site.updated_at"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>        
    <div class="row top15">
        <div class="col-md-6 col-md-offset-3">
            <div class="form-group">
                <div class="input-group">
                    <input v-on:keyup.enter="submitUrl" v-model="inputUrl" type="input" class="form-control" id="urladdress" placeholder="URL Address">
                    <span class="input-group-btn">
                        <button v-on:click="clearUrl" class="btn btn-default" type="button">Clear</button>
                    </span>
                </div>
            </div>
            <button :disabled="urlIsEmptyAndNotValid" v-on:click="submitUrl" type="submit" class="btn btn-default btn-block">Shorten My Url</button>
        </div>
    </div>
    @include('url/error')
    @include('url/create')
@endsection