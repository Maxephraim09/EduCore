<aside class="blog-rail">
    <section class="rail-card"><div class="eyebrow">Explore</div><h4>Categories</h4>@foreach($categories as $category)<a href="{{ route('blog.public.index', ['category' => $category->slug]) }}" class="rail-link">{{ $category->name }}</a>@endforeach</section>
    <section class="rail-card"><div class="eyebrow">Topics</div><h4>Tags</h4><div class="d-flex flex-wrap gap-2">@foreach($tags as $tag)<a href="{{ route('blog.public.index', ['tag' => $tag->slug]) }}" class="tag-pill">#{{ $tag->name }}</a>@endforeach</div></section>
    <section class="rail-card"><div class="eyebrow">Recent</div><h4>Recent stories</h4>@foreach($recentPosts as $recent)<a href="{{ route('blog.public.show', $recent) }}" class="rail-story">{{ $recent->title }}<small>{{ $recent->approved_at?->format('M d, Y') }}</small></a>@endforeach</section>
    @foreach($ads as $ad)<section class="rail-card ad-card">@if($ad->image)<img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}">@endif<div class="eyebrow">Sponsored</div><h4>{{ $ad->title }}</h4><p>{{ $ad->description }}</p>@if($ad->url)<a href="{{ $ad->url }}" target="_blank" class="blog-link">Learn more &rarr;</a>@endif</section>@endforeach
    @php
        $socialLinks = [
            'facebook_url' => [$blogSettings->facebook_url ?: getSetting('school_social_facebook', ''), 'Facebook', 'fab fa-facebook-f'],
            'instagram_url' => [$blogSettings->instagram_url ?: getSetting('school_social_instagram', ''), 'Instagram', 'fab fa-instagram'],
            'youtube_url' => [$blogSettings->youtube_url ?: getSetting('school_social_youtube', ''), 'YouTube', 'fab fa-youtube'],
            'x_url' => [$blogSettings->x_url ?: getSetting('school_social_twitter', ''), 'X', 'fab fa-x-twitter'],
        ];
    @endphp
    <section class="rail-card"><div class="eyebrow">Connect</div><h4>Follow the school</h4><div class="social-links">@foreach($socialLinks as $social) @if($social[0] && $social[0] !== '#')<a href="{{ $social[0] }}" target="_blank" aria-label="{{ $social[1] }}" title="{{ $social[1] }}"><i class="{{ $social[2] }}"></i></a>@endif @endforeach</div></section>
</aside>
