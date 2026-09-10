<?php

use App\Models\SystemSetting;
use Illuminate\Support\Str;

// ==================== SETTINGS HELPERS ====================

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        return SystemSetting::getValue($key, $default);
    }
}

if (!function_exists('setSetting')) {
    function setSetting($key, $value, $type = 'text', $description = null, $category = 'general')
    {
        return SystemSetting::setValue($key, $value, $type, $description, $category);
    }
}

if (!function_exists('getAllSettings')) {
    function getAllSettings()
    {
        return SystemSetting::getAllSettings();
    }
}

if (!function_exists('isReportCardPublished')) {
    function isReportCardPublished(int $examId, int $classId): bool
    {
        return getSetting("report_cards_published_exam_{$examId}_class_{$classId}", 'false') === 'true';
    }
}

if (!function_exists('setReportCardPublished')) {
    function setReportCardPublished(int $examId, int $classId, bool $published)
    {
        return setSetting(
            "report_cards_published_exam_{$examId}_class_{$classId}",
            $published ? 'true' : 'false',
            'boolean',
            "Report card publish status for exam {$examId} and class {$classId}"
        );
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount): string
    {
        $symbol = getSetting('currency_symbol', '₦');
        return $symbol . number_format((float) $amount, 2);
    }
}

// ==================== PERMISSION HELPERS ====================

if (!function_exists('currentUserCan')) {
    function currentUserCan(string $permission): bool
    {
        return auth()->check() && auth()->user()->hasPermission($permission);
    }
}

if (!function_exists('currentUserHasRole')) {
    function currentUserHasRole(string|array $roles): bool
    {
        return auth()->check() && auth()->user()->hasRole($roles);
    }
}

// ==================== SCHOOL SETTINGS HELPERS ====================

if (!function_exists('getSchoolName')) {
    function getSchoolName()
    {
        return getSetting('school_name', 'Financial Management System');
    }
}

if (!function_exists('getSchoolTagline')) {
    function getSchoolTagline()
    {
        return getSetting('school_tagline', 'Enterprise Edition v1.0');
    }
}

if (!function_exists('getSchoolAddress')) {
    function getSchoolAddress()
    {
        return getSetting('school_address', '');
    }
}

if (!function_exists('getSchoolPhone')) {
    function getSchoolPhone()
    {
        return getSetting('school_phone', '');
    }
}

if (!function_exists('getSchoolEmail')) {
    function getSchoolEmail()
    {
        return getSetting('school_email', '');
    }
}

if (!function_exists('getSchoolWebsite')) {
    function getSchoolWebsite()
    {
        return getSetting('school_website', '');
    }
}

if (!function_exists('getSchoolLogo')) {
    function getSchoolLogo()
    {
        return getSetting('school_logo_url', '');
    }
}

if (!function_exists('getSchoolIcon')) {
    function getSchoolIcon()
    {
        return getSetting('school_icon', '');
    }
}

// ==================== ACADEMIC SETTINGS HELPERS ====================

if (!function_exists('getAcademicYear')) {
    function getAcademicYear()
    {
        return getSetting('academic_year', date('Y') . '/' . (date('Y') + 1));
    }
}

if (!function_exists('getCurrentTerm')) {
    function getCurrentTerm()
    {
        return getSetting('term', '1st Term');
    }
}

if (!function_exists('getTermStartDate')) {
    function getTermStartDate()
    {
        return getSetting('term_start_date', '');
    }
}

if (!function_exists('getTermEndDate')) {
    function getTermEndDate()
    {
        return getSetting('term_end_date', '');
    }
}

// ==================== LOCALIZATION HELPERS ====================

if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol()
    {
        return getSetting('currency_symbol', '₦');
    }
}

if (!function_exists('getCurrencyCode')) {
    function getCurrencyCode()
    {
        return getSetting('currency', 'NGN');
    }
}

if (!function_exists('getDateFormat')) {
    function getDateFormat()
    {
        return getSetting('date_format', 'Y-m-d');
    }
}

if (!function_exists('getTimeFormat')) {
    function getTimeFormat()
    {
        return getSetting('time_format', 'h:i A');
    }
}

