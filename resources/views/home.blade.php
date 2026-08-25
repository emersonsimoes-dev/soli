<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Soli · Boletim Mensal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0b1d36;
            --blue: #1e40af;
            --bg: #f3f4f6;
            --muted: #64748b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, sans-serif;
            background: var(--bg);
            color: var(--navy);
            display: grid;
            place-items: center;
            padding: 24px;
        }
        main {
            width: min(560px, 100%);
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }
        p.label {
            margin: 0 0 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--blue);
        }
        h1 { margin: 0 0 8px; font-size: 28px; }
        p { margin: 0 0 12px; color: var(--muted); line-height: 1.5; }
        a { color: var(--blue); font-weight: 600; }
        .meta { font-size: 13px; }
    </style>
</head>
<body>
    <main>
        <p class="label">Soli</p>
        <h1>Soli no ar</h1>
        <p>Projeto Soli para igrejas. O boletim mensal entra na Fase 1; o painel administrativo, na Fase 2.</p>
        <p class="meta">Timezone: {{ config('app.timezone') }} · {{ now()->format('d/m/Y H:i') }}</p>
        <p class="meta"><a href="/up">Healthcheck /up</a></p>
    </main>
</body>
</html>
