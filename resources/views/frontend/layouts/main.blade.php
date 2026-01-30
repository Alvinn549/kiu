<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ asset('theme/frontend/assets/bootstrap-5.2.3/css/bootstrap.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('theme/frontend/assets/extensions/@fortawesome/fontawesome-free/css/all.min.css') }}">

    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])

    @yield('css')
</head>

<body>

    @include('sweetalert::alert')

    @yield('content')

    <script src="{{ asset('theme/frontend/assets/extensions/jquery/jquery.min.js') }}"></script>
    <link href="{{ asset('theme/frontend/assets/extensions/@fortawesome/fontawesome-free/js/all.min.js') }}">
    <script src="{{ asset('theme/frontend/assets/bootstrap-5.2.3/js/bootstrap.bundle.min.js') }}"></script>

    @yield('js')
</body>

</html>
