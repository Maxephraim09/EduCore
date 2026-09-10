<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $portalAppName = getAppName();
        $portalTitle = getSeoTitle();
        $portalDescription = getSeoDescription();
        $portalKeywords = getSeoKeywords();
        $portalIcon = getSchoolIcon();
        $portalLogo = getSchoolLogo();
        $portalOgImage = getSetting('seo_og_image', '') ?: $portalLogo;
        $portalCanonical = getSetting('seo_canonical_url', getAppUrl());
        $portalApplicant = Auth::guard('applicant')->user();
        $portalApplication = $portalApplicant
            ? \App\Models\Application::where('email', $portalApplicant->email)->latest()->first()
            : null;
        $portalHasAdmission = $portalApplication && $portalApplication->isAdmitted();
    @endphp
    <title>@yield('title', $portalTitle . ' - ' . $portalAppName)</title>
    <meta name="description" content="{{ $portalDescription }}">
    <meta name="keywords" content="{{ $portalKeywords }}">
    <meta name="author" content="{{ getSetting('seo_author', getSchoolName()) }}">
    <meta name="robots" content="{{ getSetting('seo_meta_robots', 'index, follow') }}">
    <link rel="canonical" href="{{ $portalCanonical }}">
    @if($portalOgImage)
        <meta property="og:image" content="{{ asset($portalOgImage) }}">
    @endif
    @if($portalIcon)
        <link rel="icon" href="{{ asset($portalIcon) }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; color: #1f2937; }
        .portal-dashboard { min-height: 100vh; display: grid !important; grid-template-columns: 280px minmax(0, 1fr); }
        .portal-sidebar { background: #172554 !important; color: #eef2ff !important; padding: 28px 1rem; display: flex !important; flex-direction: column; min-height: 100vh; visibility: visible !important; opacity: 1 !important; position: relative; z-index: 10; }
        .portal-sidebar .brand { display: flex; align-items: center; gap: 0.85rem; margin-bottom: 2rem; }
        .portal-sidebar .brand img { width: 42px; height: 42px; object-fit: contain; border-radius: 12px; }
        .portal-sidebar .brand-title { font-size: 1.15rem; font-weight: 700; line-height: 1.1; }
        .portal-sidebar .brand-subtitle { font-size: 0.85rem; color: rgba(238,242,255,0.72); }
        .portal-sidebar nav { flex: 1; margin-top: 1.5rem; display: block !important; visibility: visible !important; }
        .portal-sidebar a { display: block !important; padding: 12px 16px; margin-bottom: 8px; border-radius: 14px; color: #eef2ff !important; font-weight: 500; transition: all 0.2s ease; text-decoration: none; }
        .portal-sidebar a:hover, .portal-sidebar a.active { background: rgba(255,255,255,0.12); text-decoration: none; }
        .portal-sidebar .sidebar-footer { margin-top: auto; padding-top: 12px; font-size: 0.95rem; color: rgba(238,242,255,0.75); }
        .portal-sidebar-toggle { display: none; border: 0; background: transparent; color: #334155; font-size: 1.2rem; }
        .portal-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 20px clamp(18px, 4vw, 42px); display: flex; justify-content: space-between; align-items: center; gap: 20px; }
        .portal-header h1 { font-size: 1.55rem; margin: 0; }
        .portal-header .header-actions { display: flex; gap: 12px; align-items: center; }
        .portal-header .btn-logout { border-radius: 12px; }
        .portal-content { padding: clamp(18px, 4vw, 42px); min-height: calc(100vh - 92px); }
        .portal-main { min-width: 0; display: flex; flex-direction: column; min-height: 100vh; }
        .portal-main > .portal-content { flex: 1; width: 100%; }
        .portal-footer { background: #ffffff; border-top: 1px solid #e2e8f0; padding: 16px clamp(18px, 4vw, 42px); color: #64748b; font-size: 0.82rem; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .portal-card { background: #ffffff; border-radius: 24px; padding: 28px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08); }
        .badge-status { font-size: 0.8rem; padding: 8px 12px; border-radius: 999px; }
        .status-pending { background: #fde68a; color: #92400e; }
        .status-admitted { background: #d1fae5; color: #166534; }
        @media (max-width: 992px) {
            .portal-dashboard { grid-template-columns: 1fr; }
            .portal-sidebar { flex-direction: row; flex-wrap: wrap; gap: 12px; padding: 20px; position: sticky; top: 0; z-index: 1000; }
            .portal-sidebar nav { width: 100%; display: flex !important; flex-wrap: wrap; gap: 0.5rem; margin-top: 0; }
            .portal-sidebar a { margin-bottom: 0; flex: 1 1 calc(50% - 4px); }
            .portal-header { flex-direction: column; align-items: stretch; gap: 12px; }
            .portal-header .header-actions { justify-content: space-between; }
            .portal-sidebar .brand { margin-bottom: 0; }
            .portal-sidebar-toggle { display: inline-block; }
            .portal-sidebar.portal-sidebar-collapsed nav, .portal-sidebar.portal-sidebar-collapsed .sidebar-footer { display: none; }
            .portal-sidebar.portal-sidebar-collapsed { padding-bottom: 12px; }
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="portal-dashboard">
        <aside class="portal-sidebar">
            <div class="brand">
                @php $schoolLogo = getSchoolLogo(); @endphp
                @if($schoolLogo)
                    <img src="{{ asset($schoolLogo) }}" alt="{{ getSchoolName() }}">
                @else
                    <span class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:42px;height:42px;font-size:1.1rem;">🏫</span>
                @endif
                <div>
                    <div class="brand-title">{{ getSchoolName() }}</div>
                    <div class="brand-subtitle">Applicant Portal</div>
                </div>
            </div>
            <button class="portal-sidebar-toggle" type="button" aria-label="Toggle portal navigation" aria-expanded="true" onclick="document.querySelector('.portal-sidebar').classList.toggle('portal-sidebar-collapsed'); this.setAttribute('aria-expanded', document.querySelector('.portal-sidebar').classList.contains('portal-sidebar-collapsed') ? 'false' : 'true');">
                <i class="fas fa-bars"></i>
            </button>

            <nav aria-label="Applicant portal navigation">
                <a href="{{ route('application.portal.dashboard') }}" class="{{ request()->routeIs('application.portal.dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('application.portal.dashboard') ? 'page' : 'false' }}"><i class="fas fa-home me-2"></i>Dashboard</a>
                <a href="{{ route('application.portal.profile') }}" class="{{ request()->routeIs('application.portal.profile') ? 'active' : '' }}"><i class="fas fa-user me-2"></i>Profile</a>
                <a href="{{ route('application.portal.application') }}" class="{{ request()->routeIs('application.portal.application') ? 'active' : '' }}"><i class="fas fa-file-alt me-2"></i>Application</a>
                @if($portalHasAdmission)
                    <a href="{{ route('application.portal.admission') }}" class="{{ request()->routeIs('application.portal.admission') ? 'active' : '' }}"><i class="fas fa-graduation-cap me-2"></i>Admission</a>
                    <a href="{{ route('application.portal.registration') }}" class="{{ request()->routeIs('application.portal.registration') ? 'active' : '' }}"><i class="fas fa-clipboard-check me-2"></i>Registration</a>
                @endif
                <a href="{{ route('application.portal.payment-history') }}" class="{{ request()->routeIs('application.portal.payment-history') ? 'active' : '' }}"><i class="fas fa-credit-card me-2"></i>Payment History</a>
                <a href="{{ route('application.portal.enquiry') }}" class="{{ request()->routeIs('application.portal.enquiry') ? 'active' : '' }}"><i class="fas fa-question-circle me-2"></i>Enquiry</a>
            </nav>

            <div class="sidebar-footer">
                Logged in as <strong>{{ optional(Auth::guard('applicant')->user())->name ?? 'Applicant' }}</strong>
            </div>
        </aside>
        <div class="portal-main">
            <header class="portal-header">
                <div>
                    <h1>@yield('page_title', 'Applicant Dashboard')</h1>
                    <p class="text-muted mb-0">{{ getSchoolTagline() }}</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('application.portal.index') }}" class="btn btn-outline-secondary">Portal Home</a>
                    <form method="POST" action="{{ route('application.portal.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-logout">Logout</button>
                    </form>
                </div>
            </header>
            <main class="portal-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                @endif
                @yield('content')
            </main>
            <footer class="portal-footer">
                <span>&copy; {{ date('Y') }} {{ getSchoolName() }}</span>
                <span>Applicant Portal</span>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
