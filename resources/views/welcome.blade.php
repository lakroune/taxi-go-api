<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAXI GO — Welcome</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500&family=Figtree:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --card: #141420;
            --border: #232335;
            --accent: #f5c518;
            --text: #e8e8f0;
            --muted: #6e6e8a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Figtree', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 40px 36px;
            width: 340px;
            text-align: center;
            animation: up .5s ease both;
        }

        @keyframes up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            width: 52px;
            height: 52px;
            background: rgba(245, 197, 24, .12);
            border: 1px solid rgba(245, 197, 24, .2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .icon svg {
            width: 26px;
            height: 26px;
            stroke: var(--accent);
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 6px;
        }

        h1 span {
            color: var(--accent);
        }

        .sub {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 24px;
        }

        .stat {
            background: rgba(255, 255, 255, .04);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 6px;
        }

        .stat-val {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .stat-lbl {
            font-size: 10px;
            color: var(--muted);
            font-family: 'JetBrains Mono', monospace;
            margin-top: 2px;
            letter-spacing: .5px;
        }

        .url-box {
            background: rgba(255, 255, 255, .03);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10.5px;
            color: var(--muted);
            margin-bottom: 24px;
            word-break: break-all;
            text-align: left;
        }

        .url-box span {
            color: rgba(245, 197, 24, .6);
            font-size: 9px;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--accent);
            color: #0a0a0f;
            font-family: 'Figtree', sans-serif;
            font-weight: 600;
            font-size: 14px;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            transition: opacity .2s;
        }

        .btn:hover {
            opacity: .88;
        }

        .btn svg {
            width: 16px;
            height: 16px;
            stroke: #0a0a0f;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .footer {
            margin-top: 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--muted);
            opacity: .5;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24">
                <rect x="1" y="10" width="22" height="9" rx="2" />
                <path d="M5 10V7a2 2 0 0 1 2-2h4l2 3" />
                <path d="M11 5h4l2 5" />
                <circle cx="7" cy="19" r="2" />
                <circle cx="17" cy="19" r="2" />
            </svg>
        </div>

        <div class="label">API Reference</div>
        <h1>TAXI<span>GO</span></h1>
        <p class="sub">Laravel Sanctum · PostgreSQL · REST</p>

        <div class="stats">
            <div class="stat">
                <div class="stat-val">20</div>
                <div class="stat-lbl">Routes</div>
            </div>
            <div class="stat">
                <div class="stat-val">5</div>
                <div class="stat-lbl">Modules</div>
            </div>
            <div class="stat">
                <div class="stat-val">v1</div>
                <div class="stat-lbl">Version</div>
            </div>
        </div>

        <div class="url-box">
            <span>Base URL</span>
            https://api-app-86f38c.www.dockhosting.dev
        </div>

        <a href="/docs" class="btn">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
            </svg>
            Voir la documentation
        </a>

        <div class="footer">v1.0 · 2026</div>
    </div>
</body>

</html>
