<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'School Blog') · {{ getSchoolName() }}</title>
    <meta name="description" content="{{ getSeoDescription() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --ink: #17212b; --muted: #66727d; --cream: #f7f4ee; --gold: #c98b3c; --blue: #173f5f; }
        body { margin: 0; color: var(--ink); background: var(--cream); font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
        .blog-nav { background: var(--blue); color: white; }
        .blog-nav a { color: white; text-decoration: none; }
        .blog-brand { font-size: 1.15rem; font-weight: 700; letter-spacing: .02em; }
        .blog-hero { padding: 6rem 0 4.5rem; background: radial-gradient(circle at 80% 20%, rgba(201,139,60,.26), transparent 30%), linear-gradient(135deg, #173f5f, #235b75); color: white; }
        .blog-hero h1 { font-size: clamp(2.8rem, 7vw, 5.8rem); line-height: .98; max-width: 720px; }
        .blog-hero p { max-width: 580px; color: rgba(255,255,255,.78); font-size: 1.1rem; }
        .section-space { padding: 4rem 0; }
        .post-card { height: 100%; border: 0; border-radius: 4px; background: white; box-shadow: 0 16px 40px rgba(23,33,43,.08); transition: transform .2s ease, box-shadow .2s ease; }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 20px 50px rgba(23,33,43,.14); }
        .post-card .card-body { padding: 1.6rem; }
        .eyebrow { color: var(--gold); text-transform: uppercase; letter-spacing: .14em; font-size: .72rem; font-weight: 700; }
        .blog-link { color: var(--blue); font-weight: 700; text-decoration: none; }
        .blog-link:hover { color: var(--gold); }
        .blog-footer { background: var(--ink); color: rgba(255,255,255,.7); padding: 2rem 0; }
        .article-shell { max-width: 850px; margin: -3rem auto 4rem; position: relative; }
        .article-card { background: white; padding: clamp(1.5rem, 5vw, 4.5rem); box-shadow: 0 18px 50px rgba(23,33,43,.1); }
        .article-content { font-size: 1.08rem; line-height: 1.9; white-space: normal; }
        .form-control { border-radius: 2px; border-color: #d8d5ce; }
        .btn-gold { background: var(--gold); border-color: var(--gold); color: white; border-radius: 2px; }
        .blog-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 2rem; align-items: start; }
        .blog-rail { display: grid; gap: 1rem; }
        .rail-card { background: white; padding: 1.25rem; box-shadow: 0 12px 30px rgba(23,33,43,.07); }
        .rail-card h4 { margin: .35rem 0 1rem; }
        .rail-link, .rail-story { display: block; color: var(--ink); text-decoration: none; padding: .45rem 0; border-bottom: 1px solid #eee; }
        .rail-link:hover, .rail-story:hover { color: var(--gold); }
        .rail-story small { display: block; color: var(--muted); margin-top: .2rem; }
        .tag-pill { color: var(--blue); background: #eef3f4; padding: .3rem .55rem; text-decoration: none; font-size: .85rem; }
        .social-links { display: flex; flex-wrap: wrap; gap: .7rem; }
        .social-links a { color: var(--blue); text-decoration: none; font-weight: 700; }
        .social-links a { display: inline-flex; width: 2.25rem; height: 2.25rem; align-items: center; justify-content: center; border: 1px solid #dce4e6; border-radius: 50%; font-size: 1rem; }
        .social-links a:hover { background: var(--blue); color: white; }
        .ad-card img { width: 100%; max-height: 150px; object-fit: cover; margin-bottom: 1rem; }
        @media (max-width: 900px) { .blog-layout { grid-template-columns: 1fr; } .blog-rail { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 600px) { .blog-rail { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav class="blog-nav py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="blog-brand" href="{{ route('blog.public.index') }}">{{ getSchoolName() }} <span style="color: #e5b66e">/ Journal</span></a>
            <a href="{{ route('login') }}" class="small">Staff login</a>
        </div>
    </nav>
    @yield('content')
    <footer class="blog-footer"><div class="container d-flex flex-wrap justify-content-between gap-2"><span>{{ getSchoolName() }}</span><span>{{ getSchoolTagline() }}</span></div></footer>
</body>
</html>
