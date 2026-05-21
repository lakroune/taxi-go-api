<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAXI GO — API Documentation</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Figtree:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --bg2: #111118;
            --bg3: #16161f;
            --card: #1a1a24;
            --border: #252535;
            --border2: #2e2e45;
            --accent: #f5c518;
            --accent2: #ff6b35;
            --accent3: #00d4aa;
            --text: #e8e8f0;
            --muted: #6e6e8a;
            --dim: #3a3a52;
            --get: #00d4aa;
            --post: #f5c518;
            --del: #ff4757;
            --put: #a78bfa;
            --radius: 10px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Figtree', sans-serif;
            font-size: 15px;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* === GRAIN OVERLAY === */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 999;
            opacity: .4;
        }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 270px;
            height: 100vh;
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--dim);
            border-radius: 2px;
        }

        .logo-area {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            background: var(--bg2);
            z-index: 10;
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text);
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(245, 197, 24, .4);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(245, 197, 24, 0);
            }
        }

        .logo-sub {
            font-size: 11px;
            color: var(--muted);
            font-weight: 400;
            font-family: 'JetBrains Mono', monospace;
            margin-top: 4px;
            letter-spacing: .5px;
        }

        .base-url-box {
            margin: 12px 24px 0;
            padding: 8px 12px;
            background: var(--bg3);
            border: 1px solid var(--border2);
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10.5px;
            color: var(--accent3);
            word-break: break-all;
        }

        .base-url-label {
            color: var(--muted);
            font-size: 9px;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-section {
            padding: 8px 0;
        }

        .nav-group-title {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9.5px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 12px 24px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 24px;
            text-decoration: none;
            color: var(--muted);
            font-size: 13.5px;
            font-weight: 500;
            border-left: 2px solid transparent;
            transition: all .2s;
            cursor: pointer;
        }

        .nav-item:hover {
            color: var(--text);
            background: rgba(255, 255, 255, .03);
            border-left-color: var(--dim);
        }

        .nav-item.active {
            color: var(--accent);
            border-left-color: var(--accent);
            background: rgba(245, 197, 24, .05);
        }

        .nav-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: .5px;
        }

        .badge-get {
            background: rgba(0, 212, 170, .15);
            color: var(--get);
        }

        .badge-post {
            background: rgba(245, 197, 24, .15);
            color: var(--post);
        }

        .badge-del {
            background: rgba(255, 71, 87, .15);
            color: var(--del);
        }

        /* === MAIN === */
        .main {
            margin-left: 270px;
            min-height: 100vh;
        }

        /* === HERO === */
        .hero {
            padding: 80px 64px 60px;
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(245, 197, 24, .08) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: 30%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 212, 170, .05) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--accent);
            background: rgba(245, 197, 24, .1);
            border: 1px solid rgba(245, 197, 24, .2);
            padding: 5px 12px;
            border-radius: 20px;
            margin-bottom: 20px;
            letter-spacing: .5px;
        }

        .hero-tag::before {
            content: '●';
            font-size: 8px;
            animation: blink 2s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .3
            }
        }

        h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(38px, 5vw, 62px);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -2px;
            margin-bottom: 16px;
        }

        h1 span {
            color: var(--accent);
        }

        .hero-desc {
            color: var(--muted);
            max-width: 560px;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .hero-stats {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
        }

        .stat {
            display: flex;
            flex-direction: column;
        }

        .stat-val {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .stat-lbl {
            font-size: 12px;
            color: var(--muted);
            font-family: 'JetBrains Mono', monospace;
            margin-top: 4px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .divider {
            width: 1px;
            height: 40px;
            background: var(--border2);
            align-self: center;
        }

        /* === SECTION === */
        .section {
            padding: 56px 64px;
            border-bottom: 1px solid var(--border);
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .5s ease, transform .5s ease;
        }

        .section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 8px;
        }

        .section-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .icon-auth {
            background: rgba(245, 197, 24, .12);
        }

        .icon-user {
            background: rgba(0, 212, 170, .12);
        }

        .icon-passenger {
            background: rgba(167, 139, 250, .12);
        }

        .icon-driver {
            background: rgba(255, 107, 53, .12);
        }

        .icon-admin {
            background: rgba(255, 71, 87, .12);
        }

        h2 {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -.5px;
        }

        .section-desc {
            color: var(--muted);
            margin-bottom: 32px;
            max-width: 640px;
            font-size: 14px;
            padding-left: 58px;
        }

        /* === ENDPOINT CARD === */
        .endpoint-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 16px;
            overflow: hidden;
            transition: border-color .2s, transform .15s;
        }

        .endpoint-card:hover {
            border-color: var(--border2);
            transform: translateX(3px);
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .endpoint-header::after {
            content: '▾';
            position: absolute;
            right: 20px;
            color: var(--muted);
            font-size: 12px;
            transition: transform .25s;
        }

        .endpoint-card.open .endpoint-header::after {
            transform: rotate(180deg);
        }

        .method-badge {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 5px;
            letter-spacing: 1px;
            min-width: 52px;
            text-align: center;
            flex-shrink: 0;
        }

        .method-GET {
            background: rgba(0, 212, 170, .15);
            color: var(--get);
            border: 1px solid rgba(0, 212, 170, .25);
        }

        .method-POST {
            background: rgba(245, 197, 24, .15);
            color: var(--post);
            border: 1px solid rgba(245, 197, 24, .25);
        }

        .method-PUT {
            background: rgba(167, 139, 250, .15);
            color: var(--put);
            border: 1px solid rgba(167, 139, 250, .25);
        }

        .method-DELETE {
            background: rgba(255, 71, 87, .15);
            color: var(--del);
            border: 1px solid rgba(255, 71, 87, .25);
        }

        .endpoint-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13.5px;
            color: var(--text);
            flex: 1;
        }

        .endpoint-path .param {
            color: var(--accent2);
        }

        .endpoint-name {
            font-size: 13px;
            color: var(--muted);
            font-weight: 400;
            margin-left: auto;
            padding-right: 24px;
        }

        /* === ENDPOINT BODY === */
        .endpoint-body {
            display: none;
            border-top: 1px solid var(--border);
            padding: 20px;
            background: var(--bg3);
        }

        .endpoint-card.open .endpoint-body {
            display: block;
        }

        .tab-group {
            display: flex;
            gap: 4px;
            margin-bottom: 16px;
        }

        .tab-btn {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            padding: 5px 14px;
            border-radius: 5px;
            border: 1px solid var(--border2);
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            letter-spacing: .5px;
        }

        .tab-btn:hover {
            color: var(--text);
            background: var(--card);
        }

        .tab-btn.active {
            background: var(--card);
            color: var(--accent);
            border-color: rgba(245, 197, 24, .3);
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 8px 16px;
            font-size: 13.5px;
        }

        .info-label {
            color: var(--muted);
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            letter-spacing: .5px;
            text-transform: uppercase;
            padding-top: 2px;
        }

        .info-val {
            color: var(--text);
        }

        .auth-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
            font-family: 'JetBrains Mono', monospace;
        }

        .auth-required {
            background: rgba(255, 107, 53, .12);
            color: var(--accent2);
            border: 1px solid rgba(255, 107, 53, .2);
        }

        .auth-public {
            background: rgba(0, 212, 170, .12);
            color: var(--accent3);
            border: 1px solid rgba(0, 212, 170, .2);
        }

        pre {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 16px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            line-height: 1.7;
            color: var(--text);
            position: relative;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--card);
            border: 1px solid var(--border2);
            color: var(--muted);
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all .2s;
            letter-spacing: .5px;
        }

        .copy-btn:hover {
            color: var(--text);
            border-color: var(--accent);
        }

        .copy-btn.copied {
            color: var(--get);
            border-color: var(--get);
        }

        .json-key {
            color: #a78bfa;
        }

        .json-str {
            color: #86efac;
        }

        .json-num {
            color: #fb923c;
        }

        .json-bool {
            color: var(--accent);
        }

        .json-null {
            color: var(--muted);
        }

        /* === QUERY PARAMS TABLE === */
        .params-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .params-table th {
            text-align: left;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
        }

        .params-table td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .params-table tr:last-child td {
            border-bottom: none;
        }

        .param-name {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--accent2);
        }

        .param-type {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--put);
        }

        .param-req {
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
        }

        .req-yes {
            background: rgba(255, 71, 87, .1);
            color: var(--del);
        }

        .req-no {
            background: rgba(110, 110, 138, .1);
            color: var(--muted);
        }

        /* === AUTH OVERVIEW === */
        .auth-overview {
            background: var(--card);
            border: 1px solid var(--border2);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .auth-overview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent);
        }

        .auth-overview-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 10px;
            padding-left: 12px;
        }

        .auth-overview-text {
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.7;
            padding-left: 12px;
        }

        code {
            font-family: 'JetBrains Mono', monospace;
            font-size: .88em;
            background: var(--bg3);
            border: 1px solid var(--border);
            padding: 1px 6px;
            border-radius: 4px;
            color: var(--accent);
        }

        /* === FOOTER === */
        .footer {
            padding: 32px 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border);
        }

        .footer-brand {
            font-family: 'Syne', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--muted);
        }

        .footer-brand span {
            color: var(--accent);
        }

        .footer-meta {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--dim);
        }

        /* === SCROLL PROGRESS === */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 270px;
            right: 0;
            height: 2px;
            background: var(--bg);
            z-index: 200;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            width: 0%;
            transition: width .1s linear;
        }

        /* === BACK TO TOP === */
        .back-top {
            position: fixed;
            bottom: 32px;
            right: 32px;
            width: 42px;
            height: 42px;
            background: var(--card);
            border: 1px solid var(--border2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--muted);
            font-size: 16px;
            opacity: 0;
            transform: translateY(16px);
            transition: all .3s;
            z-index: 100;
            text-decoration: none;
        }

        .back-top.show {
            opacity: 1;
            transform: translateY(0);
        }

        .back-top:hover {
            background: var(--accent);
            color: var(--bg);
            border-color: var(--accent);
        }

        /* === MOBILE === */
        .hamburger {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 300;
            background: var(--card);
            border: 1px solid var(--border2);
            padding: 8px 10px;
            border-radius: 7px;
            cursor: pointer;
            color: var(--text);
            font-size: 16px;
        }

        @media (max-width: 860px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            .progress-bar {
                left: 0;
            }

            .hero {
                padding: 80px 24px 40px;
            }

            .section {
                padding: 40px 24px;
            }

            .section-desc {
                padding-left: 0;
            }

            .footer {
                padding: 24px;
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }

            .hamburger {
                display: flex;
            }

            .hero-stats {
                gap: 20px;
            }
        }

        /* === ENTRY ANIMATIONS === */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-tag {
            animation: fadeInUp .5s ease both;
        }

        h1 {
            animation: fadeInUp .55s .1s ease both;
        }

        .hero-desc {
            animation: fadeInUp .55s .2s ease both;
        }

        .hero-stats {
            animation: fadeInUp .55s .3s ease both;
        }
    </style>
