<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Get system settings using helper functions
        $appName = getAppName();
        $siteTitle = getSeoTitle();
        $siteDescription = getSeoDescription();
        $siteKeywords = getSeoKeywords();
        $seoAuthor = getSetting('seo_author', getSchoolName());
        $seoTwitterCard = getSetting('seo_twitter_card', 'summary_large_image');
        $seoTwitterSite = getSetting('seo_twitter_site', '');
        $seoMetaRobots = getSetting('seo_meta_robots', 'index, follow');
        $seoMetaLanguage = getSetting('seo_meta_language', 'en');
        $seoMetaRating = getSetting('seo_meta_rating', 'General');
        $googleVerification = getSetting('seo_google_verification', '');
        $bingVerification = getSetting('seo_bing_verification', '');
        $ogImage = getSetting('seo_og_image', '');
        $googleAnalytics = getSetting('seo_google_analytics', '');
        $canonicalUrl = getSetting('seo_canonical_url', getAppUrl());
        
        // School information
        $schoolName = getSchoolName();
        $schoolTagline = getSchoolTagline();
        $schoolLogo = getSchoolLogo();
        $schoolIcon = getSchoolIcon();
        $schoolAddress = getSetting('school_address', '');
        $schoolPhone = getSetting('school_phone', '');
        $schoolEmail = getSetting('school_email', '');
        $footerText = getFooterText();
    @endphp

    <title>@yield('title', $appName . ' - ' . $schoolName)</title>

    <!-- Primary Meta Tags -->
    <meta name="description" content="{{ $siteDescription }}">
    <meta name="keywords" content="{{ $siteKeywords }}">
    <meta name="author" content="{{ $seoAuthor }}">
    <meta name="robots" content="{{ $seoMetaRobots }}">
    <meta name="rating" content="{{ $seoMetaRating }}">
    <meta name="language" content="{{ $seoMetaLanguage }}">
    @if($googleVerification)
        <meta name="google-site-verification" content="{{ $googleVerification }}">
    @endif
    @if($bingVerification)
        <meta name="msvalidate.01" content="{{ $bingVerification }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="@yield('title', $siteTitle)">
    <meta property="og:description" content="{{ $siteDescription }}">
    @if($ogImage)
        <meta property="og:image" content="{{ asset($ogImage) }}">
    @elseif($schoolLogo)
        <meta property="og:image" content="{{ asset($schoolLogo) }}">
    @endif
    <meta property="og:site_name" content="{{ $schoolName }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="{{ $seoTwitterCard }}">
    <meta name="twitter:title" content="@yield('title', $siteTitle)">
    <meta name="twitter:description" content="{{ $siteDescription }}">
    @if($seoTwitterSite)
        <meta name="twitter:site" content="{{ $seoTwitterSite }}">
    @endif
    @if($ogImage)
        <meta name="twitter:image" content="{{ asset($ogImage) }}">
    @elseif($schoolLogo)
        <meta name="twitter:image" content="{{ asset($schoolLogo) }}">
    @endif

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Google Analytics -->
    @if($googleAnalytics)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalytics }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalytics }}');
        </script>
    @endif

    <!-- Favicon -->
    @if($schoolIcon)
        <link rel="icon" href="{{ asset($schoolIcon) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset($schoolIcon) }}" type="image/x-icon">
    @elseif($schoolLogo)
        <link rel="icon" href="{{ asset($schoolLogo) }}" type="image/x-icon">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏫</text></svg>">
    @endif

    <!-- Theme Color -->
    <meta name="theme-color" content="{{ getPrimaryColor() }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --theme-primary: {{ getPrimaryColor() }};
            --theme-secondary: {{ getSecondaryColor() }};
            --theme-sidebar: {{ getSidebarColor() }};
            --theme-header: {{ getHeaderColor() }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 260px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            overflow-x: hidden;
            height: 100vh;
            max-height: 100vh;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        .sidebar.collapsed {
            margin-left: -260px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header .school-logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }

        .sidebar-header .school-logo-wrapper img {
            max-height: 70px;
            width: auto;
            object-fit: contain;
        }

        .sidebar-header .school-logo-placeholder {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .sidebar-header .school-logo-placeholder i {
            color: white;
            font-size: 32px;
        }

        .sidebar-header h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 16px;
            line-height: 1.3;
        }

        .sidebar-header small {
            color: rgba(255,255,255,0.7);
            font-size: 11px;
            display: block;
        }

        /* Sidebar Navigation */
        .sidebar .nav-item {
            width: 100%;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 12px 20px;
            transition: all 0.3s;
            border-radius: 8px;
            margin: 5px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }

        .sidebar .nav-link span {
            flex: 1;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .sidebar .nav-link i:first-child {
            margin-right: 10px;
            width: 20px;
        }

        .sidebar .nav-link .fa-chevron-down,
        .sidebar .nav-link .fa-chevron-up {
            font-size: 12px;
            transition: transform 0.3s;
        }

        .sidebar .nav {
            padding-bottom: 20px;
        }

        /* Submenu Styles */
        .submenu {
            list-style: none;
            padding-left: 45px;
            margin: 0;
            display: none;
        }

        .submenu.show {
            display: block;
        }

        .submenu .nav-item {
            margin: 5px 0;
        }

        .submenu .nav-link {
            padding: 8px 15px;
            font-size: 0.9rem;
            margin: 2px 10px;
        }

        .submenu .nav-link i {
            font-size: 0.8rem;
            margin-right: 8px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Top Header */
        .top-header {
            background: var(--theme-header);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
            padding: 10px 20px;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--theme-primary);
            transition: all 0.3s;
        }

        .toggle-btn:hover {
            transform: scale(1.1);
        }

        .user-dropdown {
            cursor: pointer;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Footer */
        .footer {
            background: white;
            padding: 20px;
            text-align: center;
            margin-top: auto;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        /* Content Area */
        .content-wrapper {
            padding: 20px;
            flex: 1;
        }

        /* Cards */
        .card-stats {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 15px;
            cursor: pointer;
        }

        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1050;
            }

            .sidebar.collapsed {
                margin-left: -260px;
            }

            .main-content {
                margin-left: 0;
            }

            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
                display: none;
            }

            .overlay.show {
                display: block;
            }

            .submenu {
                padding-left: 30px;
            }
        }

        /* Breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        /* Badge styles */
        .badge-count {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 11px;
            margin-left: 8px;
        }

        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #868f96 0%, #596164 100%);
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #2c3e50 0%, #2c3e50 100%);
        }

        .bg-gradient-teal {
            background: linear-gradient(135deg, #20c997 0%, #20c997 100%);
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .display-6 {
            font-size: 2rem;
            font-weight: 600;
        }

        .opacity-50 {
            opacity: 0.5;
        }

        /* Sidebar header styles */
        .nav-header {
            padding: 18px 20px 7px;
            font-size: 11px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
            letter-spacing: 1.2px;
            margin-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar .nav-header:first-child { border-top: 0; }

        .sidebar .has-treeview > .nav-link .fa-chevron-down,
        .sidebar .has-treeview > .nav-link .fa-chevron-up { opacity: .65; }

        .sidebar .submenu { border-left: 1px solid rgba(255,255,255,0.16); margin-left: 28px; }

        .sidebar .has-treeview.menu-open > .submenu { display: block; }

        /* Submenu active state */
        .submenu .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }
    </style>

    @yield('styles')
    @stack('styles')
    @if(getSetting('theme_custom_css', ''))
        <style>{!! getSetting('theme_custom_css') !!}</style>
    @endif
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            @php
                $logo = getSchoolLogo();
                $name = getSchoolName();
                $tagline = getSchoolTagline();
            @endphp

            @if($logo)
                <div class="school-logo-wrapper">
                    <img src="{{ asset($logo) }}" alt="{{ $name }}" class="img-fluid">
                </div>
            @else
                <div class="school-logo-placeholder">
                    <i class="fas fa-school"></i>
                </div>
            @endif

            <h5>{{ $name }}</h5>
            <small>{{ $tagline }}</small>
        </div>

        <!-- Dynamic Sidebar Based on Role -->
        @php
            $role = Auth::check() ? Auth::user()->role : 'guest';
            $sidebarView = 'dashboards.partials.' . $role . '-sidebar';
        @endphp

        @if(view()->exists($sidebarView))
            @include($sidebarView)
        @else
            @include('dashboards.partials.default-sidebar')
        @endif
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
       
    <!-- Top Header -->
    <div class="top-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button class="toggle-btn" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="d-flex align-items-center">
                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <div class="d-flex align-items-center" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                                <div class="user-avatar me-2">
                            @if(Auth::check() && Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ substr(Auth::check() ? Auth::user()->name : 'A', 0, 1) }}
                            @endif
                        </div>
                        <div class="d-none d-md-block">
                            <small class="text-muted">Welcome,</small>
                            <strong>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</strong>
                            <span class="d-block small text-muted" style="font-size: 10px; line-height: 1;">
                                <i class="fas fa-circle text-success" style="font-size: 6px;"></i> 
                                {{ ucfirst(str_replace('_', ' ', Auth::check() ? Auth::user()->role : 'guest')) }}
                            </span>
                        </div>
                        <i class="fas fa-chevron-down ms-2 text-muted"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 260px; padding: 8px 0;">
                        <!-- User Info Header with Avatar -->
                        <li class="dropdown-item-text px-3 py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar-sm me-3" style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px; overflow: hidden; flex-shrink: 0;">
                                    @if(Auth::check() && Auth::user()->avatar_url)
                                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @elseif(Auth::check())
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    @else
                                        G
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size: 15px;">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                                    <div class="text-muted" style="font-size: 12px;">
                                        <i class="fas fa-envelope me-1"></i> {{ Auth::check() ? Auth::user()->email : '' }}
                                    </div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        <span class="badge bg-primary">{{ Auth::check() ? ucfirst(str_replace('_', ' ', Auth::user()->role ?? 'User')) : 'Guest' }}</span>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Profile Section -->
                        <li class="px-2 py-1">
                            <small class="text-muted px-2" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Profile</small>
                        </li>
                        @auth
                            @if(Route::has('profile') || Route::has('profile.index'))
                                @php
                                    $profileRoute = Route::has('profile') ? route('profile') : route('profile.index');
                                @endphp
                                <li>
                                    <a class="dropdown-item" href="{{ $profileRoute }}">
                                        <i class="fas fa-user-circle me-2" style="width: 18px; text-align: center; color: #667eea;"></i> 
                                        My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ $profileRoute }}">
                                        <i class="fas fa-cog me-2" style="width: 18px; text-align: center; color: #6c757d;"></i> 
                                        Account Settings
                                    </a>
                                </li>
                            @endif
                        @endauth
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-2" style="width: 18px; text-align: center; color: #ffc107;"></i> 
                                Change Password
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <!-- Notifications -->
                        <li class="px-2 py-1">
                            <small class="text-muted px-2" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Notifications</small>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ Auth::guard('web')->check() ? route('notifications.index') : '#' }}">
                                <i class="fas fa-bell me-2" style="width: 18px; text-align: center; color: #17a2b8;"></i> 
                                Notifications
                                @php
                                    $unreadCount = 0;
                                    if (Auth::guard('web')->check()) {
                                        $webUser = Auth::guard('web')->user();
                                        if ($webUser) {
                                            if (isset($webUser->unreadNotifications) && is_countable($webUser->unreadNotifications)) {
                                                $unreadCount = count($webUser->unreadNotifications);
                                            } elseif (method_exists($webUser, 'notifications')) {
                                                $unreadCount = $webUser->notifications()->where('is_read', false)->count();
                                            }
                                        }
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-1">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <!-- Quick Actions -->
                        <li class="px-2 py-1">
                            <small class="text-muted px-2" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Quick Actions</small>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2" style="width: 18px; text-align: center; color: #28a745;"></i> 
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="fas fa-cogs me-2" style="width: 18px; text-align: center; color: #6c757d;"></i> 
                                System Settings
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <!-- Help & Support -->
                        <li class="px-2 py-1">
                            <small class="text-muted px-2" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Support</small>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-question-circle me-2" style="width: 18px; text-align: center; color: #17a2b8;"></i> 
                                Help & Support
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-file-alt me-2" style="width: 18px; text-align: center; color: #6f42c1;"></i> 
                                Documentation
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <!-- Logout -->
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2" style="width: 18px; text-align: center;"></i> 
                                Logout
                            </a>
                        </li>
                    </ul>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @if(getSetting('theme_show_breadcrumb', 'true') === 'true')
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 text-md-start">
                    <p class="mb-0">
                        @php
                            $footerText = getFooterText();
                        @endphp
                        {!! $footerText !!}
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <a href="{{ getSetting('privacy_policy_url', '/privacy-policy') }}" class="text-decoration-none">Privacy Policy</a> |
                        <a href="{{ getSetting('terms_url', '/terms-of-use') }}" class="text-decoration-none">Terms of Use</a> |
                        <a href="mailto:{{ getSchoolEmail() ?: config('mail.from.address') }}" class="text-decoration-none">Support</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2 text-warning"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" placeholder="Enter your current password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Enter new password (min 8 characters)" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Password must be at least 8 characters long.
                        </div>
                    </div>
                    <div class="mb-0">
                        <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Submenu toggle function
        function toggleSubmenu(event, menuId) {
            event.preventDefault();
            var menu = document.getElementById(menuId);
            var icon = event.currentTarget.querySelector('.fa-chevron-down, .fa-chevron-up');

            if (menu.classList.contains('show')) {
                menu.classList.remove('show');
                if (icon) {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
                localStorage.setItem(menuId + 'State', 'closed');
            } else {
                menu.classList.add('show');
                if (icon) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
                localStorage.setItem(menuId + 'State', 'open');
            }
        }

        // Sidebar Toggle Functionality
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const mainContent = $('#mainContent');
            const overlay = $('#sidebarOverlay');

            // Load saved submenu states
            const submenus = [
                'studentSubmenu', 'classSubmenu', 'subjectSubmenu', 
                'examSubmenu', 'cbtSubmenu', 'scoresSubmenu', 'resultSubmenu',
                'feePaymentSubmenu', 'feeStructureSubmenu', 'expenseSubmenu', 'incomeSubmenu', 'assetSubmenu',
                'employeeSubmenu', 'salarySubmenu', 'loanSubmenu', 'overdraftSubmenu',
                'reportSubmenu', 'applicationSubmenu', 'recruitmentSubmenu', 'certificateSubmenu', 'notificationSubmenu',
                'gradingSubmenu', 'questionPaperSubmenu'
            ];

            submenus.forEach(function(menuId) {
                var savedState = localStorage.getItem(menuId + 'State');
                var menu = document.getElementById(menuId);
                var icon = document.getElementById(menuId + 'Icon');

                if (savedState === 'open') {
                    if (menu) menu.classList.add('show');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                } else if (savedState === 'closed') {
                    if (menu) menu.classList.remove('show');
                    if (icon) {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                }
            });

            // Toggle sidebar
            $('#sidebarToggle').click(function() {
                sidebar.toggleClass('collapsed');
                mainContent.toggleClass('expanded');

                const isCollapsed = sidebar.hasClass('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);

                if ($(window).width() <= 768) {
                    overlay.toggleClass('show');
                }
            });

            // Load saved sidebar state
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true' && $(window).width() > 768) {
                sidebar.addClass('collapsed');
                mainContent.addClass('expanded');
            }

            // Close sidebar when clicking overlay
            overlay.click(function() {
                sidebar.addClass('collapsed');
                mainContent.addClass('expanded');
                overlay.removeClass('show');
            });

            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() > 768) {
                    overlay.removeClass('show');
                    if (localStorage.getItem('sidebarCollapsed') === 'true') {
                        sidebar.addClass('collapsed');
                        mainContent.addClass('expanded');
                    } else {
                        sidebar.removeClass('collapsed');
                        mainContent.removeClass('expanded');
                    }
                } else {
                    sidebar.addClass('collapsed');
                    mainContent.addClass('expanded');
                }
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add fade-in animation to main content
            $('.content-wrapper').addClass('fade-in');

            // Auto-open submenu if on a specific page
            var currentUrl = window.location.href;
            var menuMap = {
                '/cbt/': 'cbtSubmenu',
                '/scores/': 'scoresSubmenu',
                '/results/': 'resultSubmenu',
                '/fee-payments/': 'feePaymentSubmenu',
                '/fee-structure/': 'feeStructureSubmenu',
                '/expenses/': 'expenseSubmenu',
                '/other-incomes/': 'incomeSubmenu',
                '/assets/': 'assetSubmenu',
                '/employees/': 'employeeSubmenu',
                '/salaries/': 'salarySubmenu',
                '/staff-loans/': 'loanSubmenu',
                '/staff-overdrafts/': 'overdraftSubmenu',
                '/reports/': 'reportSubmenu',
                '/application/': 'applicationSubmenu',
                '/recruitment/': 'recruitmentSubmenu',
                '/certificates/': 'certificateSubmenu',
                '/notifications/': 'notificationSubmenu',
                '/grading/': 'gradingSubmenu',
                '/question-papers/': 'questionPaperSubmenu'
            };

            for (var path in menuMap) {
                if (currentUrl.indexOf(path) !== -1) {
                    var menuId = menuMap[path];
                    var menu = document.getElementById(menuId);
                    var icon = document.getElementById(menuId + 'Icon');
                    if (menu) {
                        menu.classList.add('show');
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-up');
                        }
                        localStorage.setItem(menuId + 'State', 'open');
                    }
                    break;
                }
            }
        });
    </script>

    @stack('scripts')
    @if(getSetting('theme_custom_js', ''))
        <script>{!! getSetting('theme_custom_js') !!}</script>
    @endif
</body>
</html>