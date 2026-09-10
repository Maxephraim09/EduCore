<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-seo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic SEO -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-search me-2"></i>Basic SEO Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Site Title <span class="text-danger">*</span></label>
                                <input type="text" name="seo_site_title" class="form-control" 
                                       value="{{ $seoSiteTitle ?? getSetting('seo_site_title', 'Financial Management System') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Site Author</label>
                                <input type="text" name="seo_author" class="form-control" 
                                       value="{{ $seoAuthor ?? getSetting('seo_author', 'Excellence International School') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Meta Description <span class="text-danger">*</span></label>
                                <textarea name="seo_site_description" class="form-control" rows="3" required>{{ $seoSiteDescription ?? getSetting('seo_site_description', 'A comprehensive financial management system for educational institutions') }}</textarea>
                                <small class="text-muted">Keep between 150-160 characters for optimal SEO</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Meta Keywords</label>
                                <input type="text" name="seo_site_keywords" class="form-control" 
                                       value="{{ $seoSiteKeywords ?? getSetting('seo_site_keywords', 'financial management, school fees, education, accounting') }}">
                                <small class="text-muted">Comma-separated keywords</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Meta Robots</label>
                                <select name="seo_meta_robots" class="form-control">
                                    <option value="index, follow" {{ (getSetting('seo_meta_robots', 'index, follow') == 'index, follow') ? 'selected' : '' }}>Index, Follow</option>
                                    <option value="noindex, follow" {{ (getSetting('seo_meta_robots', 'index, follow') == 'noindex, follow') ? 'selected' : '' }}>No Index, Follow</option>
                                    <option value="index, nofollow" {{ (getSetting('seo_meta_robots', 'index, follow') == 'index, nofollow') ? 'selected' : '' }}>Index, No Follow</option>
                                    <option value="noindex, nofollow" {{ (getSetting('seo_meta_robots', 'index, follow') == 'noindex, nofollow') ? 'selected' : '' }}>No Index, No Follow</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Revisit After</label>
                                <select name="seo_meta_revisit_after" class="form-control">
                                    <option value="1 day" {{ (getSetting('seo_meta_revisit_after', '7 days') == '1 day') ? 'selected' : '' }}>1 Day</option>
                                    <option value="7 days" {{ (getSetting('seo_meta_revisit_after', '7 days') == '7 days') ? 'selected' : '' }}>7 Days</option>
                                    <option value="30 days" {{ (getSetting('seo_meta_revisit_after', '7 days') == '30 days') ? 'selected' : '' }}>30 Days</option>
                                    <option value="60 days" {{ (getSetting('seo_meta_revisit_after', '7 days') == '60 days') ? 'selected' : '' }}>60 Days</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Cards -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fab fa-facebook-square me-2"></i>Social Media Cards</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Open Graph Image</label>
                                <input type="file" name="seo_og_image" class="form-control" accept="image/*">
                                <small class="text-muted">Recommended: 1200x630px</small>
                                @if(getSetting('seo_og_image'))
                                    <div class="mt-2">
                                        <img src="{{ asset(getSetting('seo_og_image')) }}" alt="OG Image" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Twitter Card Type</label>
                                <select name="seo_twitter_card" class="form-control">
                                    <option value="summary" {{ (getSetting('seo_twitter_card', 'summary_large_image') == 'summary') ? 'selected' : '' }}>Summary</option>
                                    <option value="summary_large_image" {{ (getSetting('seo_twitter_card', 'summary_large_image') == 'summary_large_image') ? 'selected' : '' }}>Summary with Large Image</option>
                                    <option value="app" {{ (getSetting('seo_twitter_card', 'summary_large_image') == 'app') ? 'selected' : '' }}>App</option>
                                    <option value="player" {{ (getSetting('seo_twitter_card', 'summary_large_image') == 'player') ? 'selected' : '' }}>Player</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Twitter Site Handle</label>
                                <input type="text" name="seo_twitter_site" class="form-control" 
                                       value="{{ $seoTwitterSite ?? getSetting('seo_twitter_site', '@excellence_school') }}" placeholder="@username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Meta Language</label>
                                <select name="seo_meta_language" class="form-control">
                                    <option value="en" {{ (getSetting('seo_meta_language', 'en') == 'en') ? 'selected' : '' }}>English</option>
                                    <option value="fr" {{ (getSetting('seo_meta_language', 'en') == 'fr') ? 'selected' : '' }}>French</option>
                                    <option value="es" {{ (getSetting('seo_meta_language', 'en') == 'es') ? 'selected' : '' }}>Spanish</option>
                                    <option value="pt" {{ (getSetting('seo_meta_language', 'en') == 'pt') ? 'selected' : '' }}>Portuguese</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Canonical URL</label>
                                <input type="url" name="seo_canonical_url" class="form-control" 
                                       value="{{ $seoCanonicalUrl ?? getSetting('seo_canonical_url', url('/')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Sitemap URL</label>
                                <input type="text" name="seo_sitemap_url" class="form-control" 
                                       value="{{ $seoSitemapUrl ?? getSetting('seo_sitemap_url', '/sitemap.xml') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification & Analytics -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-code me-2"></i>Verification & Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Google Analytics ID</label>
                                <input type="text" name="seo_google_analytics" class="form-control" 
                                       value="{{ $seoGoogleAnalytics ?? getSetting('seo_google_analytics') }}" placeholder="UA-XXXXX-X or G-XXXXXXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Google Search Console Verification</label>
                                <input type="text" name="seo_google_verification" class="form-control" 
                                       value="{{ $seoGoogleVerification ?? getSetting('seo_google_verification') }}" placeholder="Google Site Verification Code">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Bing Webmaster Verification</label>
                                <input type="text" name="seo_bing_verification" class="form-control" 
                                       value="{{ $seoBingVerification ?? getSetting('seo_bing_verification') }}" placeholder="Bing Verification Code">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Meta Rating</label>
                                <select name="seo_meta_rating" class="form-control">
                                    <option value="General" {{ (getSetting('seo_meta_rating', 'General') == 'General') ? 'selected' : '' }}>General</option>
                                    <option value="Mature" {{ (getSetting('seo_meta_rating', 'General') == 'Mature') ? 'selected' : '' }}>Mature</option>
                                    <option value="Restricted" {{ (getSetting('seo_meta_rating', 'General') == 'Restricted') ? 'selected' : '' }}>Restricted</option>
                                    <option value="14 Years" {{ (getSetting('seo_meta_rating', 'General') == '14 Years') ? 'selected' : '' }}>14 Years</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Robots & Structured Data -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-robot me-2"></i>Robots & Structured Data</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Robots.txt Content</label>
                                <textarea name="seo_robots_txt" class="form-control" rows="5">{{ $seoRobotsTxt ?? getSetting('seo_robots_txt', "User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Structured Data (JSON-LD)</label>
                                <textarea name="seo_structured_data" class="form-control" rows="6">{{ $seoStructuredData ?? getSetting('seo_structured_data') }}</textarea>
                                <small class="text-muted">Valid JSON-LD structured data for rich snippets</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save SEO Settings
                </button>
            </div>
        </form>
    </div>
</div>