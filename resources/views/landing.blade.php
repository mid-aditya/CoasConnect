<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="COAS Connect - Platform Pendidikan Kedokteran Berbasis Klinik">
    <title>COAS Connect</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-serif-display:400,400i|dm-sans:300,400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        :root {
            --navy: #0a1628;
            --navy-mid: #132040;
            --navy-light: #1e3260;
            --accent: #3b82f6;
            --accent-bright: #60a5fa;
            --accent-pale: #dbeafe;
            --mint: #10b981;
            --mint-pale: #d1fae5;
            --gold: #f59e0b;
            --gold-pale: #fef3c7;
            --surface: #f8fafc;
            --surface-2: #f1f5f9;
            --border: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --white: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--text);
            line-height: 1.6;
        }

        /* ── NAV ── */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 100;
            background: rgba(10, 22, 40, 0.97);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 2rem;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .logo-mark {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .logo-mark svg { width: 18px; height: 18px; color: white; }
        .logo-text {
            font-family: 'DM Serif Display', serif;
            font-size: 1.2rem;
            color: white;
            letter-spacing: -0.01em;
        }
        .nav-links {
            display: flex; align-items: center; gap: 2rem;
            list-style: none;
        }
        .nav-links a {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 400;
            transition: color .2s;
        }
        .nav-links a:hover { color: white; }
        .btn-nav {
            background: var(--accent);
            color: white !important;
            padding: 0.45rem 1.1rem;
            border-radius: 6px;
            font-weight: 500 !important;
            transition: background .2s !important;
        }
        .btn-nav:hover { background: #2563eb !important; }

        /* ── HERO ── */
        .hero {
            padding: 140px 2rem 100px;
            background: var(--navy);
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -200px; right: -200px;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 65%);
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -100px; left: -100px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.07) 0%, transparent 65%);
            pointer-events: none;
        }
        .hero-inner {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 5rem; align-items: center;
            position: relative; z-index: 1;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.25);
            color: var(--accent-bright);
            padding: 5px 14px;
            border-radius: 100px;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .hero-badge .dot {
            width: 6px; height: 6px;
            background: var(--accent-bright);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        .hero h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(2.4rem, 4vw, 3.5rem);
            color: white;
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-bottom: 1.25rem;
        }
        .hero h1 em {
            font-style: italic;
            color: var(--accent-bright);
        }
        .hero p {
            color: rgba(255,255,255,0.55);
            font-size: 1.05rem;
            line-height: 1.75;
            margin-bottom: 2.5rem;
            font-weight: 300;
        }
        .hero-cta {
            display: flex; gap: 1rem; flex-wrap: wrap;
        }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--accent);
            color: white;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-size: 0.925rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s;
            border: 1px solid transparent;
        }
        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(59,130,246,0.35);
        }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            background: transparent;
            color: rgba(255,255,255,0.75);
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-size: 0.925rem;
            font-weight: 400;
            text-decoration: none;
            transition: all .2s;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.06);
            color: white;
            border-color: rgba(255,255,255,0.3);
        }
        .hero-stats {
            display: flex; gap: 2.5rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .stat-num {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            color: white;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            margin-top: 4px;
            letter-spacing: 0.02em;
        }

        /* ── HERO CARD ── */
        .hero-visual {
            position: relative;
        }
        .card-main {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 24px 80px rgba(0,0,0,0.4);
        }
        .card-header {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 1.25rem;
        }
        .card-avatar {
            width: 40px; height: 40px;
            background: var(--accent-pale);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .card-avatar svg { width: 20px; height: 20px; color: var(--accent); }
        .card-title { font-weight: 600; font-size: 0.9rem; color: var(--text); }
        .card-sub { font-size: 0.78rem; color: var(--muted); }
        .card-field {
            background: var(--surface);
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            margin-bottom: 0.5rem;
        }
        .card-field-label { font-size: 0.7rem; color: var(--muted); margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.05em; }
        .card-field-val { font-size: 0.875rem; font-weight: 500; color: var(--text); }
        .card-tags { display: flex; gap: 6px; margin-top: 0.75rem; }
        .tag {
            font-size: 0.72rem; font-weight: 500;
            padding: 3px 10px;
            border-radius: 100px;
        }
        .tag-green { background: var(--mint-pale); color: #065f46; }
        .tag-blue { background: var(--accent-pale); color: #1e40af; }

        .card-float {
            position: absolute;
            background: white;
            border-radius: 12px;
            padding: 0.9rem 1.1rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
        }
        .card-float-wa {
            bottom: -20px; right: -24px;
            display: flex; align-items: center; gap: 10px;
        }
        .wa-icon {
            width: 36px; height: 36px;
            background: var(--mint-pale);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
        }
        .wa-icon svg { width: 18px; height: 18px; color: var(--mint); }
        .wa-label { font-size: 0.825rem; font-weight: 600; color: var(--text); }
        .wa-sub { font-size: 0.72rem; color: var(--mint); }

        .card-float-progress {
            top: -20px; left: -24px;
        }
        .prog-label { font-size: 0.72rem; color: var(--muted); margin-bottom: 6px; }
        .prog-bar-bg {
            width: 140px; height: 6px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
        }
        .prog-bar-fill {
            height: 100%; width: 68%;
            background: linear-gradient(90deg, var(--accent), var(--accent-bright));
            border-radius: 3px;
        }
        .prog-num { font-size: 0.8rem; font-weight: 600; color: var(--text); margin-top: 4px; }

        /* ── SECTION SHARED ── */
        .section-label {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 0.72rem; font-weight: 600;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 0.75rem;
        }
        .section-label::before {
            content: '';
            width: 18px; height: 2px;
            background: var(--accent);
            border-radius: 1px;
        }
        .section-heading {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            color: var(--text);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        .section-sub {
            font-size: 1rem;
            color: var(--muted);
            line-height: 1.7;
            font-weight: 300;
            max-width: 520px;
            margin: 0 auto;
        }

        /* ── FEATURES ── */
        .features-section {
            padding: 100px 2rem;
            background: white;
        }
        .features-inner { max-width: 1200px; margin: 0 auto; }
        .features-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }
        .feature-cell {
            background: white;
            padding: 2rem 1.75rem;
            transition: background .2s;
            position: relative;
        }
        .feature-cell:hover { background: var(--surface); }
        .feature-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.1rem;
        }
        .feature-icon svg { width: 22px; height: 22px; }
        .icon-blue { background: var(--accent-pale); color: var(--accent); }
        .icon-green { background: var(--mint-pale); color: var(--mint); }
        .icon-amber { background: var(--gold-pale); color: var(--gold); }
        .icon-purple { background: #ede9fe; color: #7c3aed; }
        .icon-rose { background: #ffe4e6; color: #e11d48; }
        .icon-cyan { background: #cffafe; color: #0891b2; }
        .feature-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .feature-desc {
            font-size: 0.875rem;
            color: var(--muted);
            line-height: 1.65;
            font-weight: 300;
        }

        /* ── HOW IT WORKS ── */
        .how-section {
            padding: 100px 2rem;
            background: var(--navy);
            position: relative;
            overflow: hidden;
        }
        .how-section::before {
            content: '';
            position: absolute; inset: 0;
            background: repeating-linear-gradient(
                90deg,
                rgba(255,255,255,0.015) 0px, rgba(255,255,255,0.015) 1px,
                transparent 1px, transparent 80px
            );
        }
        .how-inner {
            max-width: 1200px; margin: 0 auto;
            position: relative; z-index: 1;
        }
        .how-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .how-header .section-heading { color: white; }
        .how-header .section-sub { color: rgba(255,255,255,0.45); }
        .how-steps {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 0;
            position: relative;
        }
        .how-steps::before {
            content: '';
            position: absolute;
            top: 28px; left: calc(12.5%);
            width: calc(75%);
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59,130,246,0.4), rgba(59,130,246,0.4), transparent);
        }
        .step {
            text-align: center;
            padding: 0 1.5rem;
        }
        .step-num {
            width: 56px; height: 56px;
            margin: 0 auto 1.5rem;
            background: var(--navy-light);
            border: 1px solid rgba(59,130,246,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'DM Serif Display', serif;
            font-size: 1.3rem;
            color: var(--accent-bright);
            position: relative; z-index: 1;
        }
        .step-title {
            font-size: 0.925rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.6rem;
        }
        .step-desc {
            font-size: 0.825rem;
            color: rgba(255,255,255,0.4);
            line-height: 1.65;
            font-weight: 300;
        }

        /* ── ROLES ── */
        .roles-section {
            padding: 100px 2rem;
            background: var(--surface);
        }
        .roles-inner { max-width: 1200px; margin: 0 auto; }
        .roles-header { text-align: center; margin-bottom: 4rem; }
        .roles-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem;
        }
        .role-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.75rem 1.5rem;
            transition: all .2s;
        }
        .role-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 24px rgba(59,130,246,0.1);
            transform: translateY(-2px);
        }
        .role-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }
        .role-icon svg { width: 22px; height: 22px; }
        .ri-blue { background: var(--navy); color: white; }
        .ri-green { background: #065f46; color: white; }
        .ri-purple { background: #5b21b6; color: white; }
        .ri-slate { background: #334155; color: white; }
        .role-name {
            font-size: 0.95rem; font-weight: 600; color: var(--text);
            margin-bottom: 0.4rem;
        }
        .role-desc {
            font-size: 0.8rem; color: var(--muted); line-height: 1.55;
            margin-bottom: 1.1rem; font-weight: 300;
        }
        .role-features { list-style: none; }
        .role-features li {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.8rem; color: var(--muted);
            padding: 4px 0;
        }
        .role-features li::before {
            content: '';
            width: 5px; height: 5px;
            background: var(--accent);
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── CTA ── */
        .cta-section {
            padding: 100px 2rem;
            background: white;
        }
        .cta-inner {
            max-width: 700px; margin: 0 auto;
            text-align: center;
        }
        .cta-box {
            background: var(--navy);
            border-radius: 20px;
            padding: 4rem 3rem;
            position: relative;
            overflow: hidden;
        }
        .cta-box::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 60%);
        }
        .cta-box .section-heading { color: white; margin-bottom: 1rem; }
        .cta-box p { color: rgba(255,255,255,0.5); font-size: 1rem; margin-bottom: 2rem; font-weight: 300; line-height: 1.7; }
        .cta-btns { display: flex; gap: 1rem; justify-content: center; position: relative; z-index: 1; }
        .btn-cta-white {
            display: inline-flex; align-items: center; gap: 8px;
            background: white; color: var(--navy);
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-size: 0.925rem; font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-cta-white:hover { background: var(--accent-pale); transform: translateY(-1px); }
        .btn-cta-ghost {
            display: inline-flex; align-items: center; gap: 8px;
            background: transparent; color: rgba(255,255,255,0.65);
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-size: 0.925rem; font-weight: 400;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.15);
            transition: all .2s;
        }
        .btn-cta-ghost:hover { background: rgba(255,255,255,0.06); color: white; }

        /* ── FOOTER ── */
        footer {
            background: #050d1a;
            padding: 3.5rem 2rem 2rem;
        }
        .footer-inner { max-width: 1200px; margin: 0 auto; }
        .footer-top {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            padding-bottom: 2.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 1.5rem;
        }
        .footer-brand p {
            font-size: 0.8rem; color: rgba(255,255,255,0.3);
            line-height: 1.7; margin-top: 0.75rem; font-weight: 300;
        }
        .footer-col h5 {
            font-size: 0.75rem; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            margin-bottom: 1rem;
        }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 0.5rem; }
        .footer-col a {
            font-size: 0.825rem; color: rgba(255,255,255,0.4);
            text-decoration: none; transition: color .2s; font-weight: 300;
        }
        .footer-col a:hover { color: rgba(255,255,255,0.8); }
        .footer-bottom {
            display: flex; justify-content: space-between; align-items: center;
        }
        .footer-copy { font-size: 0.78rem; color: rgba(255,255,255,0.2); }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 3rem; }
            .hero-visual { display: none; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .how-steps { grid-template-columns: repeat(2, 1fr); gap: 2rem; }
            .how-steps::before { display: none; }
            .roles-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-top { grid-template-columns: 1fr 1fr; gap: 2rem; }
        }
        @media (max-width: 640px) {
            .features-grid { grid-template-columns: 1fr; }
            .roles-grid { grid-template-columns: 1fr; }
            .hero-stats { gap: 1.5rem; }
            .cta-btns { flex-direction: column; }
            .footer-top { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav>
        <div class="nav-inner">
            <a href="/" class="nav-logo">
                <div class="logo-mark">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <span class="logo-text">COAS Connect</span>
            </a>
            <ul class="nav-links">
                <li><a href="#fitur">Fitur</a></li>
                <li><a href="#cara-kerja">Cara Kerja</a></li>
                <li><a href="#peran">Peran</a></li>
                <li><a href="{{ route('login') }}">Masuk</a></li>
                <li><a href="{{ route('register') }}" class="btn-nav">Daftar</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-inner">
            <div>
                <div class="hero-badge">
                    <span class="dot"></span>
                    Platform Pendidikan Kedokteran
                </div>
                <h1>
                    Latih Kompetensi<br>
                    <em>Klinik</em> Secara<br>
                    Terstruktur
                </h1>
                <p>
                    COAS Connect menghubungkan koas, dokter pembimbing, dan pasien melalui satu platform terpadu — log klinis digital, evaluasi real-time, dan notifikasi WhatsApp otomatis.
                </p>
                <div class="hero-cta">
                    <a href="{{ route('register') }}" class="btn-primary">
                        Mulai Sekarang
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#fitur" class="btn-secondary">Pelajari Fitur</a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="stat-num">500+</div>
                        <div class="stat-label">Koas Aktif</div>
                    </div>
                    <div>
                        <div class="stat-num">50+</div>
                        <div class="stat-label">Dosen Pembimbing</div>
                    </div>
                    <div>
                        <div class="stat-num">2.5K+</div>
                        <div class="stat-label">Log Klinis</div>
                    </div>
                </div>
            </div>

            <!-- Hero Visual Card -->
            <div class="hero-visual">
                <div class="card-float card-float-progress">
                    <div class="prog-label">Pencapaian Kompetensi</div>
                    <div class="prog-bar-bg">
                        <div class="prog-bar-fill"></div>
                    </div>
                    <div class="prog-num">68 / 100 kasus</div>
                </div>
                <div class="card-main">
                    <div class="card-header">
                        <div class="card-avatar">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="card-title">Log Klinis Baru</div>
                            <div class="card-sub">2 menit yang lalu · KOAS-042</div>
                        </div>
                    </div>
                    <div class="card-field">
                        <div class="card-field-label">Pasien</div>
                        <div class="card-field-val">Bpk. Ahmad Wijaya, 54 th</div>
                    </div>
                    <div class="card-field">
                        <div class="card-field-label">Diagnosis Kerja</div>
                        <div class="card-field-val">Pneumonia Komunitas (CAP)</div>
                    </div>
                    <div class="card-field">
                        <div class="card-field-label">Tindakan</div>
                        <div class="card-field-val">Pemeriksaan fisik, Rontgen thorax</div>
                    </div>
                    <div class="card-tags">
                        <span class="tag tag-green">✓ Disetujui</span>
                        <span class="tag tag-blue">Interna</span>
                    </div>
                </div>
                <div class="card-float card-float-wa">
                    <div class="wa-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="wa-label">WhatsApp</div>
                        <div class="wa-sub">Notifikasi terkirim</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="fitur" class="features-section">
        <div class="features-inner">
            <div class="features-header">
                <div class="section-label">Fitur Unggulan</div>
                <h2 class="section-heading">Semua yang Anda Butuhkan<br>dalam Satu Platform</h2>
                <p class="section-sub" style="margin-top: .75rem;">Dirancang khusus untuk alur kerja pendidikan klinis Indonesia — dari input log hingga evaluasi akhir rotasi.</p>
            </div>
            <div class="features-grid">
                <div class="feature-cell">
                    <div class="feature-icon icon-blue">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div class="feature-title">Log Klinis Terstruktur</div>
                    <p class="feature-desc">Template standar medis untuk anamnesis, pemeriksaan fisik, diagnosis, dan rencana terapi dalam satu formulir yang rapi.</p>
                </div>
                <div class="feature-cell">
                    <div class="feature-icon icon-green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="feature-title">Integrasi WhatsApp</div>
                    <p class="feature-desc">Notifikasi real-time ke WhatsApp dosen pembimbing. Template otomatis untuk status log, evaluasi, dan jadwal konsultasi.</p>
                </div>
                <div class="feature-cell">
                    <div class="feature-icon icon-amber">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="feature-title">Pantau Kompetensi</div>
                    <p class="feature-desc">Lacak progress berdasarkan kurikulum. Visualisasi target vs. pencapaian dengan dashboard interaktif per rotasi.</p>
                </div>
                <div class="feature-cell">
                    <div class="feature-icon icon-purple">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="feature-title">Manajemen Pasien</div>
                    <p class="feature-desc">Sistem alokasi pasien ke koas secara terstruktur. Riwayat kunjungan dan dokumentasi tersimpan aman dan terorganisir.</p>
                </div>
                <div class="feature-cell">
                    <div class="feature-icon icon-rose">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div class="feature-title">Sistem Evaluasi</div>
                    <p class="feature-desc">Rubrik evaluasi terstandar dengan feedback konstruktif dari dosen pembimbing untuk pengembangan kompetensi berkelanjutan.</p>
                </div>
                <div class="feature-cell">
                    <div class="feature-icon icon-cyan">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="feature-title">Keamanan Data</div>
                    <p class="feature-desc">Enkripsi end-to-end untuk data pasien dengan role-based access control sesuai regulasi proteksi data kesehatan Indonesia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="cara-kerja" class="how-section">
        <div class="how-inner">
            <div class="how-header">
                <div class="section-label" style="justify-content: center; color: var(--accent-bright);">
                    Cara Kerja
                </div>
                <h2 class="section-heading">Alur Kerja yang Jelas<br>dari Awal Hingga Akhir</h2>
                <p class="section-sub" style="margin-top: .75rem; color: rgba(255,255,255,0.45);">Empat langkah sederhana untuk pengalaman pendidikan klinis yang efektif dan terukur.</p>
            </div>
            <div class="how-steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-title">Registrasi & Alokasi</div>
                    <p class="step-desc">Koas mendaftar dan ditempatkan ke rotasi klinis dengan dosen pembimbing yang relevan secara otomatis.</p>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-title">Input Log Klinis</div>
                    <p class="step-desc">Dokumentasi encounters pasien menggunakan template terstruktur melalui platform kapan saja dan di mana saja.</p>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-title">Evaluasi Dosen</div>
                    <p class="step-desc">Dosen menerima notifikasi WhatsApp, mereview log, dan memberikan feedback langsung di platform.</p>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <div class="step-title">Pantau Progress</div>
                    <p class="step-desc">Dashboard menampilkan pencapaian kompetensi real-time dan area yang masih perlu ditingkatkan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles -->
    <section id="peran" class="roles-section">
        <div class="roles-inner">
            <div class="roles-header">
                <div class="section-label">Peran</div>
                <h2 class="section-heading">Dirancang untuk Setiap<br>Pemangku Kepentingan</h2>
                <p class="section-sub" style="margin-top: .75rem;">Fitur yang disesuaikan dengan kebutuhan dan tanggung jawab masing-masing pengguna.</p>
            </div>
            <div class="roles-grid">
                <div class="role-card">
                    <div class="role-icon ri-blue">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="role-name">KOAS</div>
                    <p class="role-desc">Mahasiswa kedokteran yang menjalani pendidikan klinis di rumah sakit atau puskesmas.</p>
                    <ul class="role-features">
                        <li>Input log klinis harian</li>
                        <li>Kelola data pasien</li>
                        <li>Pantau progress rotasi</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon ri-green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="role-name">Dosen Pembimbing</div>
                    <p class="role-desc">Dokter spesialis yang membimbing dan mengevaluasi koas di lapangan klinis.</p>
                    <ul class="role-features">
                        <li>Review & evaluasi log</li>
                        <li>Berikan feedback</li>
                        <li>Notifikasi WhatsApp</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon ri-purple">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="role-name">Koordinator</div>
                    <p class="role-desc">Pengelola kurikulum, rotasi akademik, dan program studi kedokteran.</p>
                    <ul class="role-features">
                        <li>Kelola kurikulum</li>
                        <li>Monitoring cohort</li>
                        <li>Laporan analytics</li>
                    </ul>
                </div>
                <div class="role-card">
                    <div class="role-icon ri-slate">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="role-name">Administrator</div>
                    <p class="role-desc">Pengelola sistem, user management, dan konfigurasi teknis platform.</p>
                    <ul class="role-features">
                        <li>Manajemen user</li>
                        <li>Konfigurasi WhatsApp</li>
                        <li>Pengaturan sistem</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="cta-inner">
            <div class="cta-box">
                <h2 class="section-heading" style="position:relative;z-index:1;">Siap Memulai<br>Transformasi Klinis?</h2>
                <p style="position:relative;z-index:1;">Bergabunglah dengan ratusan institusi pendidikan kedokteran yang telah menggunakan COAS Connect.</p>
                <div class="cta-btns">
                    <a href="{{ route('register') }}" class="btn-cta-white">
                        Daftar Gratis
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#" class="btn-cta-ghost">Hubungi Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="/" class="nav-logo" style="text-decoration:none;">
                        <div class="logo-mark">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="logo-text">COAS Connect</span>
                    </a>
                    <p>Platform pendidikan kedokteran berbasis teknologi untuk mencetak dokter yang kompeten dan terstandar.</p>
                </div>
                <div class="footer-col">
                    <h5>Platform</h5>
                    <ul>
                        <li><a href="#fitur">Fitur</a></li>
                        <li><a href="#cara-kerja">Cara Kerja</a></li>
                        <li><a href="#peran">Peran</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>Resource</h5>
                    <ul>
                        <li><a href="#">Dokumentasi</a></li>
                        <li><a href="#">API Reference</a></li>
                        <li><a href="#">Bantuan</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>Kontak</h5>
                    <ul>
                        <li><a href="mailto:support@coasconnect.id">support@coasconnect.id</a></li>
                        <li><a href="tel:+622112345678">+62 21 1234 5678</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span class="footer-copy">&copy; {{ date('Y') }} COAS Connect. All rights reserved.</span>
                <span class="footer-copy">Dibuat untuk pendidikan kedokteran Indonesia</span>
            </div>
        </div>
    </footer>

</body>
</html>