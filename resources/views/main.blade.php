<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messenger</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    @vite(['resources/js/main.js','resources/css/app.css'])
</head>
<body>
{{--<button id="myButton">Click Me</button>--}}
{{-- @include('layouts.header') --}}

<div id="app">
    <chat-component
        :current_user="{{ auth()->user() }}"
:rooms="{{json_encode($roomsWithData)}}"
        profile-url="{{ $profileUrl }}"
    ></chat-component>
</div>

</body>
</html>


<script>
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}',
        logoutUrl: '{{ route('logout') }}',
        profileUrl: '{{ route('profile.edit') }}'
    };
    // /* global $ */
    // $(document).ready(function() {
    //     $('#myButton').on('click', function() {
    //
    //         $.ajax({
    //             url: 'https://randomuser.me/api/',
    //             dataType: 'json',
    //             success: function(data) {
    //                 console.log(data);
    //             }
    //         });
    //
    //     });
    // });

</script>
