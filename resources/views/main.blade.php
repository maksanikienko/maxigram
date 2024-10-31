<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messenger</title>
    @vite(['resources/js/main.js','resources/css/app.css'])
</head>
<body>

@include('layouts.header')

<div id="app">
    <chat-component
{{--        :friend = "{{json_encode($friend)}}"--}}
        :friends = "{{json_encode($friends)}}"
        :current_user="{{ auth()->user() }}"
:rooms="{{json_encode($roomsWithData)}}"
    ></chat-component>
</div>

{{--<script>--}}
{{--    // JavaScript for showing/hiding the menu--}}
{{--    const menuButton = document.getElementById('menuButton');--}}
{{--    const menuDropdown = document.getElementById('menuDropdown');--}}

{{--    menuButton.addEventListener('click', () => {--}}
{{--        if (menuDropdown.classList.contains('hidden')) {--}}
{{--            menuDropdown.classList.remove('hidden');--}}
{{--        } else {--}}
{{--            menuDropdown.classList.add('hidden');--}}
{{--        }--}}
{{--    });--}}

{{--    // Close the menu if you click outside of it--}}
{{--    document.addEventListener('click', (e) => {--}}
{{--        if (!menuDropdown.contains(e.target) && !menuButton.contains(e.target)) {--}}
{{--            menuDropdown.classList.add('hidden');--}}
{{--        }--}}
{{--    });--}}
{{--</script>--}}

</body>
</html>
