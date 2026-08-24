<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('soli.name').' · Boletim Mensal')</title>
    <link rel="icon" href="{{ asset(config('soli.favicon')) }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset(config('soli.default_logo')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bulletin.css') }}">
</head>
<body>
    @yield('content')

    <footer class="soli-footer" aria-label="{{ config('soli.name') }}">
        <img src="{{ asset(config('soli.default_logo')) }}" width="44" height="44" alt="{{ config('soli.name') }}">
        <div>
            <strong>{{ strtoupper(config('soli.name')) }}</strong>
            <span>{{ config('soli.tagline') }}</span>
        </div>
    </footer>

    <script src="{{ asset('js/bulletin.js') }}"></script>
</body>
</html>
