<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-social') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i>Social Media Links</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><i class="fab fa-facebook text-primary me-2"></i>Facebook</label>
                                <input type="text" name="school_social_facebook" class="form-control" 
                                       value="{{ $facebook ?? getSetting('school_social_facebook') }}" 
                                       placeholder="https://facebook.com/your-school">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><i class="fab fa-twitter text-info me-2"></i>Twitter / X</label>
                                <input type="text" name="school_social_twitter" class="form-control" 
                                       value="{{ $twitter ?? getSetting('school_social_twitter') }}" 
                                       placeholder="https://twitter.com/your-school">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><i class="fab fa-instagram text-danger me-2"></i>Instagram</label>
                                <input type="text" name="school_social_instagram" class="form-control" 
                                       value="{{ $instagram ?? getSetting('school_social_instagram') }}" 
                                       placeholder="https://instagram.com/your-school">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><i class="fab fa-linkedin text-primary me-2"></i>LinkedIn</label>
                                <input type="text" name="school_social_linkedin" class="form-control" 
                                       value="{{ $linkedin ?? getSetting('school_social_linkedin') }}" 
                                       placeholder="https://linkedin.com/company/your-school">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><i class="fab fa-youtube text-danger me-2"></i>YouTube</label>
                                <input type="text" name="school_social_youtube" class="form-control" 
                                       value="{{ $youtube ?? getSetting('school_social_youtube') }}" 
                                       placeholder="https://youtube.com/@your-school">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><i class="fab fa-whatsapp text-success me-2"></i>WhatsApp</label>
                                <input type="text" name="school_social_whatsapp" class="form-control" 
                                       value="{{ $whatsapp ?? getSetting('school_social_whatsapp') }}" 
                                       placeholder="+234 801 234 5678">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Leave fields blank to hide social media icons from the system.
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Social Media Settings
                </button>
            </div>
        </form>
    </div>
</div>