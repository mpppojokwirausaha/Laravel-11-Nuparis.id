<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $staticPages = [
            ['url' => route('landingpage'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['url' => route('article-more'), 'changefreq' => 'daily', 'priority' => '0.8'],
        ];

        $articles = Article::query()
            ->latest('created_at')
            ->get(['article_slug', 'updated_at'])
            ->map(fn(Article $article): array => [
                'url' => route('article-detail', $article->article_slug),
                'lastmod' => $article->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ]);

        return response()
            ->view('front-end.sitemap', [
                'staticPages' => $staticPages,
                'articles' => $articles,
            ])
            ->header('Content-Type', 'application/xml');
    }
}
