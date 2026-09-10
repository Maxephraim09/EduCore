@extends('layouts.app')

@section('title', 'System Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">System Settings</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>System Settings</h5>
        </div>
        <div class="card-body">
            <!-- Settings Navigation Tabs -->
            <ul class="nav nav-tabs" role="tablist" id="settingsTabs">
                <li class="nav-item">
                    <a class="nav-link {{ !request()->has('tab') || request()->input('tab') == 'school' ? 'active' : '' }}" data-bs-toggle="tab" href="#school">
                        <i class="fas fa-school"></i> School
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'academic' ? 'active' : '' }}" data-bs-toggle="tab" href="#academic">
                        <i class="fas fa-graduation-cap"></i> Academic
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'localization' ? 'active' : '' }}" data-bs-toggle="tab" href="#localization">
                        <i class="fas fa-globe-americas"></i> Localization
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'payment' ? 'active' : '' }}" data-bs-toggle="tab" href="#payment">
                        <i class="fab fa-paystack"></i> Payment
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'admissions' ? 'active' : '' }}" data-bs-toggle="tab" href="#admissions">
                        <i class="fas fa-user-graduate"></i> Admissions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'communication' ? 'active' : '' }}" data-bs-toggle="tab" href="#communication">
                        <i class="fas fa-envelope"></i> Communication
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'social' ? 'active' : '' }}" data-bs-toggle="tab" href="#social">
                        <i class="fas fa-share-alt"></i> Social Media
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'security' ? 'active' : '' }}" data-bs-toggle="tab" href="#security">
                        <i class="fas fa-shield-alt"></i> Security
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'seo' ? 'active' : '' }}" data-bs-toggle="tab" href="#seo">
                        <i class="fas fa-search"></i> SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'theme' ? 'active' : '' }}" data-bs-toggle="tab" href="#theme">
                        <i class="fas fa-palette"></i> Theme
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'performance' ? 'active' : '' }}" data-bs-toggle="tab" href="#performance">
                        <i class="fas fa-rocket"></i> Performance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'maintenance' ? 'active' : '' }}" data-bs-toggle="tab" href="#maintenance">
                        <i class="fas fa-tools"></i> Maintenance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'api' ? 'active' : '' }}" data-bs-toggle="tab" href="#api">
                        <i class="fas fa-code"></i> API
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'logging' ? 'active' : '' }}" data-bs-toggle="tab" href="#logging">
                        <i class="fas fa-chart-bar"></i> Logging
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'cookie' ? 'active' : '' }}" data-bs-toggle="tab" href="#cookie">
                        <i class="fas fa-cookie-bite"></i> Cookie & GDPR
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'backup' ? 'active' : '' }}" data-bs-toggle="tab" href="#backup">
                        <i class="fas fa-database"></i> Backup
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->input('tab') == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general">
                        <i class="fas fa-globe"></i> General
                    </a>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content mt-3">
                <!-- School Settings -->
                <div class="tab-pane fade {{ !request()->has('tab') || request()->input('tab') == 'school' ? 'show active' : '' }}" id="school">
                    @include('settings.partials.school')
                </div>
                
                <!-- Academic Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'academic' ? 'show active' : '' }}" id="academic">
                    @include('settings.partials.academic')
                </div>
                
                <!-- Localization Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'localization' ? 'show active' : '' }}" id="localization">
                    @include('settings.partials.localization')
                </div>
                
                <!-- Payment Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'payment' ? 'show active' : '' }}" id="payment">
                    @include('settings.partials.payment')
                </div>
                <!-- Admissions Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'admissions' ? 'show active' : '' }}" id="admissions">
                    @include('settings.partials.admissions')
                </div>
                
                <!-- Communication Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'communication' ? 'show active' : '' }}" id="communication">
                    @include('settings.partials.communication')
                </div>
                
                <!-- Social Media Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'social' ? 'show active' : '' }}" id="social">
                    @include('settings.partials.social')
                </div>
                
                <!-- Security Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'security' ? 'show active' : '' }}" id="security">
                    @include('settings.partials.security')
                </div>
                
                <!-- SEO Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'seo' ? 'show active' : '' }}" id="seo">
                    @include('settings.partials.seo')
                </div>
                
                <!-- Theme Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'theme' ? 'show active' : '' }}" id="theme">
                    @include('settings.partials.theme')
                </div>
                
                <!-- Performance Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'performance' ? 'show active' : '' }}" id="performance">
                    @include('settings.partials.performance')
                </div>
                
                <!-- Maintenance Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'maintenance' ? 'show active' : '' }}" id="maintenance">
                    @include('settings.partials.maintenance')
                </div>
                
                <!-- API Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'api' ? 'show active' : '' }}" id="api">
                    @include('settings.partials.api')
                </div>
                
                <!-- Logging Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'logging' ? 'show active' : '' }}" id="logging">
                    @include('settings.partials.logging')
                </div>
                
                <!-- Cookie & GDPR Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'cookie' ? 'show active' : '' }}" id="cookie">
                    @include('settings.partials.cookie')
                </div>
                
                <!-- Backup Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'backup' ? 'show active' : '' }}" id="backup">
                    @include('settings.partials.backup')
                </div>
                
                <!-- General Settings -->
                <div class="tab-pane fade {{ request()->input('tab') == 'general' ? 'show active' : '' }}" id="general">
                    @include('settings.partials.general')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Handle tab switching from URL hash
    $(document).ready(function() {
        // Check if URL has a hash
        var hash = window.location.hash;
        if (hash) {
            // Remove the '#' and find the tab
            var tabId = hash.replace('#', '');
            // Find the tab link with matching href
            var tabLink = $('#settingsTabs a[href="#' + tabId + '"]');
            if (tabLink.length) {
                tabLink.tab('show');
            }
        }
        
        // Update URL hash when tab changes
        $('#settingsTabs a').on('shown.bs.tab', function(e) {
            var tabId = $(e.target).attr('href');
            if (history.pushState) {
                history.pushState(null, null, tabId);
            } else {
                window.location.hash = tabId;
            }
            
            // Update active state in sidebar (optional)
            var tabName = tabId.replace('#', '');
            $('.submenu .nav-link').removeClass('active');
            $('.submenu .nav-link[href$="' + tabName + '"]').addClass('active');
        });
        
        // Store active tab in localStorage
        $('#settingsTabs a').click(function() {
            localStorage.setItem('activeSettingsTab', $(this).attr('href'));
        });
        
        // Load last active tab
        var activeTab = localStorage.getItem('activeSettingsTab');
        if (activeTab) {
            $('#settingsTabs a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
@endpush