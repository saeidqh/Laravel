<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>IPC Management System | Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div
        id="app"
        data-login-url="{{ route('login.store') }}"
        data-csrf-token="{{ csrf_token() }}"
        data-username="{{ old('username', '') }}"
        data-error="{{ $errors->first() }}"
    ></div>
</body>
</html>