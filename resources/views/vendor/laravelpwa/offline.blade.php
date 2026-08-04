<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ $themeColor ?? '#148A3C' }}">
    <title>{{ $appName ?? 'Cost to Cost Foods' }} — Offline</title>
    <style>
        :root { color-scheme: light dark; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: linear-gradient(160deg, #f4faf6 0%, #ffffff 45%, #eef6f0 100%);
            color: #0a3d28;
            padding: 1.5rem;
        }
        .card {
            width: min(100%, 420px);
            background: #fff;
            border: 1px solid #e5eee8;
            border-radius: 1.25rem;
            padding: 1.75rem;
            text-align: center;
            box-shadow: 0 18px 40px rgba(10, 61, 40, 0.08);
        }
        img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 1rem;
            margin-bottom: 1rem;
            background: #f7faf8;
        }
        h1 { font-size: 1.35rem; margin: 0 0 .5rem; }
        p { margin: 0 0 1.25rem; color: #6E7191; line-height: 1.5; }
        button {
            appearance: none;
            border: 0;
            border-radius: .75rem;
            background: {{ $themeColor ?? '#148A3C' }};
            color: #fff;
            font-weight: 600;
            padding: .85rem 1.25rem;
            cursor: pointer;
            width: 100%;
        }
    </style>
</head>
<body>
<main class="card">
    @if (!empty($icon))
        <img src="{{ $icon }}" alt="{{ $appName ?? 'App' }}">
    @endif
    <h1>You’re offline</h1>
    <p>{{ $appName ?? 'Cost to Cost Foods' }} can’t reach the network right now. Check your connection and try again.</p>
    <button type="button" onclick="window.location.reload()">Try again</button>
</main>
</body>
</html>
