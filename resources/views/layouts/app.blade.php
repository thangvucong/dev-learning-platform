<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Learning Platform')</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body>
    @include('components.navbar')
    @include('components.auth.auth-modal')
    <div class="flex flex-1 pt-[66px]">
        @include('components.sidebar')
        @yield('content')
    </div>
    @include('components.footer')
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>

</html>
