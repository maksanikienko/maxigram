<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messenger</title>
<link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
<script src="{{ asset('build/assets/main.js') }}" defer></script>
    @vite(['resources/js/main.js','resources/css/app.css'])
</head>
<body>

{{-- @include('layouts.header') --}}

<div id="app">
    <chat-component
        :current_user="{{ auth()->user() }}"
:rooms="{{json_encode($roomsWithData)}}"
    ></chat-component>
</div>

</body>
</html>
