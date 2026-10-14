<!DOCTYPE html>
<html lang="th" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php($faviconUrl = asset('images/popstar-icon-64.png').'?v='.(file_exists(public_path('images/popstar-icon-64.png')) ? filemtime(public_path('images/popstar-icon-64.png')) : time()))
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <title>คู่มือการใช้งานระบบ | บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');

        :root {
            --bg-canvas: #f8fafc;
            --bg-card: #ffffff;
            --bg-subtle: #f1f5f9;
            --bg-sidebar: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-sub: #334155;
            --border-light: #e2e8f0;
            --border-card: rgba(226, 232, 240, 0.9);
            
            --brand-red: #d32f2f;
            --brand-red-dark: #b71c1c;
            --brand-red-light: #fee2e2;
            --brand-red-glow: rgba(211, 47, 47, 0.2);

            --brand-blue: #1e3a8a;
            --brand-blue-light: #dbeafe;
            
            --accent-emerald: #059669;
            --accent-emerald-light: #d1fae5;
            --accent-amber: #d97706;
            --accent-amber-light: #fef3c7;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
            --shadow-lg: 0 16px 36px -6px rgba(15, 23, 42, 0.12);
        }

        [data-theme="dark"] {
            --bg-canvas: #0f172a;
            --bg-card: #1e293b;
            --bg-subtle: #243044;
            --bg-sidebar: #090e17;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --text-sub: #cbd5e1;
            --border-light: #273549;
            --border-card: #334155;
            
            --brand-red: #f43f5e;
            --brand-red-dark: #e11d48;
            --brand-red-light: rgba(244, 63, 94, 0.15);
            --brand-red-glow: rgba(244, 63, 94, 0.25);

            --brand-blue: #38bdf8;
            --brand-blue-light: rgba(56, 189, 248, 0.15);
            
            --accent-emerald: #10b981;
            --accent-emerald-light: rgba(16, 185, 129, 0.15);
            --accent-amber: #f59e0b;
            --accent-amber-light: rgba(245, 158, 11, 0.15);

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
            --shadow-md: 0 8px 24px -4px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 16px 36px -6px rgba(0, 0, 0, 0.5);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        body {
            font-family: 'IBM Plex Sans Thai', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Bar */
        .manual-topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: inherit;
        }
        .topbar-logo {
            height: 44px;
            width: 44px;
            border-radius: 11px;
            object-fit: cover;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }
        .topbar-title h1 {
            font-size: 1.08rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text-main);
        }
        .topbar-title p {
            font-size: 0.76rem;
            color: var(--text-muted);
            letter-spacing: 0.02em;
        }

        /* Search & Actions */
        .topbar-search-wrap {
            flex: 1;
            max-width: 440px;
            position: relative;
        }
        .topbar-search-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .topbar-search-input {
            width: 100%;
            padding: 0.55rem 1rem 0.55rem 2.4rem;
            border-radius: 12px;
            border: 1px solid var(--border-light);
            background: var(--bg-subtle);
            color: var(--text-main);
            font-size: 0.88rem;
            font-family: inherit;
            outline: none;
        }
        .topbar-search-input:focus {
            border-color: var(--brand-red);
            background: var(--bg-card);
            box-shadow: 0 0 0 3px var(--brand-red-light);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .btn-topbar {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.52rem 0.95rem;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid var(--border-light);
            background: var(--bg-card);
            color: var(--text-main);
            box-shadow: var(--shadow-sm);
        }
        .btn-topbar:hover {
            background: var(--bg-subtle);
            border-color: var(--text-muted);
        }
        .btn-topbar.btn-portal {
            background: var(--brand-red-light);
            color: var(--brand-red);
            border-color: transparent;
        }
        .btn-topbar.btn-portal:hover {
            background: var(--brand-red);
            color: #ffffff;
        }
        .btn-theme {
            width: 38px;
            height: 38px;
            padding: 0;
            display: grid;
            place-items: center;
            font-size: 1.1rem;
        }

        /* Layout Container */
        .manual-layout {
            display: flex;
            flex: 1;
            max-width: 1560px;
            width: 100%;
            margin: 0 auto;
        }

        /* Sidebar Navigation */
        .manual-sidebar {
            width: 290px;
            flex-shrink: 0;
            position: sticky;
            top: 61px;
            height: calc(100vh - 61px);
            overflow-y: auto;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-light);
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .nav-group-title {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            padding: 0.25rem 0.75rem;
            margin-bottom: 0.35rem;
        }

        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.85rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-sub);
            text-decoration: none;
            cursor: pointer;
            margin-bottom: 2px;
        }
        .nav-link-item i {
            font-size: 1.05rem;
            color: var(--text-muted);
        }
        .nav-link-item:hover {
            background: var(--bg-subtle);
            color: var(--text-main);
        }
        .nav-link-item.active {
            background: var(--brand-red-light);
            color: var(--brand-red);
            font-weight: 700;
        }
        .nav-link-item.active i {
            color: var(--brand-red);
        }

        /* Main Content Canvas */
        .manual-content {
            flex: 1;
            min-width: 0;
            padding: 2.25rem clamp(1rem, 3vw, 3rem);
            max-width: 1160px;
        }

        .content-header-banner {
            background: linear-gradient(135deg, rgba(211, 47, 47, 0.08), rgba(30, 58, 138, 0.06));
            border: 1px solid var(--border-light);
            border-radius: 20px;
            padding: 2rem 2.25rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .content-header-banner h2 {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 0.6rem;
            color: var(--text-main);
        }
        .content-header-banner p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 780px;
        }

        /* Section Headings */
        .manual-section {
            margin-bottom: 3.5rem;
            scroll-margin-top: 80px;
        }
        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-light);
        }
        .section-head-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .section-icon-badge {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--brand-red-light);
            color: var(--brand-red);
            display: grid;
            place-items: center;
            font-size: 1.15rem;
        }
        .section-head h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
        }
        .section-tag {
            font-size: 0.8rem;
            color: var(--text-muted);
            background: var(--bg-subtle);
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
        }

        /* Visual Guide Cards (4 Cards Grid) */
        .vg-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .vg-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .vg-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--brand-red);
        }
        .vg-thumb {
            position: relative;
            aspect-ratio: 16 / 9;
            background: var(--bg-subtle);
            overflow: hidden;
        }
        .vg-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            transition: transform 0.3s ease;
        }
        .vg-card:hover .vg-thumb img {
            transform: scale(1.04);
        }
        .vg-badge-step {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(4px);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
        }
        .vg-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: space-between;
        }
        .vg-title {
            font-size: 1.12rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.45rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vg-desc {
            font-size: 0.86rem;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 1.25rem;
            flex: 1;
        }
        .vg-btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.65rem 1rem;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 700;
            border: none;
            background: var(--brand-red-light);
            color: var(--brand-red);
            cursor: pointer;
            transition: all 0.2s;
        }
        .vg-card:hover .vg-btn-action {
            background: var(--brand-red);
            color: #ffffff;
        }

        /* Interactive Reader Modal */
        .reader-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.78);
            backdrop-filter: blur(6px);
            display: none;
            place-items: center;
            padding: 1.5rem;
            overflow-y: auto;
        }
        .reader-card {
            background: var(--bg-card);
            border-radius: 24px;
            width: min(1080px, 100%);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-card);
            animation: popIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes popIn {
            from { transform: scale(0.96); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .reader-head {
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-subtle);
        }
        .reader-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .reader-close {
            background: transparent;
            border: none;
            font-size: 1.35rem;
            color: var(--text-muted);
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 8px;
        }
        .reader-close:hover {
            background: var(--border-light);
            color: var(--text-main);
        }

        .reader-step-tabs {
            display: flex;
            overflow-x: auto;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border-bottom: 1px solid var(--border-light);
            background: var(--bg-card);
        }
        .reader-tab-btn {
            padding: 0.45rem 0.95rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            border: 1px solid var(--border-light);
            background: var(--bg-subtle);
            color: var(--text-muted);
            cursor: pointer;
            white-space: nowrap;
        }
        .reader-tab-btn.active {
            background: var(--brand-red);
            border-color: var(--brand-red);
            color: #ffffff;
        }

        .reader-body {
            padding: 1.5rem 1.75rem;
            overflow-y: auto;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(300px, 0.8fr);
            gap: 1.75rem;
            align-items: start;
        }

        .reader-img-wrap {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border-light);
            background: #000;
            position: relative;
            box-shadow: var(--shadow-sm);
        }
        .reader-img-wrap img {
            width: 100%;
            height: auto;
            display: block;
            cursor: zoom-in;
        }
        .reader-img-tip {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0,0,0,0.7);
            color: #fff;
            font-size: 0.72rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            pointer-events: none;
        }

        .reader-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .reader-step-badge {
            display: inline-block;
            align-self: flex-start;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            background: var(--brand-red-light);
            color: var(--brand-red);
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .reader-step-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.35;
        }
        .reader-step-desc {
            font-size: 0.9rem;
            color: var(--text-sub);
            line-height: 1.6;
            background: var(--bg-subtle);
            padding: 1.15rem;
            border-radius: 12px;
            border-left: 4px solid var(--brand-red);
        }
        .reader-step-desc strong {
            color: var(--text-main);
        }
        .reader-tip-box {
            background: var(--accent-amber-light);
            border: 1px solid rgba(217, 119, 6, 0.25);
            border-radius: 12px;
            padding: 0.95rem 1.1rem;
            font-size: 0.85rem;
            color: #92400e;
            display: flex;
            gap: 0.65rem;
            line-height: 1.5;
        }
        [data-theme="dark"] .reader-tip-box {
            color: #fde68a;
        }

        .reader-foot {
            padding: 1rem 1.75rem;
            border-top: 1px solid var(--border-light);
            background: var(--bg-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .reader-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 1.1rem;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 700;
            border: 1px solid var(--border-light);
            background: var(--bg-card);
            color: var(--text-main);
            cursor: pointer;
        }
        .reader-nav-btn:hover:not(:disabled) {
            border-color: var(--brand-red);
            color: var(--brand-red);
        }
        .reader-nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .reader-counter {
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        /* 4M Pillars Grid */
        .pillar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .pillar-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.35rem;
            box-shadow: var(--shadow-sm);
        }
        .pillar-head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .pillar-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 1.15rem;
        }
        .pillar-man { background: #fee2e2; color: #dc2626; }
        .pillar-machine { background: #dbeafe; color: #2563eb; }
        .pillar-material { background: #d1fae5; color: #059669; }
        .pillar-money { background: #fef3c7; color: #d97706; }
        .pillar-title { font-size: 1.05rem; font-weight: 800; color: var(--text-main); }
        .pillar-desc { font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; }

        /* Workflows & Routines Tables */
        .manual-table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .docs-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.88rem;
        }
        .docs-table th {
            background: var(--bg-subtle);
            color: var(--text-muted);
            font-weight: 800;
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid var(--border-light);
            text-transform: uppercase;
            font-size: 0.74rem;
            letter-spacing: 0.05em;
        }
        .docs-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-sub);
            vertical-align: top;
            line-height: 1.55;
        }
        .docs-table tr:last-child td {
            border-bottom: none;
        }
        .docs-table strong {
            color: var(--text-main);
            font-weight: 700;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }
            .manual-topbar, .manual-sidebar, .topbar-actions, .vg-btn-action, .reader-backdrop {
                display: none !important;
            }
            body, .manual-content {
                background: #fff !important;
                color: #000 !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .content-header-banner {
                border: 1px solid #ccc !important;
                background: #f9f9f9 !important;
            }
            .vg-card {
                page-break-inside: avoid;
                border: 1px solid #ccc !important;
            }
        }

        @media (max-width: 900px) {
            .manual-sidebar { display: none; }
            .manual-topbar { padding: 0.75rem 1rem; }
            .manual-content { padding: 1.5rem 1rem; }
            .reader-body { grid-template-columns: 1fr; }
        }

        /* System Architecture & Overview Section Styles */
        .diagram-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .diagram-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-light);
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .diagram-card-head h4 {
            font-size: 1.18rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .diagram-card-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }
        .mermaid-container {
            background: var(--bg-subtle);
            border: 1px solid var(--border-light);
            border-radius: 14px;
            padding: 1.25rem;
            overflow-x: auto;
            text-align: center;
            min-height: 180px;
        }
        .mermaid-container .mermaid {
            display: flex;
            justify-content: center;
        }
        .mermaid-container svg {
            max-width: 100% !important;
            height: auto !important;
        }
        .info-callout {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.07), rgba(16, 185, 129, 0.05));
            border-left: 4px solid var(--brand-blue);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1.15rem;
            align-items: flex-start;
        }
        .info-callout-icon {
            font-size: 1.75rem;
            color: var(--brand-blue);
            line-height: 1;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .info-callout-content h4 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.35rem;
        }
        .info-callout-content p {
            font-size: 0.88rem;
            color: var(--text-sub);
            line-height: 1.6;
            margin: 0;
        }
        .badge-arch-layer {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            background: var(--brand-blue-light);
            color: var(--brand-blue);
        }

    </style>
