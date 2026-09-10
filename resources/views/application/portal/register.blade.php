<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Application Portal</title>
    
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
           REGISTER HERO
        ============================================ */
        .register-hero {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 40px 24px;
            position: relative;
            overflow: hidden;
            background: #F8FAFC;
        }

        .register-hero .bg-gradient {
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
        .register-floating {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .register-floating .float-item {
            position: absolute;
            border-radius: 50%;
            opacity: 0.06;
            animation: floatItem 25s ease-in-out infinite;
        }

        .register-floating .float-item:nth-child(1) {
            width: 160px;
            height: 160px;
            top: 5%;
            left: 5%;
            background: #5B6FF5;
            animation-delay: 0s;
        }

        .register-floating .float-item:nth-child(2) {
            width: 100px;
            height: 100px;
            top: 60%;
            right: 8%;
            background: #764BA2;
            animation-delay: -5s;
        }

        .register-floating .float-item:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 10%;
            left: 10%;
            background: #F59E0B;
            animation-delay: -10s;
        }

        .register-floating .float-item:nth-child(4) {
            width: 140px;
            height: 140px;
            top: 20%;
            right: 15%;
            background: #10B981;
            animation-delay: -15s;
        }

        .register-floating .float-item:nth-child(5) {
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
        .register-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 1;
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
           REGISTER CARD
        ============================================ */
        .register-card {
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(40px);
            border-radius: 32px;
            padding: 40px 32px;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            overflow: hidden;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #5B6FF5 0%, #764BA2 100%);
        }

        /* ============================================
           LOGO SECTION
        ============================================ */
        .register-card .logo-section {
            text-align: center;
            margin-bottom: 28px;
        }

        .register-card .logo-section .logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .register-card .logo-section .logo-wrapper img {
            max-height: 70px;
            width: auto;
            object-fit: contain;
        }

        .register-card .logo-section .logo-wrapper .logo-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(91, 111, 245, 0.12), rgba(118, 75, 162, 0.08));
            display: grid;
            place-items: center;
            color: #5B6FF5;
            font-size: 2rem;
            box-shadow: 0 8px 24px rgba(91, 111, 245, 0.12);
        }

        .register-card .logo-section .school-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 2px;
        }

        .register-card .logo-section .school-tagline {
            font-size: 0.8rem;
            color: #94A3B8;
            margin: 0;
        }

        /* ============================================
           CARD HEADER
        ============================================ */
        .register-card .card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .register-card .card-header .card-icon {
            width: 60px;
            height: 60px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: rgba(91, 111, 245, 0.12);
            color: #5B6FF5;
            font-size: 1.6rem;
            margin: 0 auto 12px;
            box-shadow: 0 8px 24px rgba(91, 111, 245, 0.12);
        }

        .register-card .card-header h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 4px;
        }

        .register-card .card-header p {
            color: #475569;
            font-size: 0.9rem;
            margin: 0;
            line-height: 1.6;
        }

        /* ============================================
           FORM STYLES
        ============================================ */
        .register-card .form-group {
            margin-bottom: 18px;
        }

        .register-card .form-group label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1E293B;
            margin-bottom: 4px;
            display: block;
        }

        .register-card .form-group label .required {
            color: #EF4444;
            margin-left: 2px;
        }

        .register-card .form-group .form-control {
            width: 100%;
            border-radius: 12px;
            border: 2px solid #E2E8F0;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #FAFBFC;
            color: #0F172A;
        }

        .register-card .form-group .form-control:focus {
            border-color: #5B6FF5;
            outline: none;
            box-shadow: 0 0 0 4px rgba(91, 111, 245, 0.10);
            background: white;
        }

        .register-card .form-group .form-control::placeholder {
            color: #94A3B8;
            font-size: 0.9rem;
        }

        .register-card .form-group .form-control.is-invalid {
            border-color: #EF4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
        }

        .register-card .form-group .input-group {
            display: flex;
            align-items: stretch;
        }

        .register-card .form-group .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid #E2E8F0;
            border-right: none;
            background: #FAFBFC;
            color: #94A3B8;
            padding: 0 14px;
            display: flex;
            align-items: center;
        }

        .register-card .form-group .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        .register-card .form-group .input-group .eye-toggle {
            border-radius: 0 12px 12px 0;
            border: 2px solid #E2E8F0;
            border-left: none;
            background: #FAFBFC;
            padding: 0 14px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #94A3B8;
        }

        .register-card .form-group .input-group .eye-toggle:hover {
            background: #F1F5F9;
            color: #5B6FF5;
        }

        .register-card .form-group .form-hint {
            font-size: 0.75rem;
            color: #94A3B8;
            margin-top: 4px;
            display: block;
        }

        .register-card .form-group .form-hint.error {
            color: #EF4444;
        }

        .register-card .form-group .form-hint.success {
            color: #22C55E;
        }

        /* Password Strength */
        .register-card .password-strength {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .register-card .password-strength .bar {
            flex: 1;
            height: 4px;
            border-radius: 4px;
            background: #E2E8F0;
            transition: all 0.3s ease;
        }

        .register-card .password-strength .bar.active.weak {
            background: #EF4444;
        }

        .register-card .password-strength .bar.active.medium {
            background: #F59E0B;
        }

        .register-card .password-strength .bar.active.strong {
            background: #22C55E;
        }

        .register-card .password-strength-label {
            font-size: 0.7rem;
            color: #94A3B8;
            margin-top: 4px;
            display: block;
            text-align: right;
        }

        /* Form Check */
        .register-card .form-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .register-card .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            flex-shrink: 0;
            accent-color: #5B6FF5;
        }

        .register-card .form-check label {
            font-size: 0.85rem;
            color: #475569;
            margin: 0;
            line-height: 1.5;
        }

        .register-card .form-check label a {
            color: #5B6FF5;
            text-decoration: none;
            font-weight: 600;
        }

        .register-card .form-check label a:hover {
            text-decoration: underline;
        }

        /* ============================================
           BUTTONS
        ============================================ */
        .register-card .btn-register {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            min-height: 56px;
            background: linear-gradient(135deg, #5B6FF5 0%, #764BA2 100%);
            color: white;
            box-shadow: 0 8px 32px rgba(91, 111, 245, 0.35);
            margin-top: 6px;
            position: relative;
        }

        .register-card .btn-register::after {
            content: '→';
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .register-card .btn-register:hover::after {
            transform: translateX(4px);
        }

        .register-card .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 48px rgba(91, 111, 245, 0.45);
        }

        .register-card .btn-register:active {
            transform: translateY(-1px) scale(0.98);
        }

        .register-card .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .register-card .btn-register .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            position: absolute;
        }

        .register-card .btn-register.loading .spinner {
            display: block;
        }

        .register-card .btn-register.loading .btn-text {
            visibility: hidden;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ============================================
           SOCIAL DIVIDER
        ============================================ */
        .register-card .social-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 22px 0 18px;
        }

        .register-card .social-divider::before,
        .register-card .social-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }

        .register-card .social-divider span {
            color: #94A3B8;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        /* Social Buttons */
        .register-card .social-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .register-card .social-btn {
            padding: 12px;
            border-radius: 12px;
            border: 2px solid #E2E8F0;
            background: white;
            font-weight: 600;
            font-size: 0.85rem;
            color: #0F172A;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .register-card .social-btn:hover {
            border-color: #5B6FF5;
            background: #FAFBFC;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        }

        .register-card .social-btn.google {
            color: #EA4335;
        }

        .register-card .social-btn.facebook {
            color: #1877F2;
        }

        /* ============================================
           FOOTER
        ============================================ */
        .register-card .auth-footer {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 2px solid rgba(226, 232, 240, 0.8);
            text-align: center;
            color: #64748B;
            font-size: 0.9rem;
        }

        .register-card .auth-footer a {
            color: #5B6FF5;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-card .auth-footer a:hover {
            color: #4A5BD4;
            text-decoration: underline;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .alert-success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .alert i {
            font-size: 1.1rem;
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 600px) {
            .register-hero {
                padding: 24px 16px;
                min-height: auto;
            }

            .register-card {
                padding: 24px 18px;
            }

            .register-card .logo-section .logo-wrapper img {
                max-height: 50px;
            }

            .register-card .logo-section .logo-wrapper .logo-placeholder {
                width: 56px;
                height: 56px;
                font-size: 1.6rem;
            }

            .register-card .logo-section .school-name {
                font-size: 1rem;
            }

            .register-card .card-header .card-icon {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
            }

            .register-card .card-header h3 {
                font-size: 1.2rem;
            }

            .register-card .social-buttons {
                grid-template-columns: 1fr;
            }
        }

        /* ============================================
           DARK MODE
        ============================================ */
        @media (prefers-color-scheme: dark) {
            body {
                background: #0F172A;
            }

            .register-hero {
                background: #0F172A;
            }

            .register-hero .bg-gradient {
                background:
                    radial-gradient(ellipse at 20% 50%, rgba(91, 111, 245, 0.12) 0%, transparent 60%),
                    radial-gradient(ellipse at 80% 20%, rgba(118, 75, 162, 0.12) 0%, transparent 50%),
                    radial-gradient(ellipse at 50% 80%, rgba(91, 111, 245, 0.08) 0%, transparent 50%);
            }

            .register-card {
                background: rgba(15, 23, 42, 0.95);
                backdrop-filter: blur(40px);
                border-color: rgba(51, 65, 85, 0.4);
            }

            .register-card .logo-section .school-name {
                color: #F1F5F9;
            }

            .register-card .logo-section .school-tagline {
                color: #64748B;
            }

            .register-card .logo-section .logo-wrapper .logo-placeholder {
                background: rgba(91, 111, 245, 0.15);
            }

            .register-card .card-header h3 {
                color: #F1F5F9;
            }

            .register-card .card-header p {
                color: #94A3B8;
            }

            .register-card .card-header .card-icon {
                background: rgba(91, 111, 245, 0.15);
            }

            .register-card .form-group label {
                color: #E2E8F0;
            }

            .register-card .form-group .form-control {
                background: #1E293B;
                border-color: #334155;
                color: #F1F5F9;
            }

            .register-card .form-group .form-control:focus {
                border-color: #5B6FF5;
                background: #1E293B;
                box-shadow: 0 0 0 4px rgba(91, 111, 245, 0.12);
            }

            .register-card .form-group .form-control::placeholder {
                color: #64748B;
            }

            .register-card .form-group .input-group-text {
                background: #1E293B;
                border-color: #334155;
                color: #64748B;
            }

            .register-card .form-group .input-group .eye-toggle {
                background: #1E293B;
                border-color: #334155;
                color: #64748B;
            }

            .register-card .form-group .input-group .eye-toggle:hover {
                background: #273548;
                color: #5B6FF5;
            }

            .register-card .form-check label {
                color: #94A3B8;
            }

            .register-card .social-divider::before,
            .register-card .social-divider::after {
                background: #334155;
            }

            .register-card .social-divider span {
                color: #64748B;
            }

            .register-card .social-btn {
                background: #1E293B;
                border-color: #334155;
                color: #E2E8F0;
            }

            .register-card .social-btn:hover {
                border-color: #5B6FF5;
                background: #273548;
            }

            .register-card .auth-footer {
                border-color: rgba(51, 65, 85, 0.4);
                color: #94A3B8;
            }

            .register-card .auth-footer a {
                color: #5B6FF5;
            }

            .register-card .password-strength .bar {
                background: #334155;
            }

            .alert-danger {
                background: #7F1D1D;
                color: #FECACA;
                border-color: #991B1B;
            }

            .alert-success {
                background: #064E3B;
                color: #A7F3D0;
                border-color: #065F46;
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

        .register-card .btn-register:focus-visible,
        .register-card .social-btn:focus-visible {
            outline: 3px solid #5B6FF5;
            outline-offset: 3px;
        }

        .register-card .form-group .form-control:focus-visible {
            outline: none;
        }

        .keyboard-nav *:focus-visible {
            outline: 3px solid #5B6FF5 !important;
            outline-offset: 3px !important;
        }
    </style>
</head>
<body>
    <div class="register-hero">
        <!-- Background -->
        <div class="bg-gradient"></div>

        <!-- Floating Elements -->
        <div class="register-floating">
            <div class="float-item"></div>
            <div class="float-item"></div>
            <div class="float-item"></div>
            <div class="float-item"></div>
            <div class="float-item"></div>
        </div>

        <!-- Main Container -->
        <div class="register-container">
            <div class="register-card">
                <!-- Logo Section -->
                <div class="logo-section">
                    <div class="logo-wrapper">
                        @php
                            $logo = getSchoolLogo();
                            $name = getSchoolName();
                            $tagline = getSchoolTagline();
                        @endphp

                        @if($logo)
                            <img src="{{ asset($logo) }}" alt="{{ $name }}" class="img-fluid">
                        @else
                            <div class="logo-placeholder">
                                <i class="fas fa-school"></i>
                            </div>
                        @endif
                    </div>
                    <div class="school-name">{{ $name }}</div>
                    <p class="school-tagline">{{ $tagline }}</p>
                </div>

                <!-- Card Header -->
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Create Your Account</h3>
                    <p>Register once and manage your admission application, payments, and status from one portal.</p>
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- Registration Form -->
                <form action="{{ route('application.register.store') }}" method="POST" id="registerForm">
                    @csrf

                    <!-- Full Name -->
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter your full name"
                               value="{{ old('name') }}"
                               required
                               autofocus>
                        @error('name')
                            <span class="form-hint error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="your@email.com"
                                   value="{{ old('email') }}"
                                   required>
                        </div>
                        @error('email')
                            <span class="form-hint error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label>Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" 
                                   name="phone" 
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="080 1234 5678"
                                   value="{{ old('phone') }}">
                        </div>
                        @error('phone')
                            <span class="form-hint error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label>Password <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min 8 characters"
                                   required
                                   minlength="8">
                            <span class="eye-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggle"></i>
                            </span>
                        </div>
                        @error('password')
                            <span class="form-hint error">{{ $message }}</span>
                        @enderror

                        <!-- Password Strength -->
                        <div class="password-strength">
                            <div class="bar" id="strength1"></div>
                            <div class="bar" id="strength2"></div>
                            <div class="bar" id="strength3"></div>
                            <div class="bar" id="strength4"></div>
                        </div>
                        <span class="password-strength-label" id="strengthLabel">Enter a strong password</span>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label>Confirm Password <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   class="form-control"
                                   placeholder="Confirm your password"
                                   required>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="form-group" style="margin-bottom: 22px;">
                        <div class="form-check">
                            <input type="checkbox" name="terms" id="terms" required>
                            <label for="terms">
                                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                                <span class="required">*</span>
                            </label>
                        </div>
                        @error('terms')
                            <span class="form-hint error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-register" id="registerBtn">
                        <span class="btn-text"><i class="fas fa-arrow-right"></i> Create Account</span>
                        <span class="spinner"></span>
                    </button>
                </form>

                <!-- Social Login -->
                <div class="social-divider">
                    <span>or continue with</span>
                </div>

                <div class="social-buttons">
                    <a href="#" class="social-btn google">
                        <i class="fab fa-google"></i> Google
                    </a>
                    <a href="#" class="social-btn facebook">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                </div>

                <!-- Footer -->
                <div class="auth-footer">
                    <p>
                        Already have an account? 
                        <a href="{{ route('application.login') }}">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============================================
            // PASSWORD STRENGTH INDICATOR
            // ============================================
            const passwordInput = document.getElementById('password');
            const strengthBars = [
                document.getElementById('strength1'),
                document.getElementById('strength2'),
                document.getElementById('strength3'),
                document.getElementById('strength4')
            ];
            const strengthLabel = document.getElementById('strengthLabel');

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let score = 0;
                if (password.length >= 8) score++;
                if (password.match(/[a-z]/)) score++;
                if (password.match(/[A-Z]/)) score++;
                if (password.match(/[0-9]/)) score++;
                if (password.match(/[^a-zA-Z0-9]/)) score++;
                score = Math.min(score, 4);

                const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
                const classes = ['', 'weak', 'medium', 'medium', 'strong'];

                strengthBars.forEach((bar, index) => {
                    bar.className = 'bar';
                    if (index < score) {
                        bar.classList.add('active', classes[score]);
                    }
                });

                strengthLabel.textContent = score > 0 ? `${labels[score]} Password` : 'Enter a strong password';
                strengthLabel.style.color = score > 2 ? '#22C55E' : score > 0 ? '#F59E0B' : '#94A3B8';
            });

            // ============================================
            // TOGGLE PASSWORD VISIBILITY
            // ============================================
            window.togglePassword = function() {
                const input = document.getElementById('password');
                const toggle = document.getElementById('passwordToggle');
                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.classList.remove('fa-eye');
                    toggle.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    toggle.classList.remove('fa-eye-slash');
                    toggle.classList.add('fa-eye');
                }
            };

            // ============================================
            // FORM SUBMISSION LOADING STATE
            // ============================================
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('registerBtn');

            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    return;
                }

                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });

            // ============================================
            // PASSWORD CONFIRMATION VALIDATION
            // ============================================
            const confirmPassword = document.getElementById('password_confirmation');
            confirmPassword.addEventListener('input', function() {
                const password = document.getElementById('password').value;
                if (this.value && this.value !== password) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
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

            document.querySelectorAll('.register-card .logo-section, .register-card .card-header, .register-card .form-group, .register-card .form-check, .register-card .btn-register, .register-card .social-divider, .register-card .social-buttons, .register-card .auth-footer').forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = `all 0.6s cubic-bezier(0.22, 1, 0.36, 1) ${index * 0.08 + 0.2}s`;
                observer.observe(item);
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

        .register-card .btn-register:focus-visible,
        .register-card .social-btn:focus-visible {
            outline: 3px solid #5B6FF5 !important;
            outline-offset: 3px !important;
        }
    </style>
</body>
</html>