</head>

<body>

    <!-- SCROLL PROGRESS -->
    <div class="progress-bar">
        <div class="progress-fill" id="progress"></div>
    </div>

    <!-- HAMBURGER -->
    <button class="hamburger" onclick="toggleSidebar()">☰</button>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="logo-area">
            <a class="logo" href="#top">
                <div class="logo-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="#0a0a0f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="10" width="22" height="9" rx="2" />
                        <path d="M5 10V7a2 2 0 0 1 2-2h4l2 3" />
                        <path d="M11 5h4l2 5" />
                        <circle cx="7" cy="19" r="2" />
                        <circle cx="17" cy="19" r="2" />
                    </svg></div>
                <div>
                    TAXI<span style="color:var(--accent)">GO</span>
                    <div class="logo-sub">API v1.0 — Laravel Sanctum</div>
                </div>
            </a>
            <div class="base-url-box">
                <div class="base-url-label">Base URL</div>
                https://api-app-86f38c.www.dockhosting.dev
            </div>
        </div>

        <nav class="nav-section">
            <div class="nav-group-title">Vue d'ensemble</div>
            <a class="nav-item active" href="#overview" onclick="setActive(this)"><svg width="13" height="13"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" style="flex-shrink:0">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg> Introduction</a>

            <div class="nav-group-title">Authentification</div>
            <a class="nav-item" href="#auth" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Register
            </a>
            <a class="nav-item" href="#auth" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Login
            </a>
            <a class="nav-item" href="#auth" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Forgot Password
            </a>
            <a class="nav-item" href="#auth" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Reset Password
            </a>

            <div class="nav-group-title">Utilisateur & Profil</div>
            <a class="nav-item" href="#user" onclick="setActive(this)">
                <span class="nav-badge badge-get">GET</span> Get User
            </a>
            <a class="nav-item" href="#user" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Upload Avatar
            </a>

            <div class="nav-group-title">Passager & Courses</div>
            <a class="nav-item" href="#passenger" onclick="setActive(this)">
                <span class="nav-badge badge-get">GET</span> Nearby Drivers
            </a>
            <a class="nav-item" href="#passenger" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Create Ride
            </a>
            <a class="nav-item" href="#passenger" onclick="setActive(this)">
                <span class="nav-badge badge-get">GET</span> Rides History
            </a>
            <a class="nav-item" href="#passenger" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Cancel Ride
            </a>
            <a class="nav-item" href="#passenger" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Rate Ride
            </a>

            <div class="nav-group-title">Chauffeur</div>
            <a class="nav-item" href="#driver" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Join as Driver
            </a>
            <a class="nav-item" href="#driver" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Upload Documents
            </a>
            <a class="nav-item" href="#driver" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Update Status
            </a>
            <a class="nav-item" href="#driver" onclick="setActive(this)">
                <span class="nav-badge badge-get">GET</span> Available Rides
            </a>
            <a class="nav-item" href="#driver" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Accept Ride
            </a>
            <a class="nav-item" href="#driver" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Complete Ride
            </a>
            <a class="nav-item" href="#driver" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Track GPS
            </a>

            <div class="nav-group-title">Administration</div>
            <a class="nav-item" href="#admin" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Approve Driver
            </a>
            <a class="nav-item" href="#admin" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Resend Verification
            </a>
            <a class="nav-item" href="#admin" onclick="setActive(this)">
                <span class="nav-badge badge-post">POST</span> Logout
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main" id="top">

        <!-- HERO -->
        <section class="hero">
            <div class="hero-tag">v1.0 — Documentation complète</div>
            <h1>TAXI<span>GO</span><br>API Reference</h1>
            <p class="hero-desc">
                Documentation complète de l'API REST TAXI GO — construite avec Laravel Sanctum et PostgreSQL.
                Couvre l'authentification, la gestion des courses, le suivi GPS en temps réel et l'administration.
            </p>
            <div class="hero-stats">
                <div class="stat">
                    <div class="stat-val">20</div>
                    <div class="stat-lbl">Endpoints</div>
                </div>
                <div class="divider"></div>
                <div class="stat">
                    <div class="stat-val">5</div>
                    <div class="stat-lbl">Modules</div>
                </div>
                <div class="divider"></div>
                <div class="stat">
                    <div class="stat-val">REST</div>
                    <div class="stat-lbl">Architecture</div>
                </div>
                <div class="divider"></div>
                <div class="stat">
                    <div class="stat-val">JWT</div>
                    <div class="stat-lbl">Auth Type</div>
                </div>
            </div>
        </section>

        <!-- OVERVIEW -->
        <section class="section" id="overview">
            <div class="section-header">
                <div class="section-icon icon-auth"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                    </svg></div>
                <h2>Introduction</h2>
            </div>
            <p class="section-desc">Informations essentielles pour utiliser l'API TAXI GO.</p>

            <div class="auth-overview">
                <div class="auth-overview-title"><svg width="15" height="15" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:7px">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>Authentification — Laravel Sanctum (Bearer Token)</div>
                <div class="auth-overview-text">
                    La majorité des endpoints nécessitent un token d'authentification Bearer.<br>
                    Après login, incluez le token dans tous vos headers : <code>Authorization: Bearer
                        {token}</code><br><br>
                    Les endpoints publics (<span style="color:var(--accent3)">Register, Login, Forgot/Reset
                        Password</span>) ne nécessitent pas de token.
                    Tous les autres requièrent une authentification active.
                </div>
            </div>

            <div class="auth-overview" style="border-color: rgba(0,212,170,.2);">
                <div class="auth-overview-title" style="color:var(--accent3)"><svg width="15" height="15"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        style="display:inline;vertical-align:middle;margin-right:7px">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>Headers requis</div>
                <div class="auth-overview-text" style="padding-left:12px">
                    <pre style="margin-top:12px; font-size:12px"><button class="copy-btn" onclick="copyCode(this)">COPIER</button>Accept: application/json
