@section('createUrl')
    <div class="row top30">
        <div class="col-md-4 col-md-offset-4 text-center">
            <div id="urlDisplay" class="hide">
                <h3>Shortened URL</h3>
                <a v-bind:href="displayUrl" v-text="displayUrl"></a>
            </div>
        </div>
    </div>
@endsection