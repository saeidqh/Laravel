<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>IPC Management System | Users</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div
        id="app"
        data-page="user-list"
        data-username="{{ auth('web')->user()->UserName }}"
        data-logout-url="{{ route('logout') }}"
        data-csrf-token="{{ csrf_token() }}"
        data-props="{{ json_encode($pageData, JSON_THROW_ON_ERROR) }}"
    ></div>
</body>
</html>