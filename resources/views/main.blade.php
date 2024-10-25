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

<div id="app">
    <chat-component
        :user = "{{json_encode($user)}}"
        :sent-messages="{{ json_encode($sentMessages) }}"
        :received-messages="{{ json_encode($receivedMessages) }}"
        :all_users = "{{json_encode($allUsers)}}"
    ></chat-component>
</div>
</body>
</html>
