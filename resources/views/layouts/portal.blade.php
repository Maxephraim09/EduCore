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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            color: #1f2937;
        }
        .portal-layout {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .portal-header {
            padding: 22px 0;
        }
        .portal-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }
        .portal-brand img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }
        .portal-brand-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1d4ed8;
        }
        .portal-brand-subtitle {
            color: #475569;
            font-size: 0.95rem;
            margin-top: 4px;
        }
        .portal-messages {
            padding: 0 0 20px;
        }
        .portal-footer {
            padding: 22px 0;
            text-align: center;
            color: #64748b;
            font-size: 0.95rem;
        }
        a {
            text-decoration: none;
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="portal-layout">
        <header class="container portal-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <a href="{{ route('landing') }}" class="portal-brand">
                    @php $schoolLogo = getSchoolLogo(); @endphp
                    @if($schoolLogo)
                        <img src="{{ asset($schoolLogo) }}" alt="{{ getSchoolName() }} Logo">
                    @else
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:48px;height:48px;font-size:1.15rem;">🏫</span>
                    @endif
                    <div>
                        <div class="portal-brand-title">{{ getSchoolName() }}</div>
                        <div class="portal-brand-subtitle">{{ getSchoolTagline() }}</div>
                    </div>
                </a>
                <div>
                    <a href="{{ route('landing') }}" class="btn btn-outline-secondary btn-sm">Back to Home</a>
                </div>
            </div>
        </header>

        <div class="container portal-messages">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <main class="container flex-grow-1">
            @yield('content')
        </main>

        <footer class="portal-footer">
            {!! getFooterText() ?: '&copy; ' . date('Y') . ' ' . getSchoolName() . '. All rights reserved.' !!}
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