</head>
<body>
    <!-- Standalone Documentation Topbar -->
    <header class="manual-topbar">
        <a href="{{ route('manual') }}" class="topbar-brand">
            <img src="{{ asset('images/popstar-icon-128.png') }}?v={{ time() }}" alt="PopStar Logo" class="topbar-logo">
            <div class="topbar-title">
                <h1>คู่มือการใช้งานระบบ (PopStar Knowledge Base)</h1>
                <p>บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด • คู่มือปฏิบัติงานและ Visual Guide</p>
            </div>
        </a>

        <div class="topbar-search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="manualSearchInput" class="topbar-search-input" placeholder="ค้นหาคู่มือ, ขั้นตอนการทำงาน, หรือสูตรคำนวณ..." onkeyup="filterManualContent(this.value)">
        </div>

        <div class="topbar-actions">
            <button type="button" class="btn-topbar" onclick="window.print()" title="พิมพ์คู่มือหรือบันทึกเป็น PDF">
                <i class="bi bi-printer"></i>
                <span>พิมพ์คู่มือ (PDF)</span>
            </button>
            <a href="{{ route('python-pos.download') }}" class="btn-topbar" title="ดาวน์โหลดโปรแกรม POS">
                <i class="bi bi-display"></i>
                <span>โหลดแอป POS</span>
            </a>
            @auth
            <a href="{{ route('core-modules.index') }}" class="btn-topbar" style="background:var(--brand-red-light); border-color:var(--brand-red); color:var(--brand-red); font-weight:700;" title="ไปยังคู่มือ PopStar 4M ภายในระบบ ERP">
                <i class="bi bi-journal-text"></i>
                <span>คู่มือในระบบ ERP (4M)</span>
            </a>
            <a href="{{ route('dashboard') }}" class="btn-topbar btn-portal" title="กลับหน้าหลัก ERP Dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>หน้าหลัก ERP</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="btn-topbar btn-portal" title="เข้าสู่ระบบ ERP">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>เข้าสู่ระบบ ERP</span>
            </a>
            <a href="{{ route('portal') }}" class="btn-topbar" title="กลับหน้าหลักรวมระบบ">
                <i class="bi bi-grid-fill"></i>
                <span>หน้าหลัก Portal</span>
            </a>
            @endauth
            <button type="button" class="btn-topbar btn-theme" id="themeToggleBtn" title="สลับโหมดมืด / โหมดสว่าง" aria-label="Toggle theme">
                <i class="bi bi-moon-stars" id="themeIcon"></i>
            </button>
        </div>
    </header>

    <div class="manual-layout">
        <!-- Standalone Sidebar Navigation -->
        <aside class="manual-sidebar">
            <div style="padding: 0.5rem 0.75rem 0.85rem; border-bottom: 1px solid var(--border-light); margin-bottom: 0.85rem;">
                <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--brand-red);">คู่มือมาตรฐาน 8 บทหลัก (SOP)</span>
                <p style="font-size: 0.76rem; color: var(--text-muted); margin: 2px 0 0;">คลิกเพื่อเปิดคู่มือพร้อมภาพจริงทีละขั้นตอน</p>
            </div>

            <div>
                <div class="nav-group-title">📊 บทที่ 1: ภาพรวม & การเริ่มต้น</div>
                <a href="#guide-overview" class="nav-link-item" onclick="openManualReader('overview'); setActiveNav(this);">
                    <i class="bi bi-speedometer2 text-primary"></i>
                    <span>1.1 ภาพรวมและศูนย์ควบคุม</span>
                </a>
                <a href="#guide-users" class="nav-link-item" onclick="openManualReader('users'); setActiveNav(this);">
                    <i class="bi bi-people-fill text-success"></i>
                    <span>1.2 ผู้ใช้และสิทธิ์การใช้งาน</span>
                </a>
                <a href="#guide-settings" class="nav-link-item" onclick="openManualReader('settings'); setActiveNav(this);">
                    <i class="bi bi-gear-wide-connected text-primary"></i>
                    <span>1.3 ตั้งค่าระบบ & เอกสาร</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">🛒 บทที่ 2: ระบบขายหน้าร้าน (POS)</div>
                <a href="#guide-python-pos" class="nav-link-item" onclick="openManualReader('python_pos'); setActiveNav(this);">
                    <i class="bi bi-terminal-fill text-danger"></i>
                    <span>2.1 แอปแคชเชียร์ (Python POS v1.12.23)</span>
                </a>
                <a href="#guide-pos" class="nav-link-item" onclick="openManualReader('pos'); setActiveNav(this);">
                    <i class="bi bi-cart4 text-danger"></i>
                    <span>2.2 ขั้นตอนการขายหน้าร้าน (JET POS v1.12.23)</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">📦 บทที่ 3: สินค้า & คลังสินค้า</div>
                <a href="#guide-products" class="nav-link-item" onclick="openManualReader('products'); setActiveNav(this);">
                    <i class="bi bi-box-seam text-danger"></i>
                    <span>3.1 แค็ตตาล็อกสินค้า & สต็อก</span>
                </a>
                <a href="#guide-transfer" class="nav-link-item" onclick="openManualReader('transfer'); setActiveNav(this);">
                    <i class="bi bi-arrow-left-right text-danger"></i>
                    <span>3.2 โอนย้ายสต็อกข้ามสาขา</span>
                </a>
                <a href="#guide-counts" class="nav-link-item" onclick="openManualReader('counts'); setActiveNav(this);">
                    <i class="bi bi-clipboard-check text-danger"></i>
                    <span>3.3 ตรวจนับสต็อก (Stock Count)</span>
                </a>
                <a href="#guide-master-data" class="nav-link-item" onclick="openManualReader('master_data'); setActiveNav(this);">
                    <i class="bi bi-box-seam-fill text-info"></i>
                    <span>3.4 ข้อมูลหลัก & สูตรผลิต BOM</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">📄 บทที่ 4: การขาย & ลูกค้า</div>
                <a href="#guide-sales" class="nav-link-item" onclick="openManualReader('sales'); setActiveNav(this);">
                    <i class="bi bi-receipt-cutoff text-warning"></i>
                    <span>4.1 งานขายและเอกสารลูกค้า</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">🚚 บทที่ 5: จัดซื้อ & ซัพพลายเชน</div>
                <a href="#guide-purchasing" class="nav-link-item" onclick="openManualReader('purchasing'); setActiveNav(this);">
                    <i class="bi bi-truck text-warning"></i>
                    <span>5.1 จัดซื้อ คลังวัตถุดิบ & ผลิต</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">💰 บทที่ 6: การเงิน บัญชี & ภาษี</div>
                <a href="#guide-finance" class="nav-link-item" onclick="openManualReader('finance'); setActiveNav(this);">
                    <i class="bi bi-calculator-fill text-danger"></i>
                    <span>6.1 การเงินและบัญชีครบวงจร</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">📈 บทที่ 7: รายงาน & วิเคราะห์</div>
                <a href="#guide-reports" class="nav-link-item" onclick="openManualReader('reports'); setActiveNav(this);">
                    <i class="bi bi-bar-chart-line-fill text-info"></i>
                    <span>7.1 รายงานและวิเคราะห์ข้อมูล</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">🔌 บทที่ 8: เชื่อมต่อ & ดูแลระบบ</div>
                <a href="#guide-integrations" class="nav-link-item" onclick="openManualReader('integrations'); setActiveNav(this);">
                    <i class="bi bi-chat-dots-fill text-success"></i>
                    <span>8.1 เชื่อมต่อภายนอก (LINE Notify)</span>
                </a>
                <a href="#guide-ai-chat" class="nav-link-item" onclick="openManualReader('ai_chat'); setActiveNav(this);">
                    <i class="bi bi-robot text-danger"></i>
                    <span>8.2 แชท AI อัจฉริยะ (Gemini Assistant)</span>
                </a>
            </div>

            <div>
                <div class="nav-group-title">📐 ผังสถาปัตยกรรม & 4M</div>
                <a href="#section-erp-architecture" class="nav-link-item" onclick="setActiveNav(this)">
                    <i class="bi bi-diagram-3-fill" style="color:#2563eb;"></i>
                    <span>สถาปัตยกรรม ERP 4 ชั้น</span>
                </a>
                <a href="#section-erp-dataflow" class="nav-link-item" onclick="setActiveNav(this)">
                    <i class="bi bi-arrow-repeat" style="color:#059669;"></i>
                    <span>ผังการไหลข้อมูลธุรกิจ</span>
                </a>
                <a href="#section-pillars" class="nav-link-item" onclick="setActiveNav(this)">
                    <i class="bi bi-diagram-3"></i>
                    <span>4M เสาหลักการบริหาร</span>
                </a>
                <a href="#section-workflows" class="nav-link-item" onclick="setActiveNav(this)">
                    <i class="bi bi-bezier2"></i>
                    <span>ผังกระบวนการ (Workflows)</span>
                </a>
            </div>
        </aside>

        <!-- Main Reading Canvas -->
        <main class="manual-content">
            <!-- Header Banner -->
            <div class="content-header-banner">
                <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(211,47,47,0.1); color:var(--brand-red); font-size:12px; font-weight:800; padding:4px 12px; border-radius:9999px; margin-bottom:10px;">
                    <i class="bi bi-globe2"></i> คู่มือทั่วไปสำหรับพนักงานหน้าร้าน & ก่อนเข้าสู่ระบบ (Public Edition)
                </div>
                <h2>ศูนย์รวมคู่มือการปฏิบัติงาน บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด</h2>
                <p>
                    คู่มือมาตรฐานการทำงานฉบับภาพประกอบ ออกแบบให้อ่านง่ายสำหรับผู้ใช้งานทั่วไป พนักงานสาขา แคชเชียร์ และผู้สนใจ มีภาพจับหน้าจอจริงพร้อมวงไฮไลต์ตัวเลขขั้นตอน 1-2-3 โดยไม่ต้องล็อกอินเข้าสู่ระบบหลังบ้าน
                </p>
                @auth
                <div style="margin-top:12px; padding:10px 14px; background:rgba(59,130,246,0.1); border-left:3px solid #3b82f6; border-radius:8px; font-size:13px; color:var(--text-main);">
                    <i class="bi bi-info-circle-fill text-primary me-1"></i>
                    คุณกำลังเข้าสู่ระบบในฐานะ <strong>{{ auth()->user()->name ?? auth()->user()->username }}</strong> สามารถเข้าดูคู่มือเชิงลึก 4M และตรวจสอบผังงานภายใน ERP ได้ที่
                    <a href="{{ route('core-modules.index') }}" style="color:#2563eb; font-weight:700; text-decoration:underline;">คู่มือ PopStar 4M ภายในระบบ ERP</a>
                </div>
                @endauth
            </div>

            <!-- Section 1: Visual Guides -->
            <section id="section-visual-guides" class="manual-section">
                <div class="section-head">
                    <div class="section-head-title">
                        <div class="section-icon-badge">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                        <h3>คู่มือภาพประกอบทีละขั้นตอน (Visual Step-by-Step Guides)</h3>
                    </div>
                    <span class="section-tag">คลิกการ์ดเพื่อเปิดดูทีละขั้นตอน</span>
                </div>

                <div class="vg-grid">
                    <!-- บทที่ 1: ภาพรวมและการเริ่มต้นใช้งาน -->
                    <div class="vg-card" onclick="openManualReader('overview')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/overview_main.png') }}" alt="ภาพรวมและศูนย์ควบคุม">
                            <span class="vg-badge-step" style="background:#2563eb;"><i class="bi bi-speedometer2 me-1"></i> บทที่ 1.1: 9 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-speedometer2 text-primary"></i> 1.1 ภาพรวมและศูนย์ควบคุม (Overview & Controls)</h4>
                                <p class="vg-desc">คู่มือแดชบอร์ดผู้บริหาร POPSTAR EXECUTIVE BOARD วิเคราะห์ยอดขายด่วน กำไรขั้นต้น อัตรากำไร และศูนย์ควบคุมต้นทุน งบประมาณ (Budget) เวลา/Payroll และมาตรการบริหาร</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#dbeafe; color:#1e40af; border-color:#93c5fd;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 1.1 (9 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <div class="vg-card" onclick="openManualReader('users')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/users_main.png') }}" alt="ผู้ใช้และสิทธิ์การใช้งาน">
                            <span class="vg-badge-step" style="background:#059669;"><i class="bi bi-shield-lock-fill me-1"></i> บทที่ 1.2: 8 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-people-fill text-success"></i> 1.2 ผู้ใช้และสิทธิ์ (Users & Security)</h4>
                                <p class="vg-desc">คู่มือค้นหาผู้ใช้, จัดการผู้ใช้สำหรับ POS, ออก PIN แคชเชียร์ 6 หลัก, เพิ่ม/แก้ไขผู้ใช้, กำหนดสาขา/สายส่ง, ผูกบทบาท RBAC, รีเซ็ตรหัสผ่าน และตารางสิทธิ์ BPlus</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#d1fae5; color:#065f46; border-color:#6ee7b7;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 1.2 (8 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <div class="vg-card" onclick="openManualReader('settings')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/settings_main.png') }}" alt="ตั้งค่าระบบและเอกสาร">
                            <span class="vg-badge-step" style="background:#2563eb;"><i class="bi bi-gear-fill me-1"></i> บทที่ 1.3: 14 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-gear-wide-connected text-primary"></i> 1.3 ตั้งค่าระบบ & เอกสาร (Settings Master)</h4>
                                <p class="vg-desc">คู่มือตั้งค่าครบ 14 ฟังก์ชัน: ฟังก์ชันเอกสาร, อัตราภาษี VAT 7%, เลขรันเอกสาร, โลโก้/ตราประทับ, หมายเหตุเอกสาร, ข้อมูลกิจการ, ศูนย์ควบคุมโมดูล, POS Designer, จัดลำดับเมนู, ปรับธีม, ข้อมูลการรับชำระ, ผูกบัญชี GL, ออกแบบใบเสร็จ, สายการอนุมัติ และ Backup/Security</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#dbeafe; color:#1e40af; border-color:#93c5fd;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 1.3 (14 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 2: ระบบขายหน้าร้าน (Point of Sale) -->
                    <div class="vg-card" onclick="openManualReader('python_pos')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/python_pos_main.png') }}" alt="แอปพลิเคชัน Python POS">
                            <span class="vg-badge-step"><i class="bi bi-terminal-fill me-1"></i> บทที่ 2.1: 6 ขั้นตอนพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-terminal-fill text-danger"></i> 2.1 แอปแคชเชียร์ (Python POS Desktop v1.12.23)</h4>
                                <p class="vg-desc">คู่มือใช้งานโปรแกรมแคชเชียร์หน้าร้านจริง เข้าสู่ระบบ เปิดกะ ยิงบาร์โค้ด ค้นหาสินค้า ชำระเงินสด/QR Code และปิดกะทำงาน (เวอร์ชันล่าสุด v1.12.23)</p>
                            </div>
                            <button type="button" class="vg-btn-action">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 2.1 (6 ขั้นตอน)
                            </button>
                        </div>
                    </div>

                    <div class="vg-card" onclick="openManualReader('pos')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/pos_main.png') }}" alt="หน้าจอโปรแกรม POS">
                            <span class="vg-badge-step"><i class="bi bi-cart4 me-1"></i> บทที่ 2.2: 5 ขั้นตอนพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-cart4 text-danger"></i> 2.2 ขั้นตอนการขายหน้าร้าน (JET POS v1.12.23)</h4>
                                <p class="vg-desc">คู่มือการเปิดกะแคชเชียร์ ค้นหาสินค้า สแกนบาร์โค้ด เลือกหมวดหมู่ จัดการตะกร้าสินค้า ชำระเงินสด/QR และพิมพ์รายงานปิดกะ Z-Report ผ่านโปรแกรมจอสัมผัสจริง (เวอร์ชันล่าสุด v1.12.23)</p>
                            </div>
                            <button type="button" class="vg-btn-action">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 2.2 (5 ขั้นตอน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 3: ระบบสินค้าและคลังสินค้า (Inventory & Warehousing) -->
                    <div class="vg-card" onclick="openManualReader('products')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/products_main.png') }}" alt="หน้ารายการสินค้า">
                            <span class="vg-badge-step"><i class="bi bi-box-seam me-1"></i> บทที่ 3.1: 5 ขั้นตอนพร้อมรูป</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-box-seam text-danger"></i> 3.1 จัดการสินค้า & สต็อก</h4>
                                <p class="vg-desc">การตรวจสอบสต็อกคงเหลือรายสาขา การค้นหาสินค้า การสร้างสินค้าใหม่ การตั้งราคา และรับเข้าสต็อกด่วน</p>
                            </div>
                            <button type="button" class="vg-btn-action">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.1 (5 ขั้นตอน)
                            </button>
                        </div>
                    </div>

                    <div class="vg-card" onclick="openManualReader('transfer')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/stock_transfers.png') }}" alt="หน้าโอนสต็อก">
                            <span class="vg-badge-step"><i class="bi bi-arrow-left-right me-1"></i> บทที่ 3.2: 4 ขั้นตอนพร้อมรูป</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-arrow-left-right text-danger"></i> 3.2 โอนย้ายสต็อกข้ามสาขา</h4>
                                <p class="vg-desc">ขั้นตอนการเบิกวัตถุดิบและสินค้าจากคลังใหญ่ (HQ) กระจายสู่สาขาหน้าร้านเพื่อเติมสต็อกให้ขายได้</p>
                            </div>
                            <button type="button" class="vg-btn-action">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.2 (4 ขั้นตอน)
                            </button>
                        </div>
                    </div>

                    <div class="vg-card" onclick="openManualReader('counts')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/stock_counts.png') }}" alt="หน้าตรวจนับสต็อก">
                            <span class="vg-badge-step"><i class="bi bi-clipboard-check me-1"></i> บทที่ 3.3: 4 ขั้นตอนพร้อมรูป</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-clipboard-check text-danger"></i> 3.3 ตรวจนับสต็อก (Stock Count)</h4>
                                <p class="vg-desc">การตรวจนับของจริงเปรียบเทียบกับระบบ คำนวณผลต่าง (Variance) ของขาด/ของเกิน และปรับยอดสมุดบัญชี</p>
                            </div>
                            <button type="button" class="vg-btn-action">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.3 (4 ขั้นตอน)
                            </button>
                        </div>
                    </div>

                    <div class="vg-card" onclick="openManualReader('master_data')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/master_data_main.png') }}" alt="ข้อมูลหลักและโครงสร้างระบบ">
                            <span class="vg-badge-step" style="background:#0284c7;"><i class="bi bi-people-fill me-1"></i> บทที่ 3.4: 13 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-box-seam-fill text-info"></i> 3.4 ข้อมูลหลัก & สูตรผลิต BOM (Master Data)</h4>
                                <p class="vg-desc">คู่มือศูนย์ตั้งต้นระบบ, ลูกค้า, ซัพพลายเออร์, สมาชิก, ผังคลังสินค้า, ตารางราคา, ราคาเครื่องชั่ง, โปรโมชั่น, ราคานาทีทอง, ป้ายราคา, บัตรส่วนลด, แต้มสะสม และแคมเปญซื้อครบ</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#e0f2fe; color:#0369a1; border-color:#bae6fd;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 3.4 (13 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 4: ระบบการขายและเอกสารลูกค้า (Sales & Invoicing) -->
                    <div class="vg-card" onclick="openManualReader('sales')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/sales_main.png') }}" alt="งานขายและเอกสาร">
                            <span class="vg-badge-step" style="background:#d97706;"><i class="bi bi-receipt-cutoff me-1"></i> บทที่ 4.1: 9 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-receipt-cutoff text-warning"></i> 4.1 งานขายและเอกสารลูกค้า (Sales & Documents)</h4>
                                <p class="vg-desc">คู่มือศูนย์เอกสารย้อนหลัง, ใบเสนอราคา, ใบจอง/ขายเชื่อ, ใบขายสด, ใบรับคืนสินค้า, ใบวางบิล, ใบเพิ่มหนี้/ใบลดหนี้, ลูกค้าสัมพันธ์ (CRM) และ Sales Pipeline Kanban</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#fef3c7; color:#92400e; border-color:#fcd34d;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 4.1 (9 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 5: ระบบการจัดซื้อและเจ้าหนี้ (Purchasing & Payables) -->
                    <div class="vg-card" onclick="openManualReader('purchasing')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/purchasing_main.png') }}" alt="จัดซื้อ คลังวัตถุดิบ และการผลิต">
                            <span class="vg-badge-step" style="background:#ea580c;"><i class="bi bi-truck me-1"></i> บทที่ 5.1: 6 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-truck text-warning"></i> 5.1 จัดซื้อ คลังวัตถุดิบ & การผลิต (Purchasing & Production)</h4>
                                <p class="vg-desc">คู่มือใบขอซื้อ/สั่งซื้อ (PO), ใบเบิกสินค้า/วัตถุดิบ, ใบคืนสินค้าจากการเบิก, ใบตัดสินค้าชำรุด, สูตรผลิต (BOM)/ใบสั่งผลิต, และแจ้งเตือนสต็อกต่ำจุดสั่งซื้อซ้ำ</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#ffedd5; color:#9a3412; border-color:#fed7aa;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 5.1 (6 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 6: ระบบการเงิน บัญชี และภาษี (Finance, Accounting & Tax) -->
                    <div class="vg-card" onclick="openManualReader('finance')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/finance_main.png') }}" alt="ระบบการเงินและบัญชี">
                            <span class="vg-badge-step" style="background:#dc2626;"><i class="bi bi-calculator-fill me-1"></i> บทที่ 6.1: 8 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-calculator-fill text-danger"></i> 6.1 การเงินและบัญชีครบวงจร (Finance & Accounting)</h4>
                                <p class="vg-desc">คู่มือสมุดเงินสดรายวัน, ทะเบียนบัญชีธนาคาร, ฝาก/ถอนเงินสด, ทะเบียนเช็ครับ-จ่าย, ผังบัญชีแยกประเภท, สมุดรายวันทั่วไป (GL), งบการเงิน/งบทดลอง และศูนย์ภาษีไทย/E-Tax</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 6.1 (8 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 7: รายงานและการวิเคราะห์เชิงบริหาร (Reports & Analytics) -->
                    <div class="vg-card" onclick="openManualReader('reports')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/reports_main.png') }}" alt="รายงานและวิเคราะห์ข้อมูล">
                            <span class="vg-badge-step" style="background:#0891b2;"><i class="bi bi-bar-chart-line-fill me-1"></i> บทที่ 7.1: 6 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-bar-chart-line-fill text-info"></i> 7.1 รายงานและวิเคราะห์ข้อมูล (Reports & Analytics)</h4>
                                <p class="vg-desc">คู่มือรายงานครบ 6 หมวด: รายงานยอดขาย, รายงานการจัดซื้อและซัพพลายเออร์, รายงานคลังสินค้าและล็อต, รายงานการเงินและการรับชำระ, รายงานภาษีซื้อ/ขาย VAT 7%, และรายงานประวัติระบบเดิม BPlus</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#cffafe; color:#155e75; border-color:#a5f3fc;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 7.1 (6 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 8.1: เชื่อมต่อภายนอก (LINE Integration) -->
                    <div class="vg-card" onclick="openManualReader('integrations')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/integrations_main.png') }}" alt="เชื่อมต่อภายนอก LINE">
                            <span class="vg-badge-step" style="background:#16a34a;"><i class="bi bi-chat-dots-fill me-1"></i> บทที่ 8.1: 1 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-chat-dots-fill text-success"></i> 8.1 เชื่อมต่อภายนอก (LINE Integration)</h4>
                                <p class="vg-desc">คู่มือตั้งค่า LINE Messaging API & LINE Notify สำหรับแจ้งเตือนยอดขายรายวัน แจ้งเตือนสินค้าใกล้หมด และแจ้งเตือนสรุปปิดรอบกะแคชเชียร์อัตโนมัติเข้ากลุ่ม LINE</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#dcfce7; color:#166534; border-color:#bbf7d0;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 8.1 (1 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>

                    <!-- บทที่ 8.2: แชท AI อัจฉริยะ (Gemini Assistant) -->
                    <div class="vg-card" onclick="openManualReader('ai_chat')">
                        <div class="vg-thumb">
                            <img src="{{ asset('images/manual/ai_chat_main.png') }}" alt="แชท AI อัจฉริยะ Gemini">
                            <span class="vg-badge-step" style="background:#d32f2f;"><i class="bi bi-robot me-1"></i> บทที่ 8.2: 4 ฟังก์ชันพร้อมรูปจริง</span>
                        </div>
                        <div class="vg-body">
                            <div>
                                <h4 class="vg-title"><i class="bi bi-robot text-danger"></i> 8.2 แชท AI อัจฉริยะ (Gemini Assistant)</h4>
                                <p class="vg-desc">คู่มือการใช้งานผู้ช่วย AI ประจำระบบ ERP: เช็กสต๊อกติดลบเรียลไทม์, แนะนำขั้นตอนการใช้งานระบบ, ตอบข้อสงสัยระเบียบภาษี/บัญชี/สูตรคำนวณ พร้อมวิธีการตั้งค่า GEMINI_API_KEY</p>
                            </div>
                            <button type="button" class="vg-btn-action" style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;">
                                <i class="bi bi-book-half"></i> เปิดอ่านบทที่ 8.2 (4 ฟังก์ชัน)
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 2: 4M Pillars -->
            <section id="section-pillars" class="manual-section">
                <div class="section-head">
                    <div class="section-head-title">
                        <div class="section-icon-badge">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h3>เสาหลักมาตรฐานระบบ 4M (PopStar Standard)</h3>
                    </div>
                    <span class="section-tag">Man • Machine • Material • Money</span>
                </div>

                <div class="pillar-grid">
                    <div class="pillar-card">
                        <div class="pillar-head">
                            <div class="pillar-icon-box pillar-man"><i class="bi bi-person-badge"></i></div>
                            <div>
                                <h4 class="pillar-title">Man (บุคลากร)</h4>
                                <small class="text-muted">สิทธิ์ RBAC & ความรับผิดชอบ</small>
                            </div>
                        </div>
                        <p class="pillar-desc">แบ่งบทบาทชัดเจนระหว่างแคชเชียร์ ผู้จัดการสาขา เจ้าหน้าที่คลัง และฝ่ายบัญชี มีบันทึกประวัติการกระทำ (Audit Log) ทุกขั้นตอน</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-head">
                            <div class="pillar-icon-box pillar-machine"><i class="bi bi-cpu"></i></div>
                            <div>
                                <h4 class="pillar-title">Machine (ระบบ & อุปกรณ์)</h4>
                                <small class="text-muted">POS • ตาชั่ง • เครื่องพิมพ์</small>
                            </div>
                        </div>
                        <p class="pillar-desc">โปรแกรมแคชเชียร์ทำงานออฟไลน์ได้ เชื่อมต่อเครื่องชั่งน้ำหนัก ปริ้นท์เตอร์ใบเสร็จความร้อน และเครื่องอ่านบาร์โค้ดอย่างไร้รอยต่อ</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-head">
                            <div class="pillar-icon-box pillar-material"><i class="bi bi-boxes"></i></div>
                            <div>
                                <h4 class="pillar-title">Material (สินค้า & สต็อก)</h4>
                                <small class="text-muted">Ledger First • ล็อต & วันหมดอายุ</small>
                            </div>
                        </div>
                        <p class="pillar-desc">สต็อกทุกชิ้นบันทึกผ่านสมุดบัญชีสต็อก (Stock Ledger) ตรวจย้อนกลับได้ ตัดยอดขายแบบเรียลไทม์ และควบคุมการสูญเสีย</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-head">
                            <div class="pillar-icon-box pillar-money"><i class="bi bi-cash-stack"></i></div>
                            <div>
                                <h4 class="pillar-title">Money (เงิน & การเงิน)</h4>
                                <small class="text-muted">กระทบยอด • Z-Report • GL</small>
                            </div>
                        </div>
                        <p class="pillar-desc">เงินสดในลิ้นชักต้องตรงกับยอดขาย Z-Report บันทึกบัญชีแยกประเภท (GL) อัตโนมัติ พร้อมรองรับภาษีมูลค่าเพิ่ม (VAT 7%)</p>
                    </div>
                </div>
            </section>

            <!-- Section 3: Workflows -->
            <section id="section-workflows" class="manual-section">
                <div class="section-head">
                    <div class="section-head-title">
                        <div class="section-icon-badge">
                            <i class="bi bi-bezier2"></i>
                        </div>
                        <h3>ผังกระบวนการทำงานหลัก (End-to-End Workflows)</h3>
                    </div>
                    <span class="section-tag">ขั้นตอนการปฏิบัติงานจริง</span>
                </div>

                <div class="manual-table-card">
                    <div class="table-responsive">
                        <table class="docs-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">กระบวนการ</th>
                                    <th style="width: 25%;">ผู้รับผิดชอบ</th>
                                    <th>ขั้นตอนการทำงาน (Step Sequence)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>1. การขายหน้าร้าน (O2C)</strong></td>
                                    <td>แคชเชียร์หน้าร้าน</td>
                                    <td>เปิดกะ (นับเงินทอน) → ยิงบาร์โค้ด/ชั่งน้ำหนัก → ใส่ส่วนลด/สมาชิก → รับเงิน (สด/QR) → พิมพ์ใบเสร็จ → ตัดสต็อกทันที</td>
                                </tr>
                                <tr>
                                    <td><strong>2. ปิดกะประจำวัน (Closing)</strong></td>
                                    <td>แคชเชียร์ + ผู้จัดการ</td>
                                    <td>ตรวจยอดขายระบบ → นับเงินสดจริงในลิ้นชัก → กรอกยอดนับจริง → ปิดกะ → พิมพ์ใบสรุป Z-Report → ส่งเงินเข้าเซฟ</td>
                                </tr>
                                <tr>
                                    <td><strong>3. เติมสต็อกสาขา (Transfer)</strong></td>
                                    <td>สาขา + คลังใหญ่</td>
                                    <td>สาขาเปิดใบขอเบิก (TR) → คลังใหญ่อนุมัติและจัดของ → จัดส่งของ → สาขาตรวจนับและกดยืนยันรับเข้าคลัง</td>
                                </tr>
                                <tr>
                                    <td><strong>4. สั่งซื้อสินค้าเข้าคลัง (P2P)</strong></td>
                                    <td>ฝ่ายจัดซื้อ + คลัง</td>
                                    <td>เปิดใบขอซื้อ (PR) → ออกใบสั่งซื้อ (PO) ส่งซัพพลายเออร์ → ซัพพลายเออร์ส่งของ → ตรวจรับเข้าคลัง (GR) → บันทึกตั้งหนี้ (AP)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Section 4: Formulas & Tax -->
            <section id="section-formulas" class="manual-section">
                <div class="section-head">
                    <div class="section-head-title">
                        <div class="section-icon-badge">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h3>สูตรคำนวณและระเบียบภาษี (Formulas & Compliance)</h3>
                    </div>
                    <span class="section-tag">มาตรฐานสรรพากรและบัญชี</span>
                </div>

                <div class="manual-table-card">
                    <div class="table-responsive">
                        <table class="docs-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">หัวข้อ</th>
                                    <th style="width: 40%;">สูตรการคำนวณ</th>
                                    <th>คำอธิบายประกอบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>ภาษีมูลค่าเพิ่ม (VAT 7% รวมใน)</strong></td>
                                    <td><code>ยอดก่อนภาษี = ยอดรวม ÷ 1.07</code><br><code>ภาษี VAT = ยอดรวม - ยอดก่อนภาษี</code></td>
                                    <td>ราคาสินค้าหน้าร้านเป็นราคารวมภาษีแล้ว ปัดเศษทศนิยม 2 ตำแหน่งตามเกณฑ์กรมสรรพากร</td>
                                </tr>
                                <tr>
                                    <td><strong>ส่วนต่างปิดกะ (Cash Variance)</strong></td>
                                    <td><code>ส่วนต่าง = เงินสดนับจริง - (ยอดขายสด + เงินทอนต้นกะ)</code></td>
                                    <td>หากผลต่างเป็นลบ (เงินขาด) แคชเชียร์ต้องรับผิดชอบชี้แจงสาเหตุ</td>
                                </tr>
                                <tr>
                                    <td><strong>ต้นทุนสินค้าเฉลี่ย (Moving Average)</strong></td>
                                    <td><code>ต้นทุนเฉลี่ยใหม่ = (มูลค่าเดิม + มูลค่ารับเข้าใหม่) ÷ จำนวนรวมใหม่</code></td>
                                    <td>ใช้สำหรับคำนวณต้นทุนขาย (COGS) และกำไรขั้นต้นแบบเรียลไทม์</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Section 5: Daily Routines Checklist -->
            <section id="section-routines" class="manual-section">
                <div class="section-head">
                    <div class="section-head-title">
                        <div class="section-icon-badge">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <h3>เช็กลิสต์ประจำวัน (Daily Operating Checklist)</h3>
                    </div>
                    <span class="section-tag">ตรวจสอบทุกวัน</span>
                </div>

                <div class="manual-table-card">
                    <div class="table-responsive">
                        <table class="docs-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">ช่วงเวลา</th>
                                    <th style="width: 35%;">รายการตรวจสอบ</th>
                                    <th>ผู้รับผิดชอบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>🌅 ก่อนเปิดร้าน (เช้า)</strong></td>
                                    <td>1. ตรวจสอบกระดาษพิมพ์ใบเสร็จและสัญญาณเน็ต<br>2. นับเงินทอนตั้งต้นและกด "เปิดกะ"<br>3. ตรวจสอบอุณหภูมิตู้แช่แข็งให้อยู่ในเกณฑ์ (-18°C)</td>
                                    <td>แคชเชียร์ + หัวหน้ากะ</td>
                                </tr>
                                <tr>
                                    <td><strong>☀️ ระหว่างวัน</strong></td>
                                    <td>1. ยิงบาร์โค้ดและชั่งน้ำหนักให้ถูกต้องตรงป้าย<br>2. ตรวจสอบสลิปโอน QR PromptPay ให้ตรงยอด<br>3. หากสินค้าใกล้หมด ให้แจ้งเปิดใบขอเบิกโอนจากคลัง</td>
                                    <td>แคชเชียร์</td>
                                </tr>
                                <tr>
                                    <td><strong>🌙 สิ้นสุดวัน (ปิดร้าน)</strong></td>
                                    <td>1. นับเงินสดจริงในลิ้นชักและกด "ปิดกะ"<br>2. พิมพ์รายงาน Z-Report แนบพร้อมเงินสดส่งมอบ<br>3. ตรวจสอบว่าบิลทั้งหมดได้รับการซิงก์เข้าสู่ระบบแล้ว</td>
                                    <td>แคชเชียร์ + ผู้จัดการสาขา</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Section 6: System Architecture & Workflow Overviews (ข้อมูลภาพรวมเชิงสถาปัตยกรรม) -->
            <section id="section-erp-architecture" class="manual-section">
                <div class="section-head">
                    <div class="section-head-title">
                        <div class="section-icon-badge" style="background:#dbeafe; color:#2563eb;">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h3>สถาปัตยกรรมและผังระบบงาน (System Architecture & Workflows)</h3>
                    </div>
                    <span class="section-tag" style="background:#dbeafe; color:#1e40af;">ข้อมูลอ้างอิงภาพรวมเชิงระบบ</span>
                </div>

                <!-- Info Callout: ให้รับรู้ภาพรวมเชิงโครงสร้าง -->
                <div class="info-callout">
                    <div class="info-callout-icon">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <div class="info-callout-content">
                        <h4>เกี่ยวกับหมวดสถาปัตยกรรมและผังระบบงาน (System Knowledge & Architecture)</h4>
                        <p>
                            หมวดนี้จัดทำขึ้นเพื่อให้<strong>ทีมงาน, พนักงานสาขา และผู้ใช้งานทุกท่าน</strong>เข้าใจภาพรวมเชิงโครงสร้างและการไหลของข้อมูลหลังบ้านของระบบ <strong>Pop ERP Food</strong> และระบบ <strong>POS (ทั้ง Web และ Desktop App)</strong> เพื่อให้เห็นภาพการเชื่อมโยงการทำงานร่วมกันระหว่างสาขา คลังสินค้า ครัวกลาง และฝ่ายบัญชีการเงิน
                        </p>
                    </div>
                </div>

                <!-- Diagram 1: ERP 4 Layers Architecture -->
                <div class="diagram-card">
                    <div class="diagram-card-head">
                        <h4>
                            <i class="bi bi-layers-fill text-primary"></i> 
                            1. ผังสถาปัตยกรรมภาพรวมระบบ 4 เลเยอร์ (Overall System Architecture)
                        </h4>
                        <span class="badge-arch-layer">Layer 1 - 4 Architecture</span>
                    </div>
                    <p class="diagram-card-desc">
                        แสดงการแบ่งระดับชั้นการทำงานของระบบ ERP ตั้งแต่ฝั่งผู้ใช้งาน (Frontends), การควบคุมความปลอดภัยและเส้นทางข้อมูล (Gateway), แกนประมวลผลธุรกิจหลัก (Core Modules), จนถึงฐานข้อมูลและการเชื่อมโยงภายนอก (Integrations)
                    </p>
                    <div class="mermaid-container">
                        <div class="mermaid">
flowchart TB
    subgraph L1["🖥️ เลเยอร์ที่ 1 : ฝั่งผู้ใช้งาน (Frontends & Clients Layer)"]
        direction LR
        L1_WEB["🌐 Back-Office Web Portal<br><b>(Laravel Blade / Alpine.js)</b><br>• ผู้บริหาร / ผู้จัดการสาขา<br>• จัดซื้อ / บัญชี / การเงิน"]
        L1_WEBPOS["💻 Web POS (หน้าร้านบนเว็บ)<br><b>(Responsive Touch UI)</b><br>• พนักงานแคชเชียร์<br>• ยิงบาร์โค้ด / รับชำระเงิน"]
        L1_PYPOS["🖥️ Desktop Python POS (Native)<br><b>(PySide6 / Qt6 + SQLite)</b><br>• ทำงานออฟไลน์ได้ (Offline-First)<br>• คุมลิ้นชัก RJ11 / เครื่องพิมพ์สลิป"]
        L1_MOBI["📱 Mobile Warehouse Web<br><b>(มือถือ / เครื่องสแกน PDA)</b><br>• สแกนรับของ / เบิกจ่าย / ตรวจนับ"]
    end

    subgraph L2["🛡️ เลเยอร์ที่ 2 : ควบคุมสิทธิ์และการจัดการ (API, Gateway & Security)"]
        direction LR
        L2_AUTH["🔐 Identity & Access Control<br>• Session & Sanctum Auth<br>• บทบาทและสิทธิ์ (RBAC)<br>• PIN Code แคชเชียร์"]
        L2_GATEWAY["🚪 API Router & Middleware<br>• RESTful API (/api/v1/...)<br>• Multi-Branch Switching"]
        L2_WORKFLOW["📑 Workflow & Approval Engine<br>• อนุมัติเอกสารขอซื้อ/สั่งซื้อ<br>• อนุมัติปรับยอดสต็อก / Audit Logs"]
    end

    subgraph L3["⚙️ เลเยอร์ที่ 3 : แกนหลักของโมดูล ERP (Core ERP Business Modules)"]
        direction TB

        subgraph M_MASTER["📦 ข้อมูลหลัก (Master Data & Settings)"]
            direction LR
            MD_PROD["สินค้า & หน่วยนับ<br>(Products & Units)"]
            MD_PRICE["ตารางราคา & สเกลราคา<br>(Price Tables)"]
            MD_PARTNER["ลูกค้า & ซัพพลายเออร์<br>(Customers/Suppliers)"]
            MD_ORG["สาขา & เล่มเอกสาร<br>(Branches & Books)"]
        end

        subgraph M_CORE_OPS["🔄 กระบวนการดำเนินงานหลัก (Core Operations Pipeline)"]
            direction LR
            SUB_PURCHASE["🛒 จัดซื้อ (Procurement)<br>PR → PO → รับของ (GRN) → AP"]
            SUB_STOCK["🏭 คลังสินค้า (Inventory)<br>สต็อก Lot/Expiry → โอนย้าย → นับสต็อก"]
            SUB_KITCHEN["🍳 ผลิต/แปรรูป (Kitchen BOM)<br>สูตรอาหาร → ผลิต → คำนวณต้นทุนจริง"]
            SUB_SALES["💰 การขาย & POS (Sales)<br>หน้าร้าน → จอง → สมาชิกแต้ม → โปรโมชั่น"]
        end

        subgraph M_FINANCE["📊 การเงิน บัญชี และภาษี (Finance, GL & Tax)"]
            direction LR
            FN_GL["สมุดรายวันทั่วไป & ผังบัญชี<br>(GL Journal & Chart of Accounts)"]
            FN_ARAP["บริหารลูกหนี้ & เจ้าหนี้<br>(AR/AP Open Items & Billing)"]
            FN_TAX["ภาษีซื้อ-ขาย & ภ.พ.30<br>(Tax Compliance & e-Tax)"]
            FN_RPT["งบการเงิน & รายงานบริหาร<br>(P&L, Balance Sheet, Dashboard)"]
        end
    end

    subgraph L4["💾 เลเยอร์ที่ 4 : ฐานข้อมูลและการเชื่อมต่อภายนอก (Data & Integrations)"]
        direction LR
        DB_MAIN[("🗄️ Main ERP Database<br>(MySQL / MariaDB)")]
        DB_LOCAL[("📁 Local POS Cache<br>(SQLite ในเครื่อง Desktop)")]
        INT_LINE["💬 LINE Official Account<br>ส่งสรุปยอดขาย / ปิดกะ"]
        INT_OCR["👁️ Smart OCR Engine<br>สแกนบิลซื้อเข้า PO"]
        INT_ETAX["🏛️ e-Tax & สรรพากร<br>ระบบภาษีอิเล็กทรอนิกส์"]
    end

    L1_WEB --> L2_GATEWAY
    L1_WEBPOS --> L2_GATEWAY
    L1_PYPOS --> L2_GATEWAY
    L1_MOBI --> L2_GATEWAY

    L2_GATEWAY --> L2_AUTH
    L2_GATEWAY --> L2_WORKFLOW
    L2_AUTH --> L3
    L2_WORKFLOW --> L3

    SUB_PURCHASE -->|"รับวัตถุดิบเข้าคลัง"| SUB_STOCK
    SUB_STOCK -->|"เบิกวัตถุดิบไปปรุง"| SUB_KITCHEN
    SUB_KITCHEN -->|"ส่งสินค้าสำเร็จรูปเข้าสต็อก"| SUB_STOCK
    SUB_STOCK -->|"ตัดสต็อกตามยอดขาย"| SUB_SALES

    SUB_PURCHASE -.->|"ตั้งหนี้ & ภาษีซื้อ"| M_FINANCE
    SUB_STOCK -.->|"ปิดรอบต้นทุนสินค้า"| M_FINANCE
    SUB_SALES -.->|"รายได้ & ภาษีขาย"| M_FINANCE

    L3 --> DB_MAIN
    L1_PYPOS <--> DB_LOCAL
    SUB_SALES --> INT_LINE
    SUB_PURCHASE --> INT_OCR
    M_FINANCE --> INT_ETAX
                        </div>
                    </div>
                </div>

                <!-- Diagram 2: Business Process Data Flow -->
                <div id="section-erp-dataflow" class="diagram-card" style="scroll-margin-top: 80px;">
                    <div class="diagram-card-head">
                        <h4>
                            <i class="bi bi-arrow-repeat text-success"></i> 
                            2. ผังการไหลของข้อมูลทางธุรกิจ (End-to-End Business Data Flow)
                        </h4>
                        <span class="badge-arch-layer" style="background:#d1fae5; color:#059669;">Data Flow Pipeline</span>
                    </div>
                    <p class="diagram-card-desc">
                        แสดงเส้นทางข้อมูลตั้งแต่วัตถุดิบเข้าสู่โรงงาน/ครัว จนถึงการขายให้ลูกค้าหน้าร้านและการลงบัญชีอัตโนมัติ
                    </p>
                    <div class="mermaid-container">
                        <div class="mermaid">
sequenceDiagram
    autonumber
    actor Sup as 🏢 ซัพพลายเออร์
    participant PO as 🛒 จัดซื้อ (Purchasing)
    participant WH as 🏭 คลังสินค้า (Warehouse)
    participant Kit as 🍳 ครัวกลาง/แปรรูป (Kitchen)
    participant POS as 💻 ขายหน้าร้าน (POS)
    actor Cust as 👤 ลูกค้า
    participant GL as 📑 บัญชี/การเงิน (GL)

    Note over PO,Sup: 1. กระบวนการสั่งซื้อและรับของ
    PO->>Sup: ออกใบสั่งซื้อ (Purchase Order: PO)
    Sup->>WH: จัดส่งสินค้า/วัตถุดิบ + ใบส่งของ
    WH->>WH: ตรวจรับสินค้า (GRN), ตรวจ QC & ลง Lot/วันหมดอายุ
    WH->>PO: บันทึกรับเข้าสต็อก
    PO->>GL: ตั้งหนี้การค้า (AP Invoice) & ภาษีซื้ออัตโนมัติ

    Note over WH,Kit: 2. กระบวนการแปรรูป / ประกอบอาหาร
    Kit->>WH: ขอเบิกวัตถุดิบตามสูตร (Recipe BOM)
    WH->>Kit: ตัดสต็อกวัตถุดิบออก (Stock Issue)
    Kit->>Kit: ดำเนินการผลิต / ตัดแต่งเนื้อสัตว์ / ปรุงอาหาร
    Kit->>WH: บันทึกรับสินค้าสำเร็จรูปเข้าสต็อก (Production Batch)

    Note over POS,Cust: 3. กระบวนการขายหน้าร้าน (POS)
    Cust->>POS: ซื้อสินค้า / ยิงบาร์โค้ด / ชั่งน้ำหนัก
    POS->>POS: ตรวจสอบโปรโมชั่น & สะสมแต้มสมาชิก
    Cust->>POS: ชำระเงิน (เงินสด / QR PromptPay)
    POS->>Cust: พิมพ์ใบเสร็จรับเงิน (Receipt / Tax Invoice)
    POS->>WH: ตัดสต็อกหน้าร้านทันที (Stock Movement)

    Note over POS,GL: 4. การปิดกะและการลงบัญชีอัตโนมัติ
    POS->>POS: แคชเชียร์นับเงินสด ปิดกะประจำวัน (Z-Report)
    POS->>GL: ส่งสรุปรายได้, ภาษีขาย (VAT Output) เข้า GL
    WH->>GL: คำนวณต้นทุนขาย (COGS) ปิดงบประจำงวด
                        </div>
                    </div>
                </div>

                <!-- Diagram 3: Dual-Platform POS System -->
                <div id="section-pos-dual" class="diagram-card" style="scroll-margin-top: 80px;">
                    <div class="diagram-card-head">
                        <h4>
                            <i class="bi bi-display-fill text-danger"></i> 
                            3. สถาปัตยกรรมระบบ POS (Web POS & Desktop Python App)
                        </h4>
                        <span class="badge-arch-layer" style="background:#fee2e2; color:#dc2626;">Dual-Platform POS</span>
                    </div>
                    <p class="diagram-card-desc">
                        แสดงการทำงานร่วมกันระหว่าง Web POS (ผ่านเบราว์เซอร์) กับ Desktop Python POS (Native App พร้อมฐานข้อมูล SQLite ในเครื่อง และการเชื่อมต่ออุปกรณ์ฮาร์ดแวร์หน้าร้าน)
                    </p>
                    <div class="mermaid-container">
                        <div class="mermaid">
flowchart TB
    %% Level 1: Central Server
    subgraph CentralERP["🌐 1. เซิร์ฟเวอร์หลัก Pop ERP (Cloud / On-Premise Laravel)"]
        direction LR
        MasterDB[("🗄️ ฐานข้อมูลกลาง<br>(MySQL Database)")]
        API["⚡ RESTful API Endpoints<br>(PosController / Token Auth)"]
        UpdateServer["🚀 Update Server<br>(v1.12.23 Release)"]
        
        API <--> MasterDB
        UpdateServer -.->|"เช็คเวอร์ชันใหม่"| API
    end

    %% Level 2: Front-End POS Terminals
    subgraph Frontends["💻 2. จุดขายหน้าร้าน (Dual-Platform Terminals)"]
        direction LR
        
        subgraph WebPOS["🌐 Web POS (บนเบราว์เซอร์ Chrome/Edge)"]
            direction TB
            WEB_UI["🖥️ Web Responsive UI<br>(Blade + Alpine.js)"]
            WEB_PRINT["🖨️ Web Print Driver<br>(Browser Dialog)"]
            WEB_UI --> WEB_PRINT
        end

        subgraph DesktopApp["🖥️ Desktop Python POS (Native App - Offline Ready)"]
            direction TB
            APP_CORE["⚡ POS Engine & Touch UI<br>(Python PySide6 / Qt)"]
            SQLiteCache[("💾 Local SQLite Cache<br>(เก็บบิลออฟไลน์ / แคชสินค้า)")]
            CustomerDisp["📺 หน้าจอฝั่งลูกค้า<br>(2nd Display QR Code)"]
            
            APP_CORE <--> SQLiteCache
            APP_CORE -->|"แสดงยอด/QR"| CustomerDisp
        end
    end

    %% Level 3: Hardware Peripherals
    subgraph Hardware["🔌 3. อุปกรณ์ฮาร์ดแวร์หน้าร้าน (Peripherals)"]
        direction LR
        HW_SCANNER["🔫 เครื่องยิงบาร์โค้ด<br>(USB HID)"]
        HW_SCALE["⚖️ เครื่องชั่งน้ำหนัก<br>(Serial COM/USB)"]
        HW_PRINTER["🧾 เครื่องพิมพ์สลิปความร้อน<br>(80mm / 58mm ESC/POS)"]
        HW_DRAWER["💵 ลิ้นชักเก็บเงิน<br>(RJ11 เชื่อมผ่านเครื่องพิมพ์)"]
        
        HW_PRINTER --- HW_DRAWER
    end

    %% Connections between layers
    CentralERP <===>|"REST API / Token Sync"| DesktopApp
    CentralERP <===>|"HTTPS Web Session"| WebPOS

    HW_SCANNER ==>|"สแกนสินค้า"| DesktopApp
    HW_SCALE ==>|"อ่านน้ำหนัก"| DesktopApp
    DesktopApp ==>|"Silent Direct Print"| HW_PRINTER
    DesktopApp ==>|"สั่งเด้งอัตโนมัติ"| HW_DRAWER

    HW_SCANNER -.->|"คีย์บอร์ดเสมือน"| WebPOS
    WEB_PRINT -.->|"สั่งพิมพ์ผ่านไดรเวอร์"| HW_PRINTER
                        </div>
                    </div>

                    <!-- Comparison Table -->
                    <div style="margin-top: 1.5rem;">
                        <h5 style="font-size: 0.95rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.75rem;">
                            <i class="bi bi-table me-1 text-danger"></i> ตารางเปรียบเทียบฟังก์ชันระหว่าง Web POS กับ Desktop Python App
                        </h5>
                        <div class="table-responsive">
                            <table class="docs-table">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">คุณสมบัติ</th>
                                        <th style="width: 37%;">💻 Web POS (บนเว็บเบราว์เซอร์)</th>
                                        <th style="width: 38%;">🖥️ Desktop Python POS (แอปพลิเคชัน)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>การติดตั้งใช้งาน</strong></td>
                                        <td>เปิดผ่าน Google Chrome ใช้งานได้ทันที ไม่ต้องลงโปรแกรม</td>
                                        <td>ติดตั้งครั้งเดียวผ่านไฟล์ .exe พร้อมทำงาน</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ความพร้อมทำงานออฟไลน์</strong></td>
                                        <td>ต้องต่ออินเทอร์เน็ตตลอดเวลา</td>
                                        <td><strong>Offline-First:</strong> เน็ตหลุดยังขาย ออกบิล และบันทึกได้ปกติ</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ลิ้นชักเก็บเงิน (Cash Drawer)</strong></td>
                                        <td>อาศัยการสั่งผ่านไดรเวอร์เครื่องพิมพ์ Windows</td>
                                        <td><strong>เด้งเปิดอัตโนมัติทันที</strong> ผ่านพอร์ต RJ11/ESC-POS</td>
                                    </tr>
                                    <tr>
                                        <td><strong>การพิมพ์ใบเสร็จความร้อน</strong></td>
                                        <td>มีหน้าต่างพรีวิวพิมพ์ของเบราว์เซอร์</td>
                                        <td><strong>พิมพ์ออกทันที (Silent Print)</strong> รวดเร็วสำหรับงานขาย</td>
                                    </tr>
                                    <tr>
                                        <td><strong>หน้าจอที่สองฝั่งลูกค้า</strong></td>
                                        <td>เปิดแท็บเบราว์เซอร์แยก</td>
                                        <td><strong>มีหน้าจอ Customer Display ในตัว</strong> แสดงยอดและ QR</td>
                                    </tr>
                                    <tr>
                                        <td><strong>เครื่องชั่งน้ำหนักดิจิทัล</strong></td>
                                        <td>เชื่อมต่อผ่าน Web Serial (ต้องเปิดสิทธิ์ในเบราว์เซอร์)</td>
                                        <td><strong>เชื่อมต่อพอร์ต RS232 Serial ตรง</strong> อ่านค่าน้ำหนักแม่นยำ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Diagram 4: Cashier Lifecycle Flow -->
                <div id="section-pos-lifecycle" class="diagram-card" style="scroll-margin-top: 80px;">
                    <div class="diagram-card-head">
                        <h4>
                            <i class="bi bi-clock-history text-warning"></i> 
                            4. วงจรการทำงานประจำวันของแคชเชียร์ (Cashier Daily Lifecycle)
                        </h4>
                        <span class="badge-arch-layer" style="background:#fef3c7; color:#d97706;">Daily Shift Routine</span>
                    </div>
                    <p class="diagram-card-desc">
                        แผนผังกระบวนการทำงานตั้งแต่แคชเชียร์เข้าสู่ระบบในตอนเช้า, การเปิดกะ, การขายและพักบิลระหว่างวัน, จนถึงการนับเงินปิดกะ Z-Report สิ้นสุดวัน
                    </p>
                    <div class="mermaid-container">
                        <div class="mermaid">
flowchart TD
    START(["🟢 เริ่มต้นวันทำงาน"]) --> LOGIN["1. เข้าสู่ระบบ (Cashier Login)<br>กรอกรหัสพนักงาน หรือ PIN แคชเชียร์"]
    LOGIN --> CHECK_SHIFT{"2. ตรวจสอบสถานะกะ"}

    CHECK_SHIFT -->|"ยังไม่เปิดกะ"| OPEN_SHIFT["3. เปิดกะการขาย (Open Shift)<br>• ระบุเงินทอนตั้งต้น (Opening Cash)<br>• บันทึกเวลาเริ่มงาน"]
    CHECK_SHIFT -->|"มีกะเดิมอยู่"| MAIN_SCREEN["🛒 หน้าจอขายสินค้าหลัก (Main POS Screen)"]
    OPEN_SHIFT --> MAIN_SCREEN

    subgraph SALES_LOOP["🔄 วงจรรอบการขายสินค้า"]
        direction TB
        ADD_ITEM["4. เพิ่มสินค้าลงตะกร้า<br>• สแกนบาร์โค้ด / ชั่งน้ำหนัก / เลือกหมวดหมู่"]
        MANAGE_CART["5. จัดการตะกร้า & สมาชิก<br>• ปรับจำนวน (+/-)<br>• ใส่เบอร์โทรสะสมแต้ม / เช็คโปรโมชั่น"]
        HOLD_CHECK{"พักบิลหรือไม่?"}
        DO_HOLD["💾 พักบิลชั่วคราว (Hold Bill)<br>ดึงกลับมาคิดต่อได้ (Resume)"]
        PAY_ACTION["6. กดปุ่ม 'ชำระเงิน'"]
        PAY_METHOD{"เลือกวิธีชำระ"}
        PAY_CASH["💵 เงินสด: คำนวณเงินทอนอัตโนมัติ"]
        PAY_QR["📱 QR PromptPay: สแกนผ่านจอลูกค้า"]
        CONFIRM_PAY["7. ยืนยันชำระเงินสำเร็จ<br>• ตัดสต็อกทันที<br>• สั่งลิ้นชักเด้งเปิด<br>• พิมพ์สลิปใบเสร็จ"]

        ADD_ITEM --> MANAGE_CART
        MANAGE_CART --> HOLD_CHECK
        HOLD_CHECK -->|"พักบิล"| DO_HOLD --> ADD_ITEM
        HOLD_CHECK -->|"ชำระเงิน"| PAY_ACTION
        PAY_ACTION --> PAY_METHOD
        PAY_METHOD -->|"เงินสด"| PAY_CASH --> CONFIRM_PAY
        PAY_METHOD -->|"PromptPay"| PAY_QR --> CONFIRM_PAY
    end

    MAIN_SCREEN --> ADD_ITEM
    CONFIRM_PAY --> NEXT_CUST{"มีลูกค้าท่านต่อไป?"}
    NEXT_CUST -->|"มี"| MAIN_SCREEN

    NEXT_CUST -->|"สิ้นสุดวัน / ปิดร้าน"| CLOSE_SHIFT["8. ปิดกะการขาย (Close Shift)<br>• นับเงินสดจริงทั้งหมดในลิ้นชัก<br>• กรอกยอดเงินสดปิดกะ (Actual Closing Cash)<br>• เปรียบเทียบยอดขายกับเงินจริง (Over / Short)"]

    CLOSE_SHIFT --> PRINT_Z["9. พิมพ์รายงานสรุปยอดกะ (Z-Report)<br>พิมพ์สลิปสรุปยอดขายแยกตามช่องทาง สรุปภาษี และส่งเงินเข้าเซฟ"]
    PRINT_Z --> SYNC_ERP["10. ส่งข้อมูลปิดกะเข้าสู่ ERP กลาง<br>แจ้งเตือนยอดเข้า LINE OA ผู้บริหาร และบันทึกลงบัญชี GL อัตโนมัติ"]
    SYNC_ERP --> END_DAY(["🏁 จบงานประจำวัน"])
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- Interactive Manual Reader Modal -->
    <div id="vgReaderModal" class="reader-backdrop" onclick="if(event.target === this) closeManualReader()">
        <div class="reader-card">
            <div class="reader-head">
                <h3 class="reader-title" id="vgModalTitle">
                    <i class="bi bi-book-half text-danger"></i> คู่มือการใช้งาน
                </h3>
                <button type="button" class="reader-close" onclick="closeManualReader()" aria-label="ปิด">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Tabs Bar -->
            <div class="reader-step-tabs" id="vgStepTabs"></div>

            <!-- Body -->
            <div class="reader-body">
                <div class="reader-img-wrap" onclick="openLightbox(document.getElementById('vgStepImg').src)">
                    <img id="vgStepImg" src="" alt="หน้าจอขั้นตอน">
                    <div class="reader-img-tip"><i class="bi bi-zoom-in me-1"></i> คลิกเพื่อดูรูปขยายใหญ่</div>
                </div>

                <div class="reader-info">
                    <span class="reader-step-badge" id="vgStepBadge">ขั้นตอนที่ 1</span>
                    <h4 class="reader-step-title" id="vgStepTitle">ชื่อขั้นตอน</h4>
                    <div class="reader-step-desc" id="vgStepDesc">คำอธิบายขั้นตอน</div>
                    <div class="reader-tip-box" id="vgStepTipWrap" style="display:none;">
                        <i class="bi bi-lightbulb-fill flex-shrink-0" style="font-size:1.1rem;"></i>
                        <span id="vgStepTip">คำแนะนำเพิ่มเติม</span>
                    </div>
                </div>
            </div>

            <!-- Foot Controls -->
            <div class="reader-foot">
                <button type="button" class="reader-nav-btn" id="vgPrevBtn" onclick="navigateStep(-1)">
                    <i class="bi bi-chevron-left"></i> ย้อนกลับ
                </button>
                <span class="reader-counter" id="vgStepCounter">ขั้นตอน 1 จาก 5</span>
                <button type="button" class="reader-nav-btn" id="vgNextBtn" onclick="navigateStep(1)">
                    ถัดไป <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div id="imgLightbox" class="reader-backdrop" style="z-index: 1100;" onclick="this.style.display='none'">
        <div style="position:relative; max-width:95vw; max-height:95vh;">
            <img id="lightboxImg" src="" style="max-width:100%; max-height:90vh; border-radius:12px; box-shadow:0 20px 50px rgba(0,0,0,0.8);">
            <button type="button" style="position:absolute; top:-15px; right:-15px; width:36px; height:36px; border-radius:50%; background:#fff; color:#000; border:none; font-size:18px; cursor:pointer;" onclick="document.getElementById('imgLightbox').style.display='none'">✕</button>
        </div>
    </div>

    <script>
        const manualGuidesData = {
            python_pos: {
                title: 'คู่มือการใช้งานโปรแกรมแคชเชียร์หน้าร้าน (Python POS v1.12.23)',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'เข้าสู่ระบบแคชเชียร์ (Cashier Login)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชื่อผู้ใช้ / รหัสพนักงาน:</strong> ระบุชื่อผู้ใช้งานของแคชเชียร์ประจำเครื่อง</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รหัสผ่าน (PIN):</strong> กรอกรหัสผ่านเพื่อยืนยันตัวตนพนักงาน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีน้ำเงิน "เข้าสู่ระบบ":</strong> หรือกด Enter เพื่อตรวจสอบสิทธิ์เข้าใช้งานโปรแกรม</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step1.png') }}',
                        tip: 'ผู้ใช้งานต้องมีสิทธิ์แคชเชียร์ที่กำหนดไว้ในระบบ ERP จึงจะสามารถเข้าสู่ระบบ POS ได้'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'เปิดกะขาย & ใส่เงินทอนเริ่มต้น (Shift Open)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ข้อมูลสาขา & แคชเชียร์:</strong> ตรวจสอบชื่อสาขาและพนักงานผู้รับผิดชอบกะการขาย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กรอกเงินทอนตั้งต้น:</strong> ระบุจำนวนเงินสดเริ่มต้นในลิ้นชัก (เช่น 1,000.00 บาท)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "🔓 เปิดกะ":</strong> เพื่อเริ่มต้นรอบการขายและเริ่มคิดเงินลูกค้า</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step2.png') }}',
                        tip: 'ยอดเงินทอนตั้งต้นนี้จะถูกนำไปรวมคำนวณกระทบยอดเงินสดเมื่อปิดกะสิ้นวัน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'หน้าต่างขายหลัก & จัดการตะกร้า (Point of Sale)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ยิงบาร์โค้ด / ค้นหา SKU / เลือกหมวดหมู่:</strong> สแกนบาร์โค้ดสินค้า หรือคลิกแท็บหมวดหมู่ด้านบนเพื่อเลือกสินค้าลงบิล</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รายการในตะกร้า & ยอดรวม:</strong> ตรวจสอบรายการในตารางฝั่งขวา ปรับจำนวน แก้ไข หรือลบรายการที่ไม่ต้องการ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีน้ำเงิน "💳 ชำระเงิน (F12)":</strong> หรือกดปุ่มคีย์ลัด [F12] เพื่อเปิดหน้าต่างรับชำระเงิน</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step3.png') }}',
                        tip: 'สามารถกด F12 บนแป้นพิมพ์เพื่อเปิดหน้าชำระเงินได้ทันทีโดยไม่ต้องคลิกเมาส์'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'ชำระเงินด้วยเงินสด & คำนวณเงินทอน (Cash Payment)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกวิธีชำระ "เงินสด":</strong> ตรวจสอบการ์ดสรุปยอดเงินและจำนวนชิ้นที่ต้องชำระ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ยอดเงินที่รับ & เงินทอน:</strong> กดแป้นตัวเลขหรือปุ่มด่วน (500, 1,000) ระบบจะแสดงเงินทอนสีเขียวอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดยืนยันชำระเงิน (Enter):</strong> บันทึกตัดสต็อก สั่งเปิดลิ้นชักเก็บเงิน และพิมพ์ใบเสร็จอัตโนมัติ</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step4.png') }}',
                        tip: 'หากลูกค้าให้เงินสดพอดี สามารถกดปุ่มจำนวนเงินพอดีเพื่อยืนยันได้รวดเร็ว'
                    },
                    {
                        badge: 'ขั้นตอนที่ 5',
                        title: 'สแกนจ่ายด้วย Thai QR Code (PromptPay)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>กดเลือกแท็บ "QR":</strong> สลับเข้าสู่โหมดรับเงินโอนพร้อมเพย์</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สแกน QR Code:</strong> ระบบจะสร้าง QR Code พร้อมเพย์ตามยอดบิลให้ลูกค้าสแกนจ่ายผ่านแอปธนาคาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดยืนยันชำระเงิน (Enter):</strong> เมื่อตรวจสอบสลิปการโอนเรียบร้อย กดยืนยันเพื่อปิดบิลและพิมพ์ใบเสร็จ</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step5.png') }}',
                        tip: 'หากมีจอแสดงผลฝั่งลูกค้า (Customer Display) QR Code จะขึ้นแสดงบนจอลูกค้าโดยอัตโนมัติ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 6',
                        title: 'ปิดกะทำงานสิ้นสุดวัน (Shift Close & Summary)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ยอดเงินในระบบ:</strong> ตรวจสอบยอดเงินสดที่ระบบคำนวณไว้ (เงินทอนตั้งต้น + ยอดขายเงินสด)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ยอดเงินนับจริง:</strong> แคชเชียร์นับเงินสดจริงทั้งหมดในลิ้นชักและกรอกลงในช่อง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีแดง "ยืนยันปิดกะ":</strong> ระบบจะทำการปิดกะ พิมพ์ใบสรุปยอดขาย Z-Report และส่งยอดซิงค์เข้าสู่ระบบ ERP</div></div>`,
                        img: '{{ asset('images/manual/python_pos_step6.png') }}',
                        tip: 'ควรปิดกะทุกครั้งเมื่อเปลี่ยนพนักงานหรือเมื่อสิ้นสุดวันทำการ'
                    }
                ]
            },
            pos: {
                title: 'ระบบขายหน้าร้าน (JET POS v1.12.23)',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'เปิดกะขาย & ใส่เงินทอนเริ่มต้น (Shift Open)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ข้อมูลสาขา & แคชเชียร์:</strong> ตรวจสอบชื่อสาขาและพนักงานผู้รับผิดชอบกะการขาย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กรอกเงินทอนตั้งต้น:</strong> ระบุจำนวนเงินสดเริ่มต้นในลิ้นชัก (เช่น 1,000.00 บาท)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "🔓 เปิดกะ":</strong> เพื่อเริ่มต้นรอบการขายและเริ่มคิดเงินลูกค้า</div></div>`,
                        img: '{{ asset('images/manual/pos_step1.png') }}',
                        tip: 'เงินทอนเริ่มต้นนี้จะถูกนำไปคำนวณกระทบยอดเงินสดเมื่อสิ้นสุดวันในใบรายงาน Z-Report'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'เลือกสินค้า / ยิงบาร์โค้ด / หมวดหมู่สินค้า',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาสินค้า & สแกนบาร์โค้ด:</strong> พิมพ์รหัส SKU ชื่อสินค้า หรือใช้เครื่องสแกนยิงบาร์โค้ดในช่องค้นหา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลือกหมวดหมู่สินค้า:</strong> คลิกแท็บหมวดหมู่ด้านบน (ทั้งหมด, เครื่องดื่ม, ของว่าง, เมนูแนะนำ) เพื่อกรองรายการ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>คลิกเลือกการ์ดสินค้า:</strong> แตะที่รูปหรือชื่อสินค้าเพื่อเพิ่มลงตะกร้าขายฝั่งขวาทันที</div></div>`,
                        img: '{{ asset('images/manual/pos_step2.png') }}',
                        tip: 'หากสินค้าขึ้นสต็อกคงเหลือ 0 ระบบจะแจ้งเตือนและบล็อกไม่ให้ขายจนกว่าจะมีการทำรายการรับโอนสต็อก'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'จัดการตะกร้าสินค้า & คำนวณยอดชำระ',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>รายการในตะกร้า & ปรับจำนวน:</strong> ตรวจสอบรายการในตารางฝั่งขวา กดปรับจำนวน หรือลบรายการที่ไม่ต้องการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สรุปยอดเงิน & ภาษี:</strong> ระบบคำนวณยอดรวม, ส่วนลด, ภาษีมูลค่าเพิ่ม (VAT 7%) และยอดสุทธิโดยอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีน้ำเงิน "💳 ชำระเงิน (F12)":</strong> หรือกดปุ่มลัด [F12] เพื่อเปิดหน้าต่างรับชำระเงิน</div></div>`,
                        img: '{{ asset('images/manual/pos_step3.png') }}',
                        tip: 'สามารถกด F12 บนแป้นพิมพ์เพื่อเปิดหน้าชำระเงินได้ทันทีโดยไม่ต้องคลิกเมาส์'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'ชำระเงินด้วยเงินสด & พิมพ์ใบเสร็จ (Cash Payment)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกวิธีชำระ "เงินสด":</strong> ตรวจสอบการ์ดสรุปยอดเงินและจำนวนชิ้นที่ต้องชำระ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ยอดเงินที่รับ & เงินทอน:</strong> กดแป้นตัวเลขหรือปุ่มด่วน (500, 1,000) ระบบจะแสดงเงินทอนสีเขียวอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดยืนยันชำระเงิน (Enter):</strong> บันทึกตัดสต็อก สั่งเปิดลิ้นชักเก็บเงิน และพิมพ์ใบเสร็จอัตโนมัติ</div></div>`,
                        img: '{{ asset('images/manual/pos_step4.png') }}',
                        tip: 'เครื่องพิมพ์ใบเสร็จและลิ้นชักเก็บเงินจะทำงานโดยอัตโนมัติเมื่อกดยืนยันชำระเงิน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 5',
                        title: 'ปิดกะทำงานประจำวัน (Shift Close & Z-Report)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ยอดเงินในระบบ:</strong> ตรวจสอบยอดเงินสดที่ระบบคำนวณไว้ (เงินทอนตั้งต้น + ยอดขายเงินสด)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ยอดเงินนับจริง:</strong> แคชเชียร์นับเงินสดจริงทั้งหมดในลิ้นชักและกรอกลงในช่อง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีแดง "ยืนยันปิดกะ":</strong> ระบบจะทำการปิดกะ พิมพ์ใบสรุปยอดขาย Z-Report และส่งยอดซิงค์เข้าสู่ระบบ ERP</div></div>`,
                        img: '{{ asset('images/manual/pos_step5.png') }}',
                        tip: 'ควรปิดกะทุกสิ้นวัน เพื่อตรวจสอบว่าเงินสดในลิ้นชักตรงกับยอดขายในระบบหรือไม่'
                    }
                ]
            },
            products: {
                title: 'ระบบจัดการสินค้าและราคา (Products Master)',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'ตรวจสอบสต็อก & เลือกดูรายสาขา',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองคลังสินค้า:</strong> เลือกสาขาที่ต้องการดูเพื่อตรวจสอบยอดคงเหลือเฉพาะสาขานั้น</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปภาพรวม:</strong> ดูจำนวนสินค้าทั้งหมด ยอดรวม และการเตือนสต็อกต่ำกว่าเกณฑ์</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางสต็อกรายชิ้น:</strong> ดูสต็อกคงเหลือ ราคาขาย และสถานะการใช้งานของสินค้าแต่ละตัว</div></div>`,
                        img: '{{ asset('images/manual/products_step1.png') }}',
                        tip: 'ตัวเลขสต็อกในตารางตัดจุดทศนิยม .0000 ให้อัตโนมัติเพื่อความอ่านง่าย แต่สำหรับสินค้าชั่งน้ำหนักจะยังคงแสดงเศษทศนิยมตามจริง'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'ค้นหาสินค้า & กรองตามประเภท',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาสินค้า:</strong> พิมพ์รหัส SKU หรือชื่อสินค้า ระบบจะค้นหาและแสดงผลทันที</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตัวกรองหมวดหมู่:</strong> เลือกประเภทสินค้าเพื่อกรองดูเฉพาะกลุ่มที่สนใจ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ผลลัพธ์การค้นหา:</strong> รายการสินค้าที่ตรงตามคำค้นหาจะแสดงในตารางทันที</div></div>`,
                        img: '{{ asset('images/manual/products_step2.png') }}',
                        tip: 'สามารถกดปุ่มล้าง (x) เพื่อยกเลิกตัวกรองทั้งหมดและกลับสู่รายการสินค้าทั้งหมดได้ตลอดเวลา'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'เพิ่มสินค้าใหม่ (+ เพิ่มสินค้า)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>หมวดหมู่ & หน่วยนับ:</strong> เลือกประเภทสินค้า (ระบบรันรหัสอัตโนมัติ) และระบุหน่วยหลัก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ชื่อสินค้า:</strong> กรอกชื่อภาษาไทยและภาษาอังกฤษให้ชัดเจน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ราคา & จุดสั่งซื้อ:</strong> กำหนดราคาขายเริ่มต้น คิดภาษี VAT และตั้งจุดเตือนสต็อกสั่งซื้อ</div></div>`,
                        img: '{{ asset('images/manual/products_step3.png') }}',
                        tip: 'หากเลือกหมวดหมู่ ระบบสามารถรันรหัสสินค้า (Auto SKU) ให้อัตโนมัติ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'สินค้าชั่งน้ำหนัก & ทะเบียนเครื่องชั่ง (PLU)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>กรองสินค้าชั่งน้ำหนัก:</strong> เลือกรูปแบบการขายเป็น "สินค้าชั่งน้ำหนัก"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "ทะเบียนสินค้าเครื่องชั่ง":</strong> กดเพื่อเข้าไปจัดการรหัส PLU เชื่อมกับตู้ชั่งหน้าร้าน</div></div>`,
                        img: '{{ asset('images/manual/products_step4.png') }}',
                        tip: 'เมื่อสินค้าลดลงถึงจุดเตือนสั่งซื้อ ระบบจะแสดงการ์ดแจ้งเตือนสีเหลืองด้านบน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 5',
                        title: 'เพิ่มสินค้า & รับเข้าสต็อกด่วน (Quick Stock-In)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ เพิ่มสินค้า":</strong> กดเพื่อสร้างสินค้าใหม่ที่ยังไม่มีในฐานข้อมูล</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่มรับเข้าด่วน (ไอคอนสีเขียว):</strong> บันทึกรับสินค้าเข้าคลังสาขาได้ทันทีโดยไม่ต้องเปิดใบซื้อ</div></div>`,
                        img: '{{ asset('images/manual/products_main.png') }}',
                        tip: 'การรับเข้าสต็อกด่วนเหมาะสำหรับการปรับยอดรับวัตถุดิบเร่งด่วนหน้าร้าน'
                    }
                ]
            },
            transfer: {
                title: 'การโอนย้ายสต็อกระหว่างสาขา (Stock Transfer)',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'รายการใบโอนสต็อก & สถานะ',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาเอกสาร:</strong> ค้นหาเลขที่ใบโอนย้ายสต็อกที่ต้องการตรวจสอบ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างใบโอนย้าย":</strong> กดเพื่อเปิดใบโอนสินค้าข้ามคลัง/สาขาใบใหม่</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบโอน:</strong> ตรวจสอบประวัติการโอน วันที่ คลังปลายทาง และสถานะ</div></div>`,
                        img: '{{ asset('images/manual/transfer_step1.png') }}',
                        tip: 'เอกสารทุกใบจะมีเลขที่รันอัตโนมัติ พร้อมระบุวันที่และผู้จัดทำ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'สร้างใบโอนสินค้าใหม่ (เลือกคลังต้นทาง-ปลายทาง)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกคลังต้นทางและปลายทาง:</strong> ระบุคลังที่ตัดของออก และคลังสาขาที่ต้องการส่งของไป</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลือกสินค้า & ระบุจำนวน:</strong> ค้นหาชื่อสินค้าและระบุจำนวนที่ต้องการโอนย้าย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "บันทึกใบโอนย้าย":</strong> กดยืนยันบันทึกเพื่อส่งเอกสารไปรออนุมัติ</div></div>`,
                        img: '{{ asset('images/manual/transfer_step2.png') }}',
                        tip: 'คลังต้นทางต้องมียอดสต็อกเพียงพอก่อนทำรายการโอน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'ใบขอเบิกโอนสินค้าจากสาขา (Transfer Request)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกคลังต้นทาง:</strong> สาขาเลือกคลังใหญ่ที่จะขอเบิกวัตถุดิบมาเติมหน้าร้าน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เพิ่มรายการสินค้าที่ขอเบิก:</strong> ใส่สินค้าและจำนวนที่สาขาต้องการขอเติมสต็อก</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "ส่งคำขอ":</strong> ส่งเรื่องให้คลังใหญ่จัดเตรียมสินค้าส่งมอบ</div></div>`,
                        img: '{{ asset('images/manual/transfer_step3.png') }}',
                        tip: 'สามารถเพิ่มสินค้าได้หลายรายการในใบขอเบิกโอนใบเดียวกัน'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'อนุมัติ & สาขาปลายทางยืนยันรับเข้า',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาและเปิดเอกสาร:</strong> ค้นหาเลขที่ใบโอนที่ส่งมาถึงสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กดปุ่มสร้าง/อนุมัติ:</strong> จัดการขั้นตอนการโอนของคลังสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ยืนยันรับเข้า:</strong> เมื่อตรวจสอบสินค้าแล้ว กดยืนยันรับเข้าเพื่อเพิ่มสต็อกสาขา</div></div>`,
                        img: '{{ asset('images/manual/transfer_step4.png') }}',
                        tip: 'หากสาขาหน้าร้านติดลบ ให้แอดมินทำรายการโอนนี้เพื่อเติมสต็อกให้สาขาเป็นบวก'
                    }
                ]
            },
            counts: {
                title: 'การตรวจนับสต็อกสินค้า (Stock Count)',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'เปิดใบตรวจนับสต็อกสินค้าใหม่',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกสาขาและตำแหน่งเก็บ:</strong> ระบุสาขาและคลังที่ต้องการตรวจนับ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลือกโหมดการนับ:</strong> "นับบางส่วน (Partial)" หรือ "นับเต็ม (Full)"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "เปิดใบตรวจนับ":</strong> ระบบจะดึงสินค้าทุกตัวในคลังมาเป็นรายการตรวจนับ</div></div>`,
                        img: '{{ asset('images/manual/counts_step1.png') }}',
                        tip: 'ระบบจะดึงสินค้าทุกตัวที่มีสต็อกในตำแหน่งเก็บนั้นมาเป็นรายการตรวจนับ พร้อมยอดตามระบบ ณ ตอนเปิดใบ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'รายการใบตรวจนับสินค้า',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตารางรายการใบตรวจนับ:</strong> ดูเลขที่ วันที่ สาขา และจำนวนรายการสินค้า</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สถานะใบตรวจนับ:</strong> ดูสถานะ กำลังนับ / รอตรวจยืนยัน / ปรับปรุงแล้ว</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่ม "กรอกยอดนับ":</strong> เข้าสู่หน้าบันทึกจำนวนที่นับได้จริงเปรียบเทียบกับระบบ</div></div>`,
                        img: '{{ asset('images/manual/counts_step2.png') }}',
                        tip: 'สามารถมีใบนับสต็อกแยกตามสาขาและวันที่ได้อย่างอิสระ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'นับสินค้าจริง & สแกนบาร์โค้ด (Tablet Mode)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>หัวเอกสาร & โหมดตรวจนับ:</strong> ตรวจสอบเลขที่ใบตรวจนับและสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กล่องสแกนบาร์โค้ด:</strong> ใช้เครื่องสแกนยิงบาร์โค้ดเพื่อบวกยอดทีละชิ้นอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มนับด่วน & Sync:</strong> กดเพิ่มทีละ +1, +5, +10 หรือนับเป็นลัง แล้วกด Sync ข้อมูล</div></div>`,
                        img: '{{ asset('images/manual/counts_step3.png') }}',
                        tip: 'แนะนำให้นับในช่วงก่อนเปิดร้านหรือหลังปิดร้านเพื่อไม่ให้ยอดขายกระทบ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'ตรวจสอบผลต่าง (Variance) & ปรับปรุงยอดบัญชี',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหา & ป้ายสรุปผลต่าง:</strong> ดูยอดนับจริง vs ยอดตามระบบ และสรุปส่วนต่าง (Variance)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายการตรวจนับ:</strong> เช็คยอดขาด/ยอดเกิน แยกตามแต่ละรายการสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มบันทึกยอดนับ & จบการนับ:</strong> บันทึกข้อมูลและส่งเรื่องเพื่อปรับปรุงยอดบัญชี</div></div>`,
                        img: '{{ asset('images/manual/counts_step4.png') }}',
                        tip: 'การปรับยอดจะบันทึกเข้า Stock Ledger อย่างโปร่งใสและตรวจสอบย้อนหลังได้'
                    }
                ]
            },
            settings: {
                title: 'คู่มือการตั้งค่าระบบและฟังก์ชันเอกสาร (System & Settings Master Guide)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ฟังก์ชั่นเอกสาร (ราคารวมภาษี, VAT 7%, วันเครดิต)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตั้งค่าราคาในเอกสาร:</strong> เลือกว่าราคาสินค้าที่แสดงเป็น "ราคารวมภาษี (Included)" หรือ "ไม่รวมภาษี (Excluded)"</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>อัตราภาษีมูลค่าเพิ่ม (VAT Rate):</strong> กำหนดอัตราร้อยละ (มาตรฐาน 7.00%) ซึ่งระบบจะนำไปคำนวณใน ภ.พ.30 และสมุดรายวัน GL อัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>เครดิตเริ่มต้น (วัน):</strong> กำหนดจำนวนวันครบกำหนดชำระเงินของลูกหนี้เมื่อเปิดใบขายเชื่อ (เช่น 30 วัน)</div></div>`,
                        img: '{{ asset('images/manual/settings_step1.png') }}',
                        tip: 'เมื่อแก้ไขอัตราภาษีหรือราคา จะมีผลกับเอกสารใหม่ที่สร้างขึ้นตั้งแต่วันนี้เป็นต้นไป โดยไม่กระทบเอกสารเดิมที่บันทึกแล้ว'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'เลขรันเอกสาร (Document Numbering & Sequences)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>รหัสเอกสารนำหน้า (Prefix):</strong> กำหนดตัวอักษรย่อ เช่น INV (ใบแจ้งหนี้), PO (ใบสั่งซื้อ), REC (ใบเสร็จ)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รูปแบบปีและเดือน (Format):</strong> เลือก YYYYMM (เช่น 202610) หรือ YYMM เพื่อระบุรอบบัญชีในเลขที่เอกสาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>จำนวนหลักและรอบรีเซ็ต:</strong> กำหนดความยาวตัวเลขรัน (เช่น 4 หลัก 0001-9999) และให้รีเซ็ตเริ่ม 0001 ใหม่ทุกต้นเดือน พร้อมปุ่มจัดการสมุดเอกสาร</div></div>`,
                        img: '{{ asset('images/manual/settings_step2.png') }}',
                        tip: 'การจัดรูปแบบเลขที่เอกสารที่ดีช่วยให้ฝ่ายบัญชีและตรวจสอบภายในสามารถสอบทานตามงวดได้อย่างถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'โลโก้และตราประทับบริษัท (Company Logo & Stamp)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>เลือกโลโก้ระบบ (Presets):</strong> เลือกตราสัญลักษณ์มาตรฐานที่ระบบเตรียมไว้ให้เพื่อความรวดเร็ว</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>อัปโหลดโลโก้และตราประทับใหม่:</strong> อัปโหลดรูปภาพ PNG พื้นหลังโปร่งใส ใช้แสดงบนหัวบิลและตรายางดิจิทัลบนเอกสาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> เพื่ออัปเดตรูปภาพเข้าสู่ระบบส่วนกลางและแสดงบนเอกสารทุกประเภททันที</div></div>`,
                        img: '{{ asset('images/manual/settings_step3.png') }}',
                        tip: 'ควรใช้รูปภาพความละเอียดสูง (แนะนำ 600x600 px ขึ้นไป) เพื่อให้พิมพ์ออกมาคมชัดบนเอกสารกระดาษและ PDF'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'หมายเหตุเอกสาร (Document Notes & Footer Text)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ข้อความหมายเหตุท้ายเอกสาร:</strong> กำหนดข้อความที่จะแสดงท้ายใบเสนอราคาและใบแจ้งหนี้อัตโนมัติ เช่น "สินค้าแช่แข็งกรุณาตรวจรับทันทีเมื่อส่งมอบ / ราคานี้ยืน 15 วัน"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> บันทึกข้อความเพื่อนำไปใช้เป็นค่าเริ่มต้นสำหรับเอกสารฉบับถัดไปทันที</div></div>`,
                        img: '{{ asset('images/manual/settings_step4.png') }}',
                        tip: 'การระบุเงื่อนไขการส่งมอบและอายุราคาชัดเจนช่วยลดข้อพิพาททางการค้ากับลูกค้า'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ข้อมูลกิจการและผู้เสียภาษี (Company Information)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชื่อกิจการ (ไทย/อังกฤษ):</strong> กรอกชื่อนิติบุคคลให้ตรงกับหนังสือรับรองกระทรวงพาณิชย์</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เลขประจำตัวผู้เสียภาษี 13 หลัก:</strong> ระบุเลข 13 หลักและรหัสสาขา (00000 สำหรับสำนักงานใหญ่)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ที่อยู่และช่องทางติดต่อ:</strong> ระบุที่อยู่สถานประกอบการ เบอร์โทร และอีเมล ซึ่งจะถูกนำไปพิมพ์ลงในเอกสารภาษี</div></div>`,
                        img: '{{ asset('images/manual/settings_step5.png') }}',
                        tip: 'ข้อมูลนี้มีความสำคัญทางกฎหมายภาษีอากร ตรวจสอบความถูกต้องให้ตรงกับ ภ.พ.20 ของกิจการ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'ศูนย์ควบคุมโมดูลระบบ (Module Control Center)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บหมวดหมู่โมดูล:</strong> สลับดูแฟ้มหลัก แฟ้มเสริม และการควบคุมโมดูลแต่ละสายงาน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปสถานะโมดูล:</strong> แสดงจำนวนโมดูลที่เปิดใช้งานและโมดูลที่ปิดพักไว้</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>สวิตช์เปิด/ปิดโมดูลระบบ:</strong> เปิดใช้โมดูล Kitchen BOM, แปรรูปเนื้อสัตว์ Transform, e-Tax หรือสั่งจองตามความพร้อมของธุรกิจ</div></div>`,
                        img: '{{ asset('images/manual/settings_step6.png') }}',
                        tip: 'การปิดโมดูลจะซ่อนเมนูและระงับการเข้าถึงชั่วคราว แต่ข้อมูลประวัติในอดีตยังคงถูกเก็บรักษาไว้อย่างปลอดภัย 100%'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'POS Designer (ออกแบบผังหน้าจอแคชเชียร์)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ผังหน้าจอ Canvas (12x8 Grid):</strong> ลากและจัดวางตำแหน่งช่องค้นหา, แท็บหมวดหมู่, และรายการการ์ดสินค้าให้เหมาะกับหน้าจอสัมผัส</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ส่วนประกอบ Toolbox:</strong> เลือกดึงวิดเจ็ตตารางตะกร้าขายฝั่งขวา, แป้นตัวเลข, และปุ่มคิดเงินด่วน (F12)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "Build & เผยแพร่ (Publish)":</strong> ส่งผังหน้าจอเวอร์ชันใหม่ไปยังเครื่องแคชเชียร์ POS หน้าร้านทุกสาขา</div></div>`,
                        img: '{{ asset('images/manual/settings_step7.png') }}',
                        tip: 'เมื่อกด Publish เลย์เอาต์เวอร์ชันใหม่ เครื่อง POS หน้าร้านจะรับค่าและปรับหน้าจอตามโดยอัตโนมัติเมื่อทำการ Sync'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'จัดลำดับเมนูระบบ (Menu Section Ordering)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>รายการหมวดหมู่เมนูหลัก:</strong> แสดงลำดับแถบเมนูปัจจุบัน เช่น ภาพรวม, งานประจำวัน, POS, คลัง, การเงิน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่มเลื่อนขึ้น (▲) และเลื่อนลง (▼):</strong> กดปรับสลับตำแหน่งหมวดหมู่เมนูให้สอดคล้องกับ Workflow ขององค์กร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> จัดเก็บบันทึกลำดับเมนูใหม่ให้มีผลกับผู้ใช้งานทุกคนในระบบ</div></div>`,
                        img: '{{ asset('images/manual/settings_step8.png') }}',
                        tip: 'การจัดลำดับเมนูที่ใช้บ่อยให้อยู่ด้านบนช่วยลดเวลาในการสลับหน้าจอของพนักงาน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'ปรับธีมระบบและเลย์เอาต์ (ERP Theme & Layout)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชุดสีของระบบ (Themes):</strong> เลือกพาเลตต์สีที่สบายตา เช่น ฟ้า JET, กรมท่า, เขียวมรกต, เทาสเลต, หรืออ่านง่าย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รูปแบบหน้าหลังบ้าน (Layout):</strong> เลือกระหว่างเลย์เอาต์ "แบบเดิม PopCentral (Sidebar Rail)" หรือ "แบบ Odoo (Topbar Modern)"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึก Layout":</strong> บันทึกรูปแบบหน้าจอและชุดสีเพื่อปรับใช้อัตโนมัติทันที</div></div>`,
                        img: '{{ asset('images/manual/settings_step9.png') }}',
                        tip: 'การปรับเปลี่ยนธีมและเลย์เอาต์มีผลเฉพาะการแสดงผล UI โดยไม่กระทบต่อข้อมูลขาย สต็อก หรือบัญชีใดๆ ทั้งสิ้น'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 10',
                        title: 'ข้อมูลการรับชำระเงิน (Payments & PromptPay QR)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ข้อมูลบัญชีธนาคาร:</strong> กำหนดธนาคาร เลขที่บัญชี และชื่อบัญชีสำหรับรับโอนเงินค่าสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>QR รับชำระ PromptPay:</strong> ผูกเบอร์โทรหรือเลขผู้เสียภาษี 13 หลักสำหรับสร้าง Dynamic QR Code รับเงินผ่านจอ POS</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> ยืนยันการตั้งค่าช่องทางชำระเงินเข้าสู่ระบบแคชเชียร์</div></div>`,
                        img: '{{ asset('images/manual/settings_step10.png') }}',
                        tip: 'Dynamic QR Code ที่สร้างขึ้นจะระบุยอดเงินที่ต้องชำระพอดี ช่วยป้องกันปัญหาลูกค้าสแกนจ่ายเงินขาดหรือเกิน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 11',
                        title: 'บันทึกบัญชีและผูกผังบัญชี (Default GL Accounts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ผังบัญชีอัตโนมัติ:</strong> กำหนดรหัสบัญชีที่ระบบใช้ลงรายการอัตโนมัติ เช่น รายได้จากการขาย, ลูกหนี้, เจ้าหนี้, ภาษีซื้อ/ขาย และต้นทุนขาย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>งบการเงินและการปิดบัญชี:</strong> เชื่อมต่องบดลอง งบกำไรขาดทุน และงบแสดงฐานะการเงินจากรายการบัญชีอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กดปุ่มสีเขียว "💾 บันทึกข้อมูล":</strong> ยืนยันการผูกรหัสบัญชีเข้าสู่ระบบสมุดรายวัน GL</div></div>`,
                        img: '{{ asset('images/manual/settings_step11.png') }}',
                        tip: 'การผูกผังบัญชีที่ถูกต้องจะทำให้ระบบสามารถออกงบการเงิน P&L ได้ทันทีเมื่อปิดกะแคชเชียร์'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 12',
                        title: 'ออกแบบแม่แบบใบเสร็จ POS (Receipt Template Customizer)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ส่วนประกอบหัวสลิป & สาขา:</strong> ลากและวางโลโก้, ชื่อกิจการ, เลขผู้เสียภาษี, และข้อมูลสาขาลงบนแบบใบเสร็จ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ส่วนประกอบท้ายสลิป & ข้อความขอบคุณ:</strong> กำหนดข้อความท้ายใบเสร็จ, สรุปภาษีมูลค่าเพิ่ม, เส้นคั่น, และ QR Code สอบถาม</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>Live Preview ใบเสร็จจริง (80 มม. / 58 มม.):</strong> แสดงผลจำลองสลิปขนาดจริง พร้อมปุ่ม "บันทึกแบบ" ส่งไปยังเครื่องพิมพ์ใบเสร็จความร้อน</div></div>`,
                        img: '{{ asset('images/manual/settings_step12.png') }}',
                        tip: 'สามารถเลือกขนาดกระดาษความร้อน 80 มม. หรือ 58 มม. ให้ตรงกับรุ่นของเครื่องพิมพ์สลิปประจำสาขาได้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 13',
                        title: 'สายการอนุมัติเอกสารและวงเงิน (Workflows & Approval Limits)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>สายการอนุมัติใบสั่งซื้อ (PO):</strong> กำหนดเพดานวงเงิน เช่น เกิน 50,000 บาท ต้องให้ผู้จัดการฝ่ายจัดซื้ออนุมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การอนุมัติปรับยอดสต็อก & ใบลดหนี้:</strong> กำหนดให้หัวหน้าคลังหรือผู้จัดการสาขาอนุมัติผ่าน PIN ป้องกันการทุจริต</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "⚙️ บันทึก Workflow":</strong> ปรับเปลี่ยนรายชื่อผู้อนุมัติ ลำดับขั้น และเงื่อนไขตามโครงสร้างองค์กร</div></div>`,
                        img: '{{ asset('images/manual/settings_step13.png') }}',
                        tip: 'เอกสารที่ต้องผ่านการอนุมัติจะมีสถานะเป็น Submitted/Pending Approval จนกว่าผู้มีสิทธิ์จะกดอนุมัติ จึงจะส่งต่อไปตัดสต็อกหรือลงบัญชีได้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 14',
                        title: 'สำรองข้อมูลและความปลอดภัย (Backup, Security & Database Operations)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>สำรองข้อมูลฉุกเฉิน (Backup Snapshot):</strong> กดปุ่ม "💾 Backup ตอนนี้" เพื่อบีบอัดและสร้างไฟล์สำรองฐานข้อมูลพร้อมรหัสตรวจสอบ SHA-256</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตรวจสอบการกู้คืนและความปลอดภัย (Verify Restore & MFA):</strong> ทดสอบกู้คืนไฟล์สำรองโดยไม่เขียนทับฐานข้อมูลจริง และตรวจสอบสถานะ MFA Session ของผู้ใช้งานในระบบ</div></div>`,
                        img: '{{ asset('images/manual/settings_step14.png') }}',
                        tip: 'การสำรองข้อมูลอย่างสม่ำเสมอเป็นหัวใจสำคัญในการปกป้องข้อมูลทางการเงินและประวัติสต็อกของธุรกิจ'
                    }
                ]
            },
            users: {
                title: 'คู่มือการจัดการผู้ใช้และสิทธิ์การเข้าถึง (User & Security Master Guide)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ค้นหาและกรองสถานะผู้ใช้ (Search & Status Filtering)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาผู้ใช้:</strong> พิมพ์รหัสพนักงาน, ชื่อ-นามสกุล, ตำแหน่งงาน หรือเบอร์โทรศัพท์ เพื่อค้นหาผู้ใช้ได้อย่างรวดเร็ว</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กรองตามสถานะบัญชี:</strong> เลือกดู "เปิดใช้งาน" (Active), "ปิดใช้งาน" (Inactive) หรือ "รอเปลี่ยนรหัสผ่าน" (Must Change Password)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "🔍 ค้นหา" และ "ล้าง":</strong> กดเพื่อประมวลผลการกรอง หรือกดล้างค่าเพื่อแสดงรายชื่อผู้ใช้ทั้งหมด</div></div>`,
                        img: '{{ asset('images/manual/users_step1.png') }}',
                        tip: 'การค้นหาสามารถพิมพ์เฉพาะบางส่วนของชื่อหรือเบอร์โทรศัพท์ได้โดยไม่ต้องกรอกครบทั้งคำ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'แผงควบคุมผู้ใช้สำหรับ POS & การออก PIN (POS Cashier & PIN Issuance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>การ์ดผู้ใช้ POS:</strong> แสดงรายชื่อแคชเชียร์ที่มีสิทธิ์เข้าใช้งานโปรแกรมหน้าร้าน พร้อมแสดงรหัสสาขาที่สังกัด</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ป้ายสถานะความพร้อม:</strong> แสดง "พร้อมเข้า POS" (สีเขียว) เมื่อมี PIN ถูกต้อง หรือ "ต้องออก/เปลี่ยน PIN" (สีส้ม)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "🔑 ออก PIN POS":</strong> กดเพื่อเปิดหน้าต่างสร้าง PIN ชั่วคราว 6 หลักสำหรับให้แคชเชียร์ล็อกอินหน้าเครื่อง POS</div></div>`,
                        img: '{{ asset('images/manual/users_step2.png') }}',
                        tip: 'PIN POS จะแยกเป็นอิสระจากรหัสผ่านของระบบ ERP เพื่อความสะดวกและรวดเร็วในการสแกนหรือกดแป้นตัวเลขหน้าร้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'แบบฟอร์มเพิ่มผู้ใช้ใหม่ - ข้อมูลทั่วไป (User Profile & Account Info)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชื่อผู้ใช้ (Username):</strong> กำหนดชื่อบัญชีภาษาอังกฤษสำหรับใช้เข้าสู่ระบบ (ห้ามซ้ำกันในระบบ)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ชื่อ-นามสกุล (เต็ม):</strong> กรอกชื่อจริงของพนักงาน เพื่อใช้แสดงในเอกสารขาย, บันทึกการอนุมัติ และ Audit Log</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตำแหน่งงาน & ช่องทางติดต่อ:</strong> ระบุตำแหน่ง เช่น แคชเชียร์, ผู้จัดการสาขา พร้อมเบอร์โทรและอีเมล</div></div>`,
                        img: '{{ asset('images/manual/users_step3.png') }}',
                        tip: 'ข้อมูลชื่อ-นามสกุลจะปรากฏบนหัวใบเสร็จรับเงินและลายเซ็นท้ายใบกำกับภาษีอย่างเป็นทางการ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'การกำหนดสาขาและสิทธิ์การเข้าถึง (Branch Access & Sales Territory)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>สาขาหลัก (Primary Branch):</strong> กำหนดสาขาประจำตัวของพนักงาน สำหรับเป็นค่าเริ่มต้นในการเปิดบิลและตัดสต็อก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สาขาที่อนุญาตเพิ่มเติม (Multiple Branches):</strong> เลือกสาขาอื่นๆ ที่อนุญาตให้พนักงานเข้าเครื่อง POS หรือทำเอกสารข้ามสาขาได้</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>สายการขาย / สายส่งเริ่มต้น:</strong> ระบุเส้นทางสายส่ง (Sales Area) สำหรับพนักงานขายหน่วยรถ (Van Sales)</div></div>`,
                        img: '{{ asset('images/manual/users_step4.png') }}',
                        tip: 'หากเลือก "ทุกสาขา / ส่วนกลาง" ผู้ใช้จะสามารถดูรายงานและทำเอกสารได้ทุกคลังสินค้าตามระดับสิทธิ์ที่ได้รับ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'การผูกบทบาทและสิทธิ์การใช้งาน (Assign Roles & Password Setup)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ชิปเลือกบทบาท (Role Chips):</strong> ติ๊กเลือกหน้าที่ เช่น แคชเชียร์, คลังสินค้า, ฝ่ายจัดซื้อ, ผู้จัดการสาขา, ผู้ดูแลระบบ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตั้งรหัสผ่านเริ่มต้น:</strong> กำหนดรหัสผ่านอย่างน้อย 4 ตัวอักษรขึ้นไป พร้อมช่องยืนยันรหัสผ่าน (จัดเก็บเข้ารหัส Bcrypt)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "เพิ่มผู้ใช้" / บันทึกข้อมูล:</strong> กดเพื่อบันทึกข้อมูลพนักงานเข้าสู่ระบบฐานข้อมูลส่วนกลางทันที</div></div>`,
                        img: '{{ asset('images/manual/users_step5.png') }}',
                        tip: 'ผู้ใช้ 1 คนสามารถมีหลายบทบาทพร้อมกันได้ เช่น ติ๊ก "แคชเชียร์" ร่วมกับ "คลังสินค้า" เพื่อให้สามารถขายหน้าร้านและรับโอนของได้ในบัญชีเดียว'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'ตารางรายชื่อผู้ใช้และสถานะสิทธิ์ (User Directory & Security Badges)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>คอลัมน์ผู้ใช้และบทบาท:</strong> แสดง Username, ชื่อ-นามสกุล, สาขา พร้อมป้ายสิทธิ์ย่อย เช่น "เปิด POS", "ขาย POS"</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>เข้าใช้ล่าสุด & สถานะบัญชี:</strong> ตรวจสอบวันเวลาที่ล็อกอินล่าสุด และสถานะ "ใช้งาน" (สีเขียว) หรือ "ปิด" (สีเทา)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มคำสั่ง "✏️ แก้ไข":</strong> กดเพื่อดึงข้อมูลผู้ใช้ขึ้นไปยังแบบฟอร์มด้านบนเพื่อแก้ไขข้อมูลหรือเปลี่ยนสาขา</div></div>`,
                        img: '{{ asset('images/manual/users_step6.png') }}',
                        tip: 'ตารางรองรับการจัดหน้า (Pagination) เมื่อมีพนักงานจำนวนมาก ทำให้โหลดข้อมูลได้รวดเร็วและเป็นระเบียบ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'การรีเซ็ตรหัสผ่าน ERP และออก PIN POS ใหม่ (Password & PIN Reset Flow)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>หน้าต่างยืนยันรีเซ็ต:</strong> แสดง Username และชื่อพนักงานเพื่อป้องกันการรีเซ็ตผิดบัญชี</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>มาตรฐานความปลอดภัย:</strong> ระบบจะสุ่มรหัสผ่านใหม่หรือ PIN 6 หลักชั่วคราว และบังคับให้พนักงานเปลี่ยนทันทีเมื่อเข้าสู่ระบบ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "🔑 ยืนยัน" & "📋 คัดลอก":</strong> กดยืนยันแล้วระบบจะแสดงรหัสผ่านครั้งเดียว พร้อมปุ่มคัดลอกส่งต่อให้พนักงานอย่างปลอดภัย</div></div>`,
                        img: '{{ asset('images/manual/users_step7.png') }}',
                        tip: 'รหัสผ่านชั่วคราวจะแสดงบนหน้าจอเพียงครั้งเดียวเท่านั้น เพื่อป้องกันไม่ให้บุคคลอื่นแอบดูย้อนหลัง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'เมทริกซ์บทบาทและสิทธิ์ตามแบบฉบับ BPlus (Roles & Permissions Matrix)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>คอลัมน์บทบาทมาตรฐาน:</strong> แสดงรหัสบทบาทระดับสากล เช่น GM, BRANCH_MGR, ACC_MGR, CASHIER, IT_MGR</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ป้ายสิทธิ์การใช้งาน (Permissions):</strong> แสดงสิทธิ์ย่อยทั้งหมดที่บทบาทนั้นได้รับอย่างละเอียดและโปร่งใส</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>การบังคับสิทธิ์ระดับ Route:</strong> ระบบตรวจสิทธิ์ทุกฟังก์ชัน เช่น pos.use (เปิดจอ), pos.sell (ขาย), pos.void (ยกเลิกบิล) ป้องกันการข้ามสิทธิ์</div></div>`,
                        img: '{{ asset('images/manual/users_step8.png') }}',
                        tip: 'โครงสร้างสิทธิ์ยึดตามมาตรฐาน BPlus เพื่อความปลอดภัยสูงสุดในการควบคุมเงินสดและสต็อกสินค้าของกิจการ'
                    }
                ]
            },
            finance: {
                title: 'คู่มือระบบการเงินและบัญชีครบวงจร (Finance & Accounting)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ปรับปรุงสมุดเงินสด & รายการเงินสดประจำวัน (Cash Books & Adjustments)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>วันที่ สาขา และยอดรับ/จ่ายเงินสด:</strong> ระบุวันที่ทำรายการ, เลือกสาขาที่รับผิดชอบ และกรอกจำนวนเงินสดรับเข้าหรือจ่ายออก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>คำอธิบาย เหตุผล และผู้อนุมัติ:</strong> ระบุรายการและเหตุผลความจำเป็นในการปรับปรุง พร้อมเลือกผู้มีอำนาจอนุมัติก่อนกดปุ่ม "บันทึก"</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางบัญชีธนาคาร & สมุดเงินสด:</strong> แสดงทะเบียนบัญชีธนาคารพร้อมสถานะ และประวัติรายการเงินสดที่บันทึกไว้ในระบบ</div></div>`,
                        img: '{{ asset('images/manual/finance_step1.png') }}',
                        tip: 'รายการปกติ (ขาย POS, ปิดกะ, ขายสด, รับชำระ, ค่าใช้จ่าย) ระบบเดินเข้าสมุดเงินสดให้อัตโนมัติ — ฟอร์มนี้ใช้เฉพาะรายการปรับปรุงพิเศษที่ต้องมีเหตุผลและผู้อนุมัติกำกับ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ทะเบียนบัญชีธนาคารบริษัท (Bank Accounts Management)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาบัญชีธนาคาร:</strong> พิมพ์ค้นหาจากชื่อธนาคาร หรือเลขที่บัญชีเพื่อตรวจสอบข้อมูลอย่างรวดเร็ว</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มบัญชี":</strong> เปิดหน้าต่างป๊อปอัปเพื่อบันทึกสมุดบัญชีธนาคารเล่มใหม่ ระบุธนาคาร เลขบัญชี ชื่อบัญชี และสาขา</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการบัญชีธนาคาร:</strong> แสดงชื่อธนาคาร, เลขที่บัญชี, ชื่อเจ้าของบัญชี, สาขาสังกัด พร้อมปุ่ม "แก้ไข" ปรับปรุงข้อมูล</div></div>`,
                        img: '{{ asset('images/manual/finance_step2.png') }}',
                        tip: 'บัญชีธนาคารที่ลงทะเบียนไว้จะถูกนำไปใช้อ้างอิงอัตโนมัติในการรับชำระเงินโอน, การออกใบเสร็จรับเงิน และการกระทบยอดสเตทเมนต์'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'บันทึกฝากและถอนเงินสดกับธนาคาร (Cash Transfers Between Cash & Bank)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ประเภทรายการโอน:</strong> เลือกประเภท "ฝากเงินสดเข้าธนาคาร" (เงินออกจากลิ้นชักเข้าแบงก์) หรือ "ถอนเงินสดจากธนาคาร" (นำเงินสดเข้าลิ้นชัก)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ข้อมูลการโอนเงิน:</strong> ระบุวันที่, จำนวนเงิน, สาขาต้นทาง และเลือกสมุดบัญชีธนาคารปลายทาง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>การ์ดประวัติรายการล่าสุด:</strong> แสดงประวัติการทำรายการฝาก/ถอนเงินสดย้อนหลัง พร้อมเชื่อมโยงการลงสมุดเงินสดและ GL ให้อัตโนมัติ</div></div>`,
                        img: '{{ asset('images/manual/finance_step3.png') }}',
                        tip: 'เมื่อบันทึกฝากหรือถอนเงินสด ระบบจะตัดยอดสมุดเงินสดและเดบิต/เครดิตบัญชีแยกประเภท (GL) ให้ทันทีโดยไม่ต้องลงบัญชีซ้ำ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ทะเบียนเช็ครับและเช็คจ่าย (Cheques Register & Status Tracking)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บสลับ "เช็ครับ" / "เช็คจ่าย":</strong> เลือกระหว่างเช็ครับจากลูกค้า หรือเช็คจ่ายสั่งจ่ายให้เจ้าหนี้/ผู้จำหน่าย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปสถานะเช็ค 4 กลุ่ม:</strong> ติดตามจำนวนและมูลค่าเช็ค (ในมือ รอนำฝาก, นำฝาก รอผ่าน, ผ่านแล้ว, เช็คคืน/เด้ง) พร้อมระบบแจ้งเตือนเช็คใกล้ครบกำหนด</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "+ บันทึกเช็คเอง" & ฟิลเตอร์:</strong> กรองค้นหาตามเลขที่เช็ค/ธนาคาร/ช่วงวันที่ หรือกดบันทึกเช็คเข้าสู่ทะเบียนโดยตรง</div></div>`,
                        img: '{{ asset('images/manual/finance_step4.png') }}',
                        tip: 'ระบบมีแถบแจ้งเตือนล่วงหน้าเมื่อมีเช็คที่ใกล้ถึงกำหนดนำฝากหรือครบกำหนดตัดบัญชีภายใน 7 วัน เพื่อป้องกันเช็คขาดอายุ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ผังบัญชีแยกประเภท & บัญชีเริ่มต้นลงบัญชี (Chart of Accounts & Defaults)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาผังบัญชี:</strong> ค้นหารหัสบัญชีหรือชื่อบัญชีทั้งภาษาไทยและอังกฤษได้อย่างสะดวก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "นำเข้า Excel" & "+ เพิ่มบัญชี":</strong> นำเข้าชุดผังบัญชีมาตรฐานผ่านไฟล์ Excel หรือสร้างรหัสบัญชีแยกประเภทใหม่ได้ทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>การ์ดบัญชีเริ่มต้นสำหรับลงบัญชีอัตโนมัติ:</strong> กำหนดคู่บัญชีมาตรฐาน เช่น เงินสด (1010), ธนาคาร (1020), ลูกหนี้ (1030), เจ้าหนี้ (2010), รายได้ (4010), ภาษี (2150)</div></div>`,
                        img: '{{ asset('images/manual/finance_step5.png') }}',
                        tip: 'เมื่อกำหนดบัญชีเริ่มต้นครบ ระบบ ERP จะบันทึกสมุดรายวันทั่วไป (GL) ให้อัตโนมัติทุกครั้งที่มีการเปิดบิลขาย ซื้อสินค้า หรือรับชำระหนี้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'สมุดรายวันทั่วไป & บันทึกบัญชีแยกประเภท GL (General Journal Entries)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บสลับ "ผังบัญชี" / "สมุดรายวันทั่วไป":</strong> นำทางระหว่างโครงสร้างผังบัญชีและรายการเดินบัญชีรายวัน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดสรุปยอดเดบิต เครดิต และผลต่าง:</strong> แสดงยอดรวมเดบิต ยอดรวมเครดิต และยืนยันผลต่างต้องเป็น 0.00 เสมอ (ระบบสมดุลคู่)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการลงบัญชีอัตโนมัติ:</strong> แสดงวันที่, เอกสารอ้างอิง, รหัสบัญชี, คำอธิบายรายการ พร้อมยอดเดบิตและเครดิตแยกรายบรรทัด</div></div>`,
                        img: '{{ asset('images/manual/finance_step6.png') }}',
                        tip: 'สมุดรายวันทั่วไปเป็นระบบ Read-Only อัตโนมัติที่โพสต์โดย GlPostingService ทำให้ตัวเลขบัญชีตรงกับเอกสารขาย/ซื้อและ POS แบบ 100%'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'งบการเงินและงบทดลองตรวจสอบยอดดุล (Financial Statements & Trial Balance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่มสลับประเภทงบการเงิน:</strong> เลือกดู "งบทดลอง (Trial Balance)", "งบกำไรขาดทุน (P&L)" หรือ "งบแสดงฐานะการเงิน (Balance Sheet)"</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟิลเตอร์ช่วงวันที่ & ปุ่มพิมพ์รายงาน:</strong> กำหนดช่วงวันที่ต้องการตรวจสอบ กดปุ่ม "แสดง" เพื่อคำนวณสด หรือกด "พิมพ์" พิมพ์ออกกระดาษ/PDF</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>แผ่นงบทดลอง & สถานะตรวจสอบยอดดุล:</strong> แสดงรหัสบัญชี, ชื่อบัญชี, หมวดหมู่, ยอดรวมเดบิต/เครดิต พร้อมเครื่องหมาย "✓ เดบิตเท่ากับเครดิต — งบดุล"</div></div>`,
                        img: '{{ asset('images/manual/finance_step7.png') }}',
                        tip: 'งบการเงินคำนวณแบบ Real-time จากสมุดรายวันทั่วไป สามารถตรวจสอบสุขภาพการเงิน กำไรสุทธิ และยอดสินทรัพย์คงเหลือได้ทันทีทุกเวลา'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'ศูนย์ภาษีไทยและ E-Tax Invoice (Tax Compliance & Electronic Filing)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟิลเตอร์เดือนภาษีและสาขา:</strong> เลือกงวดเดือนภาษีที่ต้องการสรุปยอด และเลือกสาขาหรือ "ทั้งบริษัท" เพื่อรวมศูนย์</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แท็บแบบภาษี & วงจรจัดทำชุดใหม่:</strong> เลือกประเภทแบบภาษี (ภ.พ.30, ภ.ง.ด.3, ภ.ง.ด.53) และกด "จัดทำชุดใหม่" เพื่อรวมยอดอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางแบบภาษี & เวิร์กโฟลว์ 3 ขั้น:</strong> ติดตามขั้นตอน "ผู้จัดทำ (Prepared)" → "ผู้ตรวจ (Reviewed)" → "บันทึกเลขอ้างอิงยื่นจริง (Submitted)" พร้อมดาวน์โหลด CSV และตรวจสอบ SHA-256</div></div>`,
                        img: '{{ asset('images/manual/finance_step8.png') }}',
                        tip: 'ระบบรองรับการสร้างแพ็กเกจ E-Tax Invoice พร้อมสร้าง UUID, Hash SHA-256 และสถานะตอบรับจาก Service Provider ตามเกณฑ์กรมสรรพากร'
                    }
                ]
            },
            sales: {
                title: 'คู่มืองานขายและเอกสารครบวงจร (Sales & Documents)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ศูนย์เอกสารย้อนหลัง (Document Browser & History)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบตัวกรองวันที่ & ประเภทเอกสาร:</strong> เลือกช่วงวันที่ (วันเดียวหรือช่วงวัน), เลือกรอบปี/เดือน และเลือกประเภทเอกสารที่ต้องการค้นหา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางเอกสารย้อนหลัง:</strong> แสดงเลขที่เอกสาร, วันที่, ลูกค้า, ยอดเงินรวม, สถานะเอกสาร และผู้ออกเอกสาร</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มค้นหากรองข้อมูล:</strong> กดปุ่ม "กรอง" เพื่อเรียกดูข้อมูลตามเงื่อนไข พร้อมปุ่มเปิดดูรายละเอียด พิมพ์ใบเสร็จ หรือพิมพ์ใบกำกับภาษี</div></div>`,
                        img: '{{ asset('images/manual/sales_step1.png') }}',
                        tip: 'ศูนย์เอกสารย้อนหลังเป็นศูนย์กลางค้นหาเอกสารทุกชนิดในระบบ ทั้งบิลขาย POS, ใบขายสด, ใบจอง, ใบวางบิล และใบลดหนี้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ใบเสนอราคา (Quotations)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองสถานะใบเสนอราคา:</strong> คัดกรองตามสถานะ (ทุกสถานะ, รอลูกค้าตอบรับ, ตอบรับแล้ว, ปฏิเสธ) พร้อมช่องค้นหาเลขที่หรือชื่อลูกค้า</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างใบเสนอราคา":</strong> กดปุ่มสีเขียวเพื่อเปิดฟอร์มสร้างใบเสนอราคาใหม่ ระบุลูกค้า สาขา เงื่อนไข และรายการสินค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบเสนอราคา:</strong> แสดงเลขที่, วันที่ออก, ชื่อลูกค้า, วันยืนราคาถึง, จำนวนรายการ, ยอดสุทธิ และสถานะตอบรับ</div></div>`,
                        img: '{{ asset('images/manual/sales_step2.png') }}',
                        tip: 'เมื่อลูกค้าตกลงตอบรับใบเสนอราคา สามารถกดแปลงเป็น "ใบจอง / ใบสั่งขาย" ได้ทันทีโดยไม่ต้องคีย์ข้อมูลสินค้าซ้ำ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'ใบจองและขายเชื่อ (Bookings & Credit Sales)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบจอง":</strong> กดปุ่มสีน้ำเงินเพื่อบันทึกการจองสินค้าล่วงหน้า หรือเปิดคำสั่งขายเชื่อแบบให้เครดิตเทอม</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แถบคัดกรองสมุดและสายส่ง:</strong> เลือกสมุดเอกสาร, สาขา, เขต/สายการขาย และผู้รับผิดชอบใบจองเพื่อติดตามงานขาย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบจอง:</strong> แสดงสถานะใบจอง (รอดำเนินการ, แปลงเป็นใบขายแล้ว), กำหนดส่งมอบ, ยอดรวม และประวัติการจัดส่ง</div></div>`,
                        img: '{{ asset('images/manual/sales_step3.png') }}',
                        tip: 'การเปิดใบจองจะช่วยกันสต็อกไว้ไม่ให้สาขาอื่นขายตัดหน้า และสามารถแปลงเป็นใบขายเชื่อเพื่อตัดสต็อกจริงเมื่อส่งของ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ใบขายสด (Cash Sales)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบขายสด":</strong> กดปุ่มเพื่อเปิดเอกสารขายสดหน้าร้านหรือขายตรง รับเงินทันทีพร้อมตัดสต็อก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>โครงสร้างข้อมูลเอกสาร:</strong> ระบุสาขา, ลูกค้า (ทั่วไปหรือเฉพาะราย), วิธีชำระเงิน (เงินสด/โอน), และหมายเหตุ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางประวัติใบขายสด:</strong> แสดงเลขที่ใบเสร็จ, วันที่ขาย, สาขา, ลูกค้า, ยอดเงินรวม และปุ่มพิมพ์ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ</div></div>`,
                        img: '{{ asset('images/manual/sales_step4.png') }}',
                        tip: 'ใบขายสดบันทึกยอดเงินเข้าสมุดเงินสดอัตโนมัติ และตัดยอดสินค้าออกจากคลังของสาขานั้นทันทีที่ยืนยันเอกสาร'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ใบรับคืนสินค้า (Sale Returns)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ศูนย์รับคืนสินค้า:</strong> หน้าจอจัดการรับคืนสินค้าจากการขาย ไม่ว่าจะเป็นสินค้าชำรุด เปลี่ยนแปลง หรือลูกค้ายกเลิก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>โครงสร้างข้อมูลรับคืน:</strong> ระบุเลขอ้างอิงใบขายเดิม, สาขาที่รับสินค้ากลับเข้าคลัง, รายการสินค้า และสาเหตุการรับคืน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางประวัติเอกสารรับคืน:</strong> ตรวจสอบรายการคืนสินค้า ยอดเงินคืน และการปรับปรุงยอดสต็อกกลับเข้าคลังสินค้า</div></div>`,
                        img: '{{ asset('images/manual/sales_step5.png') }}',
                        tip: 'เมื่ออนุมัติใบรับคืนสินค้า ระบบจะปรับเพิ่มสต็อกกลับเข้าคลังที่ระบุ และสร้างบันทึกทางการเงินเพื่อคืนเงินหรือตั้งยอดเครดิต'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'ใบวางบิล (Billing Notes)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองสถานะใบวางบิล:</strong> คัดกรองตามสถานะ (ทุกสถานะ, รอเก็บเงิน, เก็บเงินแล้ว) เพื่อติดตามลูกหนี้การค้า</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างใบวางบิล":</strong> กดปุ่มเพื่อเลือกลูกค้าและรวบรวมใบแจ้งหนี้/ใบส่งของที่ค้างชำระมารวมเป็นใบวางบิลฉบับเดียว</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบวางบิล:</strong> แสดงเลขที่, วันที่ออก, ลูกค้า, วันครบกำหนดชำระ, ยอดรวม และปุ่มพิมพ์เอกสารใบวางบิลส่งลูกค้า</div></div>`,
                        img: '{{ asset('images/manual/sales_step6.png') }}',
                        tip: 'ใบวางบิลช่วยรวมยอดใบแจ้งหนี้หลายใบของลูกค้าคนเดียวกันในรอบบิล เพื่อให้ลูกค้าชำระเงินรอบเดียวอย่างถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'ใบเพิ่มหนี้และใบลดหนี้ (Credit & Debit Notes)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บเลือกใบลดหนี้ / ใบเพิ่มหนี้:</strong> คลิกสลับระหว่างโหมดใบลดหนี้ (CN - ลดภาระหนี้) และใบเพิ่มหนี้ (DN - เพิ่มภาระหนี้)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ช่องค้นหาและตัวกรอง:</strong> พิมพ์ค้นหาเลขที่เอกสาร, ชื่อลูกค้า หรืออ้างอิงใบกำกับภาษีต้นเรื่อง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางเอกสาร CN/DN:</strong> แสดงเลขที่, วันที่, ลูกค้า, เลขที่ใบกำกับภาษีเดิมที่อ้างอิง, เหตุผลในการออก และยอดเงินปรับปรุง</div></div>`,
                        img: '{{ asset('images/manual/sales_step7.png') }}',
                        tip: 'การออกใบลดหนี้/เพิ่มหนี้ต้องระบุสาเหตุตามข้อกำหนดสรรพากร (เช่น คำนวณราคาผิด, สินค้าชำรุด) เพื่อให้กระทบภาษีมูลค่าเพิ่มถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'ลูกค้าสัมพันธ์ (Customer Relationship Management - CRM)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ค้นหาและสรุปสถานะลูกค้า (Customer 360):</strong> ค้นหาประวัติลูกค้าจากชื่อ รหัส หรือเบอร์โทร ดูยอดค้างชำระและสถิติซื้อขาย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เปิดงานขาย":</strong> เริ่มต้นเปิดงานขายหรือดีลใหม่กับลูกค้าเพื่อส่งต่อไปยัง Sales Pipeline</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>แบบฟอร์มบันทึกกิจกรรมติดตาม (Activities):</strong> บันทึกการโทรศัพท์, นัดหมายพบปะ, หรือส่งอีเมล พร้อมกำหนดวัน Due Date ติดตามผล</div></div>`,
                        img: '{{ asset('images/manual/sales_step8.png') }}',
                        tip: 'CRM รวมข้อมูลลูกค้า ยอดขายประวัติ และกิจกรรมติดตามไว้ในที่เดียว ช่วยให้ทีมขายบริหารความสัมพันธ์กับลูกค้าได้อย่างมีประสิทธิภาพ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'Pipeline งานขาย (Sales Pipeline Kanban)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาโอกาสการขาย:</strong> ค้นหาดีลงานขายตามชื่อลูกค้า หรือหัวข้องานเพื่อคัดกรองในบอร์ด</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มโอกาสการขาย":</strong> เปิดดีลใหม่ ระบุมูลค่าที่คาดหวัง (Expected Amount) ลูกค้า และผู้รับผิดชอบงานขาย</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>บอร์ด Kanban ลำดับขั้นงานขาย:</strong> แสดงการเลื่อนสถานะงานขาย (ลูกค้าใหม่ → ติดต่อแล้ว → สนใจ → ส่งใบเสนอราคา → ปิดการขาย) พร้อมยอดเงินรวมในแต่ละขั้น</div></div>`,
                        img: '{{ asset('images/manual/sales_step9.png') }}',
                        tip: 'Sales Pipeline ช่วยให้ผู้บริหารและทีมขายมองเห็นสถานะงานขายทั้งหมดและคาดการณ์ยอดขายล่วงหน้าได้อย่างแม่นยำ'
                    }
                ]
            },
            master_data: {
                title: 'คู่มือระบบข้อมูลหลักและข้อมูลตั้งต้น (Master Data)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ศูนย์ตั้งต้นระบบ & นำเข้าไฟล์ (Master Data Setup Hub & Import)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>การ์ดประเภทสินค้า & สถิติระบบ:</strong> แสดงจำนวนประเภทสินค้าในระบบปัจจุบัน และปุ่มดาวน์โหลด Template สำหรับกำหนดกลุ่ม</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การ์ดข้อมูลสินค้าหลัก:</strong> ดาวน์โหลด Template สินค้า (รหัส, บาร์โค้ด, ราคา, หน่วยนับ) และอัปโหลดไฟล์นำเข้าข้อมูล</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "ตรวจไฟล์ก่อนนำเข้า":</strong> ตรวจสอบความถูกต้องของโครงสร้างไฟล์และเช็กความซ้ำซ้อนของรหัสสินค้าก่อนบันทึกจริง</div></div>`,
                        img: '{{ asset('images/manual/master_data_step1.png') }}',
                        tip: 'การเตรียมไฟล์ Excel/CSV ควรบันทึกเป็น CSV UTF-8 เพื่อป้องกันปัญหาตัวอักษรภาษาไทยแสดงผลผิดเพี้ยน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ลูกค้าและลูกหนี้การค้า (Customers & Debtors Management)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาข้อมูลลูกค้า:</strong> ค้นหาจากรหัสลูกค้า, ชื่อร้านค้า, หรือเบอร์โทรศัพท์เพื่อตรวจสอบประวัติได้อย่างรวดเร็ว</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มลูกค้า":</strong> เปิดฟอร์มลงทะเบียนลูกค้าใหม่ กำหนดวงเงินเครดิต เงื่อนไขการชำระเงิน สาขา และสายส่ง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายชื่อลูกค้า & สถานะหนี้:</strong> แสดงรหัส, ชื่อลูกค้า, สาขา, พนักงานผู้ดูแล, สายการขาย, วงเงินเครดิต และยอดค้างชำระ</div></div>`,
                        img: '{{ asset('images/manual/master_data_step2.png') }}',
                        tip: 'การกำหนดวงเงินเครดิตและระยะเวลาให้สินเชื่อ (Credit Term) ช่วยให้ระบบแจ้งเตือนอัตโนมัติเมื่อลูกค้าสั่งซื้อเกินวงเงินที่อนุมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'ผู้จำหน่ายและเจ้าหนี้การค้า (Suppliers & Creditors Management)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ช่องค้นหาผู้จำหน่าย:</strong> ค้นหาจากรหัสคู่ค้า, ชื่อบริษัท หรือเลขประจำตัวผู้เสียภาษี 13 หลัก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มซัพพลายเออร์":</strong> บันทึกข้อมูลบริษัทคู่ค้า เลขประจำตัวผู้เสียภาษี ที่อยู่สำนักงานใหญ่/สาขา และบัญชีธนาคารสำหรับจ่ายเงิน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายชื่อผู้จำหน่าย:</strong> แสดงรหัส, ชื่อคู่ค้า, ยอดหนี้สะสมค้างจ่าย และสถานะการติดต่อสำหรับเปิดใบสั่งซื้อ (PO)</div></div>`,
                        img: '{{ asset('images/manual/master_data_step3.png') }}',
                        tip: 'บันทึกเลขประจำตัวผู้เสียภาษีและสถานะสำนักงานใหญ่/สาขาให้ครบถ้วน เพื่อให้ระบบดึงข้อมูลไปออกใบกำกับภาษีซื้อและรายงานภาษีได้ถูกต้อง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ระบบทะเบียนสมาชิก (Members Registry & Tier)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มเพิ่มสมาชิกใหม่:</strong> ระบุรหัสสมาชิก, เบอร์โทรศัพท์, ชื่อ-นามสกุล, และประเภทสมาชิก (เช่น บุคคลทั่วไป, VIP)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มสมาชิก":</strong> บันทึกสมาชิกใหม่ขึ้นระบบทันที ข้อมูลจะซิงค์ไปยังเครื่องแคชเชียร์ POS ทุกเครื่อง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการสมาชิก:</strong> แสดงรหัส, ชื่อสมาชิก, ประเภท, ยอดแต้มสะสมปัจจุบัน และสถานะการใช้งาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step4.png') }}',
                        tip: 'เบอร์โทรศัพท์ของสมาชิกจะถูกใช้เป็นคีย์ลัดที่หน้าเครื่อง POS เพียงพิมพ์เบอร์โทรระบบจะดึงชื่อและแต้มขึ้นจอทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ผังคลังสินค้าและสถานที่จัดเก็บ (Warehouse Locations & Shelf Bins)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบค้นหาคลังสินค้า:</strong> ค้นหาจากรหัสหรือชื่อคลังสินค้าภายในสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มคลัง":</strong> กำหนดคลังสินค้าหรือโซนจัดเก็บใหม่ เช่น คลังหน้าร้าน, คลังห้องเย็น, หรือคลังสินค้าชำรุด</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางคลังสินค้า & สถานะ Default:</strong> แสดงรหัสคลัง, ชื่อสถานที่จัดเก็บ, ประเภท และสัญลักษณ์คลังหลักประจำสาขา</div></div>`,
                        img: '{{ asset('images/manual/master_data_step5.png') }}',
                        tip: 'คลังที่ตั้งเป็น DEFAULT จะถูกใช้เป็นคลังตัดสต็อกหลักของบิลขาย POS และใบขายสดโดยอัตโนมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'โครงสร้างตารางราคา (Price Tables & Price Tiers)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ศูนย์จัดการราคาขาย:</strong> แผงควบคุมกลยุทธ์ราคา (ราคาปกติ POS, โปรโมชั่น, นาทีทอง, เครื่องชั่ง) พร้อมสถานะการผูกสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แถบค้นหารายการตารางราคา:</strong> ค้นหาตารางราคาขายปลีก ขายส่ง หรือราคาลูกค้าเฉพาะกลุ่ม</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการราคา:</strong> แสดงรหัสตาราง, ชื่อตาราง, คำอธิบาย, จำนวนรายการสินค้า และสถานะการเปิดใช้</div></div>`,
                        img: '{{ asset('images/manual/master_data_step6.png') }}',
                        tip: 'หากสาขาไม่ได้เลือกตารางราคาเฉพาะ ระบบจะใช้ราคามาตรฐานกลางของระบบโดยอัตโนมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'ราคาเครื่องชั่งและพิมพ์บาร์โค้ด (Scale Pricing & Barcodes)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>คำแนะนำสินค้าเครื่องชั่ง (PLU):</strong> ข้อกำหนดช่วงรหัส PLU และขั้นตอนส่งออกข้อมูลเข้าตาชั่งดิจิทัล</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แถบสถิติสินค้าเครื่องชั่ง:</strong> แสดงจำนวนสินค้าในกลุ่มเครื่องชั่ง (ช่วงเลข PLU นำหน้าด้วย 8)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางจับคู่ PLU & ราคาต่อหน่วย:</strong> แสดงรหัส PLU, รหัสสินค้า, ชื่อสินค้า และราคาต่อกิโลกรัมสำหรับพิมพ์สติกเกอร์</div></div>`,
                        img: '{{ asset('images/manual/master_data_step7.png') }}',
                        tip: 'บาร์โค้ดที่เครื่องชั่งพิมพ์ออกมา (ขึ้นต้นด้วย 8) เครื่องสแกนที่ POS จะถอดรหัสเป็นน้ำหนักและคำนวณราคาให้เองทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'ระบบโปรโมชั่นและส่วนลด (Promotions & Campaign Discounts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มเพิ่มโปรโมชั่น:</strong> ระบุรหัส, ชื่อโปรโมชั่น, ประเภท (ลดเปอร์เซ็นต์, ลดบาท, คูปอง, ของแถม) และสินค้าที่ร่วมรายการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่ม":</strong> บันทึกโปรโมชั่นและส่งต่อไปคำนวณอัตโนมัติที่หน้าร้าน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการโปรโมชั่น:</strong> แสดงช่วงเวลาที่มีผลบังคับใช้ สินค้า เงื่อนไข และสถานะเปิด/ปิด</div></div>`,
                        img: '{{ asset('images/manual/master_data_step8.png') }}',
                        tip: 'สามารถกำหนดวันที่เริ่มต้นและสิ้นสุดของโปรโมชั่นล่วงหน้าได้ เมื่อถึงเวลาโปรโมชั่นจะมีผลทันทีโดยไม่ต้องตั้งค่าใหม่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'ราคานาทีทอง (Flash Sales & Time-limited Discounts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มสร้างแคมเปญนาทีทอง:</strong> ระบุรหัส, ชื่อกลุ่มนาทีทอง, สาขาที่ร่วมรายการ, วันที่ และช่วงเวลา (เช่น 18:00 - 19:30)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างแคมเปญ":</strong> บันทึกแคมเปญนาทีทองเพื่อเปิดราคาส่วนลดพิเศษเฉพาะช่วงเวลาเร่งด่วน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการแคมเปญนาทีทอง:</strong> ตรวจสอบประวัติแคมเปญ ช่วงเวลา และสถานะการทำงาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step9.png') }}',
                        tip: 'เหมาะสำหรับการระบายสินค้าสด เบเกอรี่ หรืออาหารปรุงสำเร็จก่อนเวลาปิดร้าน โดยระบบ POS จะตัดราคาพิเศษตามเวลาจริง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 10',
                        title: 'ป้ายราคาและพิมพ์ป้ายสินค้า (Price Tags & Shelf Talker)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "พิมพ์ป้ายราคา":</strong> เลือกสินค้าและส่งพิมพ์ป้ายราคาติดชั้นวาง (Shelf Talkers) ตามขนาดที่ต้องการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มเพิ่มรูปแบบป้ายราคา:</strong> กำหนดรหัสป้าย, ชื่อรูปแบบ, แหล่งราคา (ราคาขายปกติ หรือราคาโปรโมชั่น)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรูปแบบป้ายราคา:</strong> แสดงรายการเทมเพลตป้ายราคา ขนาดตัวอักษร และสถานะการใช้งาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step10.png') }}',
                        tip: 'สามารถเลือกพิมพ์เฉพาะสินค้าที่มีการปรับปรุงราคาใหม่ในสัปดาห์นี้ เพื่อความสะดวกรวดเร็วในการเปลี่ยนป้ายราคาหน้าร้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 11',
                        title: 'บัตรส่วนลดและคูปอง (Discount Cards & Vouchers)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มออกบัตรส่วนลด:</strong> ระบุรหัสบัตร, ชื่อบัตร, ผูกกับสมาชิกเฉพาะราย (ถ้ามี), จำนวนส่วนลด และวันหมดอายุ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ ออกบัตร":</strong> บันทึกบัตรส่วนลดและสร้างสิทธิ์ใช้งานในระบบทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการบัตรส่วนลด:</strong> ตรวจสอบรหัสบัตร, ชื่อบัตร, สมาชิกผู้ถือบัตร, มูลค่าส่วนลด และยอดใช้งานแล้ว</div></div>`,
                        img: '{{ asset('images/manual/master_data_step11.png') }}',
                        tip: 'บัตรส่วนลดสามารถนำไปสแกนบาร์โค้ดที่หน้าจอชำระเงิน POS เพื่อหักลบยอดบิลได้โดยตรง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 12',
                        title: 'แต้มสะสมสมาชิก (Member Points & Loyalty Rules)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>การ์ดกติกาแต้มปัจจุบัน:</strong> แสดงอัตราการให้แต้มที่กำลังมีผลใช้งาน (เช่น ซื้อครบ 25 บาท รับ 1 แต้ม)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มเพิ่มกติกาแต้ม:</strong> กำหนดรหัส, ชื่อกติกา, ประเภท (สะสมแต้มปกติ หรือตัวคูณแต้ม Multiplier), บาทต่อแต้ม และมูลค่าแต้ม</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางกติกาแต้มทั้งหมด:</strong> แสดงประวัติกติกาแต้ม อัตราตัวคูณพิเศษ และสถานะเปิด/ปิดใช้งาน</div></div>`,
                        img: '{{ asset('images/manual/master_data_step12.png') }}',
                        tip: 'สามารถตั้งกติกาแต้มคูณสอง (Multiplier x2) เฉพาะช่วงเทศกาลหรือวันเกิดสมาชิกเพื่อกระตุ้นยอดขายได้'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 13',
                        title: 'แคมเปญซื้อครบจำนวน (Quantity Step Promotions)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มสร้างแคมเปญซื้อครบ:</strong> ระบุรหัส, ชื่อแคมเปญ, ประเภท (ซื้อครบ N ชิ้น แถมฟรี หรือลดพิเศษ), จำนวนขั้นต่ำ และสินค้าที่ร่วมรายการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ สร้างแคมเปญ":</strong> บันทึกแคมเปญเพื่อให้ระบบ POS ตรวจสอบจำนวนชิ้นในตะกร้าอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการแคมเปญ:</strong> แสดงรหัส, ชื่อแคมเปญ, สินค้า, เงื่อนไขจำนวน และของแถม/ส่วนลดที่ได้รับ</div></div>`,
                        img: '{{ asset('images/manual/master_data_step13.png') }}',
                        tip: 'แคมเปญซื้อครบ (เช่น 1 แถม 1 หรือ 3 ชิ้น 100 บาท) คำนวณแบบ Real-time ที่ POS ช่วยเพิ่มยอดขายต่อบิลได้อย่างดี'
                    }
                ]
            }
