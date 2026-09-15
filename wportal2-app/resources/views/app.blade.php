<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/app.css">
    <link rel="icon" href="{{ secure_asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP|Roboto&display=swap&subset=japanese" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex,nofollow,noarchive">
    @vite('resources/script/app.tsx')
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
