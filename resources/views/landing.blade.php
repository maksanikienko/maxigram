<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maxigram — Messenger for everyone</title>
    <meta name="description" content="Fast, secure and beautiful messenger. Connect with friends and groups in real-time.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/js/landing.js', 'resources/css/app.css'])
</head>
<body class="bg-background text-foreground">
<div id="landing-app">
    <landing-page
        login-url="{{ route('login') }}"
        register-url="{{ route('register') }}"
    ></landing-page>
</div>
</body>
</html>
