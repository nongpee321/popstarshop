<!DOCTYPE html>
<html lang="th" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php($faviconUrl = asset('images/popstar-icon-64.png').'?v='.(file_exists(public_path('images/popstar-icon-64.png')) ? filemtime(public_path('images/popstar-icon-64.png')) : time()))
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <title>PopStar Gateway | {{ $companyName ?? 'บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด' }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');

        :root {
            --bg-canvas: #f8fafc;
            --bg-card: #ffffff;
            --bg-subtle: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-sub: #475569;
            --border-light: #e2e8f0;
            --border-card: rgba(226, 232, 240, 0.85);
            
            --brand-red: #d32f2f;
            --brand-red-dark: #b71c1c;
            --brand-red-light: #fee2e2;
            --brand-red-glow: rgba(211, 47, 47, 0.22);
            
            --brand-blue: #1e3a8a;
            --brand-blue-dark: #172554;
            --brand-blue-light: #dbeafe;
            --brand-blue-glow: rgba(30, 58, 138, 0.22);

            --accent-emerald: #059669;
            --accent-emerald-light: #d1fae5;
            --accent-amber: #d97706;
            --accent-amber-light: #fef3c7;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 10px 30px -5px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
            --shadow-hover: 0 20px 40px -10px rgba(15, 23, 42, 0.16), 0 8px 12px -4px rgba(15, 23, 42, 0.05);
        }

        [data-theme="dark"] {
            --bg-canvas: #090d16;
            --bg-card: #131b2e;
            --bg-subtle: #1a243b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-sub: #cbd5e1;
            --border-light: #1e293b;
            --border-card: rgba(51, 65, 85, 0.65);
            
            --brand-red: #ef4444;
            --brand-red-dark: #dc2626;
            --brand-red-light: rgba(239, 68, 68, 0.15);
            --brand-red-glow: rgba(239, 68, 68, 0.35);
            
            --brand-blue: #3b82f6;
            --brand-blue-dark: #2563eb;
            --brand-blue-light: rgba(59, 130, 246, 0.15);
            --brand-blue-glow: rgba(59, 130, 246, 0.35);

            --accent-emerald: #10b981;
            --accent-emerald-light: rgba(16, 185, 129, 0.15);
            --accent-amber: #f59e0b;
            --accent-amber-light: rgba(245, 158, 11, 0.15);

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.4);
            --shadow-md: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
            --shadow-hover: 0 20px 40px -10px rgba(0, 0, 0, 0.7);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease;
        }

        body {
            font-family: 'IBM Plex Sans Thai', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Ambient Background Gradients */
        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.85;
        }
        .ambient-blob-1 {
            position: absolute;
            top: -10vw;
            left: -10vw;
            width: 45vw;
            height: 45vw;
            max-width: 600px;
            max-height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--brand-red-glow) 0%, transparent 70%);
            filter: blur(55px);
        }
        .ambient-blob-2 {
            position: absolute;
            bottom: -10vw;
            right: -10vw;
            width: 50vw;
            height: 50vw;
            max-width: 700px;
            max-height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--brand-blue-glow) 0%, transparent 70%);
            filter: blur(65px);
        }

        /* Top Navigation Header */
        .top-nav {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 2rem;
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
        }
        .brand-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
        }
        .brand-logo-img {
            height: 48px;
            width: auto;
            max-width: 54px;
            object-fit: contain;
            border-radius: 10px;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.18));
            transition: transform 0.2s ease;
        }
        .brand-header:hover .brand-logo-img {
            transform: scale(1.05);
        }
        .brand-text h1 {
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1.2;
            color: var(--text-main);
        }
        .brand-text p {
            font-size: 0.82rem;
            color: var(--text-muted);
            letter-spacing: 0.02em;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .theme-toggle-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            color: var(--text-main);
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            font-size: 1.15rem;
        }
        .theme-toggle-btn:hover {
            background: var(--bg-subtle);
            border-color: var(--text-muted);
        }

        /* Hero Section */
        .portal-container {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 3.5rem;
            width: 100%;
        }

        .hero-title-area {
            text-align: center;
            margin-bottom: 2.5rem;
            max-width: 820px;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: var(--brand-red-light);
            color: var(--brand-red);
            font-size: 0.82rem;
            font-weight: 700;
            border-radius: 9999px;
            margin-bottom: 1rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .badge-pill i {
            font-size: 0.95rem;
        }
        .hero-title {
            font-size: clamp(1.85rem, 3.2vw, 2.65rem);
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
            color: var(--text-main);
        }
        .hero-subtitle {
            font-size: clamp(0.95rem, 1.35vw, 1.12rem);
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* 4 Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.75rem;
            width: 100%;
            max-width: 1240px;
            margin-bottom: 2.5rem;
        }

        .portal-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 24px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), border-color 0.25s ease;
        }

        .portal-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        /* Card 1: ERP Accent */
        .portal-card.card-erp:hover {
            border-color: rgba(211, 47, 47, 0.45);
        }
        .portal-card.card-erp::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #b71c1c, #ef4444);
        }

        /* Card 2: Storefront Accent */
        .portal-card.card-store:hover {
            border-color: rgba(30, 58, 138, 0.45);
        }
        .portal-card.card-store::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3a8a, #3b82f6);
        }

        /* Card 3: POS App Download Accent */
        .portal-card.card-download:hover {
            border-color: rgba(16, 185, 129, 0.45);
        }
        .portal-card.card-download::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #059669, #10b981);
        }

        /* Card 4: Manual Guide Accent */
        .portal-card.card-manual:hover {
            border-color: rgba(217, 119, 6, 0.45);
        }
        .portal-card.card-manual::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #d97706, #f59e0b);
        }

        .card-top {
            margin-bottom: 1.5rem;
        }

        .card-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.25rem;
        }

        .card-icon-wrap {
            width: 62px;
            height: 62px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
        }
        .card-icon-wrap img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 10px;
        }
        .card-icon-wrap i {
            font-size: 1.85rem;
        }

        .card-erp .card-icon-wrap {
            background: linear-gradient(135deg, #fff5f5, #ffe4e6);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        [data-theme="dark"] .card-erp .card-icon-wrap {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .card-store .card-icon-wrap {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        [data-theme="dark"] .card-store .card-icon-wrap {
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .card-download .card-icon-wrap {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #059669;
        }
        [data-theme="dark"] .card-download .card-icon-wrap {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .card-manual .card-icon-wrap {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1px solid rgba(217, 119, 6, 0.2);
            color: #d97706;
        }
        [data-theme="dark"] .card-manual .card-icon-wrap {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #f59e0b;
        }

        .system-status-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            position: relative;
        }
        .status-dot::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background-color: #10b981;
            opacity: 0.4;
            animation: pulse-ring 1.8s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.7; }
            50% { transform: scale(1.6); opacity: 0; }
            100% { transform: scale(0.95); opacity: 0; }
        }

        .status-erp { background: rgba(16, 185, 129, 0.12); color: #059669; }
        [data-theme="dark"] .status-erp { color: #10b981; }
        .status-store { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
        [data-theme="dark"] .status-store { color: #3b82f6; }
        .status-download { background: rgba(16, 185, 129, 0.12); color: #059669; }
        [data-theme="dark"] .status-download { color: #10b981; }
        .status-manual { background: rgba(245, 158, 11, 0.12); color: #d97706; }
        [data-theme="dark"] .status-manual { color: #f59e0b; }

        .card-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }
        .card-tagline {
            font-size: 0.86rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .card-desc {
            font-size: 0.92rem;
            color: var(--text-sub);
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 1.5rem;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            font-size: 0.86rem;
            color: var(--text-sub);
            line-height: 1.45;
        }
        .feature-item i {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .card-erp .feature-item i { color: var(--brand-red); }
        .card-store .feature-item i { color: var(--brand-blue); }
        .card-download .feature-item i { color: var(--accent-emerald); }
        .card-manual .feature-item i { color: var(--accent-amber); }

        /* Resource Meta Pill */
        .url-preview-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--bg-subtle);
            border: 1px solid var(--border-light);
            padding: 0.4rem 0.8rem;
            border-radius: 10px;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
            font-family: 'Plus Jakarta Sans', monospace;
            word-break: break-all;
        }
        .url-preview-pill i { font-size: 0.9rem; }

        /* Action Buttons */
        .btn-launch {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            width: 100%;
            padding: 0.9rem 1.4rem;
            font-size: 0.98rem;
            font-weight: 700;
            border-radius: 14px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }
        .btn-launch:active { transform: scale(0.98); }

        .btn-erp {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            color: #ffffff;
        }
        .btn-erp:hover {
            background: linear-gradient(135deg, #e53935, #c62828);
            box-shadow: 0 6px 20px rgba(211, 47, 47, 0.35);
            color: #ffffff;
        }

        .btn-store {
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
            color: #ffffff;
        }
        .btn-store:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.35);
            color: #ffffff;
        }

        .btn-download {
            background: linear-gradient(135deg, #059669, #047857);
            color: #ffffff;
        }
        .btn-download:hover {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            color: #ffffff;
        }

        .btn-manual {
            background: linear-gradient(135deg, #d97706, #b45309);
            color: #ffffff;
        }
        .btn-manual:hover {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.35);
            color: #ffffff;
        }

        /* Footer */
        .portal-footer {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-card);
            width: 100%;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.85rem;
        }
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }
        .footer-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.86rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-link:hover { color: var(--brand-red); }
        .footer-copy {
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        @media (max-width: 992px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .top-nav { padding: 1rem; }
            .portal-card { padding: 1.6rem 1.35rem; }
            .hero-title { font-size: 1.65rem; }
            .brand-text h1 { font-size: 1.05rem; }
        }

        /* Intro Splash Overlay */
        .portal-intro-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: var(--bg-canvas); /* Matches theme */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.8s;
        }

        .portal-intro-overlay.intro-dismissed {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .intro-box {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .intro-logo-container {
            position: relative;
            margin-bottom: 2rem;
            animation: introLogoZoom 1.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes introLogoZoom {
            0% { transform: scale(0.7); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        .intro-logo-img {
            position: relative;
            z-index: 1;
            width: 180px;
            height: auto;
            max-height: 180px;
            object-fit: contain;
            filter: drop-shadow(0 15px 35px rgba(0, 0, 0, 0.15));
            animation: introLogoFloat 4s ease-in-out infinite alternate;
        }

        @keyframes introLogoFloat {
            0% { transform: translateY(0); }
            100% { transform: translateY(-8px); }
        }

        [data-theme="dark"] .intro-logo-img {
            filter: drop-shadow(0 15px 35px rgba(0, 0, 0, 0.6));
        }

        .intro-text-wrapper {
            animation: introTextFade 1.2s cubic-bezier(0.16, 1, 0.3, 1) 0.5s both;
        }

        @keyframes introTextFade {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .intro-title {
            color: var(--text-main);
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .intro-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            letter-spacing: 0.05em;
            margin-bottom: 2rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .intro-loader {
            width: 44px;
            height: 44px;
            border: 3px solid var(--border-light);
            border-top-color: var(--brand-red);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
            opacity: 0.85;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .intro-skip-btn {
            margin-top: 2.5rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.2s;
            animation: introTextFade 1.2s cubic-bezier(0.16, 1, 0.3, 1) 1.2s both;
        }

        .intro-skip-btn:hover {
            color: var(--text-main);
        }
    </style>
</head>
<body>
    <!-- Cinematic Intro Splash Screen -->
    <div id="portalIntroOverlay" class="portal-intro-overlay" onclick="dismissIntro()">
        <div class="intro-box">
            <div class="intro-logo-container">
                <img src="{{ asset('images/intro-star-logo.png') }}?v={{ time() }}" alt="PopStar Logo" class="intro-logo-img">
            </div>
            
            <div class="intro-text-wrapper">
                <h2 class="intro-title">{{ $companyName ?? 'บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด' }}</h2>
                <p class="intro-subtitle">Digital Gateway System</p>
                
                <div class="intro-loader"></div>
            </div>
        </div>
    </div>

    <div class="ambient-bg">
        <div class="ambient-blob-1"></div>
        <div class="ambient-blob-2"></div>
    </div>

    <!-- Navigation Header -->
    <header class="top-nav">
        <a href="{{ route('portal') }}" class="brand-header">
            <img src="{{ asset('images/intro-star-logo.png') }}?v={{ time() }}" alt="PopStar Logo" class="brand-logo-img">
            <div class="brand-text">
                <h1>{{ $companyName ?? 'บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด' }}</h1>
                <p>{{ $companyNameEn ?? 'PopStar Food Trading Co., Ltd.' }}</p>
            </div>
        </a>

        <div class="header-actions">
            <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="สลับโหมดมืด / โหมดสว่าง" aria-label="Toggle theme">
                <i class="bi bi-moon-stars" id="themeIcon"></i>
            </button>
        </div>
    </header>

    <!-- Main Portal Area -->
    <main class="portal-container">
        <div class="hero-title-area">
            <div class="badge-pill">
                <i class="bi bi-shield-check"></i>
                PopStar Gateway & Resource Hub
            </div>
            <h2 class="hero-title">ศูนย์รวมระบบงานและบริการออนไลน์</h2>
            <p class="hero-subtitle">
                เข้าสู่ระบบ ERP หลังบ้าน, สั่งซื้อสินค้าทางเว็บหลัก, ดาวน์โหลดตัวติดตั้งแอป POS หรือศึกษาคู่มือการใช้งานระบบ
            </p>
        </div>

        <div class="cards-grid">
            <!-- Card 1: ERP & JET POS -->
            <div class="portal-card card-erp">
                <div class="card-top">
                    <div class="card-header-bar">
                        <div class="card-icon-wrap">
                            <img src="{{ asset('images/popstar-icon-128.png') }}?v={{ time() }}" alt="PopStar ERP">
                        </div>
                        <div class="system-status-chip status-erp">
                            <span class="status-dot"></span>
                            ระบบพร้อมใช้งาน (Core)
                        </div>
                    </div>

                    <h3 class="card-title">ระบบ ERP & JET POS</h3>
                    <p class="card-tagline">เข้าสู่ระบบ - บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด</p>
                    <p class="card-desc">
                        ระบบจัดการธุรกิจหลังบ้านแบบครบวงจร สำหรับผู้บริหาร พนักงานขาย คลังสินค้า และฝ่ายบัญชี
                    </p>

                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="bi bi-cart-check-fill"></i>
                            <span><strong>จุดขายหน้าร้าน (JET POS):</strong> ขายรวดเร็ว ชั่งน้ำหนัก & ออกใบเสร็จ</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-boxes"></i>
                            <span><strong>คลังสินค้า & สต็อก:</strong> ตรวจนับ โอนย้ายข้ามสาขา และจัดซื้อสินค้า</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span><strong>บัญชีและการเงิน:</strong> สรุปยอดขาย ปิดกะ รายงานภาษี และกำไรขาดทุน</span>
                        </li>
                    </ul>

                    <div class="url-preview-pill">
                        <i class="bi bi-link-45deg"></i>
                        <span>http://localhost:8000/login</span>
                    </div>
                </div>

                <div class="card-bottom">
                    <a href="{{ route('login') }}" class="btn-launch btn-erp">
                        <span>เข้าสู่ระบบ ERP & POS</span>
                        <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: PopStarShops.com -->
            <div class="portal-card card-store">
                <div class="card-top">
                    <div class="card-header-bar">
                        <div class="card-icon-wrap">
                            <img src="{{ asset('images/popstar-shop-logo.png') }}" onerror="this.src='{{ asset('images/logos/popstar-business.png') }}'" alt="PopStar Shop">
                        </div>
                        <div class="system-status-chip status-store">
                            <i class="bi bi-globe2"></i>
                            เว็บไซต์หลัก (Official)
                        </div>
                    </div>

                    <h3 class="card-title">PopStarShops.com</h3>
                    <p class="card-tagline">เว็บไซต์ทางการ & ร้านค้าออนไลน์ ปลีก-ส่ง</p>
                    <p class="card-desc">
                        เว็บไซต์หลักของ ป๊อบสตาร์ฟู้ดเทรดดิ้ง ช้อปสินค้าแช่แข็งคุณภาพสูง อาหารแปรรูป สั่งซื้อออนไลน์ได้ตลอด 24 ชั่วโมง
                    </p>

                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="bi bi-basket3-fill"></i>
                            <span><strong>สินค้าแช่แข็งครบวงจร:</strong> หมู ไก่ เนื้อ อาหารทะเล และของสดพรีเมียม</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-truck"></i>
                            <span><strong>จัดส่งควบคุมอุณหภูมิ:</strong> ขนส่งความเย็นถึงหน้าบ้าน สดใหม่ ปลอดภัย</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-tags-fill"></i>
                            <span><strong>ราคาส่งผู้ประกอบการ:</strong> สั่งซื้อยกลัง ยกร้าน พร้อมโปรโมชั่นประจำเดือน</span>
                        </li>
                    </ul>

                    <div class="url-preview-pill">
                        <i class="bi bi-link-45deg"></i>
                        <span>https://popstarshops.com/</span>
                    </div>
                </div>

                <div class="card-bottom">
                    <a href="https://popstarshops.com/" target="_blank" rel="noopener noreferrer" class="btn-launch btn-store">
                        <span>ไปยังเว็บไซต์ PopStarShops.com</span>
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: Download POS App -->
            <div class="portal-card card-download">
                <div class="card-top">
                    <div class="card-header-bar">
                        <div class="card-icon-wrap">
                            <i class="bi bi-display"></i>
                        </div>
                        <div class="system-status-chip status-download">
                            <i class="bi bi-windows"></i>
                            Windows App (v{{ $posInstaller['version'] ?? '1.12.23' }})
                        </div>
                    </div>

                    <h3 class="card-title">ดาวน์โหลดแอป JET POS</h3>
                    <p class="card-tagline">โปรแกรมแคชเชียร์สำหรับติดตั้งบนเครื่องจุดขาย (Desktop App)</p>
                    <p class="card-desc">
                        แอปพลิเคชันสำหรับเครื่องคิดเงินหน้าร้าน ทำงานได้เสถียรทั้งแบบ Online และ Offline รองรับสแกนบาร์โค้ด ตาชั่ง และเครื่องพิมพ์ใบเสร็จ
                    </p>

                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="bi bi-wifi-off"></i>
                            <span><strong>ขายออฟไลน์ได้ (Offline Ready):</strong> อินเทอร์เน็ตหลุดก็ยังเปิดบิลขายได้ตามปกติ</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-printer"></i>
                            <span><strong>เชื่อมฮาร์ดแวร์แคชเชียร์:</strong> ปริ้นท์ใบเสร็จ ลิ้นชักเก็บเงิน และเครื่องชั่งน้ำหนัก</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                            <span><strong>ไฟล์ติดตั้ง:</strong> {{ $posInstaller['filename'] ?? 'PopCentral-POS-UAT-1.12.23-setup.exe' }} ({{ $posInstaller['size_mb'] ?? '43.7' }} MB)</span>
                        </li>
                    </ul>

                    <div class="url-preview-pill">
                        <i class="bi bi-download"></i>
                        <span>{{ $posInstaller['filename'] ?? 'PopCentral-POS-UAT-1.12.23-setup.exe' }}</span>
                    </div>
                </div>

                <div class="card-bottom">
                    <a href="{{ route('python-pos.download') }}" class="btn-launch btn-download">
                        <span>ดาวน์โหลดโปรแกรม POS (Windows)</span>
                        <i class="bi bi-cloud-arrow-down-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Card 4: User Manual & Visual Guide -->
            <div class="portal-card card-manual">
                <div class="card-top">
                    <div class="card-header-bar">
                        <div class="card-icon-wrap">
                            <i class="bi bi-book-half"></i>
                        </div>
                        <div class="system-status-chip status-manual">
                            <i class="bi bi-camera-fill"></i>
                            Visual Guide พร้อมภาพ
                        </div>
                    </div>

                    <h3 class="card-title">คู่มือการใช้งานระบบ</h3>
                    <p class="card-tagline">ขั้นตอนการปฏิบัติงาน 4M & ภาพประกอบแบบละเอียด</p>
                    <p class="card-desc">
                        คลังคู่มือมาตรฐานการทำงานสำหรับพนักงานทุกตำแหน่ง มีภาพหน้าจอจริง วงกรอบแดงพร้อมตัวเลขขั้นตอน 1-2-3 เข้าใจง่าย
                    </p>

                    <ul class="feature-list">
                        <li class="feature-item">
                            <i class="bi bi-receipt"></i>
                            <span><strong>วิธีขายหน้าร้าน (POS):</strong> เปิดกะ สแกนบาร์โค้ด รับเงิน และพิมพ์รายงานปิดกะ</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-box-seam"></i>
                            <span><strong>วิธีจัดการสินค้า & คลัง:</strong> เพิ่มสินค้า ปรับราคา และโอนย้ายสต็อกสาขา</span>
                        </li>
                        <li class="feature-item">
                            <i class="bi bi-images"></i>
                            <span><strong>ภาพจับหน้าจอจริง:</strong> ขยายดูรูปใหญ่ได้ มีเลขลำดับกำกับทุกขั้นตอน</span>
                        </li>
                    </ul>

                    <div class="url-preview-pill">
                        <i class="bi bi-link-45deg"></i>
                        <span>http://localhost:8000/manual</span>
                    </div>
                </div>

                <div class="card-bottom">
                    <a href="{{ route('manual') }}" class="btn-launch btn-manual">
                        <span>เปิดอ่านคู่มือการใช้งานระบบ</span>
                        <i class="bi bi-journal-text"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="portal-footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="{{ route('login') }}" class="footer-link">
                    <i class="bi bi-lock-fill"></i> เข้าสู่ระบบ ERP
                </a>
                <a href="https://popstarshops.com/" target="_blank" rel="noopener noreferrer" class="footer-link">
                    <i class="bi bi-shop"></i> ร้านค้าออนไลน์ PopStarShops.com
                </a>
                <a href="{{ route('python-pos.download') }}" class="footer-link">
                    <i class="bi bi-display"></i> ดาวน์โหลดแอป POS
                </a>
                <a href="{{ route('manual') }}" class="footer-link">
                    <i class="bi bi-book-half"></i> คู่มือการใช้งานระบบ
                </a>
            </div>
            <p class="footer-copy">
                &copy; {{ date('Y') }} {{ $companyName ?? 'บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด' }} (PopStar Food Trading Co., Ltd.) สงวนลิขสิทธิ์ทุกประการ
            </p>
        </div>
    </footer>

    <script>
        // Dark Mode Logic
        (function() {
            const html = document.documentElement;
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            
            // Check stored theme or prefers-color-scheme
            const savedTheme = localStorage.getItem('popstar_theme') || 
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            
            applyTheme(savedTheme);

            function applyTheme(theme) {
                html.setAttribute('data-theme', theme);
                localStorage.setItem('popstar_theme', theme);
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill';
                } else {
                    themeIcon.className = 'bi bi-moon-stars-fill';
                }
            }

            themeToggleBtn.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        })();

        // Cinematic Intro Logic
        let introTimer = null;
        function playIntro() {
            const overlay = document.getElementById('portalIntroOverlay');
            if (!overlay) return;

            // Clear previous timers
            if (introTimer) clearTimeout(introTimer);

            // Reset states
            overlay.classList.remove('intro-dismissed');

            // Automatically dismiss after 2.8 seconds
            introTimer = setTimeout(() => {
                dismissIntro();
            }, 2800);
        }

        function dismissIntro() {
            if (introTimer) {
                clearTimeout(introTimer);
                introTimer = null;
            }
            const overlay = document.getElementById('portalIntroOverlay');
            if (overlay) {
                overlay.classList.add('intro-dismissed');
            }
        }

        // Auto play intro on first visit/load
        document.addEventListener('DOMContentLoaded', function() {
            playIntro();
        });
    </script>
</body>
</html>
