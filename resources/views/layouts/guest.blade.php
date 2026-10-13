<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome (icons used throughout the app) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg-base: #0b0d12;
                --bg-surface: #151823;
                --bg-elevated: #1c2030;
                --border: #2a2e3d;
                --border-accent: #4f46e5;
                --accent: #6366f1;
                --accent-2: #818cf8;
                --accent-glow: rgba(99,102,241,0.12);
                --text-primary: #f1f2f6;
                --text-secondary: #c0c4d4;
                --text-muted: #6b7080;
                --success: #10b981;
                --danger: #ef4444;
                --radius: 14px;
                --radius-sm: 8px;
            }

            html, body {
                background: var(--bg-base);
                min-height: 100vh;
            }

            .auth-wrapper {
                min-height: 100vh;
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                box-sizing: border-box;
                background:
                    radial-gradient(circle at 15% 15%, rgba(99,102,241,0.10), transparent 45%),
                    radial-gradient(circle at 85% 85%, rgba(129,140,248,0.08), transparent 45%),
                    var(--bg-base);
            }

            .auth-card {
                width: 100%;
                max-width: 420px;
                background: var(--bg-surface);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 36px 32px;
                box-shadow: 0 20px 50px rgba(0,0,0,0.5);
                box-sizing: border-box;
            }

            .auth-brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                margin-bottom: 28px;
            }

            .auth-brand-icon {
                width: 52px;
                height: 52px;
                border-radius: var(--radius-sm);
                background: var(--accent-glow);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--accent-2);
                font-size: 22px;
            }

            .auth-title {
                font-size: 18px;
                font-weight: 700;
                color: var(--text-primary);
                text-align: center;
            }

            .auth-subtitle {
                font-size: 13px;
                color: var(--text-muted);
                text-align: center;
                line-height: 1.5;
            }

            .auth-status {
                background: rgba(16,185,129,0.12);
                border: 1px solid rgba(16,185,129,0.3);
                color: var(--success);
                border-radius: var(--radius-sm);
                padding: 10px 14px;
                font-size: 13px;
                margin-bottom: 20px;
            }

            .auth-field {
                margin-bottom: 18px;
            }

            .auth-label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: var(--text-secondary);
                margin-bottom: 6px;
            }

            .auth-input {
                width: 100%;
                background: var(--bg-base);
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                padding: 11px 14px;
                font-size: 14px;
                color: var(--text-primary);
                transition: all 0.15s;
                box-sizing: border-box;
            }

            .auth-input::placeholder { color: var(--text-muted); }

            .auth-input:focus {
                outline: none;
                border-color: var(--border-accent);
                box-shadow: 0 0 0 3px var(--accent-glow);
            }

            .auth-error {
                color: var(--danger);
                font-size: 12px;
                margin-top: 6px;
            }

            .auth-remember {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 22px;
            }

            .auth-remember input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: var(--accent);
                cursor: pointer;
            }

            .auth-remember label {
                font-size: 13px;
                color: var(--text-secondary);
                cursor: pointer;
            }

            .auth-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-top: 22px;
            }

            .auth-row.auth-row-end {
                justify-content: flex-end;
            }

            .auth-link {
                font-size: 13px;
                color: var(--text-muted);
                text-decoration: none;
                transition: color 0.15s;
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
            }

            .auth-link:hover { color: var(--accent-2); }

            .auth-submit {
                background: var(--accent);
                color: #fff;
                border: none;
                border-radius: var(--radius-sm);
                padding: 11px 24px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.15s;
                box-shadow: 0 8px 20px rgba(99,102,241,0.3);
                white-space: nowrap;
            }

            .auth-submit:hover {
                background: var(--accent-2);
                transform: translateY(-1px);
            }

            .auth-body-text {
                font-size: 13px;
                color: var(--text-secondary);
                line-height: 1.6;
                margin-bottom: 20px;
            }

            @media (max-width: 400px) {
                .auth-card { padding: 28px 22px; }

                .auth-row:not(.auth-row-end) {
                    flex-direction: column-reverse;
                    align-items: stretch;
                    gap: 14px;
                }

                .auth-row .auth-submit { width: 100%; }
                .auth-row:not(.auth-row-end) .auth-link { text-align: center; }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        {{ $slot }}
    </body>
</html> 