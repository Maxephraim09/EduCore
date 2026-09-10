<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\BlogSubscriber;
use App\Models\BlogAd;
use App\Models\BlogSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function publicIndex(Request $request)
    {
        $posts = BlogPost::approved()->with(['author', 'category', 'tags'])->withCount('approvedComments')
            ->when($request->category, fn ($query, $category) => $query->whereHas('category', fn ($q) => $q->where('slug', $category)))
            ->when($request->tag, fn ($query, $tag) => $query->whereHas('tags', fn ($q) => $q->where('slug', $tag)))
            ->latest('approved_at')->paginate(12)->withQueryString();

        return view('blog.public-index', $this->publicData(['posts' => $posts]));
    }

    public function publicShow(BlogPost $post)
    {
        abort_unless($post->status === 'approved', 404);
        $post->increment('views');
        $post->load(['author', 'category', 'tags', 'approvedComments' => fn ($query) => $query->latest()]);
        return view('blog.public-show', $this->publicData(compact('post')));
    }

    public function comment(Request $request, BlogPost $post)
    {
        abort_unless($post->status === 'approved', 404);
        $data = $request->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:255', 'body' => 'required|string|max:2000']);
        $post->comments()->create(array_merge($data, ['user_id' => auth()->id()]));
        return back()->with('success', 'Your comment was submitted for review.');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $posts = BlogPost::with(['author', 'category'])->withCount(['comments', 'approvedComments'])->when($user->role === 'teacher', fn ($q) => $q->where('user_id', $user->id))->latest()->paginate(15);
        return view('blog.index', compact('posts'));
    }

    public function comments()
    {
        $comments = BlogComment::with(['post', 'user'])->latest()->paginate(30);
        return view('blog.comments', compact('comments'));
    }

    public function subscribers()
    {
        $subscribers = BlogSubscriber::latest()->paginate(30);
        return view('blog.subscribers', compact('subscribers'));
    }

    public function presentation()
    {
        return view('blog.presentation', [
            'settings' => BlogSetting::firstOrCreate([]),
            'ads' => BlogAd::latest()->get(),
        ]);
    }

    public function updatePresentation(Request $request)
    {
        $data = $request->validate([
            'hero_title' => 'required|string|max:160', 'hero_text' => 'nullable|string|max:500',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'facebook_url' => 'nullable|url', 'instagram_url' => 'nullable|url', 'youtube_url' => 'nullable|url', 'x_url' => 'nullable|url',
        ]);
        $settings = BlogSetting::firstOrCreate([]);
        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) Storage::disk('public')->delete($settings->hero_image);
            $data['hero_image'] = $request->file('hero_image')->store('blog', 'public');
        }
        $settings->update($data);
        return back()->with('success', 'Blog hero and social links updated.');
    }

    public function storeAd(Request $request)
    {
        $data = $request->validate(['title' => 'required|string|max:160', 'description' => 'nullable|string|max:500', 'url' => 'nullable|url', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']);
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('blog/ads', 'public');
        $data['is_active'] = $request->boolean('is_active');
        BlogAd::create($data);
        return back()->with('success', 'Advertisement created.');
    }

    public function updateAd(Request $request, BlogAd $ad)
    {
        $data = $request->validate(['title' => 'required|string|max:160', 'description' => 'nullable|string|max:500', 'url' => 'nullable|url', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']);
        if ($request->hasFile('image')) { if ($ad->image) Storage::disk('public')->delete($ad->image); $data['image'] = $request->file('image')->store('blog/ads', 'public'); }
        $data['is_active'] = $request->boolean('is_active');
        $ad->update($data);
        return back()->with('success', 'Advertisement updated.');
    }

    public function destroyAd(BlogAd $ad)
    {
        if ($ad->image) Storage::disk('public')->delete($ad->image);
        $ad->delete();
        return back()->with('success', 'Advertisement deleted.');
    }

    public function subscribe(Request $request)
    {
        BlogSubscriber::updateOrCreate(
            ['email' => strtolower($request->validate(['email' => 'required|email|max:255'])['email'])],
            ['name' => $request->input('name'), 'is_active' => true]
        );

        return back()->with('success', 'You are now subscribed to the school blog.');
    }

    public function create()
    {
        return view('blog.form', ['post' => new BlogPost(), 'categories' => BlogCategory::orderBy('name')->get(), 'tags' => BlogTag::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['featured_image'] = $this->storeImage($request);
        $data['user_id'] = auth()->id();
        $data['status'] = auth()->user()->role === 'teacher' ? 'pending' : ($request->boolean('publish') ? 'approved' : 'draft');
        if ($data['status'] === 'approved') { $data['approved_at'] = now(); $data['approved_by'] = auth()->id(); }
        $post = BlogPost::create($data);
        $this->syncTags($post, $request->input('tags', ''));
        return redirect()->route('admin.blog.index')->with('success', 'Blog post saved successfully.');
    }

    public function edit(BlogPost $post)
    {
        $this->ensureCanManage($post);
        return view('blog.form', ['post' => $post->load('tags'), 'categories' => BlogCategory::orderBy('name')->get(), 'tags' => BlogTag::orderBy('name')->get()]);
    }

    public function update(Request $request, BlogPost $post)
    {
        $this->ensureCanManage($post);
        $data = $this->validated($request);
        if ($request->hasFile('featured_image')) {
            $this->deleteImage($post->featured_image);
            $data['featured_image'] = $this->storeImage($request);
        }
        if (auth()->user()->role === 'teacher') { $data['status'] = 'pending'; $data['rejection_reason'] = null; }
        $post->update($data);
        $this->syncTags($post, $request->input('tags', ''));
        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated and submitted for review.');
    }

    public function approve(BlogPost $post)
    {
        $post->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => auth()->id(), 'rejection_reason' => null]);
        return back()->with('success', 'Blog post approved and published.');
    }

    public function reject(Request $request, BlogPost $post)
    {
        $data = $request->validate(['rejection_reason' => 'required|string|max:1000']);
        $post->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason']]);
        return back()->with('success', 'Blog post rejected with feedback.');
    }

    public function destroy(BlogPost $post)
    {
        $this->ensureCanManage($post);
        $this->deleteImage($post->featured_image);
        $post->delete();
        return back()->with('success', 'Blog post deleted.');
    }

    public function categories()
    {
        return view('blog.categories', ['categories' => BlogCategory::withCount('posts')->orderBy('name')->get()]);
    }

    public function storeCategory(Request $request)
    {
        BlogCategory::create($request->validate(['name' => 'required|string|max:100|unique:blog_categories,name', 'description' => 'nullable|string']));
        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, BlogCategory $category)
    {
        $category->update($request->validate([
            'name' => 'required|string|max:100|unique:blog_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted. Posts were kept uncategorised.');
    }

    public function storeTag(Request $request)
    {
        BlogTag::firstOrCreate(['slug' => Str::slug($request->validate(['name' => 'required|string|max:80'])['name'])], ['name' => $request->name]);
        return back()->with('success', 'Tag saved.');
    }

    public function approveComment(BlogComment $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved.');
    }

    public function rejectComment(BlogComment $comment)
    {
        $comment->update(['is_approved' => false]);
        return back()->with('success', 'Comment hidden from the public blog.');
    }

    public function destroyComment(BlogComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['title' => 'required|string|max:255', 'category_id' => 'nullable|exists:blog_categories,id', 'excerpt' => 'nullable|string|max:500', 'content' => 'required|string', 'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096']);
    }

    private function syncTags(BlogPost $post, string $tags): void
    {
        $ids = collect(explode(',', $tags))->map(fn ($tag) => trim($tag))->filter()->unique()->map(fn ($tag) => BlogTag::firstOrCreate(['slug' => Str::slug($tag)], ['name' => $tag])->id);
        $post->tags()->sync($ids);
    }

    private function ensureCanManage(BlogPost $post): void
    {
        abort_unless(auth()->user()->role !== 'teacher' || $post->user_id === auth()->id(), 403);
    }

    private function storeImage(Request $request): ?string
    {
        return $request->hasFile('featured_image')
            ? $request->file('featured_image')->store('blog', 'public')
            : null;
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function publicData(array $data): array
    {
        return array_merge($data, [
            'categories' => BlogCategory::orderBy('name')->get(),
            'tags' => BlogTag::orderBy('name')->get(),
            'recentPosts' => BlogPost::approved()->latest('approved_at')->limit(5)->get(),
            'ads' => BlogAd::where('is_active', true)->latest()->get(),
            'blogSettings' => BlogSetting::firstOrCreate([]),
        ]);
    }
}