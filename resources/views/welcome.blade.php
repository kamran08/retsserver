<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="http://unpkg.com/view-design/dist/styles/iview.css">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/responsive.css') }}">

    <title>Duare admin Web!</title>

</head>

<body>
    <script>
        (function() {
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };
            @if(Auth::check())
            window.authUser = {!! Auth::user() !!}
            @else
            window.authUser = false
            @endif
        })();
    </script>
    <div id="app">
        <mainapp></mainapp>
    </div>


    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>