<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Admission</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* ============================================
           RESET & BASE
        ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #F8FAFC;
            min-height: 100vh;
        }

        /* ============================================
           TOP BAR WITH LOGO
        ============================================ */
        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top-bar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .top-bar .brand img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .top-bar .brand .logo-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(91, 111, 245, 0.12), rgba(118, 75, 162, 0.08));
            display: grid;
            place-items: center;
            color: #5B6FF5;
            font-size: 1.2rem;
        }

        .top-bar .brand .school-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0F172A;
        }

        .top-bar .brand .school-name .highlight {
            color: #5B6FF5;
        }

        .top-bar .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .top-bar .nav-links a {
            color: #475569;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 6px 14px;
            border-radius: 8px;
        }

        .top-bar .nav-links a:hover {
            color: #5B6FF5;
            background: rgba(91, 111, 245, 0.08);
        }

        .top-bar .nav-links .btn-nav {
            background: linear-gradient(135deg, #5B6FF5 0%, #764BA2 100%);
            color: white !important;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(91, 111, 245, 0.25);
        }

        .top-bar .nav-links .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(91, 111, 245, 0.35);
            background: linear-gradient(135deg, #4A5BD4 0%, #663A8F 100%);
        }

        /* ============================================
           HERO SECTION
        ============================================ */
        .portal-hero {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 100px 24px 60px;
            position: relative;
            overflow: hidden;
            background: #F8FAFC;
        }

        .portal-hero .bg-gradient {
            position: absolute;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(91, 111, 245, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(118, 75, 162, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(91, 111, 245, 0.05) 0%, transparent 50%);
            animation: gradientFloat 20s ease-in-out infinite alternate;
        }

        @keyframes gradientFloat {
            0% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.1) rotate(2deg); }
        }

        /* Floating Elements */
        .floating-elements {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-elements .float-item {
            position: absolute;
            border-radius: 50%;
            opacity: 0.06;
            animation: floatItem 25s ease-in-out infinite;
        }

        .float-item:nth-child(1) {
            width: 160px;
            height: 160px;
            top: 5%;
            left: 5%;
            background: #5B6FF5;
            animation-delay: 0s;
        }

        .float-item:nth-child(2) {
            width: 100px;
            height: 100px;
            top: 60%;
            right: 8%;
            background: #764BA2;
            animation-delay: -5s;
        }

        .float-item:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 10%;
            left: 10%;
            background: #F59E0B;
            animation-delay: -10s;
        }

        .float-item:nth-child(4) {
            width: 140px;
            height: 140px;
            top: 20%;
            right: 15%;
            background: #10B981;
            animation-delay: -15s;
        }

        .float-item:nth-child(5) {
            width: 60px;
            height: 60px;
            bottom: 30%;
            left: 50%;
            background: #EF4444;
            animation-delay: -20s;
        }

        @keyframes floatItem {
            0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
            25% { transform: translate(40px, -50px) scale(1.2) rotate(90deg); }
            50% { transform: translate(-30px, 30px) scale(0.8) rotate(180deg); }
            75% { transform: translate(50px, 20px) scale(1.1) rotate(270deg); }
        }

        /* ============================================
           MAIN CONTAINER
        ============================================ */
        .portal-container {
            max-width: 1120px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
            animation: containerIn 0.9s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes containerIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ============================================
           LEFT COLUMN - CONTENT
        ============================================ */
        .portal-content {
            padding-right: 24px;
        }

        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            border-radius: 100px;
            background: rgba(91, 111, 245, 0.12);
            color: #5B6FF5;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 24px;
            border: 1px solid rgba(91, 111, 245, 0.15);
        }

        .portal-badge .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #5B6FF5;
            animation: pulseDot 2s ease-in-out infinite;
            box-shadow: 0 0 12px rgba(91, 111, 245, 0.4);
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }

        .portal-content h1 {
            font-size: clamp(2.8rem, 4.5vw, 4.2rem);
            font-weight: 900;
            line-height: 1.06;
            letter-spacing: -0.04em;
            color: #0F172A;
            margin-bottom: 16px;
        }

        .portal-content h1 .gradient-text {
            background: linear-gradient(135deg, #5B6FF5 0%, #764BA2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .portal-content .subtitle {
            font-size: 1.1rem;
            color: #475569;
            line-height: 1.8;
            margin-bottom: 32px;
            max-width: 480px;
        }

        /* Portal Stats */
        .portal-stats {
            display: flex;
            gap: 32px;
            margin-bottom: 36px;
            padding: 20px 0;
            border-top: 2px solid rgba(226, 232, 240, 0.8);
            border-bottom: 2px solid rgba(226, 232, 240, 0.8);
        }

        .portal-stats .stat-item {
            text-align: left;
        }

        .portal-stats .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0F172A;
            display: block;
            line-height: 1.2;
        }

        .portal-stats .stat-number .plus {
            color: #5B6FF5;
        }

        .portal-stats .stat-label {
            font-size: 0.8rem;
            color: #94A3B8;
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        /* Action Buttons */
        .portal-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .portal-actions .btn {
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            min-height: 56px;
        }

        .portal-actions .btn-primary {
            background: linear-gradient(135deg, #5B6FF5 0%, #764BA2 100%);
            color: white;
            box-shadow: 0 8px 32px rgba(91, 111, 245, 0.35);
        }

        .portal-actions .btn-primary::after {
            content: '→';
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .portal-actions .btn-primary:hover::after {
            transform: translateX(4px);
        }

        .portal-actions .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 48px rgba(91, 111, 245, 0.45);
        }

        .portal-actions .btn-outline {
            background: transparent;
            color: #0F172A;
            border: 2px solid rgba(226, 232, 240, 0.8);
        }

        .portal-actions .btn-outline:hover {
            border-color: #5B6FF5;
            color: #5B6FF5;
            transform: translateY(-3px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background: rgba(91, 111, 245, 0.12);
        }

        /* Trust Badges */
        .portal-trust {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .portal-trust .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94A3B8;
            font-size: 0.8rem;
        }

        .portal-trust .trust-item i {
            color: #5B6FF5;
            font-size: 0.9rem;
        }

        /* ============================================
           RIGHT COLUMN - CARD
        ============================================ */
        .portal-visual {
            display: grid;
            place-items: center;
        }

        .portal-card {
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(40px);
            border-radius: 32px;
            padding: 40px 32px;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            overflow: hidden;
        }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #5B6FF5 0%, #764BA2 100%);
        }

        .portal-card .card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .portal-card .card-header .card-icon {
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: rgba(91, 111, 245, 0.12);
            color: #5B6FF5;
            font-size: 1.8rem;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(91, 111, 245, 0.15);
        }

        .portal-card .card-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 4px;
        }

        .portal-card .card-header p {
            color: #475569;
            font-size: 0.9rem;
            margin: 0;
        }

        .portal-card .card-features {
            display: grid;
            gap: 12px;
            margin-bottom: 28px;
        }

        .portal-card .card-feature {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #F8FAFC;
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: default;
        }

        .portal-card .card-feature:hover {
            transform: translateX(6px);
            border-color: #5B6FF5;
            background: white;
        }

        .portal-card .card-feature .cf-icon {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: rgba(91, 111, 245, 0.12);
            color: #5B6FF5;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .portal-card .card-feature .cf-text {
            font-size: 0.9rem;
            color: #475569;
        }

        .portal-card .card-feature .cf-text strong {
            color: #0F172A;
            font-weight: 600;
        }

        .portal-card .card-footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid rgba(226, 232, 240, 0.8);
        }

        .portal-card .card-footer .btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            background: linear-gradient(135deg, #5B6FF5 0%, #764BA2 100%);
            color: white;
            box-shadow: 0 8px 32px rgba(91, 111, 245, 0.35);
        }

        .portal-card .card-footer .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(91, 111, 245, 0.4);
        }

        .portal-card .card-footer .btn-outline-card {
            background: transparent;
            color: #0F172A;
            border: 2px solid rgba(226, 232, 240, 0.8);
            box-shadow: none;
            margin-top: 10px;
        }

        .portal-card .card-footer .btn-outline-card:hover {
            border-color: #5B6FF5;
            color: #5B6FF5;
            background: rgba(91, 111, 245, 0.12);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .portal-card .card-footer .divider-text {
            display: block;
            color: #94A3B8;
            font-size: 0.8rem;
            margin: 12px 0;
            position: relative;
        }

        .portal-card .card-footer .divider-text::before,
        .portal-card .card-footer .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: rgba(226, 232, 240, 0.8);
        }

        .portal-card .card-footer .divider-text::before {
            left: 0;
        }

        .portal-card .card-footer .divider-text::after {
            right: 0;
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 900px) {
            .portal-container {
                grid-template-columns: 1fr;
                gap: 40px;
                max-width: 600px;
            }

            .portal-content {
                padding-right: 0;
                text-align: center;
            }

            .portal-content .subtitle {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }

            .portal-stats {
                justify-content: center;
            }

            .portal-stats .stat-item {
                text-align: center;
            }

            .portal-actions {
                justify-content: center;
            }

            .portal-trust {
                justify-content: center;
            }

            .portal-visual {
                order: -1;
            }

            .portal-card {
                max-width: 100%;
            }
        }

        @media (max-width: 600px) {
            .portal-hero {
                padding: 80px 16px 40px;
                min-height: auto;
            }

            .top-bar {
                padding: 10px 16px;
            }

            .top-bar .brand .school-name {
                font-size: 0.9rem;
            }

            .top-bar .nav-links a {
                font-size: 0.8rem;
                padding: 4px 10px;
            }

            .top-bar .nav-links .btn-nav {
                padding: 6px 14px;
                font-size: 0.8rem;
            }

            .portal-content h1 {
                font-size: 2.2rem;
            }

            .portal-content .subtitle {
                font-size: 1rem;
            }

            .portal-stats {
                gap: 16px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .portal-stats .stat-number {
                font-size: 1.4rem;
            }

            .portal-actions .btn {
                padding: 14px 24px;
                font-size: 0.9rem;
                min-height: 48px;
                width: 100%;
                justify-content: center;
            }

            .portal-actions {
                flex-direction: column;
                width: 100%;
            }

            .portal-trust {
                gap: 12px;
                justify-content: center;
            }

            .portal-trust .trust-item {
                font-size: 0.7rem;
            }

            .portal-card {
                padding: 28px 20px;
            }

            .portal-card .card-header .card-icon {
                width: 52px;
                height: 52px;
                font-size: 1.4rem;
            }

            .portal-card .card-feature {
                padding: 10px 14px;
            }

            .portal-card .card-feature .cf-text {
                font-size: 0.8rem;
            }
        }

        /* ============================================
           DARK MODE
        ============================================ */
        @media (prefers-color-scheme: dark) {
            body {
                background: #0F172A;
            }

            .top-bar {
                background: rgba(15, 23, 42, 0.95);
                border-bottom-color: rgba(51, 65, 85, 0.4);
            }

            .top-bar .brand .school-name {
                color: #F1F5F9;
            }

            .top-bar .nav-links a {
                color: #94A3B8;
            }

            .top-bar .nav-links a:hover {
                color: #5B6FF5;
                background: rgba(91, 111, 245, 0.12);
            }

            .portal-hero {
                background: #0F172A;
            }

            .portal-hero .bg-gradient {
                background:
                    radial-gradient(ellipse at 20% 50%, rgba(91, 111, 245, 0.12) 0%, transparent 60%),
                    radial-gradient(ellipse at 80% 20%, rgba(118, 75, 162, 0.12) 0%, transparent 50%),
                    radial-gradient(ellipse at 50% 80%, rgba(91, 111, 245, 0.08) 0%, transparent 50%);
            }

            .portal-card {
                background: rgba(15, 23, 42, 0.95);
                backdrop-filter: blur(40px);
                border-color: rgba(51, 65, 85, 0.4);
            }

            .portal-card .card-feature {
                background: #1E293B;
                border-color: rgba(51, 65, 85, 0.4);
            }

            .portal-card .card-feature:hover {
                background: #273548;
                border-color: #5B6FF5;
            }

            .portal-content h1 {
                color: #F1F5F9;
            }

            .portal-content .subtitle {
                color: #94A3B8;
            }

            .portal-stats .stat-number {
                color: #F1F5F9;
            }

            .portal-stats .stat-label {
                color: #64748B;
            }

            .portal-stats {
                border-color: rgba(51, 65, 85, 0.4);
            }

            .portal-actions .btn-outline {
                color: #94A3B8;
                border-color: rgba(51, 65, 85, 0.6);
            }

            .portal-actions .btn-outline:hover {
                border-color: #5B6FF5;
                color: #5B6FF5;
                background: rgba(91, 111, 245, 0.12);
            }

            .portal-trust .trust-item {
                color: #64748B;
            }

            .portal-card .card-header h3 {
                color: #F1F5F9;
            }

            .portal-card .card-header p {
                color: #94A3B8;
            }

            .portal-card .card-feature .cf-text {
                color: #94A3B8;
            }

            .portal-card .card-feature .cf-text strong {
                color: #F1F5F9;
            }

            .portal-card .card-footer {
                border-color: rgba(51, 65, 85, 0.4);
            }

            .portal-card .card-footer .btn-outline-card {
                color: #94A3B8;
                border-color: rgba(51, 65, 85, 0.6);
            }

            .portal-card .card-footer .btn-outline-card:hover {
                border-color: #5B6FF5;
                color: #5B6FF5;
                background: rgba(91, 111, 245, 0.12);
            }

            .portal-card .card-footer .divider-text {
                color: #64748B;
            }

            .portal-card .card-footer .divider-text::before,
            .portal-card .card-footer .divider-text::after {
                background: rgba(51, 65, 85, 0.4);
            }

            .portal-badge {
                background: rgba(91, 111, 245, 0.15);
                border-color: rgba(91, 111, 245, 0.2);
            }
        }

        /* ============================================
           ACCESSIBILITY
        ============================================ */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        .portal-actions .btn:focus-visible,
        .portal-card .card-footer .btn:focus-visible {
            outline: 3px solid #5B6FF5;
            outline-offset: 3px;
        }

        .top-bar .nav-links a:focus-visible {
            outline: 2px solid #5B6FF5;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar with Logo -->
    <nav class="top-bar">
        <a href="{{ route('landing') }}" class="brand">
            @php
                $logo = getSchoolLogo();
                $name = getSchoolName();
            @endphp

            @if($logo)
                <img src="{{ asset($logo) }}" alt="{{ $name }}">
            @else
                <div class="logo-placeholder">
                    <i class="fas fa-school"></i>
                </div>
            @endif
            <span class="school-name">{{ $name }}</span>
        </a>

        <div class="nav-links">
            <a href="{{ route('landing') }}">Home</a>
            <a href="{{ route('application.login') }}">Login</a>
            <a href="{{ route('application.register') }}" class="btn-nav">Apply Now</a>
        </div>
    </nav>

    <div class="portal-hero">
        <!-- Background -->
        <div class="bg-gradient"></div>

        <!-- Floating Elements -->
        <div class="floating-elements">
            <div class="float-item"></div>
            <div class="float-item"></div>
            <div class="float-item"></div>
            <div class="float-item"></div>
            <div class="float-item"></div>
        </div>

        <!-- Main Container -->
        <div class="portal-container">
            <!-- Left Column - Content -->
            <div class="portal-content">
                <div class="portal-badge">
                    <span class="badge-dot"></span>
                    Open for 2025/2026 Session
                </div>

                <h1>
                    Begin Your<br>
                    <span class="gradient-text">Admission Journey</span>
                </h1>

                <p class="subtitle">
                    Join our community of scholars. Apply for admission today and take the first step
                    toward a bright future in a nurturing and innovative learning environment.
                </p>

                <!-- Stats -->
                <div class="portal-stats">
                    <div class="stat-item">
                        <span class="stat-number">1,200<span class="plus">+</span></span>
                        <span class="stat-label">Students</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">98<span class="plus">%</span></span>
                        <span class="stat-label">Pass Rate</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">4.8<span class="plus">★</span></span>
                        <span class="stat-label">Parent Rating</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="portal-actions">
                    <a href="{{ route('application.register') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </a>
                    <a href="{{ route('application.login') }}" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i>
                        Applicant Login
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="portal-trust">
                    <span class="trust-item">
                        <i class="fas fa-lock"></i>
                        256-bit SSL
                    </span>
                    <span class="trust-item">
                        <i class="fas fa-headset"></i>
                        24/7 Support
                    </span>
                    <span class="trust-item">
                        <i class="fas fa-shield-alt"></i>
                        Data Protection
                    </span>
                    <span class="trust-item">
                        <i class="fas fa-clock"></i>
                        Quick Processing
                    </span>
                </div>
            </div>

            <!-- Right Column - Card -->
            <div class="portal-visual">
                <div class="portal-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Ready to Apply?</h3>
                        <p>Complete your admission in 3 simple steps</p>
                    </div>

                    <div class="card-features">
                        <div class="card-feature">
                            <div class="cf-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="cf-text">
                                <strong>Step 1:</strong> Fill the application form
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="cf-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="cf-text">
                                <strong>Step 2:</strong> Pay application fee
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="cf-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="cf-text">
                                <strong>Step 3:</strong> Track your application
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('application.register') }}" class="btn">
                            <i class="fas fa-arrow-right"></i>
                            Get Started Now
                        </a>
                        <span class="divider-text">or</span>
                        <a href="{{ route('application.login') }}" class="btn btn-outline-card">
                            <i class="fas fa-sign-in-alt"></i>
                            Login to Continue
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============================================
            // SCROLL ANIMATION
            // ============================================
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -30px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.card-feature').forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition =
                    `all 0.6s cubic-bezier(0.22, 1, 0.36, 1) ${index * 0.1 + 0.3}s`;
                observer.observe(item);
            });

            // ============================================
            // KEYBOARD NAVIGATION
            // ============================================
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-nav');
                }
            });

            document.addEventListener('mousedown', function() {
                document.body.classList.remove('keyboard-nav');
            });

            // ============================================
            // SMOOTH SCROLL
            // ============================================
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>

    <style>
        /* Keyboard navigation focus styles */
        .keyboard-nav *:focus-visible {
            outline: 3px solid #5B6FF5 !important;
            outline-offset: 3px !important;
        }

        .portal-actions .btn:focus-visible,
        .portal-card .card-footer .btn:focus-visible {
            outline: 3px solid #5B6FF5 !important;
            outline-offset: 3px !important;
        }
    </style>
</body>
</html>