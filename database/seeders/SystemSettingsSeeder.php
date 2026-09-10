<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            ['key' => 'school_name', 'value' => 'Excellence International School', 'type' => 'text', 'description' => 'School name'],
            ['key' => 'school_tagline', 'value' => 'Excellence in Education', 'type' => 'text', 'description' => 'School tagline'],
            ['key' => 'school_address', 'value' => '', 'type' => 'textarea', 'description' => 'School address'],
            ['key' => 'school_phone', 'value' => '', 'type' => 'text', 'description' => 'School phone'],
            ['key' => 'school_email', 'value' => '', 'type' => 'text', 'description' => 'School email'],
            ['key' => 'school_website', 'value' => '', 'type' => 'text', 'description' => 'School website'],
            ['key' => 'currency', 'value' => 'NGN', 'type' => 'text', 'description' => 'Currency code'],
            ['key' => 'currency_symbol', 'value' => '₦', 'type' => 'text', 'description' => 'Currency symbol'],
            ['key' => 'timezone', 'value' => 'Africa/Lagos', 'type' => 'text', 'description' => 'Timezone'],
            ['key' => 'app_name', 'value' => 'Financial Management System', 'type' => 'text', 'description' => 'Application name'],
        ];

        foreach ($defaults as $setting) {
            SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                ]
            );
        }
    }

    /**
     * Display all settings grouped by category
     */
    public function index()
    {
        // School Settings
        $schoolName = SystemSetting::getValue('school_name', 'Excellence International School');
        $schoolTagline = SystemSetting::getValue('school_tagline', 'Excellence in Education');
        $schoolAddress = SystemSetting::getValue('school_address', '');
        $schoolPhone = SystemSetting::getValue('school_phone', '');
        $schoolEmail = SystemSetting::getValue('school_email', '');
        $schoolWebsite = SystemSetting::getValue('school_website', '');
        $schoolLogo = SystemSetting::getValue('school_logo_url', '');
        $schoolIcon = SystemSetting::getValue('school_icon', '');
        
        // Academic Settings
        $academicYear = SystemSetting::getValue('academic_year', date('Y') . '/' . (date('Y') + 1));
        $term = SystemSetting::getValue('term', '1st Term');
        $termStartDate = SystemSetting::getValue('term_start_date', '');
        $termEndDate = SystemSetting::getValue('term_end_date', '');
        $nextTermStart = SystemSetting::getValue('next_term_start', '');
        $vacationStart = SystemSetting::getValue('vacation_start', '');
        $vacationEnd = SystemSetting::getValue('vacation_end', '');
        
        // Currency & Localization
        $currency = SystemSetting::getValue('currency', 'NGN');
        $currencySymbol = SystemSetting::getValue('currency_symbol', '₦');
        $dateFormat = SystemSetting::getValue('date_format', 'Y-m-d');
        $timeFormat = SystemSetting::getValue('time_format', 'h:i A');
        $timezone = SystemSetting::getValue('timezone', 'Africa/Lagos');
        
        // Social Media
        $facebook = SystemSetting::getValue('school_social_facebook', '');
        $twitter = SystemSetting::getValue('school_social_twitter', '');
        $instagram = SystemSetting::getValue('school_social_instagram', '');
        $linkedin = SystemSetting::getValue('school_social_linkedin', '');
        $youtube = SystemSetting::getValue('school_social_youtube', '');
        $whatsapp = SystemSetting::getValue('school_social_whatsapp', '');
        
        // Payment Settings
        $paystackPublicKey = SystemSetting::getValue('paystack_public_key', '');
        $paystackSecretKey = SystemSetting::getValue('paystack_secret_key', '');
        $paystackEnvironment = SystemSetting::getValue('paystack_environment', 'live');
        $paymentProcessingFee = SystemSetting::getValue('payment_processing_fee', '1.5');
        $latePaymentFee = SystemSetting::getValue('late_payment_fee', '5');
        $paymentGracePeriod = SystemSetting::getValue('payment_grace_period', '7');
        
        // Bank Settings
        $bankName = SystemSetting::getValue('school_bank_name', 'First Bank Nigeria');
        $accountNumber = SystemSetting::getValue('school_account_number', '1234567890');
        $accountName = SystemSetting::getValue('school_account_name', 'Excellence International School');
        $bankCode = SystemSetting::getValue('school_bank_code', '011');
        $accountType = SystemSetting::getValue('school_account_type', 'Current');
        
        // SMS Settings
        $smsProvider = SystemSetting::getValue('sms_provider', 'twilio');
        $smsApiKey = SystemSetting::getValue('sms_api_key', '');
        $smsApiSecret = SystemSetting::getValue('sms_api_secret', '');
        $smsSenderId = SystemSetting::getValue('sms_sender_id', 'FMSystem');
        $smsEnabled = SystemSetting::getValue('sms_enabled', 'true');
        $smsForFees = SystemSetting::getValue('sms_for_fees', 'true');
        $smsForResults = SystemSetting::getValue('sms_for_results', 'true');
        $smsForAttendance = SystemSetting::getValue('sms_for_attendance', 'true');
        
        // Email Settings
        $mailDriver = SystemSetting::getValue('mail_driver', 'smtp');
        $mailHost = SystemSetting::getValue('mail_host', 'smtp.gmail.com');
        $mailPort = SystemSetting::getValue('mail_port', 587);
        $mailUsername = SystemSetting::getValue('mail_username', '');
        $mailPassword = SystemSetting::getValue('mail_password', '');
        $mailEncryption = SystemSetting::getValue('mail_encryption', 'tls');
        $mailFromAddress = SystemSetting::getValue('mail_from_address', '');
        $mailFromName = SystemSetting::getValue('mail_from_name', 'Financial Management System');
        $mailReplyTo = SystemSetting::getValue('mail_reply_to', '');
        $emailFooterText = SystemSetting::getValue('email_footer_text', 'This is an automated message. Please do not reply.');
        
        // Backup Settings
        $backupPath = SystemSetting::getValue('backup_path', storage_path('app/backups'));
        $autoBackup = SystemSetting::getValue('auto_backup', 'daily');
        $backupRetention = SystemSetting::getValue('backup_retention', 30);
        
        // Security Settings
        $sessionTimeout = SystemSetting::getValue('session_timeout', 60);
        $maxLoginAttempts = SystemSetting::getValue('max_login_attempts', 5);
        $passwordMinLength = SystemSetting::getValue('password_min_length', 8);
        $require2fa = SystemSetting::getValue('require_2fa', 'false');
        $sessionDriver = SystemSetting::getValue('session_driver', 'database');
        $cacheDriver = SystemSetting::getValue('cache_driver', 'database');
        
        // SEO Settings
        $seoSiteTitle = SystemSetting::getValue('seo_site_title', 'Financial Management System');
        $seoSiteDescription = SystemSetting::getValue('seo_site_description', 'A comprehensive financial management system for educational institutions');
        $seoSiteKeywords = SystemSetting::getValue('seo_site_keywords', 'financial management, school fees, education, accounting');
        $seoAuthor = SystemSetting::getValue('seo_author', 'Excellence International School');
        $seoOgImage = SystemSetting::getValue('seo_og_image', '');
        $seoTwitterCard = SystemSetting::getValue('seo_twitter_card', 'summary_large_image');
        $seoTwitterSite = SystemSetting::getValue('seo_twitter_site', '@excellence_school');
        $seoGoogleAnalytics = SystemSetting::getValue('seo_google_analytics', '');
        $seoGoogleVerification = SystemSetting::getValue('seo_google_verification', '');
        $seoBingVerification = SystemSetting::getValue('seo_bing_verification', '');
        $seoRobotsTxt = SystemSetting::getValue('seo_robots_txt', "User-agent: *\nAllow: /");
        $seoSitemapUrl = SystemSetting::getValue('seo_sitemap_url', '/sitemap.xml');
        $seoCanonicalUrl = SystemSetting::getValue('seo_canonical_url', url('/'));
        $seoMetaRobots = SystemSetting::getValue('seo_meta_robots', 'index, follow');
        $seoMetaLanguage = SystemSetting::getValue('seo_meta_language', 'en');
        $seoMetaRevisitAfter = SystemSetting::getValue('seo_meta_revisit_after', '7 days');
        $seoMetaRating = SystemSetting::getValue('seo_meta_rating', 'General');
        $seoStructuredData = SystemSetting::getValue('seo_structured_data', '');
        
        // General Settings
        $appName = SystemSetting::getValue('app_name', 'Financial Management System');
        $appDebug = SystemSetting::getValue('app_debug', 'false');
        $appEnvironment = SystemSetting::getValue('app_environment', 'production');
        $appUrl = SystemSetting::getValue('app_url', url('/'));
        $emailPrimaryColor = SystemSetting::getValue('email_primary_color', '#1e3c72');
        $emailSecondaryColor = SystemSetting::getValue('email_secondary_color', '#2a5298');
        
        // Theme Settings
        $themePrimaryColor = SystemSetting::getValue('theme_primary_color', '#667eea');
        $themeSecondaryColor = SystemSetting::getValue('theme_secondary_color', '#764ba2');
        $themeDarkMode = SystemSetting::getValue('theme_dark_mode', 'false');
        $themeSidebarColor = SystemSetting::getValue('theme_sidebar_color', '#1e3c72');
        $themeHeaderColor = SystemSetting::getValue('theme_header_color', '#2a5298');
        $themeFooterText = SystemSetting::getValue('theme_footer_text', '© 2024 Financial Management System. All rights reserved.');
        $themeCustomCss = SystemSetting::getValue('theme_custom_css', '');
        $themeCustomJs = SystemSetting::getValue('theme_custom_js', '');
        $themeShowBreadcrumb = SystemSetting::getValue('theme_show_breadcrumb', 'true');
        $themeShowFooterLinks = SystemSetting::getValue('theme_show_footer_links', 'true');
        
        // Performance Settings
        $performanceCacheLifetime = SystemSetting::getValue('performance_cache_lifetime', 3600);
        $performanceOptimizeAssets = SystemSetting::getValue('performance_optimize_assets', 'true');
        $performanceMinifyCss = SystemSetting::getValue('performance_minify_css', 'true');
        $performanceMinifyJs = SystemSetting::getValue('performance_minify_js', 'true');
        $performanceCompressImages = SystemSetting::getValue('performance_compress_images', 'true');
        $performanceLazyLoad = SystemSetting::getValue('performance_lazy_load', 'true');
        $performanceCdnEnabled = SystemSetting::getValue('performance_cdn_enabled', 'false');
        $performanceCdnUrl = SystemSetting::getValue('performance_cdn_url', '');
        
        // Maintenance Settings
        $maintenanceMode = SystemSetting::getValue('maintenance_mode', 'false');
        $maintenanceMessage = SystemSetting::getValue('maintenance_message', 'We are currently performing scheduled maintenance. Please check back later.');
        $maintenanceAllowedIps = SystemSetting::getValue('maintenance_allowed_ips', '127.0.0.1, 192.168.1.1');
        $maintenanceRetryAfter = SystemSetting::getValue('maintenance_retry_after', 60);
        $maintenanceSecret = SystemSetting::getValue('maintenance_secret', '');
        
        // API Settings
        $apiEnabled = SystemSetting::getValue('api_enabled', 'true');
        $apiRateLimit = SystemSetting::getValue('api_rate_limit', 60);
        $apiThrottleAttempts = SystemSetting::getValue('api_throttle_attempts', 100);
        $apiThrottleDecay = SystemSetting::getValue('api_throttle_decay', 1);
        $apiDebug = SystemSetting::getValue('api_debug', 'false');
        $apiVersion = SystemSetting::getValue('api_version', 'v1');
        $apiDocsUrl = SystemSetting::getValue('api_docs_url', '/api/docs');
        $apiSandboxMode = SystemSetting::getValue('api_sandbox_mode', 'false');
        
        // Logging Settings
        $logRetentionDays = SystemSetting::getValue('log_retention_days', 30);
        $logLevel = SystemSetting::getValue('log_level', 'debug');
        $logChannel = SystemSetting::getValue('log_channel', 'daily');
        $enableAuditLog = SystemSetting::getValue('enable_audit_log', 'true');
        $enableActivityLog = SystemSetting::getValue('enable_activity_log', 'true');
        $monitoringEnabled = SystemSetting::getValue('monitoring_enabled', 'false');
        $monitoringEmail = SystemSetting::getValue('monitoring_email', 'admin@school.edu.ng');
        $monitoringAlertThreshold = SystemSetting::getValue('monitoring_alert_threshold', 90);
        
        // Cookie & GDPR Settings
        $cookieConsentEnabled = SystemSetting::getValue('cookie_consent_enabled', 'true');
        $cookieConsentText = SystemSetting::getValue('cookie_consent_text', 'This site uses cookies to enhance your experience.');
        $cookiePolicyUrl = SystemSetting::getValue('cookie_policy_url', '/cookie-policy');
        $privacyPolicyUrl = SystemSetting::getValue('privacy_policy_url', '/privacy-policy');
        $termsUrl = SystemSetting::getValue('terms_url', '/terms-of-use');
        $gdprEnabled = SystemSetting::getValue('gdpr_enabled', 'true');
        $dataRetentionNotice = SystemSetting::getValue('data_retention_notice', 'Your data is retained for 5 years.');

        return view('settings.index', compact(
            'schoolName', 'schoolTagline', 'schoolAddress', 'schoolPhone', 'schoolEmail', 
            'schoolWebsite', 'schoolLogo', 'schoolIcon',
            'academicYear', 'term', 'termStartDate', 'termEndDate', 'nextTermStart', 
            'vacationStart', 'vacationEnd',
            'currency', 'currencySymbol', 'dateFormat', 'timeFormat', 'timezone',
            'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'whatsapp',
            'paystackPublicKey', 'paystackSecretKey', 'paystackEnvironment',
            'paymentProcessingFee', 'latePaymentFee', 'paymentGracePeriod',
            'bankName', 'accountNumber', 'accountName', 'bankCode', 'accountType',
            'smsProvider', 'smsApiKey', 'smsApiSecret', 'smsSenderId',
            'smsEnabled', 'smsForFees', 'smsForResults', 'smsForAttendance',
            'mailDriver', 'mailHost', 'mailPort', 'mailUsername', 'mailPassword',
            'mailEncryption', 'mailFromAddress', 'mailFromName', 'mailReplyTo', 'emailFooterText',
            'backupPath', 'autoBackup', 'backupRetention',
            'sessionTimeout', 'maxLoginAttempts', 'passwordMinLength', 'require2fa',
            'sessionDriver', 'cacheDriver',
            'seoSiteTitle', 'seoSiteDescription', 'seoSiteKeywords', 'seoAuthor',
            'seoOgImage', 'seoTwitterCard', 'seoTwitterSite', 'seoGoogleAnalytics',
            'seoGoogleVerification', 'seoBingVerification', 'seoRobotsTxt',
            'seoSitemapUrl', 'seoCanonicalUrl', 'seoMetaRobots', 'seoMetaLanguage',
            'seoMetaRevisitAfter', 'seoMetaRating', 'seoStructuredData',
            'appName', 'appDebug', 'appEnvironment', 'appUrl',
            'emailPrimaryColor', 'emailSecondaryColor',
            'themePrimaryColor', 'themeSecondaryColor', 'themeDarkMode',
            'themeSidebarColor', 'themeHeaderColor', 'themeFooterText',
            'themeCustomCss', 'themeCustomJs', 'themeShowBreadcrumb', 'themeShowFooterLinks',
            'performanceCacheLifetime', 'performanceOptimizeAssets',
            'performanceMinifyCss', 'performanceMinifyJs', 'performanceCompressImages',
            'performanceLazyLoad', 'performanceCdnEnabled', 'performanceCdnUrl',
            'maintenanceMode', 'maintenanceMessage', 'maintenanceAllowedIps',
            'maintenanceRetryAfter', 'maintenanceSecret',
            'apiEnabled', 'apiRateLimit', 'apiThrottleAttempts', 'apiThrottleDecay',
            'apiDebug', 'apiVersion', 'apiDocsUrl', 'apiSandboxMode',
            'logRetentionDays', 'logLevel', 'logChannel', 'enableAuditLog',
            'enableActivityLog', 'monitoringEnabled', 'monitoringEmail',
            'monitoringAlertThreshold',
            'cookieConsentEnabled', 'cookieConsentText', 'cookiePolicyUrl',
            'privacyPolicyUrl', 'termsUrl', 'gdprEnabled', 'dataRetentionNotice'
        ));
    }

    // ==================== SCHOOL SETTINGS ====================
    public function updateSchoolSettings(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_tagline' => 'nullable|string|max:255',
            'school_address' => 'required|string',
            'school_phone' => 'required|string',
            'school_email' => 'required|email',
            'school_website' => 'nullable|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'school_icon' => 'nullable|image|mimes:ico,png,jpg|max:512',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('school_logo')) {
                $logo = $request->file('school_logo');
                $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('uploads'), $logoName);
                SystemSetting::setValue('school_logo_url', 'uploads/' . $logoName, 'text', 'School logo URL', 'school');
            }

            if ($request->hasFile('school_icon')) {
                $icon = $request->file('school_icon');
                $iconName = 'favicon_' . time() . '.' . $icon->getClientOriginalExtension();
                $icon->move(public_path('uploads'), $iconName);
                SystemSetting::setValue('school_icon', 'uploads/' . $iconName, 'text', 'School favicon', 'school');
            }

            if ($request->has('remove_logo')) {
                SystemSetting::setValue('school_logo_url', '', 'text', 'School logo URL', 'school');
            }

            SystemSetting::setValue('school_name', $request->school_name, 'text', 'School name', 'school');
            SystemSetting::setValue('school_tagline', $request->school_tagline ?? '', 'text', 'School tagline', 'school');
            SystemSetting::setValue('school_address', $request->school_address, 'textarea', 'School address', 'school');
            SystemSetting::setValue('school_phone', $request->school_phone, 'text', 'School phone', 'school');
            SystemSetting::setValue('school_email', $request->school_email, 'email', 'School email', 'school');
            SystemSetting::setValue('school_website', $request->school_website ?? '', 'text', 'School website', 'school');

            DB::commit();
            return redirect()->route('settings.index')->with('success', 'School settings updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating school settings: ' . $e->getMessage());
        }
    }

    // ==================== ACADEMIC SETTINGS ====================
    public function updateAcademicSettings(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|max:20',
            'term' => 'required|string',
            'term_start_date' => 'nullable|date',
            'term_end_date' => 'nullable|date',
            'next_term_start' => 'nullable|date',
            'vacation_start' => 'nullable|date',
            'vacation_end' => 'nullable|date',
        ]);

        try {
            SystemSetting::setValue('academic_year', $request->academic_year, 'text', 'Academic year', 'academic');
            SystemSetting::setValue('term', $request->term, 'select', 'Current term', 'academic');
            SystemSetting::setValue('term_start_date', $request->term_start_date ?? '', 'date', 'Term start date', 'academic');
            SystemSetting::setValue('term_end_date', $request->term_end_date ?? '', 'date', 'Term end date', 'academic');
            SystemSetting::setValue('next_term_start', $request->next_term_start ?? '', 'date', 'Next term start', 'academic');
            SystemSetting::setValue('vacation_start', $request->vacation_start ?? '', 'date', 'Vacation start', 'academic');
            SystemSetting::setValue('vacation_end', $request->vacation_end ?? '', 'date', 'Vacation end', 'academic');

            return redirect()->route('settings.index')->with('success', 'Academic settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating academic settings: ' . $e->getMessage());
        }
    }

    // ==================== LOCALIZATION SETTINGS ====================
    public function updateLocalizationSettings(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:5',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'timezone' => 'required|string',
        ]);

        try {
            SystemSetting::setValue('currency', $request->currency, 'select', 'Currency', 'localization');
            SystemSetting::setValue('currency_symbol', $request->currency_symbol, 'text', 'Currency symbol', 'localization');
            SystemSetting::setValue('date_format', $request->date_format, 'select', 'Date format', 'localization');
            SystemSetting::setValue('time_format', $request->time_format, 'select', 'Time format', 'localization');
            SystemSetting::setValue('timezone', $request->timezone, 'select', 'System timezone', 'localization');

            return redirect()->route('settings.index')->with('success', 'Localization settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating localization settings: ' . $e->getMessage());
        }
    }

    // ==================== SOCIAL MEDIA SETTINGS ====================
    public function updateSocialSettings(Request $request)
    {
        $request->validate([
            'school_social_facebook' => 'nullable|string|max:255',
            'school_social_twitter' => 'nullable|string|max:255',
            'school_social_instagram' => 'nullable|string|max:255',
            'school_social_linkedin' => 'nullable|string|max:255',
            'school_social_youtube' => 'nullable|string|max:255',
            'school_social_whatsapp' => 'nullable|string|max:255',
        ]);

        try {
            SystemSetting::setValue('school_social_facebook', $request->school_social_facebook ?? '', 'text', 'Facebook URL', 'social');
            SystemSetting::setValue('school_social_twitter', $request->school_social_twitter ?? '', 'text', 'Twitter URL', 'social');
            SystemSetting::setValue('school_social_instagram', $request->school_social_instagram ?? '', 'text', 'Instagram URL', 'social');
            SystemSetting::setValue('school_social_linkedin', $request->school_social_linkedin ?? '', 'text', 'LinkedIn URL', 'social');
            SystemSetting::setValue('school_social_youtube', $request->school_social_youtube ?? '', 'text', 'YouTube URL', 'social');
            SystemSetting::setValue('school_social_whatsapp', $request->school_social_whatsapp ?? '', 'text', 'WhatsApp number', 'social');

            return redirect()->route('settings.index')->with('success', 'Social media settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating social media settings: ' . $e->getMessage());
        }
    }

    // ==================== PAYMENT SETTINGS ====================
    public function updatePaystackSettings(Request $request)
    {
        $request->validate([
            'paystack_public_key' => 'required|string',
            'paystack_secret_key' => 'required|string',
            'paystack_environment' => 'required|in:live,test',
            'payment_processing_fee' => 'nullable|numeric|min:0',
            'late_payment_fee' => 'nullable|numeric|min:0',
            'payment_grace_period' => 'nullable|integer|min:0',
        ]);

        try {
            SystemSetting::setValue('paystack_public_key', $request->paystack_public_key, 'text', 'Paystack public key', 'payment');
            SystemSetting::setValue('paystack_secret_key', $request->paystack_secret_key, 'password', 'Paystack secret key', 'payment');
            SystemSetting::setValue('paystack_environment', $request->paystack_environment, 'select', 'Paystack environment', 'payment');
            SystemSetting::setValue('payment_processing_fee', $request->payment_processing_fee ?? '1.5', 'number', 'Payment processing fee', 'payment');
            SystemSetting::setValue('late_payment_fee', $request->late_payment_fee ?? '5', 'number', 'Late payment fee percentage', 'payment');
            SystemSetting::setValue('payment_grace_period', $request->payment_grace_period ?? '7', 'number', 'Grace period for payments (days)', 'payment');

            return redirect()->route('settings.index')->with('success', 'Payment settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating payment settings: ' . $e->getMessage());
        }
    }

    public function updateBankSettings(Request $request)
    {
        $request->validate([
            'school_bank_name' => 'required|string|max:255',
            'school_account_number' => 'required|string|max:20',
            'school_account_name' => 'required|string|max:255',
            'school_bank_code' => 'nullable|string|max:10',
            'school_account_type' => 'required|in:Current,Savings',
        ]);

        try {
            SystemSetting::setValue('school_bank_name', $request->school_bank_name, 'text', 'School bank name', 'bank');
            SystemSetting::setValue('school_account_number', $request->school_account_number, 'text', 'School account number', 'bank');
            SystemSetting::setValue('school_account_name', $request->school_account_name, 'text', 'School account name', 'bank');
            SystemSetting::setValue('school_bank_code', $request->school_bank_code ?? '011', 'text', 'Bank code', 'bank');
            SystemSetting::setValue('school_account_type', $request->school_account_type, 'select', 'Account type', 'bank');

            return redirect()->route('settings.index')->with('success', 'Bank settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating bank settings: ' . $e->getMessage());
        }
    }

    // ==================== SMS SETTINGS ====================
    public function updateSmsSettings(Request $request)
    {
        $request->validate([
            'sms_provider' => 'required|string|in:twilio,africastalking,messagebird,nexmo',
            'sms_api_key' => 'required|string',
            'sms_api_secret' => 'required|string',
            'sms_sender_id' => 'required|string|max:11',
            'sms_enabled' => 'boolean',
            'sms_for_fees' => 'boolean',
            'sms_for_results' => 'boolean',
            'sms_for_attendance' => 'boolean',
        ]);

        try {
            SystemSetting::setValue('sms_provider', $request->sms_provider, 'select', 'SMS provider', 'sms');
            SystemSetting::setValue('sms_api_key', $request->sms_api_key, 'password', 'SMS API key', 'sms');
            SystemSetting::setValue('sms_api_secret', $request->sms_api_secret, 'password', 'SMS API secret', 'sms');
            SystemSetting::setValue('sms_sender_id', $request->sms_sender_id, 'text', 'SMS sender ID', 'sms');
            SystemSetting::setValue('sms_enabled', $request->has('sms_enabled') ? 'true' : 'false', 'checkbox', 'Enable SMS notifications', 'sms');
            SystemSetting::setValue('sms_for_fees', $request->has('sms_for_fees') ? 'true' : 'false', 'checkbox', 'Send SMS for fee payments', 'sms');
            SystemSetting::setValue('sms_for_results', $request->has('sms_for_results') ? 'true' : 'false', 'checkbox', 'Send SMS for results', 'sms');
            SystemSetting::setValue('sms_for_attendance', $request->has('sms_for_attendance') ? 'true' : 'false', 'checkbox', 'Send SMS for attendance', 'sms');

            return redirect()->route('settings.index')->with('success', 'SMS settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating SMS settings: ' . $e->getMessage());
        }
    }

    // ==================== EMAIL SETTINGS ====================
    public function updateEmailSettings(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_driver,smtp|string|nullable',
            'mail_port' => 'required_if:mail_driver,smtp|integer|nullable',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_reply_to' => 'nullable|email',
            'email_footer_text' => 'nullable|string',
        ]);

        try {
            SystemSetting::setValue('mail_driver', $request->mail_driver, 'select', 'Mail driver', 'email');
            SystemSetting::setValue('mail_host', $request->mail_host, 'text', 'SMTP host', 'email');
            SystemSetting::setValue('mail_port', $request->mail_port, 'number', 'SMTP port', 'email');
            SystemSetting::setValue('mail_username', $request->mail_username, 'text', 'SMTP username', 'email');
            SystemSetting::setValue('mail_password', $request->mail_password, 'password', 'SMTP password', 'email');
            SystemSetting::setValue('mail_encryption', $request->mail_encryption, 'select', 'SMTP encryption', 'email');
            SystemSetting::setValue('mail_from_address', $request->mail_from_address, 'email', 'From email address', 'email');
            SystemSetting::setValue('mail_from_name', $request->mail_from_name, 'text', 'From name', 'email');
            SystemSetting::setValue('mail_reply_to', $request->mail_reply_to ?? '', 'email', 'Reply-to email', 'email');
            SystemSetting::setValue('email_footer_text', $request->email_footer_text ?? '', 'textarea', 'Email footer text', 'email');

            $this->updateEnvFile([
                'MAIL_MAILER' => $request->mail_driver,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                'MAIL_FROM_NAME' => '"' . $request->mail_from_name . '"',
            ]);

            return redirect()->route('settings.index')->with('success', 'Email settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating email settings: ' . $e->getMessage());
        }
    }

    // ==================== BACKUP SETTINGS ====================
    public function updateBackupSettings(Request $request)
    {
        $request->validate([
            'auto_backup' => 'required|in:daily,weekly,monthly,disabled',
            'backup_retention' => 'required|integer|min:1|max:365',
            'backup_path' => 'nullable|string',
            'backup_include_files' => 'boolean',
        ]);

        try {
            SystemSetting::setValue('auto_backup', $request->auto_backup, 'select', 'Auto backup frequency', 'backup');
            SystemSetting::setValue('backup_retention', $request->backup_retention, 'number', 'Backup retention (days)', 'backup');
            SystemSetting::setValue('backup_path', $request->backup_path ?? 'storage/app/backups', 'text', 'Backup storage path', 'backup');
            SystemSetting::setValue('backup_include_files', $request->has('backup_include_files') ? 'true' : 'false', 'checkbox', 'Include files in backup', 'backup');

            return redirect()->route('settings.index')->with('success', 'Backup settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating backup settings: ' . $e->getMessage());
        }
    }

    public function backup()
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $backupFile = $backupPath . '/backup_' . date('Y-m-d_H-i-s') . '.sqlite';
            
            if (config('database.default') == 'sqlite') {
                $databasePath = database_path('database.sqlite');
                if (File::exists($databasePath)) {
                    File::copy($databasePath, $backupFile);
                }
            } else {
                $dbName = env('DB_DATABASE');
                $dbUser = env('DB_USERNAME');
                $dbPass = env('DB_PASSWORD');
                $dbHost = env('DB_HOST');
                $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$backupFile}";
                exec($command);
            }

            return response()->download($backupFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,sqlite,zip|max:51200',
        ]);

        try {
            $file = $request->file('backup_file');
            $extension = $file->getClientOriginalExtension();

            if ($extension == 'sqlite') {
                $file->move(database_path(), 'database.sqlite');
            } elseif ($extension == 'sql') {
                $sql = File::get($file->getRealPath());
                DB::unprepared($sql);
            }

            return back()->with('success', 'Database restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function listBackups()
    {
        $backupPath = storage_path('app/backups');
        $backups = [];
        
        if (File::exists($backupPath)) {
            $files = File::files($backupPath);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'path' => $file->getRealPath()
                ];
            }
        }
        
        return response()->json($backups);
    }

    public function deleteBackup($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (File::exists($backupPath)) {
            File::delete($backupPath);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Backup file not found']);
    }

    // ==================== SECURITY SETTINGS ====================
    public function updateSecuritySettings(Request $request)
    {
        $request->validate([
            'session_timeout' => 'required|integer|min:5',
            'max_login_attempts' => 'required|integer|min:1',
            'password_min_length' => 'required|integer|min:6',
            'require_2fa' => 'boolean',
            'session_driver' => 'required|string|in:database,file,cookie,redis',
            'cache_driver' => 'required|string|in:database,file,redis,memcached',
        ]);

        try {
            SystemSetting::setValue('session_timeout', $request->session_timeout, 'number', 'Session timeout (minutes)', 'security');
            SystemSetting::setValue('max_login_attempts', $request->max_login_attempts, 'number', 'Maximum login attempts', 'security');
            SystemSetting::setValue('password_min_length', $request->password_min_length, 'number', 'Minimum password length', 'security');
            SystemSetting::setValue('require_2fa', $request->has('require_2fa') ? 'true' : 'false', 'checkbox', 'Require 2FA for admin', 'security');
            SystemSetting::setValue('session_driver', $request->session_driver, 'select', 'Session driver', 'security');
            SystemSetting::setValue('cache_driver', $request->cache_driver, 'select', 'Cache driver', 'security');

            return redirect()->route('settings.index')->with('success', 'Security settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating security settings: ' . $e->getMessage());
        }
    }

    // ==================== SEO SETTINGS ====================
    public function updateSeoSettings(Request $request)
    {
        $request->validate([
            'seo_site_title' => 'required|string|max:255',
            'seo_site_description' => 'required|string|max:500',
            'seo_site_keywords' => 'nullable|string|max:255',
            'seo_author' => 'nullable|string|max:255',
            'seo_og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'seo_twitter_card' => 'required|string|in:summary,summary_large_image,app,player',
            'seo_twitter_site' => 'nullable|string|max:255',
            'seo_google_analytics' => 'nullable|string|max:255',
            'seo_google_verification' => 'nullable|string|max:255',
            'seo_bing_verification' => 'nullable|string|max:255',
            'seo_robots_txt' => 'nullable|string',
            'seo_canonical_url' => 'nullable|url',
            'seo_meta_robots' => 'required|string|in:index, follow,noindex, follow,index, nofollow,noindex, nofollow',
            'seo_meta_language' => 'required|string|max:5',
            'seo_meta_revisit_after' => 'required|string',
            'seo_meta_rating' => 'required|string',
            'seo_structured_data' => 'nullable|json',
        ]);

        try {
            if ($request->hasFile('seo_og_image')) {
                $image = $request->file('seo_og_image');
                $imageName = 'og-image_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $imageName);
                SystemSetting::setValue('seo_og_image', 'uploads/' . $imageName, 'text', 'Open Graph image', 'seo');
            }

            SystemSetting::setValue('seo_site_title', $request->seo_site_title, 'text', 'Site title for SEO', 'seo');
            SystemSetting::setValue('seo_site_description', $request->seo_site_description, 'textarea', 'Site description for SEO', 'seo');
            SystemSetting::setValue('seo_site_keywords', $request->seo_site_keywords ?? '', 'text', 'Site keywords for SEO', 'seo');
            SystemSetting::setValue('seo_author', $request->seo_author ?? '', 'text', 'Site author', 'seo');
            SystemSetting::setValue('seo_twitter_card', $request->seo_twitter_card, 'select', 'Twitter card type', 'seo');
            SystemSetting::setValue('seo_twitter_site', $request->seo_twitter_site ?? '', 'text', 'Twitter site handle', 'seo');
            SystemSetting::setValue('seo_google_analytics', $request->seo_google_analytics ?? '', 'text', 'Google Analytics ID', 'seo');
            SystemSetting::setValue('seo_google_verification', $request->seo_google_verification ?? '', 'text', 'Google verification code', 'seo');
            SystemSetting::setValue('seo_bing_verification', $request->seo_bing_verification ?? '', 'text', 'Bing verification code', 'seo');
            SystemSetting::setValue('seo_robots_txt', $request->seo_robots_txt ?? '', 'textarea', 'Robots.txt content', 'seo');
            SystemSetting::setValue('seo_canonical_url', $request->seo_canonical_url ?? '', 'text', 'Canonical URL', 'seo');
            SystemSetting::setValue('seo_meta_robots', $request->seo_meta_robots, 'text', 'Meta robots', 'seo');
            SystemSetting::setValue('seo_meta_language', $request->seo_meta_language, 'text', 'Meta language', 'seo');
            SystemSetting::setValue('seo_meta_revisit_after', $request->seo_meta_revisit_after, 'text', 'Meta revisit after', 'seo');
            SystemSetting::setValue('seo_meta_rating', $request->seo_meta_rating, 'text', 'Meta rating', 'seo');
            SystemSetting::setValue('seo_structured_data', $request->seo_structured_data ?? '', 'textarea', 'Structured data JSON-LD', 'seo');

            return redirect()->route('settings.index')->with('success', 'SEO settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating SEO settings: ' . $e->getMessage());
        }
    }

    // ==================== GENERAL SETTINGS ====================
    public function updateGeneralSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_debug' => 'boolean',
            'app_environment' => 'required|in:local,development,production',
            'app_url' => 'required|url',
            'email_primary_color' => 'nullable|string|max:10',
            'email_secondary_color' => 'nullable|string|max:10',
        ]);

        try {
            SystemSetting::setValue('app_name', $request->app_name, 'text', 'Application name', 'misc');
            SystemSetting::setValue('app_debug', $request->has('app_debug') ? 'true' : 'false', 'checkbox', 'Debug mode', 'misc');
            SystemSetting::setValue('app_environment', $request->app_environment, 'select', 'Application environment', 'misc');
            SystemSetting::setValue('app_url', $request->app_url, 'text', 'Application URL', 'misc');
            SystemSetting::setValue('email_primary_color', $request->email_primary_color ?? '#1e3c72', 'color', 'Email primary color', 'misc');
            SystemSetting::setValue('email_secondary_color', $request->email_secondary_color ?? '#2a5298', 'color', 'Email secondary color', 'misc');

            $this->updateEnvFile([
                'APP_NAME' => '"' . $request->app_name . '"',
                'APP_DEBUG' => $request->has('app_debug') ? 'true' : 'false',
                'APP_ENV' => $request->app_environment,
                'APP_URL' => $request->app_url,
            ]);

            return redirect()->route('settings.index')->with('success', 'General settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating general settings: ' . $e->getMessage());
        }
    }

    // ==================== THEME SETTINGS ====================
    public function updateThemeSettings(Request $request)
    {
        $request->validate([
            'theme_primary_color' => 'nullable|string|max:10',
            'theme_secondary_color' => 'nullable|string|max:10',
            'theme_dark_mode' => 'boolean',
            'theme_sidebar_color' => 'nullable|string|max:10',
            'theme_header_color' => 'nullable|string|max:10',
            'theme_footer_text' => 'nullable|string|max:255',
            'theme_custom_css' => 'nullable|string',
            'theme_custom_js' => 'nullable|string',
            'theme_show_breadcrumb' => 'boolean',
            'theme_show_footer_links' => 'boolean',
        ]);

        try {
            SystemSetting::setValue('theme_primary_color', $request->theme_primary_color ?? '#667eea', 'color', 'Primary theme color', 'theme');
            SystemSetting::setValue('theme_secondary_color', $request->theme_secondary_color ?? '#764ba2', 'color', 'Secondary theme color', 'theme');
            SystemSetting::setValue('theme_dark_mode', $request->has('theme_dark_mode') ? 'true' : 'false', 'checkbox', 'Enable dark mode', 'theme');
            SystemSetting::setValue('theme_sidebar_color', $request->theme_sidebar_color ?? '#1e3c72', 'color', 'Sidebar color', 'theme');
            SystemSetting::setValue('theme_header_color', $request->theme_header_color ?? '#2a5298', 'color', 'Header color', 'theme');
            SystemSetting::setValue('theme_footer_text', $request->theme_footer_text ?? '© 2024 Financial Management System. All rights reserved.', 'text', 'Footer text', 'theme');
            SystemSetting::setValue('theme_custom_css', $request->theme_custom_css ?? '', 'textarea', 'Custom CSS', 'theme');
            SystemSetting::setValue('theme_custom_js', $request->theme_custom_js ?? '', 'textarea', 'Custom JavaScript', 'theme');
            SystemSetting::setValue('theme_show_breadcrumb', $request->has('theme_show_breadcrumb') ? 'true' : 'false', 'checkbox', 'Show breadcrumb navigation', 'theme');
            SystemSetting::setValue('theme_show_footer_links', $request->has('theme_show_footer_links') ? 'true' : 'false', 'checkbox', 'Show footer links', 'theme');

            return redirect()->route('settings.index')->with('success', 'Theme settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating theme settings: ' . $e->getMessage());
        }
    }

    // ==================== PERFORMANCE SETTINGS ====================
    public function updatePerformanceSettings(Request $request)
    {
        $request->validate([
            'performance_cache_lifetime' => 'required|integer|min:1',
            'performance_optimize_assets' => 'boolean',
            'performance_minify_css' => 'boolean',
            'performance_minify_js' => 'boolean',
            'performance_compress_images' => 'boolean',
            'performance_lazy_load' => 'boolean',
            'performance_cdn_enabled' => 'boolean',
            'performance_cdn_url' => 'nullable|url',
        ]);

        try {
            SystemSetting::setValue('performance_cache_lifetime', $request->performance_cache_lifetime, 'number', 'Cache lifetime (minutes)', 'performance');
            SystemSetting::setValue('performance_optimize_assets', $request->has('performance_optimize_assets') ? 'true' : 'false', 'checkbox', 'Optimize assets', 'performance');
            SystemSetting::setValue('performance_minify_css', $request->has('performance_minify_css') ? 'true' : 'false', 'checkbox', 'Minify CSS', 'performance');
            SystemSetting::setValue('performance_minify_js', $request->has('performance_minify_js') ? 'true' : 'false', 'checkbox', 'Minify JavaScript', 'performance');
            SystemSetting::setValue('performance_compress_images', $request->has('performance_compress_images') ? 'true' : 'false', 'checkbox', 'Compress images', 'performance');
            SystemSetting::setValue('performance_lazy_load', $request->has('performance_lazy_load') ? 'true' : 'false', 'checkbox', 'Enable lazy loading', 'performance');
            SystemSetting::setValue('performance_cdn_enabled', $request->has('performance_cdn_enabled') ? 'true' : 'false', 'checkbox', 'Enable CDN', 'performance');
            SystemSetting::setValue('performance_cdn_url', $request->performance_cdn_url ?? '', 'text', 'CDN URL', 'performance');

            return redirect()->route('settings.index')->with('success', 'Performance settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating performance settings: ' . $e->getMessage());
        }
    }

    // ==================== MAINTENANCE SETTINGS ====================
    public function updateMaintenanceSettings(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'maintenance_allowed_ips' => 'nullable|string',
            'maintenance_retry_after' => 'nullable|integer|min:10',
            'maintenance_secret' => 'nullable|string|max:255',
        ]);

        try {
            SystemSetting::setValue('maintenance_mode', $request->has('maintenance_mode') ? 'true' : 'false', 'checkbox', 'Enable maintenance mode', 'maintenance');
            SystemSetting::setValue('maintenance_message', $request->maintenance_message ?? '', 'textarea', 'Maintenance message', 'maintenance');
            SystemSetting::setValue('maintenance_allowed_ips', $request->maintenance_allowed_ips ?? '', 'text', 'Allowed IPs during maintenance', 'maintenance');
            SystemSetting::setValue('maintenance_retry_after', $request->maintenance_retry_after ?? 60, 'number', 'Retry after (seconds)', 'maintenance');
            SystemSetting::setValue('maintenance_secret', $request->maintenance_secret ?? '', 'text', 'Maintenance bypass secret', 'maintenance');

            return redirect()->route('settings.index')->with('success', 'Maintenance settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating maintenance settings: ' . $e->getMessage());
        }
    }

    // ==================== API SETTINGS ====================
    public function updateApiSettings(Request $request)
    {
        $request->validate([
            'api_enabled' => 'boolean',
            'api_rate_limit' => 'required|integer|min:1',
            'api_throttle_attempts' => 'required|integer|min:1',
            'api_throttle_decay' => 'required|integer|min:1',
            'api_debug' => 'boolean',
            'api_version' => 'required|string',
            'api_docs_url' => 'nullable|string',
            'api_sandbox_mode' => 'boolean',
        ]);

        try {
            SystemSetting::setValue('api_enabled', $request->has('api_enabled') ? 'true' : 'false', 'checkbox', 'Enable API access', 'api');
            SystemSetting::setValue('api_rate_limit', $request->api_rate_limit, 'number', 'API rate limit (per minute)', 'api');
            SystemSetting::setValue('api_throttle_attempts', $request->api_throttle_attempts, 'number', 'Throttle attempts', 'api');
            SystemSetting::setValue('api_throttle_decay', $request->api_throttle_decay, 'number', 'Throttle decay (minutes)', 'api');
            SystemSetting::setValue('api_debug', $request->has('api_debug') ? 'true' : 'false', 'checkbox', 'API debug mode', 'api');
            SystemSetting::setValue('api_version', $request->api_version, 'text', 'API version', 'api');
            SystemSetting::setValue('api_docs_url', $request->api_docs_url ?? '/api/docs', 'text', 'API documentation URL', 'api');
            SystemSetting::setValue('api_sandbox_mode', $request->has('api_sandbox_mode') ? 'true' : 'false', 'checkbox', 'Sandbox mode', 'api');

            return redirect()->route('settings.index')->with('success', 'API settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating API settings: ' . $e->getMessage());
        }
    }

    // ==================== LOGGING & MONITORING SETTINGS ====================
    public function updateLoggingSettings(Request $request)
    {
        $request->validate([
            'log_retention_days' => 'required|integer|min:1',
            'log_level' => 'required|string|in:debug,info,notice,warning,error,critical,alert,emergency',
            'log_channel' => 'required|string|in:single,daily,stack,syslog,errorlog',
            'enable_audit_log' => 'boolean',
            'enable_activity_log' => 'boolean',
            'monitoring_enabled' => 'boolean',
            'monitoring_email' => 'nullable|email',
            'monitoring_alert_threshold' => 'required|integer|min:1|max:100',
        ]);

        try {
            SystemSetting::setValue('log_retention_days', $request->log_retention_days, 'number', 'Log retention (days)', 'logging');
            SystemSetting::setValue('log_level', $request->log_level, 'select', 'Log level', 'logging');
            SystemSetting::setValue('log_channel', $request->log_channel, 'select', 'Log channel', 'logging');
            SystemSetting::setValue('enable_audit_log', $request->has('enable_audit_log') ? 'true' : 'false', 'checkbox', 'Enable audit logging', 'logging');
            SystemSetting::setValue('enable_activity_log', $request->has('enable_activity_log') ? 'true' : 'false', 'checkbox', 'Enable activity logging', 'logging');
            SystemSetting::setValue('monitoring_enabled', $request->has('monitoring_enabled') ? 'true' : 'false', 'checkbox', 'Enable monitoring', 'logging');
            SystemSetting::setValue('monitoring_email', $request->monitoring_email ?? '', 'email', 'Monitoring email', 'logging');
            SystemSetting::setValue('monitoring_alert_threshold', $request->monitoring_alert_threshold, 'number', 'Alert threshold', 'logging');

            return redirect()->route('settings.index')->with('success', 'Logging settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating logging settings: ' . $e->getMessage());
        }
    }

    // ==================== COOKIE & GDPR SETTINGS ====================
    public function updateCookieSettings(Request $request)
    {
        $request->validate([
            'cookie_consent_enabled' => 'boolean',
            'cookie_consent_text' => 'nullable|string|max:1000',
            'cookie_policy_url' => 'nullable|string|max:255',
            'privacy_policy_url' => 'nullable|string|max:255',
            'terms_url' => 'nullable|string|max:255',
            'gdpr_enabled' => 'boolean',
            'data_retention_notice' => 'nullable|string|max:1000',
        ]);

        try {
            SystemSetting::setValue('cookie_consent_enabled', $request->has('cookie_consent_enabled') ? 'true' : 'false', 'checkbox', 'Enable cookie consent banner', 'cookie');
            SystemSetting::setValue('cookie_consent_text', $request->cookie_consent_text ?? '', 'textarea', 'Cookie consent text', 'cookie');
            SystemSetting::setValue('cookie_policy_url', $request->cookie_policy_url ?? '/cookie-policy', 'text', 'Cookie policy URL', 'cookie');
            SystemSetting::setValue('privacy_policy_url', $request->privacy_policy_url ?? '/privacy-policy', 'text', 'Privacy policy URL', 'cookie');
            SystemSetting::setValue('terms_url', $request->terms_url ?? '/terms-of-use', 'text', 'Terms of use URL', 'cookie');
            SystemSetting::setValue('gdpr_enabled', $request->has('gdpr_enabled') ? 'true' : 'false', 'checkbox', 'Enable GDPR compliance', 'cookie');
            SystemSetting::setValue('data_retention_notice', $request->data_retention_notice ?? '', 'textarea', 'Data retention notice', 'cookie');

            return redirect()->route('settings.index')->with('success', 'Cookie & GDPR settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating cookie settings: ' . $e->getMessage());
        }
    }

    // ==================== UTILITY METHODS ====================
    public function clearCache()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        return back()->with('success', 'Application cache cleared successfully!');
    }

    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $lines = explode("\n", $content);
            
            foreach ($lines as $line) {
                if (preg_match('/\[(.*?)\].*?(\w+):(.*)/', $line, $matches)) {
                    $logs[] = [
                        'date' => $matches[1] ?? '',
                        'type' => $matches[2] ?? 'info',
                        'message' => $matches[3] ?? $line,
                        'raw' => $line
                    ];
                }
            }
        }
        
        $logs = array_reverse($logs);
        $logLevels = [
            'error' => count(array_filter($logs, fn($l) => strtolower($l['type']) == 'error')),
            'warning' => count(array_filter($logs, fn($l) => strtolower($l['type']) == 'warning')),
            'info' => count(array_filter($logs, fn($l) => strtolower($l['type']) == 'info')),
        ];
        
        return view('settings.logs', compact('logs', 'logLevels'));
    }

    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (File::exists($logFile)) {
            File::put($logFile, '');
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false]);
    }

    public function testSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $smsProvider = SystemSetting::getValue('sms_provider');
            $apiKey = SystemSetting::getValue('sms_api_key');
            $apiSecret = SystemSetting::getValue('sms_api_secret');
            $senderId = SystemSetting::getValue('sms_sender_id');

            Log::info('SMS Test Attempt', [
                'provider' => $smsProvider,
                'phone' => $request->phone,
                'message' => $request->message,
                'sender_id' => $senderId
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'SMS test logged successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            Mail::raw('This is a test email from your Financial Management System.', function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Test Email Configuration');
            });
            
            return response()->json([
                'success' => true, 
                'message' => 'Email sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function updateEnvFile($data)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);
        
        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }
        
        File::put($envPath, $envContent);
    }
}