Content-Type: application/json
Authorization: Bearer {auth_token}   // pour les routes protégées</pre>
                </div>
            </div>

            <div class="auth-overview" style="border-color: rgba(167,139,250,.2); border-left-color: var(--put);">
                <div class="auth-overview-title" style="color:var(--put)"><svg width="15" height="15"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        style="display:inline;vertical-align:middle;margin-right:7px">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>Configuration environment</div>
                <div class="auth-overview-text">
                    <code>base_url</code> = <code>https://api-app-86f38c.www.dockhosting.dev</code><br>
                    <code>auth_token</code> = Renseigné automatiquement après un Login réussi via Postman.<br><br>
                    Le système utilise <strong>PostgreSQL</strong> comme base de données et les tokens Sanctum expirent
                    selon la config Laravel.
                </div>
            </div>
        </section>

        <!-- ===== 1. AUTH ===== -->
        <section class="section" id="auth">
            <div class="section-header">
                <div class="section-icon icon-auth"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="7.5" cy="15.5" r="5.5" />
                        <path d="M21 2l-9.6 9.6" />
                        <path d="M15.5 7.5l3 3L22 7l-3-3" />
                    </svg></div>
                <h2>Authentification Publique</h2>
            </div>
            <p class="section-desc">Routes publiques pour inscription, connexion et réinitialisation de mot de passe.
                Aucun token requis.</p>

            <!-- Register -->
            <div class="endpoint-card" id="card-register">
                <div class="endpoint-header" onclick="toggleCard('card-register')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/auth/register</span>
                    <span class="endpoint-name">Register User</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-register')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-register')">Body</button>
                        <button class="tab-btn" onclick="switchTab(this,'response','card-register')">Réponse</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Méthode</div>
                            <div class="info-val"><span class="method-badge method-POST">POST</span></div>
                            <div class="info-label">Endpoint</div>
                            <div class="info-val"><code>/api/auth/register</code></div>
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-public"><svg width="8"
                                        height="8" viewBox="0 0 8 8"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <circle cx="4" cy="4" r="4" fill="currentColor" />
                                    </svg>Public</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Crée un nouveau compte utilisateur
                                (passager). Retourne un token Sanctum utilisable immédiatement.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table" style="margin-bottom:16px">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">name</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Nom complet</td>
                            </tr>
                            <tr>
                                <td class="param-name">email</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Email unique</td>
                            </tr>
                            <tr>
                                <td class="param-name">password</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Min. 8 caractères</td>
                            </tr>
                            <tr>
                                <td class="param-name">password_confirmation</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Doit correspondre à password</td>
                            </tr>
                            <tr>
                                <td class="param-name">phone</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Ex: +212600000000</td>
                            </tr>
                        </table>
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"name"</span>: <span class="json-str">"Ismail Lakroune"</span>,
  <span class="json-key">"email"</span>: <span class="json-str">"ismail@taxigo.ma"</span>,
  <span class="json-key">"password"</span>: <span class="json-str">"password123"</span>,
                            <span class="json-key">"password_confirmation"</span>: <span class="json-str">"password123"</span>,
                            <span class="json-key">"phone"</span>: <span class="json-str">"+212600000000"</span>
                            }</pre>
                    </div>
                    <div class="tab-pane" data-tab="response">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
                            <span class="json-key">"message"</span>: <span class="json-str">"Utilisateur créé avec succès"</span>,
                            <span class="json-key">"token"</span>: <span class="json-str">"1|abc123def456..."</span>,
                            <span class="json-key">"user"</span>: {
                                <span class="json-key">"id"</span>: <span class="json-num">1</span>,
                                <span class="json-key">"name"</span>: <span class="json-str">"Ismail Lakroune"</span>,
                                <span class="json-key">"email"</span>: <span class="json-str">"ismail@taxigo.ma"</span>,
                                <span class="json-key">"phone"</span>: <span class="json-str">"+212600000000"</span>
                            }
                            }</pre>
                    </div>
                </div>
            </div>

            <!-- Login -->
            <div class="endpoint-card" id="card-login">
                <div class="endpoint-header" onclick="toggleCard('card-login')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/auth/login</span>
                    <span class="endpoint-name">Login User</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-login')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-login')">Body</button>
                        <button class="tab-btn" onclick="switchTab(this,'response','card-login')">Réponse</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Méthode</div>
                            <div class="info-val"><span class="method-badge method-POST">POST</span></div>
                            <div class="info-label">Endpoint</div>
                            <div class="info-val"><code>/api/auth/login</code></div>
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-public"><svg width="8"
                                        height="8" viewBox="0 0 8 8"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <circle cx="4" cy="4" r="4" fill="currentColor" />
                                    </svg>Public</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Authentifie un utilisateur et retourne un
                                token Sanctum. Postman sauvegarde automatiquement ce token dans la variable
                                <code>auth_token</code>.
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table" style="margin-bottom:16px">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">email</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Email enregistré</td>
                            </tr>
                            <tr>
                                <td class="param-name">password</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Mot de passe</td>
                            </tr>
                        </table>
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
                                <span class="json-key">"email"</span>: <span class="json-str">"ismail@taxigo.ma"</span>,
                                <span class="json-key">"password"</span>: <span class="json-str">"password123"</span>
                                }</pre>
                    </div>
                    <div class="tab-pane" data-tab="response">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
                                <span class="json-key">"token"</span>: <span class="json-str">"2|xyz789..."</span>,
                                <span class="json-key">"user"</span>: {
                                    <span class="json-key">"id"</span>: <span class="json-num">1</span>,
                                    <span class="json-key">"name"</span>: <span class="json-str">"Ismail Lakroune"</span>,
                                    <span class="json-key">"email"</span>: <span class="json-str">"ismail@taxigo.ma"</span>,
                                    <span class="json-key">"role"</span>: <span class="json-str">"passenger"</span>
                                }
                                }</pre>
                    </div>
                </div>
            </div>

            <!-- Forgot Password -->
            <div class="endpoint-card" id="card-forgot">
                <div class="endpoint-header" onclick="toggleCard('card-forgot')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/auth/forgot-password</span>
                    <span class="endpoint-name">Forgot Password</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-forgot')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-forgot')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-public"><svg width="8"
                                        height="8" viewBox="0 0 8 8"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <circle cx="4" cy="4" r="4" fill="currentColor" />
                                    </svg>Public</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Envoie un lien de réinitialisation par
                                email.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"email"</span>: <span class="json-str">"ismail@taxigo.ma"</span>
}</pre>
                    </div>
                </div>
            </div>

            <!-- Reset Password -->
            <div class="endpoint-card" id="card-reset">
                <div class="endpoint-header" onclick="toggleCard('card-reset')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/auth/reset-password</span>
                    <span class="endpoint-name">Reset Password</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-reset')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-reset')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-public"><svg width="8"
                                        height="8" viewBox="0 0 8 8"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <circle cx="4" cy="4" r="4" fill="currentColor" />
                                    </svg>Public</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Réinitialise le mot de passe en utilisant
                                le token reçu par email.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"token"</span>: <span class="json-str">"token_received_from_email"</span>,
  <span class="json-key">"email"</span>: <span class="json-str">"ismail@taxigo.ma"</span>,
  <span class="json-key">"password"</span>: <span class="json-str">"newpassword123"</span>,
  <span class="json-key">"password_confirmation"</span>: <span class="json-str">"newpassword123"</span>
}</pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== 2. USER ===== -->
        <section class="section" id="user">
            <div class="section-header">
                <div class="section-icon icon-user"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg></div>
                <h2>Utilisateur & Profil</h2>
            </div>
            <p class="section-desc">Gestion des profils authentifiés. Tous ces endpoints nécessitent un Bearer Token
                valide.</p>

            <!-- Get User -->
            <div class="endpoint-card" id="card-getuser">
                <div class="endpoint-header" onclick="toggleCard('card-getuser')">
                    <span class="method-badge method-GET">GET</span>
                    <span class="endpoint-path">/api/user</span>
                    <span class="endpoint-name">Get Authenticated User</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-getuser')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'response','card-getuser')">Réponse</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Méthode</div>
                            <div class="info-val"><span class="method-badge method-GET">GET</span></div>
                            <div class="info-label">Endpoint</div>
                            <div class="info-val"><code>/api/user</code></div>
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Retourne le profil complet de
                                l'utilisateur actuellement connecté.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="response">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"id"</span>: <span class="json-num">1</span>,
  <span class="json-key">"name"</span>: <span class="json-str">"Ismail Lakroune"</span>,
  <span class="json-key">"email"</span>: <span class="json-str">"ismail@taxigo.ma"</span>,
  <span class="json-key">"phone"</span>: <span class="json-str">"+212600000000"</span>,
  <span class="json-key">"role"</span>: <span class="json-str">"passenger"</span>,
  <span class="json-key">"avatar_url"</span>: <span class="json-null">null</span>,
  <span class="json-key">"email_verified_at"</span>: <span class="json-str">"2026-05-01T10:00:00Z"</span>
}</pre>
                    </div>
                </div>
            </div>

            <!-- Upload Avatar -->
            <div class="endpoint-card" id="card-avatar">
                <div class="endpoint-header" onclick="toggleCard('card-avatar')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/user/avatar</span>
                    <span class="endpoint-name">Upload Profile Avatar</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-avatar')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-avatar')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Content-Type</div>
                            <div class="info-val"><code>multipart/form-data</code></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Upload une photo de profil. Formats
                                acceptés : JPEG, PNG, WebP. Taille max recommandée : 2MB.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">avatar</td>
                                <td class="param-type">file</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Fichier image (JPEG/PNG)</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== 3. PASSENGER ===== -->
        <section class="section" id="passenger">
            <div class="section-header">
                <div class="section-icon icon-passenger"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" />
                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                        <line x1="12" y1="12" x2="12" y2="16" />
                        <line x1="10" y1="14" x2="14" y2="14" />
                    </svg></div>
                <h2>Passager & Cycle de Course</h2>
            </div>
            <p class="section-desc">Actions réalisées par le passager : recherche de chauffeurs, demande de course,
                historique et évaluation.</p>

            <!-- Nearby Drivers -->
            <div class="endpoint-card" id="card-nearby">
                <div class="endpoint-header" onclick="toggleCard('card-nearby')">
                    <span class="method-badge method-GET">GET</span>
                    <span class="endpoint-path">/api/drivers/nearby</span>
                    <span class="endpoint-name">Get Nearby Drivers</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-nearby')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'params','card-nearby')">Query Params</button>
                        <button class="tab-btn" onclick="switchTab(this,'response','card-nearby')">Réponse</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Retourne la liste des chauffeurs
                                disponibles dans un rayon donné, avec leur position GPS en temps réel.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="params">
                        <table class="params-table">
                            <tr>
                                <th>Paramètre</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Exemple</th>
                            </tr>
                            <tr>
                                <td class="param-name">lat</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">32.2924</td>
                            </tr>
                            <tr>
                                <td class="param-name">lng</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">-9.2348</td>
                            </tr>
                            <tr>
                                <td class="param-name">radius</td>
                                <td class="param-type">integer</td>
                                <td><span class="param-req req-no">NON</span></td>
                                <td style="color:var(--muted)">5 (km, défaut)</td>
                            </tr>
                        </table>
                    </div>
                    <div class="tab-pane" data-tab="response">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>[
  {
    <span class="json-key">"id"</span>: <span class="json-num">2</span>,
    <span class="json-key">"name"</span>: <span class="json-str">"Ahmed El Fassi"</span>,
    <span class="json-key">"rating"</span>: <span class="json-num">4.8</span>,
    <span class="json-key">"vehicle_type"</span>: <span class="json-str">"taxi"</span>,
    <span class="json-key">"lat"</span>: <span class="json-num">32.2950</span>,
    <span class="json-key">"lng"</span>: <span class="json-num">-9.2360</span>,
    <span class="json-key">"distance_km"</span>: <span class="json-num">0.8</span>
  }
]</pre>
                    </div>
                </div>
            </div>

            <!-- Create Ride -->
            <div class="endpoint-card" id="card-ride">
                <div class="endpoint-header" onclick="toggleCard('card-ride')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/rides/request</span>
                    <span class="endpoint-name">Create Ride Request</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-ride')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-ride')">Body</button>
                        <button class="tab-btn" onclick="switchTab(this,'response','card-ride')">Réponse</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Crée une nouvelle demande de course.
                                Supporte les courses partagées (<code>is_shared: true</code>). Les coordonnées GPS sont
                                en degrés décimaux.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table" style="margin-bottom:16px">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">origin_lat</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Latitude de départ</td>
                            </tr>
                            <tr>
                                <td class="param-name">origin_lng</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Longitude de départ</td>
                            </tr>
                            <tr>
                                <td class="param-name">dest_lat</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Latitude de destination</td>
                            </tr>
                            <tr>
                                <td class="param-name">dest_lng</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Longitude de destination</td>
                            </tr>
                            <tr>
                                <td class="param-name">is_shared</td>
                                <td class="param-type">boolean</td>
                                <td><span class="param-req req-no">NON</span></td>
                                <td style="color:var(--muted)">true = course partagée</td>
                            </tr>
                        </table>
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"origin_lat"</span>: <span class="json-num">32.2924</span>,
  <span class="json-key">"origin_lng"</span>: <span class="json-num">-9.2348</span>,
  <span class="json-key">"dest_lat"</span>: <span class="json-num">32.3212</span>,
  <span class="json-key">"dest_lng"</span>: <span class="json-num">-9.2431</span>,
  <span class="json-key">"is_shared"</span>: <span class="json-bool">false</span>
}</pre>
                    </div>
                    <div class="tab-pane" data-tab="response">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"id"</span>: <span class="json-num">42</span>,
  <span class="json-key">"status"</span>: <span class="json-str">"pending"</span>,
  <span class="json-key">"is_shared"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"estimated_price"</span>: <span class="json-num">25.00</span>,
  <span class="json-key">"created_at"</span>: <span class="json-str">"2026-05-21T09:15:00Z"</span>
}</pre>
                    </div>
                </div>
            </div>

            <!-- Rides History -->
            <div class="endpoint-card" id="card-history">
                <div class="endpoint-header" onclick="toggleCard('card-history')">
                    <span class="method-badge method-GET">GET</span>
                    <span class="endpoint-path">/api/rides/history</span>
                    <span class="endpoint-name">Get Rides History</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-history')">Info</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Retourne l'historique paginé des courses
                                de l'utilisateur connecté (passager ou chauffeur).</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancel Ride -->
            <div class="endpoint-card" id="card-cancel">
                <div class="endpoint-header" onclick="toggleCard('card-cancel')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/rides/<span class="param">{id}</span>/cancel</span>
                    <span class="endpoint-name">Cancel Ride</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-cancel')">Info</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">URL Param</div>
                            <div class="info-val"><code>{id}</code> — ID de la course à annuler</div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Annule une course en attente ou en cours.
                                Seul l'initiateur peut annuler.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rate -->
            <div class="endpoint-card" id="card-rate">
                <div class="endpoint-header" onclick="toggleCard('card-rate')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/rides/rate</span>
                    <span class="endpoint-name">Submit Ride Rating</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-rate')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-rate')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Soumet une évaluation (1-5 étoiles) pour
                                une course terminée, avec commentaire optionnel.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table" style="margin-bottom:16px">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">ride_id</td>
                                <td class="param-type">integer</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">ID de la course</td>
                            </tr>
                            <tr>
                                <td class="param-name">score</td>
                                <td class="param-type">integer</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Note de 1 à 5</td>
                            </tr>
                            <tr>
                                <td class="param-name">comment</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-no">NON</span></td>
                                <td style="color:var(--muted)">Commentaire libre</td>
                            </tr>
                        </table>
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"ride_id"</span>: <span class="json-num">1</span>,
  <span class="json-key">"score"</span>: <span class="json-num">5</span>,
  <span class="json-key">"comment"</span>: <span class="json-str">"Excellent chauffeur, conduite prudente !"</span>
}</pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== 4. DRIVER ===== -->
        <section class="section" id="driver">
            <div class="section-header">
                <div class="section-icon icon-driver"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="1" y="10" width="22" height="9" rx="2" />
                        <path d="M5 10V7a2 2 0 0 1 2-2h4l2 3" />
                        <path d="M11 5h4l2 5" />
                        <circle cx="7" cy="19" r="2" />
                        <circle cx="17" cy="19" r="2" />
                    </svg></div>
                <h2>Actions Chauffeur</h2>
            </div>
            <p class="section-desc">Fonctionnalités dédiées au chauffeur : inscription, documents, statut, acceptation
                et finalisation de courses.</p>

            <!-- Join -->
            <div class="endpoint-card" id="card-join">
                <div class="endpoint-header" onclick="toggleCard('card-join')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/driver/join</span>
                    <span class="endpoint-name">Join as Driver</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-join')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-join')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Demande d'inscription comme chauffeur. La
                                demande passe en attente d'approbation admin.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table" style="margin-bottom:16px">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">vehicle_type</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Ex: "taxi", "berline"</td>
                            </tr>
                            <tr>
                                <td class="param-name">plate_number</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Immatriculation</td>
                            </tr>
                            <tr>
                                <td class="param-name">license</td>
                                <td class="param-type">string</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Numéro de permis</td>
                            </tr>
                            <tr>
                                <td class="param-name">insurance_expiry</td>
                                <td class="param-type">date</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Format YYYY-MM-DD</td>
                            </tr>
                        </table>
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"vehicle_type"</span>: <span class="json-str">"taxi"</span>,
  <span class="json-key">"plate_number"</span>: <span class="json-str">"12345-أ-26"</span>,
  <span class="json-key">"license"</span>: <span class="json-str">"Num-Permis-9988"</span>,
  <span class="json-key">"insurance_expiry"</span>: <span class="json-str">"2027-12-31"</span>
}</pre>
                    </div>
                </div>
            </div>

            <!-- Upload Documents -->
            <div class="endpoint-card" id="card-docs">
                <div class="endpoint-header" onclick="toggleCard('card-docs')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/driver/documents</span>
                    <span class="endpoint-name">Upload Vehicle Documents</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-docs')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-docs')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Content-Type</div>
                            <div class="info-val"><code>multipart/form-data</code></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Upload le permis de conduire et
                                l'attestation d'assurance du chauffeur.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">license_file</td>
                                <td class="param-type">file</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Scan permis de conduire</td>
                            </tr>
                            <tr>
                                <td class="param-name">insurance_file</td>
                                <td class="param-type">file</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Attestation d'assurance</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="endpoint-card" id="card-status">
                <div class="endpoint-header" onclick="toggleCard('card-status')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/driver/status</span>
                    <span class="endpoint-name">Update Driver Status & Location</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-status')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-status')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Met à jour le statut du chauffeur
                                (<code>available</code>, <code>busy</code>, <code>offline</code>) et sa position GPS.
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"status"</span>: <span class="json-str">"available"</span>,
  <span class="json-key">"lat"</span>: <span class="json-num">32.2924</span>,
  <span class="json-key">"lng"</span>: <span class="json-num">-9.2348</span>
}</pre>
                    </div>
                </div>
            </div>

            <!-- Available Rides -->
            <div class="endpoint-card" id="card-avail">
                <div class="endpoint-header" onclick="toggleCard('card-avail')">
                    <span class="method-badge method-GET">GET</span>
                    <span class="endpoint-path">/api/driver/available-rides</span>
                    <span class="endpoint-name">Get Available Nearby Rides</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-avail')">Info</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Liste les courses en attente
                                (<code>pending</code>) disponibles autour de la position actuelle du chauffeur.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accept Ride -->
            <div class="endpoint-card" id="card-accept">
                <div class="endpoint-header" onclick="toggleCard('card-accept')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/driver/rides/<span class="param">{id}</span>/accept</span>
                    <span class="endpoint-name">Accept Ride Request</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-accept')">Info</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">URL Param</div>
                            <div class="info-val"><code>{id}</code> — ID de la course à accepter</div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Le chauffeur accepte une course en
                                attente. Le statut de la course passe à <code>accepted</code>.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complete Ride -->
            <div class="endpoint-card" id="card-complete">
                <div class="endpoint-header" onclick="toggleCard('card-complete')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/rides/<span class="param">{id}</span>/complete</span>
                    <span class="endpoint-name">Complete Ride & Cash Payment</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active"
                            onclick="switchTab(this,'info','card-complete')">Info</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Finalise la course et enregistre le
                                paiement en espèces. La course passe au statut <code>completed</code>.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Track GPS -->
            <div class="endpoint-card" id="card-gps">
                <div class="endpoint-header" onclick="toggleCard('card-gps')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/driver/track-location</span>
                    <span class="endpoint-name">Track Location (GPS Haute Fréquence)</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-gps')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'body','card-gps')">Body</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Envoie la position GPS en temps réel.
                                Utilisé pendant une course active pour le suivi côté passager. Fréquence : toutes les
                                3-5 secondes recommandée.</div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="body">
                        <table class="params-table" style="margin-bottom:16px">
                            <tr>
                                <th>Champ</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Description</th>
                            </tr>
                            <tr>
                                <td class="param-name">lat</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Latitude GPS</td>
                            </tr>
                            <tr>
                                <td class="param-name">lng</td>
                                <td class="param-type">float</td>
                                <td><span class="param-req req-yes">OUI</span></td>
                                <td style="color:var(--muted)">Longitude GPS</td>
                            </tr>
                            <tr>
                                <td class="param-name">heading</td>
                                <td class="param-type">integer</td>
                                <td><span class="param-req req-no">NON</span></td>
                                <td style="color:var(--muted)">Direction en degrés (0-360)</td>
                            </tr>
                            <tr>
                                <td class="param-name">speed</td>
                                <td class="param-type">integer</td>
                                <td><span class="param-req req-no">NON</span></td>
                                <td style="color:var(--muted)">Vitesse en km/h</td>
                            </tr>
                        </table>
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"lat"</span>: <span class="json-num">32.2962</span>,
  <span class="json-key">"lng"</span>: <span class="json-num">-9.2415</span>,
  <span class="json-key">"heading"</span>: <span class="json-num">180</span>,
  <span class="json-key">"speed"</span>: <span class="json-num">40</span>
}</pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== 5. ADMIN ===== -->
        <section class="section" id="admin">
            <div class="section-header">
                <div class="section-icon icon-admin"><svg width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg></div>
                <h2>Administration & Système</h2>
            </div>
            <p class="section-desc">Actions d'administration et maintenance système. Nécessitent des droits admin.</p>

            <!-- Approve Driver -->
            <div class="endpoint-card" id="card-approve">
                <div class="endpoint-header" onclick="toggleCard('card-approve')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/admin/driver/<span class="param">{id}</span>/approve</span>
                    <span class="endpoint-name">Admin Approve Driver</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-approve')">Info</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Admin Token</span></div>
                            <div class="info-label">URL Param</div>
                            <div class="info-val"><code>{id}</code> — ID du chauffeur à approuver</div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Approuve la demande d'un chauffeur. Le
                                chauffeur peut ensuite se connecter et accepter des courses.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resend Verification -->
            <div class="endpoint-card" id="card-verify">
                <div class="endpoint-header" onclick="toggleCard('card-verify')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/email/verification-notification</span>
                    <span class="endpoint-name">Resend Email Verification</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-verify')">Info</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Renvoie l'email de vérification à
                                l'utilisateur connecté dont l'email n'est pas encore vérifié.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout -->
            <div class="endpoint-card" id="card-logout">
                <div class="endpoint-header" onclick="toggleCard('card-logout')">
                    <span class="method-badge method-POST">POST</span>
                    <span class="endpoint-path">/api/auth/logout</span>
                    <span class="endpoint-name">Logout User</span>
                </div>
                <div class="endpoint-body">
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchTab(this,'info','card-logout')">Info</button>
                        <button class="tab-btn" onclick="switchTab(this,'response','card-logout')">Réponse</button>
                    </div>
                    <div class="tab-pane active" data-tab="info">
                        <div class="info-grid">
                            <div class="info-label">Auth</div>
                            <div class="info-val"><span class="auth-badge auth-required"><svg width="10"
                                        height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="display:inline;vertical-align:middle;margin-right:5px">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>Bearer Token</span></div>
                            <div class="info-label">Description</div>
                            <div class="info-val" style="color:var(--muted)">Révoque le token Sanctum actuel. Après
                                cette opération, le token est invalide et toute requête avec ce token retournera 401.
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" data-tab="response">
                        <pre><button class="copy-btn" onclick="copyCode(this)">COPIER</button>{
  <span class="json-key">"message"</span>: <span class="json-str">"Déconnecté avec succès"</span>
}</pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="footer">
            <div class="footer-brand">TAXI<span>GO</span> API — v1.0</div>
            <div class="footer-meta">Laravel Sanctum · PostgreSQL · REST · 2026</div>
        </footer>
    </main>

    <!-- BACK TO TOP -->
    <a href="#top" class="back-top" id="backTop"><svg width="15" height="15" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round">
            <line x1="12" y1="19" x2="12" y2="5" />
            <polyline points="5 12 12 5 19 12" />
        </svg></a>

    <script>
        // === SCROLL PROGRESS & BACK TO TOP ===
        const progress = document.getElementById('progress');
        const backTop = document.getElementById('backTop');
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const total = document.documentElement.scrollHeight - window.innerHeight;
            progress.style.width = (scrolled / total * 100) + '%';
            backTop.classList.toggle('show', scrolled > 400);

            // highlight nav
            document.querySelectorAll('.section').forEach(sec => {
                const top = sec.getBoundingClientRect().top;
                if (top < 200 && top > -sec.offsetHeight + 100) {
                    const id = sec.id;
                    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                    document.querySelectorAll(`.nav-item[href="#${id}"]`).forEach(n => n.classList.add(
                        'active'));
                }
            });
        });

        // === SCROLL ANIMATIONS ===
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) e.target.classList.add('visible');
            });
        }, {
            threshold: 0.08
        });
        document.querySelectorAll('.section').forEach(s => observer.observe(s));

        // === TOGGLE CARD ===
        function toggleCard(id) {
            const card = document.getElementById(id);
            card.classList.toggle('open');
        }

        // === SWITCH TAB ===
        function switchTab(btn, tabName, cardId) {
            const card = document.getElementById(cardId);
            card.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            card.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            card.querySelector(`.tab-pane[data-tab="${tabName}"]`).classList.add('active');
        }

        // === COPY CODE ===
        function copyCode(btn) {
            const pre = btn.closest('pre');
            const text = pre.innerText.replace('COPIER', '').replace('COPIÉ', '').trim();
            navigator.clipboard.writeText(text).then(() => {
                btn.textContent = 'COPIÉ';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = 'COPIER';
                    btn.classList.remove('copied');
                }, 2000);
            });
        }

        // === NAV ACTIVE ===
        function setActive(el) {
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('sidebar').classList.remove('open');
        }

        // === MOBILE SIDEBAR ===
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        // === OPEN FIRST CARD PER SECTION ===
        document.querySelectorAll('.section').forEach(sec => {
            const first = sec.querySelector('.endpoint-card');
            if (first) first.classList.add('open');
        });
    </script>
</body>

</html>