if (!function_exists('getTimezone')) {
    function getTimezone()
    {
        return getSetting('timezone', 'Africa/Lagos');
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = null)
    {
        if (!$date) return '';
        $format = $format ?? getDateFormat();
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

// ==================== PAYMENT HELPERS ====================

if (!function_exists('getPaystackPublicKey')) {
    function getPaystackPublicKey()
    {
        return getSetting('paystack_public_key', '');
    }
}

if (!function_exists('getPaystackSecretKey')) {
    function getPaystackSecretKey()
    {
        return getSetting('paystack_secret_key', '');
    }
}

if (!function_exists('isPaystackConfigured')) {
    function isPaystackConfigured()
    {
        return !empty(getPaystackPublicKey()) && !empty(getPaystackSecretKey());
    }
}

if (!function_exists('getBankName')) {
    function getBankName()
    {
        return getSetting('school_bank_name', '');
    }
}

if (!function_exists('getAccountNumber')) {
    function getAccountNumber()
    {
        return getSetting('school_account_number', '');
    }
}

if (!function_exists('getAccountName')) {
    function getAccountName()
    {
        return getSetting('school_account_name', '');
    }
}

// ==================== SMS HELPERS ====================

if (!function_exists('isSmsEnabled')) {
    function isSmsEnabled()
    {
        return getSetting('sms_enabled', 'true') === 'true';
    }
}

if (!function_exists('getSmsProvider')) {
    function getSmsProvider()
    {
        return getSetting('sms_provider', 'twilio');
    }
}

if (!function_exists('getSmsSenderId')) {
    function getSmsSenderId()
    {
        return getSetting('sms_sender_id', 'FMSystem');
    }
}

// ==================== THEME HELPERS ====================

if (!function_exists('getPrimaryColor')) {
    function getPrimaryColor()
    {
        return getSetting('theme_primary_color', '#667eea');
    }
}

if (!function_exists('getSecondaryColor')) {
    function getSecondaryColor()
    {
        return getSetting('theme_secondary_color', '#764ba2');
    }
}

if (!function_exists('isDarkMode')) {
    function isDarkMode()
    {
        return getSetting('theme_dark_mode', 'false') === 'true';
    }
}

if (!function_exists('getSidebarColor')) {
    function getSidebarColor()
    {
        return getSetting('theme_sidebar_color', '#1e3c72');
    }
}

if (!function_exists('getHeaderColor')) {
    function getHeaderColor()
    {
        return getSetting('theme_header_color', '#2a5298');
    }
}

if (!function_exists('getFooterText')) {
    function getFooterText()
    {
        $footerText = trim((string) getSetting('theme_footer_text', ''));
        $legacyFooterText = [
            'Financial Management System',
            '© 2024 Financial Management System. All rights reserved.',
        ];

        if ($footerText === '' || in_array($footerText, $legacyFooterText, true)) {
            return '© ' . date('Y') . ' ' . getAppName() . '. All rights reserved.';
        }

        return $footerText;
    }
}

// ==================== SECURITY HELPERS ====================

if (!function_exists('getSessionTimeout')) {
    function getSessionTimeout()
    {
        return (int) getSetting('session_timeout', 60);
    }
}

if (!function_exists('getMaxLoginAttempts')) {
    function getMaxLoginAttempts()
    {
        return (int) getSetting('max_login_attempts', 5);
    }
}

if (!function_exists('getPasswordMinLength')) {
    function getPasswordMinLength()
    {
        return (int) getSetting('password_min_length', 8);
    }
}

if (!function_exists('is2faRequired')) {
    function is2faRequired()
    {
        return getSetting('require_2fa', 'false') === 'true';
    }
}

// ==================== SEO HELPERS ====================

if (!function_exists('getSeoTitle')) {
    function getSeoTitle()
    {
        return getSetting('seo_site_title', 'Financial Management System');
    }
}

if (!function_exists('getSeoDescription')) {
    function getSeoDescription()
    {
        return getSetting('seo_site_description', 'A comprehensive financial management system for educational institutions');
    }
}

if (!function_exists('getSeoKeywords')) {
    function getSeoKeywords()
    {
        return getSetting('seo_site_keywords', 'financial management, school fees, education, accounting');
    }
}

if (!function_exists('getGoogleAnalyticsId')) {
    function getGoogleAnalyticsId()
    {
        return getSetting('seo_google_analytics', '');
    }
}

// ==================== MAINTENANCE HELPERS ====================

if (!function_exists('isMaintenanceMode')) {
    function isMaintenanceMode()
    {
        return getSetting('maintenance_mode', 'false') === 'true';
    }
}

if (!function_exists('getMaintenanceMessage')) {
    function getMaintenanceMessage()
    {
        return getSetting('maintenance_message', 'We are currently performing scheduled maintenance. Please check back later.');
    }
}

// ==================== API HELPERS ====================

if (!function_exists('isApiEnabled')) {
    function isApiEnabled()
    {
        return getSetting('api_enabled', 'true') === 'true';
    }
}

if (!function_exists('getApiRateLimit')) {
    function getApiRateLimit()
    {
        return (int) getSetting('api_rate_limit', 60);
    }
}

if (!function_exists('getApiVersion')) {
    function getApiVersion()
    {
        return getSetting('api_version', 'v1');
    }
}

// ==================== COOKIE & GDPR HELPERS ====================

if (!function_exists('isCookieConsentEnabled')) {
    function isCookieConsentEnabled()
    {
        return getSetting('cookie_consent_enabled', 'true') === 'true';
    }
}

if (!function_exists('isGdprEnabled')) {
    function isGdprEnabled()
    {
        return getSetting('gdpr_enabled', 'true') === 'true';
    }
}

// ==================== USER HELPERS ====================

if (!function_exists('getCurrentUser')) {
    function getCurrentUser()
    {
        return auth()->user();
    }
}

if (!function_exists('getCurrentEmployee')) {
    function getCurrentEmployee()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return \App\Models\Employee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();
    }
}

if (!function_exists('getCurrentEmployeeId')) {
    function getCurrentEmployeeId()
    {
        return getCurrentEmployee()?->id;
    }
}

if (!function_exists('isAuthenticated')) {
    function isAuthenticated()
    {
        return auth()->check();
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
}

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin()
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }
}

