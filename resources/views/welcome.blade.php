<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .title {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .subtitle {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .buttons a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 25px;
            text-decoration: none;
            color: white;
        }
        .login-button {
            background-color: #4CAF50;
        }
        .register-button {
            background-color: #f44336;
        }
        .image {
            border-radius: 50%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('storage/') }}" alt="Profile Image" class="image" width="150" height="150">
        <div class="title">SELAMAT DATANG DI HALAMAN WEB</div>
        <div class="subtitle">TUGAS UTS PEMROGRAMAN BERBASI FRAMEWORK</div>
        <br>
        <br>
        <br>
        <div class="buttons">
            <a href="{{ route('login') }}" class="login-button">LOGIN</a>
            <a href="{{ route('register') }}" class="register-button">REGISTER</a>
        </div>
        <br>
        <br>
        <br>
        <div class="subtitle">DUMA ZINDY ARITONANG</div>
    </div>
</body>
</html>
