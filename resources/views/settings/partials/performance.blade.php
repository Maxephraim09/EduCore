<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-performance') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-rocket me-2"></i>Performance Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Cache Lifetime (minutes)</label>
                                <input type="number" name="performance_cache_lifetime" class="form-control" 
                                       value="{{ $performanceCacheLifetime ?? getSetting('performance_cache_lifetime', 3600) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="performance_optimize_assets" class="form-check-input" id="optimizeAssets" 
                                           value="1" {{ (getSetting('performance_optimize_assets', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="optimizeAssets">Optimize Assets</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="performance_minify_css" class="form-check-input" id="minifyCss" 
                                           value="1" {{ (getSetting('performance_minify_css', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="minifyCss">Minify CSS</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="performance_minify_js" class="form-check-input" id="minifyJs" 
                                           value="1" {{ (getSetting('performance_minify_js', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="minifyJs">Minify JavaScript</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="performance_compress_images" class="form-check-input" id="compressImages" 
                                           value="1" {{ (getSetting('performance_compress_images', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="compressImages">Compress Images</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="performance_lazy_load" class="form-check-input" id="lazyLoad" 
                                           value="1" {{ (getSetting('performance_lazy_load', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="lazyLoad">Enable Lazy Loading</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="performance_cdn_enabled" class="form-check-input" id="cdnEnabled" 
                                           value="1" {{ (getSetting('performance_cdn_enabled', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cdnEnabled">Enable CDN</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>CDN URL</label>
                                <input type="text" name="performance_cdn_url" class="form-control" 
                                       value="{{ $performanceCdnUrl ?? getSetting('performance_cdn_url') }}" placeholder="https://cdn.example.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Performance Settings
                </button>
            </div>
        </form>
    </div>
</div>