if (!function_exists('getUserRole')) {
    function getUserRole()
    {
        return auth()->check() ? auth()->user()->role : null;
    }
}

if (!function_exists('getUserRoleName')) {
    function getUserRoleName()
    {
        if (!auth()->check()) return 'Guest';
        $role = auth()->user()->role ?? 'staff';
        return ucfirst(str_replace('_', ' ', $role));
    }
}

if (!function_exists('getUserInitials')) {
    function getUserInitials()
    {
        if (!auth()->check()) return 'A';
        $name = auth()->user()->name ?? 'Administrator';
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }
}

if (!function_exists('getUserAvatar')) {
    function getUserAvatar()
    {
        return auth()->check() ? auth()->user()->avatar : null;
    }
}

if (!function_exists('getGreeting')) {
    function getGreeting()
    {
        $hour = date('H');
        if ($hour < 12) return 'Good Morning';
        if ($hour < 17) return 'Good Afternoon';
        return 'Good Evening';
    }
}

// ==================== APPLICATION HELPERS ====================

if (!function_exists('getAppName')) {
    function getAppName()
    {
        return getSetting('app_name', 'Financial Management System');
    }
}

if (!function_exists('getAppEnvironment')) {
    function getAppEnvironment()
    {
        return getSetting('app_environment', 'production');
    }
}

if (!function_exists('isDebugMode')) {
    function isDebugMode()
    {
        return getSetting('app_debug', 'false') === 'true';
    }
}

if (!function_exists('getAppUrl')) {
    function getAppUrl()
    {
        return getSetting('app_url', url('/'));
    }
}

// ==================== NOTIFICATION HELPERS ====================

if (!function_exists('getNotificationCount')) {
    function getNotificationCount()
    {
        if (!auth()->check()) return 0;
        try {
            // Check if Notification model exists
            if (!class_exists(\App\Models\Notification::class)) {
                return 0;
            }
            return \App\Models\Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('getUnreadNotifications')) {
    function getUnreadNotifications($limit = 5)
    {
        if (!auth()->check()) return collect();
        try {
            if (!class_exists(\App\Models\Notification::class)) {
                return collect();
            }
            return \App\Models\Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }
}

// ==================== GENERATION HELPERS ====================

if (!function_exists('generateReference')) {
    function generateReference($prefix = 'REF')
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }
}

if (!function_exists('generateReceiptNumber')) {
    function generateReceiptNumber()
    {
        return 'RCP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateTransactionId')) {
    function generateTransactionId()
    {
        return 'TXN-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
    }
}

// ==================== TEXT HELPERS ====================

if (!function_exists('truncateText')) {
    function truncateText($text, $limit = 100, $end = '...')
    {
        if (strlen($text) <= $limit) return $text;
        return substr($text, 0, $limit) . $end;
    }
}

// ==================== ROUTE HELPERS ====================

if (!function_exists('isActiveRoute')) {
    function isActiveRoute($routes, $class = 'active')
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (request()->routeIs($route)) return $class;
            }
            return '';
        }
        return request()->routeIs($routes) ? $class : '';
    }
}

if (!function_exists('isActiveMenu')) {
    function isActiveMenu($routes, $class = 'active')
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (request()->routeIs($route . '.*') || request()->routeIs($route)) {
                    return $class;
                }
            }
            return '';
        }
        return request()->routeIs($routes . '.*') || request()->routeIs($routes) ? $class : '';
    }
}

// ==================== DATA HELPERS ====================

if (!function_exists('getClassList')) {
    function getClassList()
    {
        return \App\Models\ClassModel::where('is_active', true)
            ->orderBy('name')
            ->orderBy('section')
            ->get();
    }
}

if (!function_exists('getSubjectList')) {
    function getSubjectList()
    {
        return \App\Models\Subject::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}

if (!function_exists('getStudentCount')) {
    function getStudentCount()
    {
        return \App\Models\Student::where('is_active', true)->count();
    }
}

if (!function_exists('getEmployeeCount')) {
    function getEmployeeCount()
    {
        return \App\Models\Employee::where('is_active', true)->count();
    }
}