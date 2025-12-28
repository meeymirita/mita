<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
    </head>
    <body class="body">
        <div class="image">

        </div>
    </body>
    <style>
        .image {
            background-image: url('https://i.ytimg.com/vi/GeKxz5dOX6w/maxresdefault.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            width: 100%;
            height: 1000px;
        }
    </style>
</html>
