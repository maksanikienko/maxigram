<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, interactive-widget=resizes-content">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maxigram</title>

    <!-- PWA — required for iOS Web Push -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Maxigram">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#6366f1">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

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
        profileUrl: '{{ route('profile.edit') }}',
        vapidPublicKey: '{{ config('webpush.vapid.public_key') }}'
    };
</script>
</body>
</html>
