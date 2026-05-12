<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::where('is_published', true)
            ->where('published_at', '<=', now());

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('tags', function ($tagQuery) use ($search) {
                      $tagQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->get('tag'));
            });
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->get('category'));
        }

        $articles = $query->latest('published_at')->paginate(12);
        $categories = Category::whereHas('articles', function ($q) {
            $q->where('is_published', true);
        })->get();
        
        $tags = \App\Models\Tag::whereHas('articles', function ($q) {
            $q->where('is_published', true);
        })->get();

        return view('knowledge-base.index', compact('articles', 'categories', 'tags'));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $article->increment('views');

        return view('knowledge-base.show', compact('article'));
    }
}