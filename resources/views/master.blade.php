<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Make a long URL short">
        <meta name="keywords" content="URL,Shortener">

        <title>URL Shortener</title>

        <!-- Fonts -->
        <link href="/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="/css/main.css" rel="stylesheet" type="text/css">

        <!-- Javascript -->
        <script src="js/app.js"></script>

    </head>
    <body>
        <div id="urlform" class="container">
            @yield('content')
            @yield('error')
            @yield('createUrl')
        </div>
    </body>
    <script src="js/url.js"></script>
</html>
