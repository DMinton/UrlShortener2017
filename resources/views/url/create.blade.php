@section('createUrl')
    <div v-if="displayUrl" class="row top30">
        <div class="col-md-4 col-md-offset-4 text-center">
            <h3>Shortened URL</h3>
            <a v-bind:href="displayUrl" v-text="displayUrl" target="_blank"></a>
        </div>
    </div>
@endsection