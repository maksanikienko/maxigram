<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, interactive-widget=resizes-content">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maxigram — Messenger</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/js/main.js', 'resources/css/app.css'])
</head>
<body class="overflow-hidden">

<div id="app">
    <chat-component
        :current_user="{{ auth()->user() }}"
        :rooms="{{ json_encode($roomsWithData) }}"
        profile-url="{{ route('profile.edit') }}"
    ></chat-component>
</div>

<script>
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}',
        logoutUrl: '{{ route('logout') }}',
        profileUrl: '{{ route('profile.edit') }}'
    };
</script>
</body>
</html>
