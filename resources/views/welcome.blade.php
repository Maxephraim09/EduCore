<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Get all settings from database
        $schoolName = getSchoolName();
        $schoolTagline = getSchoolTagline();
        $schoolAddress = getSchoolAddress();
        $schoolPhone = getSchoolPhone();
        $schoolEmail = getSchoolEmail();
        $schoolWebsite = getSchoolWebsite();
        $schoolLogo = getSchoolLogo();
        $schoolIcon = getSchoolIcon();
        
        // Social Media
        $facebook = getSetting('school_social_facebook', '#');
        $twitter = getSetting('school_social_twitter', '#');
        $instagram = getSetting('school_social_instagram', '#');
        $linkedin = getSetting('school_social_linkedin', '#');
        $youtube = getSetting('school_social_youtube', '#');
        $whatsapp = getSetting('school_social_whatsapp', '#');
        
        // Theme Colors
        $primaryColor = getPrimaryColor();
        $secondaryColor = getSecondaryColor();
        $themeFooterText = getFooterText();
        
        // SEO Settings
        $seoTitle = getSeoTitle();
        $seoDescription = getSeoDescription();
        $seoKeywords = getSeoKeywords();
        $seoOgImage = getSetting('seo_og_image', '');
        $googleAnalytics = getGoogleAnalyticsId();
        
        // Academic Year
        $academicYear = \App\Models\SystemSetting::getValue('academic_year', date('Y') . '/' . (date('Y') + 1));
        $currentTerm = \App\Models\SystemSetting::getValue('term', 'First Term');
    @endphp

    <title>{{ $seoTitle }}</title>

    <!-- Meta Tags -->
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="author" content="{{ $schoolName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    @if($seoOgImage)
        <meta property="og:image" content="{{ asset($seoOgImage) }}">
    @endif
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

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
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏫</text></svg>">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|playfair-display:700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Landing Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/landingpage.css') }}">
</head>

