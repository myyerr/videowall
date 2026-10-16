<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-Banner Informatika') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg-body:   #101318;
                --bg-card:   #1a1f28;
                --bg-input:  #1e232e;
                --border-1:  #282d38;
                --accent:    #3b82f6;
                --accent-dim: rgba(59,130,246,0.1);
                --text-1:    #e8eaed;
                --text-2:    #9aa0ad;
                --text-3:    #5c6370;
                --success:   #22c55e;
                --danger:    #ef4444;
                --radius:    8px;
                --radius-sm: 6px;
                --speed:     .18s ease;
            }

            html, body {
                background: var(--bg-body);
                min-height: 100vh;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                -webkit-font-smoothing: antialiased;
            }

            .auth-wrapper {
                min-height: 100vh;
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                box-sizing: border-box;
            }

            .auth-card {
                width: 100%;
                max-width: 380px;
                background: var(--bg-card);
                border: 1px solid var(--border-1);
                border-radius: var(--radius);
                padding: 32px 28px;
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
                width: 44px; height: 44px;
                border-radius: var(--radius-sm);
                background: var(--accent);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 18px;
            }

            .auth-title {
                font-size: 16px;
                font-weight: 700;
                color: var(--text-1);
                text-align: center;
            }

            .auth-subtitle {
                font-size: 13px;
                color: var(--text-3);
                text-align: center;
                line-height: 1.5;
            }

            .auth-status {
                background: rgba(34,197,94,0.08);
                border: 1px solid rgba(34,197,94,0.2);
                color: var(--success);
                border-radius: var(--radius-sm);
                padding: 8px 12px;
                font-size: 13px;
                margin-bottom: 16px;
            }

            .auth-field { margin-bottom: 16px; }

            .auth-label {
                display: block;
                font-size: 12px;
                font-weight: 600;
                color: var(--text-2);
                margin-bottom: 6px;
            }

            .auth-input {
                width: 100%;
                background: var(--bg-input);
                border: 1px solid var(--border-1);
                border-radius: var(--radius-sm);
                padding: 10px 12px;
                font-size: 13px;
                color: var(--text-1);
                transition: border-color var(--speed);
                box-sizing: border-box;
                font-family: inherit;
            }

            .auth-input::placeholder { color: var(--text-3); }
            .auth-input:focus { outline: none; border-color: var(--accent); }

            .auth-error {
                color: var(--danger);
                font-size: 12px;
                margin-top: 5px;
            }

            .auth-remember {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 20px;
            }

            .auth-remember input[type="checkbox"] {
                width: 15px; height: 15px;
                accent-color: var(--accent);
                cursor: pointer;
            }

            .auth-remember label {
                font-size: 13px;
                color: var(--text-2);
                cursor: pointer;
            }

            .auth-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-top: 20px;
            }

            .auth-row.auth-row-end { justify-content: flex-end; }

            .auth-link {
                font-size: 13px;
                color: var(--text-3);
                text-decoration: none;
                transition: color var(--speed);
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
            }
            .auth-link:hover { color: var(--accent); }

            .auth-submit {
                background: var(--accent);
                color: #fff;
                border: none;
                border-radius: var(--radius-sm);
                padding: 10px 20px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: background var(--speed);
                white-space: nowrap;
                font-family: inherit;
            }
            .auth-submit:hover { background: #2563eb; }

            .auth-body-text {
                font-size: 13px;
                color: var(--text-2);
                line-height: 1.6;
                margin-bottom: 16px;
            }

            @media (max-width: 400px) {
                .auth-card { padding: 24px 20px; }
                .auth-row:not(.auth-row-end) {
                    flex-direction: column-reverse;
                    align-items: stretch;
                    gap: 12px;
                }
                .auth-row .auth-submit { width: 100%; text-align: center; }
                .auth-row:not(.auth-row-end) .auth-link { text-align: center; }
            }
        </style>
    </head>
    <body>
        {{ $slot }}
    </body>
</html>