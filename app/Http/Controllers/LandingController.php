<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $latestBlogPosts = BlogPost::approved()
            ->with('category')
            ->latest('approved_at')
            ->limit(3)
            ->get();

        return view('welcome', compact('latestBlogPosts'));
    }
}