<body>
    <!-- ========================================
    TOP BAR
    ======================================== -->
    <div class="top-bar">
        <div class="container">
            <div class="contact-info">
                <span><i class="fas fa-phone"></i> <a href="tel:{{ $schoolPhone }}">{{ $schoolPhone }}</a></span>
                <span><i class="fas fa-envelope"></i> <a href="mailto:{{ $schoolEmail }}">{{ $schoolEmail }}</a></span>
                <span><i class="fas fa-map-marker-alt"></i> {{ $schoolAddress }}</span>
            </div>
            <div class="social-links">
                @if($facebook && $facebook != '#')
                    <a href="{{ $facebook }}" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if($twitter && $twitter != '#')
                    <a href="{{ $twitter }}" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                @endif
                @if($instagram && $instagram != '#')
                    <a href="{{ $instagram }}" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                @endif
                @if($linkedin && $linkedin != '#')
                    <a href="{{ $linkedin }}" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                @endif
                @if($youtube && $youtube != '#')
                    <a href="{{ $youtube }}" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                @endif
                @if($whatsapp && $whatsapp != '#')
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================
    NAVIGATION
    ======================================== -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="/" class="logo">
                <div class="logo-icon">
                    @if($schoolLogo)
                        <img src="{{ asset($schoolLogo) }}" alt="{{ $schoolName }}">
                    @else
                        🏫
                    @endif
                </div>
                <div class="logo-text">
                    {{ $schoolName }}
                    <small>{{ $schoolTagline }}</small>
                </div>
            </a>

            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                ☰
            </button>

            <div class="nav-links" id="navLinks">
                <a href="#about">About</a>
                <a href="#programs">Programs</a>
                <a href="#teachers">Teachers</a>
                <a href="#calendar">Calendar</a>
                <a href="#blog">Blog</a>
                <a href="#gallery">Gallery</a>
                <a href="#contact">Contact</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-outline">Log In</a>
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn-primary">Get Started</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- ========================================
    HERO SECTION - FIXED
    ======================================== -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <span class="badge">
                    <i class="fas fa-graduation-cap"></i> {{ $academicYear }}
                </span>
                <h1>
                    Welcome to <br>
                    <span class="highlight">{{ $schoolName }}</span>
                </h1>
                <p>
                    {{ $schoolTagline }} We are committed to nurturing young minds and building future leaders through quality education and holistic development.
                </p>

                <div class="hero-buttons">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                            <i class="fas fa-arrow-right"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('application.portal.index') }}" class="btn-hero-primary">
                            <i class="fas fa-user-plus"></i> Enroll Now
                        </a>
                        <a href="{{ route('login') }}" class="btn-hero-secondary">
                            <i class="fas fa-sign-in-alt"></i> Staff / Student Login
                        </a>
                    @endauth
                </div>

                <div class="mt-4 p-3 bg-white shadow-sm rounded">
                    <h5><i class="fas fa-file-download me-2"></i>Download Student Report Card</h5>
                    <p class="text-muted mb-3">Use your registration number and school-issued PIN.</p>
                    <a href="{{ route('result-checker.form') }}" class="btn btn-primary"><i class="fas fa-search me-2"></i>Check Result</a>
                </div>

                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-number"><i class="fas fa-users"></i> 500+</div>
                        <div class="stat-label">Students</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><i class="fas fa-star"></i> 98%</div>
                        <div class="stat-label">Pass Rate</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><i class="fas fa-chalkboard-teacher"></i> 40+</div>
                        <div class="stat-label">Expert Teachers</div>
                    </div>
                </div>
            </div>

            <div class="hero-image">
                <div class="image-wrapper">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23f3f4f6'/%3E%3Crect x='50' y='40' width='300' height='220' rx='12' fill='%23e5e7eb'/%3E%3Crect x='80' y='70' width='240' height='30' rx='6' fill='%23667eea'/%3E%3Crect x='80' y='115' width='180' height='16' rx='4' fill='%239ca3af'/%3E%3Crect x='80' y='140' width='200' height='16' rx='4' fill='%239ca3af'/%3E%3Crect x='80' y='165' width='160' height='16' rx='4' fill='%239ca3af'/%3E%3Crect x='80' y='200' width='240' height='12' rx='4' fill='%23667eea' opacity='0.3'/%3E%3Crect x='80' y='220' width='200' height='12' rx='4' fill='%23667eea' opacity='0.2'/%3E%3Ccircle cx='340' cy='80' r='20' fill='%234f46e5' opacity='0.15'/%3E%3Ccircle cx='340' cy='80' r='12' fill='%23667eea' opacity='0.3'/%3E%3Ccircle cx='340' cy='80' r='6' fill='%23667eea'/%3E%3Crect x='300' y='200' width='60' height='40' rx='8' fill='%233fb950' opacity='0.15'/%3E%3Crect x='310' y='210' width='40' height='20' rx='4' fill='%233fb950'/%3E%3C/svg%3E" 
                         alt="{{ $schoolName }}" 
                         style="width: 100%; height: auto; border-radius: 16px;">
                </div>
                <div class="floating-badge top-right">
                    <div class="badge-icon green"><i class="fas fa-check"></i></div>
                    <div>
                        <div class="badge-text">98% Uptime</div>
                        <div class="badge-sub">Reliable platform</div>
                    </div>
                </div>
                <div class="floating-badge bottom-left">
                    <div class="badge-icon blue"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <div class="badge-text">Real-time Reports</div>
                        <div class="badge-sub">Instant insights</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    MISSION, VISION, VALUES
    ======================================== -->
    <section class="mission-vision section-padding">
        <div class="container">
            <div class="section-header">
                <span class="subtitle"><i class="fas fa-bullseye"></i> Our Core</span>
                <h2>Mission, Vision &amp; <span>Values</span></h2>
                <p>Guiding principles that shape our educational approach</p>
            </div>

            <div class="mv-grid">
                <div class="mv-card">
                    <div class="mv-icon"><i class="fas fa-rocket"></i></div>
                    <h4>Our Mission</h4>
                    <p>To provide holistic education that develops competent, confident, and compassionate leaders who will make a positive impact on society.</p>
                </div>
                <div class="mv-card">
                    <div class="mv-icon"><i class="fas fa-eye"></i></div>
                    <h4>Our Vision</h4>
                    <p>To be a world-class educational institution that produces globally competitive graduates who are leaders in their chosen fields.</p>
                </div>
                <div class="mv-card">
                    <div class="mv-icon"><i class="fas fa-heart"></i></div>
                    <h4>Our Values</h4>
                    <p>Excellence, Integrity, Innovation, Respect, and Service – the core principles that guide everything we do.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    ABOUT SECTION
    ======================================== -->
    <section class="about section-padding" id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-image">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 400'%3E%3Crect width='500' height='400' fill='%23f3f4f6'/%3E%3Crect x='50' y='50' width='400' height='300' rx='16' fill='%23e5e7eb'/%3E%3Ccircle cx='250' cy='150' r='80' fill='%23667eea' opacity='0.15'/%3E%3Ccircle cx='250' cy='150' r='50' fill='%23667eea' opacity='0.3'/%3E%3Ccircle cx='250' cy='150' r='30' fill='%23667eea'/%3E%3Crect x='150' y='280' width='200' height='20' rx='10' fill='%23667eea' opacity='0.3'/%3E%3Crect x='180' y='310' width='140' height='20' rx='10' fill='%23667eea' opacity='0.2'/%3E%3C/svg%3E" 
                         alt="About {{ $schoolName }}" 
                         style="width: 100%; height: auto; display: block;">
                </div>
                <div class="about-content">
                    <span class="subtitle" style="display: inline-block; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: var(--primary); background: var(--primary-light); padding: 4px 16px; border-radius: 50px; margin-bottom: 12px;">
                        <i class="fas fa-info-circle"></i> About Us
                    </span>
                    <h2>About <span>{{ $schoolName }}</span></h2>
                    <p>
                        {{ $schoolName }} is a premier educational institution dedicated to providing world-class education and nurturing the next generation of leaders. Our mission is to foster academic excellence, character development, and lifelong learning.
                    </p>
                    <p>
                        With a team of highly qualified educators and a modern curriculum, we prepare our students for success in an ever-changing global landscape.
                    </p>
                    <div class="about-features">
                        <div class="about-feature">
                            <span class="check">✓</span> Academic Excellence
                        </div>
                        <div class="about-feature">
                            <span class="check">✓</span> Character Development
                        </div>
                        <div class="about-feature">
                            <span class="check">✓</span> Modern Facilities
                        </div>
                        <div class="about-feature">
                            <span class="check">✓</span> Qualified Teachers
                        </div>
                        <div class="about-feature">
                            <span class="check">✓</span> Holistic Education
                        </div>
                        <div class="about-feature">
                            <span class="check">✓</span> Technology Integration
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    PROGRAMS SECTION
    ======================================== -->
    <section class="programs section-padding" id="programs">
        <div class="container">
            <div class="section-header">
                <span class="subtitle"><i class="fas fa-graduation-cap"></i> Our Programs</span>
                <h2>Academic <span>Programs</span></h2>
                <p>Comprehensive educational programs designed to develop well-rounded individuals</p>
            </div>

            <div class="programs-grid">
                <div class="program-card">
                    <div class="program-icon purple"><i class="fas fa-university"></i></div>
                    <h4>Primary Education</h4>
                    <p>Strong foundation in core subjects with emphasis on literacy, numeracy, and character building.</p>
                </div>

                <div class="program-card">
                    <div class="program-icon green"><i class="fas fa-flask"></i></div>
                    <h4>Secondary Education</h4>
                    <p>Comprehensive curriculum covering sciences, arts, and vocational studies for holistic development.</p>
                </div>

                <div class="program-card">
                    <div class="program-icon orange"><i class="fas fa-laptop-code"></i></div>
                    <h4>STEM Program</h4>
                    <p>Cutting-edge Science, Technology, Engineering, and Mathematics education for future innovators.</p>
                </div>

                <div class="program-card">
                    <div class="program-icon blue"><i class="fas fa-palette"></i></div>
                    <h4>Creative Arts</h4>
                    <p>Music, drama, visual arts, and creative expression programs that nurture talent and creativity.</p>
                </div>

                <div class="program-card">
                    <div class="program-icon pink"><i class="fas fa-futbol"></i></div>
                    <h4>Sports & Athletics</h4>
                    <p>Comprehensive sports programs promoting physical fitness, teamwork, and sportsmanship.</p>
                </div>

                <div class="program-card">
                    <div class="program-icon indigo"><i class="fas fa-globe-americas"></i></div>
                    <h4>Leadership & Life Skills</h4>
                    <p>Leadership development, entrepreneurship, and life skills programs for future success.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    OUR TEACHERS
    ======================================== -->
    <section class="teachers section-padding" id="teachers">
        <div class="container">
            <div class="section-header">
                <span class="subtitle"><i class="fas fa-chalkboard-teacher"></i> Our Team</span>
                <h2>Meet Our <span>Teachers</span></h2>
                <p>Dedicated educators committed to student success and excellence</p>
            </div>

            <div class="teachers-grid">
                <div class="teacher-card">
                    <div class="teacher-avatar">JD</div>
                    <h4>Dr. John Doe</h4>
                    <div class="teacher-role">Head of Science</div>
                    <p>Ph.D. in Physics with 15 years of experience in education.</p>
                </div>
                <div class="teacher-card">
                    <div class="teacher-avatar">JS</div>
                    <h4>Mrs. Jane Smith</h4>
                    <div class="teacher-role">Senior English Teacher</div>
                    <p>M.A. in English Literature, passionate about language and literature.</p>
                </div>
                <div class="teacher-card">
                    <div class="teacher-avatar">MK</div>
                    <h4>Mr. Michael Kalu</h4>
                    <div class="teacher-role">Mathematics Specialist</div>
                    <p>B.Sc. in Mathematics with expertise in innovative teaching methods.</p>
                </div>
                <div class="teacher-card">
                    <div class="teacher-avatar">SO</div>
                    <h4>Mrs. Sarah Okafor</h4>
                    <div class="teacher-role">Head of Arts</div>
                    <p>M.F.A. in Fine Arts, dedicated to nurturing creative expression.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    WHY CHOOSE US
    ======================================== -->
    <section class="why-choose">
        <div class="container">
            <div class="section-header">
                <span class="subtitle" style="color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.15);">Why Choose Us</span>
                <h2>Why Choose <span>Us</span></h2>
                <p>What makes {{ $schoolName }} the preferred choice for quality education</p>
            </div>

            <div class="why-grid">
                <div class="why-card">
                    <div class="why-icon"><i class="fas fa-user-graduate"></i></div>
                    <h4>Expert Teachers</h4>
                    <p>Highly qualified and experienced educators dedicated to student success</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><i class="fas fa-school"></i></div>
                    <h4>Modern Facilities</h4>
                    <p>State-of-the-art classrooms, laboratories, and learning environments</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><i class="fas fa-book-open"></i></div>
                    <h4>Rich Curriculum</h4>
                    <p>Comprehensive and up-to-date curriculum that meets international standards</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><i class="fas fa-handshake"></i></div>
                    <h4>Community</h4>
                    <p>Supportive and inclusive community that fosters growth and collaboration</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    ACADEMIC CALENDAR
    ======================================== -->
    <section class="calendar-section section-padding" id="calendar">
        <div class="container">
            <div class="section-header">
                <span class="subtitle"><i class="fas fa-calendar-alt"></i> Calendar</span>
                <h2>Academic <span>Calendar</span></h2>
                <p>Stay informed about important academic events and dates</p>
            </div>

            <div class="calendar-grid">
                <div class="calendar-item">
                    <div class="cal-icon"><i class="fas fa-school"></i></div>
                    <div class="cal-date">September 2024</div>
                    <div class="cal-event">School Resumes</div>
                </div>
                <div class="calendar-item">
                    <div class="cal-icon"><i class="fas fa-pencil-alt"></i></div>
                    <div class="cal-date">October 2024</div>
                    <div class="cal-event">First CA Tests</div>
                </div>
                <div class="calendar-item">
                    <div class="cal-icon"><i class="fas fa-book-open"></i></div>
                    <div class="cal-date">December 2024</div>
                    <div class="cal-event">End of Term Exams</div>
                </div>
                <div class="calendar-item">
                    <div class="cal-icon"><i class="fas fa-trophy"></i></div>
                    <div class="cal-date">March 2025</div>
                    <div class="cal-event">Inter-House Sports</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    BLOG & NEWS
    ======================================== -->
    <section class="blog-section section-padding" id="blog">
        <div class="container">
            <div class="section-header">
                <span class="subtitle"><i class="fas fa-newspaper"></i> Blog & News</span>
                <h2>Latest <span>News</span></h2>
                <p>Stay updated with the latest happenings at {{ $schoolName }}</p>
            </div>

            <div class="blog-grid">
                @forelse($latestBlogPosts as $post)
                    <article class="blog-card">
                        <div class="blog-image">@if($post->featured_image)<img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" style="width:100%;height:100%;object-fit:cover">@else<i class="fas fa-newspaper"></i>@endif</div>
                        <div class="blog-content">
                            <div class="blog-date"><i class="far fa-calendar-alt"></i> {{ $post->approved_at?->format('M d, Y') }}</div>
                            <h4>{{ $post->title }}</h4>
                            <p>{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 140) }}</p>
                            <a href="{{ route('blog.public.show', $post) }}" class="blog-read-more">Read More &rarr;</a>
                        </div>
                    </article>
                @empty
                    <p class="text-muted">No blog stories have been published yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ========================================
    GALLERY SECTION
    ======================================== -->
    <section class="gallery-section section-padding" id="gallery">
        <div class="container">
            <div class="section-header">
                <span class="subtitle"><i class="fas fa-images"></i> Gallery</span>
                <h2>Campus <span>Life</span></h2>
                <p>Experience the vibrant atmosphere and learning environment at {{ $schoolName }}</p>
            </div>

            <div class="gallery-grid">
                <div class="gallery-item" style="background: #e0e7ff;">
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px; color: #6366f1;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="overlay">Modern Classrooms</div>
                </div>
                <div class="gallery-item" style="background: #d1fae5;">
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px; color: #059669;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="overlay">Well-stocked Library</div>
                </div>
                <div class="gallery-item" style="background: #fef3c7;">
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px; color: #d97706;">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <div class="overlay">Sports Facilities</div>
                </div>
                <div class="gallery-item" style="background: #fce7f3;">
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px; color: #db2777;">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <div class="overlay">Science Labs</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    TESTIMONIALS
    ======================================== -->
    <section class="testimonials-section section-padding" id="testimonials">
        <div class="container">
            <div class="section-header">
                <span class="subtitle"><i class="fas fa-quote-left"></i> Testimonials</span>
                <h2>What <span>Parents Say</span></h2>
                <p>Hear from parents and students about their experience at {{ $schoolName }}</p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <blockquote>"My child has flourished since joining {{ $schoolName }}. The dedicated teachers and supportive environment have made all the difference."</blockquote>
                    <div class="author">
                        <div class="avatar">JO</div>
                        <div class="info">
                            <div class="name">Mrs. Jennifer Okafor</div>
                            <div class="role">Parent</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <blockquote>"The academic standards and character development at {{ $schoolName }} are exceptional. My children are thriving both academically and personally."</blockquote>
                    <div class="author">
                        <div class="avatar">MO</div>
                        <div class="info">
                            <div class="name">Mr. Michael Ogunleye</div>
                            <div class="role">Parent</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <blockquote>"I love the opportunities {{ $schoolName }} provides for students to excel. The teachers really care about each student's success."</blockquote>
                    <div class="author">
                        <div class="avatar">SO</div>
                        <div class="info">
                            <div class="name">Mrs. Sarah Ogunyemi</div>
                            <div class="role">Parent</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    ACTIONS
    ======================================== -->
    <section class="section-padding" id="contact">
        <div class="container">
            <div class="cta-section">
                <div class="cta-content">
                    <h3>Ready to Join {{ $schoolName }}?</h3>
                    <p>Enroll your child today and give them the gift of quality education.</p>
                </div>
                <div class="cta-actions" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center">
                    <a href="{{ route('application.portal.index') }}" class="btn-cta">
                        <i class="fas fa-user-plus"></i> Start Application
                    </a>
                    <a href="{{ app('router')->has('recruitment.jobs.index') ? route('recruitment.jobs.index') : route('login') }}" class="btn-cta">
                        <i class="fas fa-chalkboard-teacher"></i> Teacher Recruitment
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================
    FOOTER
    ======================================== -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">
                        <div class="logo-icon" style="width: 40px; height: 40px; font-size: 20px;">
                            @if($schoolLogo)
                                <img src="{{ asset($schoolLogo) }}" alt="{{ $schoolName }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                            @else
                                🏫
                            @endif
                        </div>
                        <div class="logo-text" style="font-size: 18px;">
                            {{ $schoolName }}
                            <small style="color: rgba(255,255,255,0.6);">{{ $schoolTagline }}</small>
                        </div>
                    </div>
                    <p style="margin-top: 16px;"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> {{ $schoolAddress }}</p>
                    <p><i class="fas fa-phone" style="margin-right: 8px;"></i> {{ $schoolPhone }}</p>
                    <p><i class="fas fa-envelope" style="margin-right: 8px;"></i> <a href="mailto:{{ $schoolEmail }}" style="color: rgba(255,255,255,0.7);">{{ $schoolEmail }}</a></p>
                </div>

                <div>
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#programs">Programs</a></li>
                        <li><a href="#teachers">Teachers</a></li>
                        <li><a href="#calendar">Calendar</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#gallery">Gallery</a></li>
                        <li><a href="#testimonials">Testimonials</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h5>For Students</h5>
                    <ul>
                        <li><a href="{{ route('login') }}">Student Portal</a></li>
                        <li><a href="{{ route('login') }}">Enrollment</a></li>
                        <li><a href="#">Academic Calendar</a></li>
                        <li><a href="#">Exam Schedule</a></li>
                    </ul>
                </div>

                <div>
                    <h5>For Staff</h5>
                    <ul>
                        <li><a href="{{ route('login') }}">Staff Portal</a></li>
                        <li><a href="#">Teacher Resources</a></li>
                        <li><a href="#">HR Portal</a></li>
                        <li><a href="#">Recruitment</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>{{ $themeFooterText }}</p>
                <div class="footer-social">
                    @if($facebook && $facebook != '#')
                        <a href="{{ $facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if($twitter && $twitter != '#')
                        <a href="{{ $twitter }}" target="_blank"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if($instagram && $instagram != '#')
                        <a href="{{ $instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if($linkedin && $linkedin != '#')
                        <a href="{{ $linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    @endif
                    @if($youtube && $youtube != '#')
                        <a href="{{ $youtube }}" target="_blank"><i class="fab fa-youtube"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <!-- ========================================
    SCRIPTS
    ======================================== -->
    <script>
        // ===== Mobile Menu Toggle =====
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const navLinks = document.getElementById('navLinks');

        mobileToggle.addEventListener('click', function() {
            navLinks.classList.toggle('open');
            this.textContent = navLinks.classList.contains('open') ? '✕' : '☰';
        });

        // Close mobile menu on link click
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('open');
                mobileToggle.textContent = '☰';
            });
        });

        // ===== Smooth Scroll =====
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // ===== Animate Elements on Scroll =====
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.program-card').forEach(card => {
            observer.observe(card);
        });

        // ===== Navbar Background on Scroll =====
        const navbar = document.getElementById('navbar');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // ===== Show features immediately =====
        setTimeout(() => {
            document.querySelectorAll('.program-card').forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('visible');
                }, 100 * index);
            });
        }, 300);
    </script>
</body>
</html>