,
            purchasing: {
                title: 'คู่มือจัดซื้อ คลังวัตถุดิบ & การผลิต (Purchasing & Production Master Guide)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'ใบขอซื้อและใบสั่งซื้อ (Purchase Orders - PO)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบขอซื้อ":</strong> กดเพื่อเปิดฟอร์มสร้างเอกสารขอซื้อ/สั่งซื้อใหม่ ระบุซัพพลายเออร์ วันที่ส่งมอบ และรายการสินค้า</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>แผง KPI สรุปสถานะ:</strong> ติดตามจำนวนใบขอซื้อรอดำเนินการ, รออนุมัติ, และยอดสั่งซื้อรวมประจำเดือน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรายการใบสั่งซื้อ:</strong> ตรวจสอบประวัติใบสั่งซื้อ เลขที่เอกสาร วันที่ ซัพพลายเออร์ ยอดเงินสุทธิ และสถานะใบสั่งซื้อ</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step1.png') }}',
                        tip: 'เมื่อใบสั่งซื้อผ่านการอนุมัติและรับสินค้าเข้าคลังแล้ว ระบบจะปรับเพิ่มยอดสต็อกและตั้งเจ้าหนี้การค้าอัตโนมัติ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'ใบเบิกสินค้าและวัตถุดิบ (Stock Requisitions)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบเบิกสินค้า":</strong> เปิดฟอร์มเบิกวัตถุดิบไปใช้ในครัว การแปรรูป หรือการใช้อุปโภคบริโภคประจำสาขา</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางประวัติใบเบิก:</strong> ตรวจสอบเลขที่ใบเบิก คลังต้นทาง ผู้เบิก วัตถุประสงค์ และสถานะการเบิกสินค้า</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step2.png') }}',
                        tip: 'การเบิกสินค้าจะบันทึกต้นทุนวัตถุดิบทันทีและตัดยอดคงเหลือออกจากคลังตามวิธีต้นทุนเฉลี่ย'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'ใบคืนสินค้าจากการเบิก (Requisition Returns)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบคืนสินค้าจากการเบิก":</strong> อ้างอิงใบเบิกเดิมเพื่อนำวัตถุดิบหรือสินค้าที่ใช้ไม่หมดส่งคืนเข้าคลัง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายการใบคืนสินค้า:</strong> ตรวจสอบรายการคืนสินค้า จำนวนที่รับคืน และมูลค่าต้นทุนที่ปรับลดลง</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step3.png') }}',
                        tip: 'สินค้าที่รับคืนจะกลับเข้าสต็อกพร้อมขายหรือพร้อมใช้อีกครั้งโดยไม่ต้องตั้งเอกสารรับเข้าใหม่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'ใบตัดสินค้าชำรุด / เสียหาย (Stock Damages & Write-Offs)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ สร้างใบตัดสินค้าชำรุด":</strong> บันทึกตัดยอดสินค้าแตกหัก เน่าเสีย หมดอายุ หรือสูญหายออกจากคลัง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายการตัดชำรุด:</strong> แสดงรายการสินค้า สาเหตุการชำรุด มูลค่าความเสียหาย และผู้อนุมัติตัดจ่าย</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step4.png') }}',
                        tip: 'การตัดสินค้าชำรุดช่วยให้ตัวเลขสต็อกในระบบตรงกับสินค้าที่มีอยู่จริงบนชั้นวาง ป้องกันแคชเชียร์ขายของที่ไม่มีอยู่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'สูตรการผลิต (BOM) และใบสั่งผลิต (Production Orders)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "+ เพิ่มสูตร (BOM)":</strong> กำหนดสูตรการผลิต เช่น ระบุวัตถุดิบ อัตราส่วน และต้นทุนมาตรฐานในการทำสินค้าสำเร็จรูป</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>รายการสูตรการผลิต (Recipes):</strong> ตรวจสอบสูตรทั้งหมด รายการวัตถุดิบย่อย และอัตราสูญเสีย (Yield Loss)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>รายการใบสั่งผลิต (Production Orders):</strong> บันทึกสั่งผลิต ตัดสต็อกวัตถุดิบตามสูตร และเพิ่มสต็อกสินค้าสำเร็จรูป</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step5.png') }}',
                        tip: 'ระบบรองรับการผลิตแบบ Auto-BOM ตัดวัตถุดิบอัตโนมัติเมื่อกดขายหน้าร้าน หรือสั่งผลิตล่วงหน้าเป็นล็อต'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'สินค้าใกล้หมด & จุดสั่งซื้อซ้ำ (Low Stock Alerts & Reorder Points)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่ม "พิมพ์รายงาน":</strong> พิมพ์รายการสินค้าที่สต็อกต่ำกว่าจุดสั่งซื้อซ้ำ (ROP) เพื่อส่งให้ฝ่ายจัดซื้อเปิด PO</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางแจ้งเตือนสต็อกต่ำ:</strong> แสดงรหัสสินค้า ชื่อ จำนวนคงเหลือ จุดปลอดภัย (Safety Stock) และปริมาณที่แนะนำให้สั่งซื้อ</div></div>`,
                        img: '{{ asset('images/manual/purchasing_step6.png') }}',
                        tip: 'ตั้งค่าจุดสั่งซื้อซ้ำและสต็อกขั้นต่ำได้ในหน้าข้อมูลสินค้า เพื่อให้ระบบแจ้งเตือนก่อนของขาดสต็อกจริง'
                    }
                ]
            },
            reports: {
                title: 'คู่มือรายงานและวิเคราะห์ข้อมูล (Reports & Business Analytics Guide)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'รายงานยอดขายและวิเคราะห์การขาย (Sales Reports & Analytics)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บหมวดรายงาน & ช่วงวันที่:</strong> เลือกดูยอดขายรายวัน รายเดือน สรุปตามสาขา สินค้าขายดี หรือพนักงานขาย</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางวิเคราะห์ยอดขาย:</strong> แสดงจำนวนบิล ยอดขายรวม ส่วนลด ยอดสุทธิ และกำไรขั้นต้น</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่ม "พิมพ์รายงาน / Export Excel":</strong> พิมพ์เอกสารรายงานหรือดาวน์โหลดเป็นไฟล์สเปรดชีตเพื่อนำไปใช้งานต่อ</div></div>`,
                        img: '{{ asset('images/manual/reports_step1.png') }}',
                        tip: 'รายงานขายสะท้อนข้อมูลแบบ Real-time ทันทีที่มีการปิดบิลที่เครื่อง POS หรือเปิดบิลขายเชื่อหลังบ้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'รายงานการจัดซื้อและเจ้าหนี้ (Purchasing & Supplier Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองการจัดซื้อ:</strong> เลือกช่วงเวลา ซัพพลายเออร์ หมวดหมู่วัตถุดิบ และสถานะการรับของ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสรุปการซื้อ:</strong> สรุปยอดซื้อสุทธิ ปริมาณการสั่งซื้อ ยอดค้างรับ และประวัติราคาต้นทุนย้อนหลัง</div></div>`,
                        img: '{{ asset('images/manual/reports_step2.png') }}',
                        tip: 'ใช้เปรียบเทียบแนวโน้มราคาวัตถุดิบจากซัพพลายเออร์แต่ละรายเพื่อบริหารต้นทุนให้ต่ำที่สุด'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'รายงานคลังสินค้าและสต็อกเคลื่อนไหว (Inventory Movements & Stock Aging)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองคลังสินค้า:</strong> เลือกคลัง ค้นหารหัสสินค้า หรือตรวจสอบล็อตสินค้าใกล้หมดอายุ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสต็อกคงเหลือและการเคลื่อนไหว:</strong> แสดงยอดยกมา รับเข้า จ่ายออก โอนย้าย ปรับปรุง และยอดคงเหลือปลายงวด</div></div>`,
                        img: '{{ asset('images/manual/reports_step3.png') }}',
                        tip: 'ตรวจสอบ Stock Card รายตัวเพื่อดูประวัติย้อนหลังทุกธุรกรรมที่ทำให้ยอดสต็อกเปลี่ยนแปลง'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'รายงานการเงินและการรับชำระ (Payment & Cashier Shift Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองประเภทการชำระ:</strong> เลือกช่องทาง เช่น เงินสด, PromptPay QR, บัตรเครดิต, หรือการโอนเงิน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสรุปการเงินและรอบกะ:</strong> แสดงยอดเงินรับจริง เปรียบเทียบกับยอดขายหน้าร้าน และสรุปเงินสดขาด/เกิน</div></div>`,
                        img: '{{ asset('images/manual/reports_step4.png') }}',
                        tip: 'รายงานรอบกะช่วยให้ฝ่ายการเงินตรวจสอบยอดเงินนำฝากของแคชเชียร์แต่ละรอบได้อย่างแม่นยำ 100%'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'รายงานภาษีซื้อและภาษีขาย (Input / Output VAT Tax Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตัวกรองภาษี:</strong> เลือกรายงานภาษีขาย (ภ.พ.30) หรือรายงานภาษีซื้อ กำหนดเดือนและปีภาษี</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางรายงานภาษีมูลค่าเพิ่ม:</strong> แสดงเลขที่ใบกำกับภาษี วันที่ ชื่อผู้ซื้อ/ผู้ขาย เลขผู้เสียภาษี มูลค่าสินค้า และภาษี 7%</div></div>`,
                        img: '{{ asset('images/manual/reports_step5.png') }}',
                        tip: 'รายงานจัดรูปแบบถูกต้องตามข้อกำหนดกรมสรรพากร สามารถนำไปใช้ยื่นแบบ ภ.พ.30 ประจำเดือนได้ทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'รายงานระบบเดิม BPlus (BPlus Legacy Reports)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ศูนย์รวมรายงานระบบเดิม:</strong> เข้าถึงรายงานประวัติเดิมของระบบ BPlus ที่นำเข้าสู่ฐานข้อมูล PopCentral</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสืบค้นประวัติย้อนหลัง:</strong> ค้นหาบิลเก่า ยอดขายประวัติ และสถิติย้อนหลังหลายปีเพื่อการเปรียบเทียบ</div></div>`,
                        img: '{{ asset('images/manual/reports_step6.png') }}',
                        tip: 'ข้อมูลระบบเดิมถูกจัดเก็บไว้อย่างปลอดภัย สามารถค้นหาย้อนหลังเพื่อตรวจสอบทางบัญชีหรือสรรพากรได้ตลอดเวลา'
                    }
                ]
            },
            integrations: {
                title: 'คู่มือเชื่อมต่อภายนอก (LINE Notify & Messaging API Guide)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'การเชื่อมต่อ LINE Notify และ Messaging API (LINE Integration)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มตั้งค่า Channel Access Token:</strong> กรอก Channel ID, Channel Secret และ Access Token จาก LINE Developers Console</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "+ เพิ่มกลุ่มแจ้งเตือน" / บันทึก:</strong> ผูกห้องแชตกลุ่มสำหรับส่งข้อความแจ้งเตือนอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางเหตุการณ์แจ้งเตือน:</strong> เลือกเปิด/ปิดการแจ้งเตือนตามเหตุการณ์ เช่น สรุปยอดขายประจำวัน, สินค้าสต็อกใกล้หมด, หรือแจ้งเตือนปิดกะแคชเชียร์</div></div>`,
                        img: '{{ asset('images/manual/integrations_step1.png') }}',
                        tip: 'ข้อความที่ส่งเข้า LINE ช่วยให้ผู้บริหารและผู้จัดการสาขารู้สถานะร้านค้าได้แบบ Real-time โดยไม่ต้องเปิดเข้าคอมพิวเตอร์'
                    }
                ]
            },
            overview: {
                title: 'คู่มือภาพรวมและศูนย์ควบคุมบริหาร (Executive Dashboard & Controls Guide)',
                steps: [
                    {
                        badge: 'ฟังก์ชันที่ 1',
                        title: 'แดชบอร์ดภาพรวมผู้บริหาร & ตัวกรองข้อมูล (Executive Board & Smart Filters)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ป้ายหัวข้อ & สถานะเชื่อมต่อสด:</strong> แสดงชื่อระบบ POPSTAR EXECUTIVE BOARD พร้อมไฟสถานะ "ระบบออนไลน์" ยืนยันการเชื่อมต่อฐานข้อมูลแบบ Real-time</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>สรุปยอดขายด่วน & ตัวเลือกสลับสาขา:</strong> ดูยอดขายรวมวันนี้, จำนวนบิลที่เปิดขาย, ยอดแจ้งเตือนคงค้าง และเมนูดรอปดาวน์เลือกดูเฉพาะสาขาหรือรวมทุกสาขาทั่วประเทศ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>แถบตัวกรองช่วงเวลา & Auto-Refresh:</strong> ระบุช่วงวันที่จาก-ถึง, ปุ่มลัด "วันนี้", "7 วัน", "30 วัน" พร้อมตัวเลขนับถอยหลังรีเฟรชข้อมูลอัตโนมัติทุก 60 วินาที</div></div>`,
                        img: '{{ asset('images/manual/overview_step1.png') }}',
                        tip: 'การสลับสาขาใน Dropdown จะอัปเดตทั้งตัวเลขสรุป, กราฟ และตารางสต๊อกของสาขานั้นๆ ทันทีโดยไม่ต้องเปิดหน้ารายงานใหม่'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 2',
                        title: 'แผงแจ้งเตือนด่วน & 4 ดัชนีชี้วัดทางการเงิน (Alert Banner & Financial KPIs Drill-down)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบแจ้งเตือนด่วนสีแดง (Alert Banner):</strong> ตรวจจับ Lot สินค้าใกล้หมดอายุ หรือสต๊อกต่ำ/ติดลบ พร้อมปุ่มกด "ตรวจสอบทันที ➔" เจาะลึกรายงาน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>KPI รายได้สุทธิ & กำไรขั้นต้น (Gross Profit):</strong> รายได้รวมหลังหักส่วนลด เทียบ % การเติบโตช่วงก่อนหน้า พร้อมคำนวณกำไรขั้นต้นและ Gross Margin % ทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>KPI ลูกหนี้คงค้าง & ยอดขาย POS:</strong> สรุปยอดลูกหนี้ค้างชำระ ยอดเกินกำหนด (Overdue) และยอดขายสดหน้าร้าน POS พร้อมสถานะระบบ POS ที่เชื่อมต่ออยู่</div></div>`,
                        img: '{{ asset('images/manual/overview_step2.png') }}',
                        tip: 'กล่อง KPI ทุกกล่องถูกออกแบบให้เป็นลิงก์ Drill-down สามารถคลิกเพื่อพุ่งตรงเข้าสู่หน้ารายงานเชิงลึกของหมวดนั้นได้ทันที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 3',
                        title: 'กราฟวิเคราะห์แนวโน้มธุรกิจ (Interactive Analytical Charts)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>กราฟเส้นยอดขายรายวัน (Daily Sales Trend):</strong> แสดงแนวโน้มการเติบโตของรายได้ในแต่ละวัน สามารถนำเมาส์ชี้หรือคลิกจุดบนกราฟเพื่อดูรายละเอียดยอดขายของวันนั้น</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>กราฟแท่งเปรียบเทียบยอดขายแยกสาขา (Branch Sales):</strong> เปรียบเทียบส่วนแบ่งยอดขายของแต่ละสาขาแบบคู่ขนาน คลิกที่แท่งกราฟเพื่อเจาะดูรายงานยอดขายแยกพนักงาน</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>กราฟโดนัทสัดส่วนเอกสารขาย (Sales Mix):</strong> แสดงสัดส่วนประเภทการขาย เช่น ยอดบิล POS, เอกสารขายเชื่อ B2B หรือช่องทางออนไลน์ ช่วยให้มองเห็นสัดส่วนช่องทางทำเงินชัดเจน</div></div>`,
                        img: '{{ asset('images/manual/overview_step3.png') }}',
                        tip: 'กราฟวิเคราะห์รองรับการแสดงผลทั้งธีม Light และ Dark Mode โดยปรับเปลี่ยนโทนสีอัตโนมัติเพื่อให้อ่านง่าย สบายตา'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 4',
                        title: 'เฝ้าระวังสินค้าขายดี & สต๊อกวิกฤต (Top 10 Products & Critical Stock Alert)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ตารางสินค้าขายดี Top 10:</strong> แสดงรายการสินค้าที่ทำยอดขายสูงสุด อัปเดตตามรอบบิลขายจริง แสดงรหัส SKU, สาขาที่ขายดี, จำนวนชิ้นที่จำหน่าย และยอดขายรวมเป็นบาท</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ตารางสต๊อกต่ำสุด / ติดลบ:</strong> เฝ้าระวังวัตถุดิบและสินค้าที่คงเหลือต่ำกว่าเกณฑ์ หรือสต๊อกติดลบ แสดงตำแหน่งคลังจัดเก็บ และจำนวนคงเหลือที่ต้องรีบเติมทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ป้ายกำกับเตือน "ตรวจนับด่วน":</strong> ช่วยให้ฝ่ายจัดซื้อและผู้จัดการคลังทราบสินค้าที่กำลังจะขาดมือเพื่อสั่งซื้อหรือโอนย้ายได้ทันท่วงที</div></div>`,
                        img: '{{ asset('images/manual/overview_step4.png') }}',
                        tip: 'หากพบสต๊อกติดลบ ให้ผู้ดูแลสาขาทำรายการตรวจนับสต็อก (Stock Count) หรือรับสินค้าเข้าคลังทันทีเพื่อไม่ให้กระทบยอดขายหน้าร้าน'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 5',
                        title: 'ศูนย์ควบคุมกำไรสุทธิและต้นทุนรายสาขา (Net Profit & Branch Cost Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บหมวดการควบคุม & ปฏิทินเลือกงวด:</strong> เข้าสู่เมนู "ศูนย์ควบคุมบริหาร" สลับดู 6 แท็บการควบคุม และเลือกงวดบัญชีรายเดือน (Period) เพื่อดูข้อมูลงวดที่ต้องการ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>4 กล่อง KPI ธรรมาภิบาลต้นทุน:</strong> สรุปยอดขายรวมทั้งเครือ, ต้นทุนขาย (COGS % ของยอดขาย), ค่าใช้จ่ายดำเนินงาน และกำไรสุทธิ (% มาร์จิ้น)</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางกำไรหลังต้นทุนและค่าใช้จ่าย:</strong> แจกแจงผลประกอบการแยกตามสาขาแบบละเอียด (สำนักงานใหญ่, หน้าร้าน, ตลาด) พร้อมคำนวณยอดรวมสุทธิทั้งองค์กร</div></div>`,
                        img: '{{ asset('images/manual/overview_step5.png') }}',
                        tip: 'ศูนย์ควบคุมช่วยให้ตรวจจับต้นทุนผิดปกติหรือการรั่วไหลของวัตถุดิบได้ตั้งแต่เนิ่นๆ ก่อนกระทบกำไรสุทธิของกิจการ'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 6',
                        title: 'วางแผนสั่งซื้อวัตถุดิบจาก Min/Max (Purchase Planning from Min/Max)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แท็บ "แผนซื้อ" & ตรวจสอบงวด:</strong> สลับมายังแท็บแผนซื้อเพื่อบริหารคำแนะนำการจัดซื้อวัตถุดิบและสินค้าสำหรับทุกสาขา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ปุ่ม "สร้างคำแนะนำใหม่":</strong> กดเพื่อสั่งให้ระบบคำนวณแผนจัดซื้ออัตโนมัติจากจุด Min/Max, Safety Stock และอัตราการใช้วัตถุดิบจริง</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางแผนจัดซื้อ:</strong> แสดงเลขที่แผน, ชื่อสินค้า/SKU, ซัพพลายเออร์ผู้ขาย, จำนวนชิ้นที่แนะนำให้ซื้อ และสถานะเพื่อนำไปสร้างใบสั่งซื้อ (PO) ต่อไป</div></div>`,
                        img: '{{ asset('images/manual/overview_step6.png') }}',
                        tip: 'ระบบจะดึงซัพพลายเออร์หลักที่ผูกไว้กับสินค้าแต่ละรายการมาใส่ในแผนให้อัตโนมัติ ทำให้ฝ่ายจัดซื้อเปิด PO ได้เร็วขึ้นกว่าเดิม'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 7',
                        title: 'บริหารงบประมาณและศูนย์ต้นทุน (Cost Centers & Budget Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มเพิ่ม Cost Center:</strong> กำหนดรหัสและชื่อศูนย์ต้นทุน เช่น สาขา, แผนกจัดซื้อ, แผนกครัวกลาง, หรือฝ่ายบริหาร เพื่อจัดสรรค่าใช้จ่ายให้ตรงหน่วยงาน</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มตั้งวงเงิน Budget:</strong> กำหนดปีงบประมาณ, ศูนย์ต้นทุน, เดือน, ผังบัญชีค่าใช้จ่าย และวงเงินงบประมาณสูงสุดที่อนุญาตให้ใช้</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตาราง Budget & ปุ่ม "เทียบงบ vs จริง":</strong> ตรวจสอบรายการงบประมาณที่อนุมัติแล้ว พร้อมคลิกดูรายงานเปรียบเทียบการเบิกจ่ายจริงกับวงเงินที่ตั้งไว้</div></div>`,
                        img: '{{ asset('images/manual/overview_step7.png') }}',
                        tip: 'การคุมงบประมาณผ่าน Cost Center ช่วยป้องกันไม่ให้แต่ละแผนกใช้จ่ายเงินเกินเพดานที่ผู้บริหารอนุมัติไว้ในแต่ละงวด'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 8',
                        title: 'บริหารเวลาทำงานและรอบจ่ายเงินเดือน (Attendance & Payroll Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ฟอร์มบันทึกเวลาเข้างาน:</strong> เลือกรหัสพนักงาน, วันที่ปฏิบัติงาน, สถานะ (มาทำงาน, สาย, ลา, ขาด, วันหยุด) และระบุชั่วโมงทำงานล่วงเวลา (OT)</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ระบบประมวลผลรอบเงินเดือน:</strong> เลือกงวดประจำเดือนและกดปุ่ม "คำนวณ Payroll" เพื่อคำนวณฐานเงินเดือน ค่าล่วงเวลา และรายการหักอัตโนมัติ</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางรอบ Payroll & การอนุมัติ:</strong> สรุปยอดรายได้รวม, หักรวม, ยอดสุทธิ และปุ่ม "ตรวจ / อนุมัติ" เพื่อเปลี่ยนสถานะจาก ร่าง ➔ อนุมัติ ➔ จ่ายแล้ว</div></div>`,
                        img: '{{ asset('images/manual/overview_step8.png') }}',
                        tip: 'ระบบผูกข้อมูลการลงเวลาทำงานเข้ากับการคำนวณ Payroll โดยตรง ช่วยลดความผิดพลาดในการจ่ายค่าแรงและโอที'
                    },
                    {
                        badge: 'ฟังก์ชันที่ 9',
                        title: 'นำเข้าคำสั่งซื้อ E-Commerce & ตรวจสอบระบบ (E-Commerce & Monitoring Governance)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>แถบสลับหมวด E-Commerce & Monitoring:</strong> สลับดูระบบเชื่อมต่อคำสั่งซื้อช่องทางออนไลน์ และแท็บ Monitoring สำหรับตรวจสอบความเสถียรของระบบ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>ฟอร์มนำเข้าคำสั่งซื้อออนไลน์:</strong> บันทึกออเดอร์จากช่องทางภายนอก (Lazada, Shopee, TikTok Shop), ระบุ Order ID, ชื่อลูกค้า, สถานะ, ยอดเงินรวม และรายการสินค้าแบบ JSON</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ตารางคำสั่งซื้อที่นำเข้าแล้ว:</strong> แสดงรายการออเดอร์ออนไลน์ทั้งหมด พร้อมสถานะการตัดสต็อก และความพร้อมในการจัดส่งสินค้าถึงมือลูกค้า</div></div>`,
                        img: '{{ asset('images/manual/overview_step9.png') }}',
                        tip: 'แท็บ Monitoring ช่วยให้ผู้บริหารและฝ่ายไอทีตรวจสอบเหตุการณ์ผิดปกติ (Event Severity) ได้ทันทีเมื่อเกิดข้อผิดพลาดในการเชื่อมต่อ'
                    }
                ]
            },
            ai_chat: {
                title: 'คู่มือการใช้งานแชท AI อัจฉริยะ (Gemini Assistant v2.5)',
                steps: [
                    {
                        badge: 'ขั้นตอนที่ 1',
                        title: 'เปิดหน้าต่างแชท AI (Floating AI Widget)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ปุ่มไอคอน AI สีแดง (มุมขวาล่าง):</strong> ปรากฏอยู่ทุกหน้าจอของระบบ ERP เพียงคลิกปุ่มไอคอนหุ่นยนต์เพื่อเปิดหน้าต่างสนทนา</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>หน้าต่างสนทนาแบบ Pop-up:</strong> เปิดขึ้นมาทันทีโดยไม่ต้องเปลี่ยนหน้าจอหรือออกจากงานที่กำลังทำอยู่</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>ปุ่มย่อ/ปิด:</strong> สามารถคลิกปุ่มเดิมอีกครั้ง (สัญลักษณ์ X) เพื่อพับเก็บหน้าต่างแชทได้ตลอดเวลา</div></div>`,
                        img: '{{ asset('images/manual/ai_chat_step1.png') }}',
                        tip: 'ประวัติการพูดคุยจะถูกบันทึกไว้ในเครื่องของคุณอัตโนมัติ ทำให้สามารถเปิดกลับมาอ่านคำตอบเดิมได้เสมอ'
                    },
                    {
                        badge: 'ขั้นตอนที่ 2',
                        title: 'การสอบถามข้อมูลสต๊อกจริง (Real-Time Stock Query)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>พิมพ์คำถามภาษาไทยที่เป็นธรรมชาติ:</strong> เช่น <em>"ตอนนี้มีสินค้าสต๊อกติดลบไหม?"</em> หรือ <em>"สินค้าตัวไหนใกล้หมด?"</em></div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>AI ดึงข้อมูลจริงจากระบบ ERP:</strong> ฝั่งเซิร์ฟเวอร์จะคิวรี่ตาราง <code>stock_balances</code> มาประกอบคำตอบทันที</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>คำตอบตรงจุด ไม่เดาตัวเลข:</strong> AI ถูกตั้งกฎ System Instruction ป้องกันการมโนข้อมูล รายงานตัวเลขจริงจากฐานข้อมูลเท่านั้น</div></div>`,
                        img: '{{ asset('images/manual/ai_chat_step2.png') }}',
                        tip: 'หากมีสินค้าสต๊อกติดลบ AI จะระบุชื่อสินค้า จำนวนที่ติดลบ และสาขา/ตำแหน่งจัดเก็บให้ทราบทันที'
                    },
                    {
                        badge: 'ขั้นตอนที่ 3',
                        title: 'ความสามารถและขอบเขตคำถามที่ AI ช่วยเหลือได้ (Capabilities)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>คำถามด้านสต๊อกและคลังสินค้า:</strong> ถามสถานะสต๊อกติดลบ, สินค้าใกล้หมด, ขั้นตอนการโอนย้าย และการตรวจนับ</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>คำถามขั้นตอนระบบ POS & จัดการร้าน:</strong> วิธีการเปิดกะ, ปิดกะ Z-Report, การขายออฟไลน์เมื่อเน็ตหลุด, การชั่งน้ำหนัก</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>คำถามการเงิน บัญชี และภาษีสรรพากร:</strong> สูตร Moving Average, การคำนวณ VAT 7%, การกระทบยอดเงินสด และผังบัญชี GL</div></div>`,
                        img: '{{ asset('images/manual/ai_chat_step3.png') }}',
                        tip: 'สามารถพิมพ์ถามเป็นภาษาพูดทั่วไปได้เลย AI เข้าใจบริบทของธุรกิจอาหารแช่แข็ง ชาบู หมูกระทะ เป็นอย่างดี'
                    },
                    {
                        badge: 'ขั้นตอนที่ 4',
                        title: 'สถาปัตยกรรมความปลอดภัยและการตั้งค่า (Architecture & Setup)',
                        desc: `<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">1</span><div><strong>ความปลอดภัยระดับสูง (Zero Leak):</strong> กุญแจ <code>GEMINI_API_KEY</code> เก็บรักษาฝั่งเซิร์ฟเวอร์ ป้องกันไม่ให้บุคคลภายนอกเข้าถึงได้</div></div>
<div style="display:flex; gap:8px; margin-bottom:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">2</span><div><strong>การตั้งค่าไฟล์ .env:</strong> กำหนดค่า <code>GEMINI_API_KEY=your_key_here</code> พร้อมระบบ Fallback ดึงค่าได้แม้มีการแคชคอนฟิก</div></div>
<div style="display:flex; gap:8px;"><span style="background:#d32f2f; color:#fff; border-radius:50%; width:20px; height:20px; display:grid; place-items:center; font-size:11px; font-weight:bold; flex-shrink:0;">3</span><div><strong>โมเดลประมวลผลเร็ว (Gemini Flash):</strong> ใช้เวลาตอบคำถามเพียง 1-2 วินาที รองรับการสนทนาต่อเนื่องแบบ Multi-Turn</div></div>`,
                        img: '{{ asset('images/manual/ai_chat_step4.png') }}',
                        tip: 'สามารถขอรับ API Key ฟรีได้จาก Google AI Studio เพื่อเปิดใช้งานฟังก์ชัน AI ในระบบได้ทันที'
                    }
                ]
            }
        };

        let currentGuideKey = 'pos';
        let currentStepIdx = 0;

        function openManualReader(key, stepIndex = 0) {
            if (!manualGuidesData[key]) return;
            currentGuideKey = key;
            currentStepIdx = stepIndex;
            const guide = manualGuidesData[key];

            document.getElementById('vgModalTitle').innerHTML = `<i class="bi bi-book-half text-danger me-2"></i> ${guide.title}`;

            // Build tabs
            const tabsWrap = document.getElementById('vgStepTabs');
            tabsWrap.innerHTML = '';
            guide.steps.forEach((step, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `reader-tab-btn ${idx === currentStepIdx ? 'active' : ''}`;
                btn.textContent = `${idx + 1}. ${step.title}`;
                btn.onclick = () => renderStep(idx);
                tabsWrap.appendChild(btn);
            });

            renderStep(currentStepIdx);
            document.getElementById('vgReaderModal').style.display = 'grid';
        }

        function renderStep(idx) {
            const guide = manualGuidesData[currentGuideKey];
            if (!guide || !guide.steps[idx]) return;
            currentStepIdx = idx;

            // Update Tab states
            const tabs = document.getElementById('vgStepTabs').children;
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].className = `reader-tab-btn ${i === idx ? 'active' : ''}`;
            }

            const step = guide.steps[idx];
            document.getElementById('vgStepBadge').textContent = step.badge;
            document.getElementById('vgStepTitle').textContent = step.title;
            document.getElementById('vgStepDesc').innerHTML = step.desc;
            document.getElementById('vgStepImg').src = step.img;

            const tipWrap = document.getElementById('vgStepTipWrap');
            if (step.tip) {
                tipWrap.style.display = 'flex';
                document.getElementById('vgStepTip').textContent = step.tip;
            } else {
                tipWrap.style.display = 'none';
            }

            document.getElementById('vgPrevBtn').disabled = (idx === 0);
            document.getElementById('vgNextBtn').disabled = (idx === guide.steps.length - 1);
            document.getElementById('vgStepCounter').textContent = `ขั้นตอน ${idx + 1} จาก ${guide.steps.length}`;
        }

        function navigateStep(direction) {
            const guide = manualGuidesData[currentGuideKey];
            if (!guide) return;
            const newIdx = currentStepIdx + direction;
            if (newIdx >= 0 && newIdx < guide.steps.length) {
                renderStep(newIdx);
            }
        }

        function closeManualReader() {
            document.getElementById('vgReaderModal').style.display = 'none';
        }

        function openLightbox(src) {
            if (!src) return;
            document.getElementById('lightboxImg').src = src;
            document.getElementById('imgLightbox').style.display = 'grid';
        }

        function setActiveNav(element) {
            document.querySelectorAll('.nav-link-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }

        function filterManualContent(query) {
            const q = query.toLowerCase().trim();
            const cards = document.querySelectorAll('.vg-card, .pillar-card, .docs-table tbody tr');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (q === '' || text.includes(q)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Theme Toggle
        (function() {
            const html = document.documentElement;
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            
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
                applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
            });
        })();
    </script>

    <!-- Mermaid.js for Interactive System Diagrams -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof mermaid !== 'undefined') {
                function initMermaid() {
                    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                    mermaid.initialize({
                        startOnLoad: false,
                        theme: isDark ? 'dark' : 'base',
                        themeVariables: isDark ? {
                            background: '#0f172a',
                            primaryColor: '#1e293b',
                            primaryTextColor: '#f1f5f9',
                            primaryBorderColor: '#38bdf8',
                            lineColor: '#64748b',
                            secondaryColor: '#1e293b',
                            tertiaryColor: '#162032',
                            fontSize: '13px',
                            fontFamily: "'IBM Plex Sans Thai', 'Plus Jakarta Sans', sans-serif"
                        } : {
                            background: '#ffffff',
                            primaryColor: '#f8fafc',
                            primaryTextColor: '#0f172a',
                            primaryBorderColor: '#0ea5e9',
                            lineColor: '#94a3b8',
                            secondaryColor: '#f1f5f9',
                            tertiaryColor: '#e2e8f0',
                            fontSize: '13px',
                            fontFamily: "'IBM Plex Sans Thai', 'Plus Jakarta Sans', sans-serif"
                        },
                        securityLevel: 'loose'
                    });
                    mermaid.run();
                }

                initMermaid();

                // Re-run on theme change
                const themeBtn = document.getElementById('themeToggleBtn');
                if (themeBtn) {
                    themeBtn.addEventListener('click', function() {
                        setTimeout(() => {
                            // Reset processed flags to re-render diagrams
                            document.querySelectorAll('.mermaid').forEach(el => {
                                if (el.getAttribute('data-original-code')) {
                                    el.innerHTML = el.getAttribute('data-original-code');
                                } else {
                                    el.setAttribute('data-original-code', el.innerHTML);
                                }
                                el.removeAttribute('data-processed');
                            });
                            initMermaid();
                        }, 50);
                    });
                }
            }
        });
    </script>

</body>
</html>
