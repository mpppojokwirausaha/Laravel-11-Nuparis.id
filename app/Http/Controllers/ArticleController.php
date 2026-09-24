<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Info;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Menampilkan halaman artikel (list)
     */
    public function article()
    {
        return view('front-end.article-more', [
            'title' => 'Event | ' . config('app.name'),
            'infos' => (new Info())->getInfo(),
            'agencies_footer' => (new Partner())->getAgencies(),
        ]);
    }

    public function articleData()
    {
        try {
            $articles = Article::with('ArticleCategory')->latest()->get();
            return response()->json([
                'success' => true,
                'articlesData' => $articles->toArray(),
                'total' => $articles->count()
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            \Log::error('articles API error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage(),
                'eventData' => []
            ], 500);
        }
    }

    /**
     * Menampilkan detail aktivitas
     */
    public function articleDetail($article_slug)
    {
        $article = Article::with('articleCategory')
            ->where('article_slug', $article_slug)
            ->first();

        if (!$article) {
            abort(404);
        }

        $this->incrementViewCount($article);
        $article->cleaned_content = $this->cleanArticleContent($article->article_description);

        $seoDescription = $article->excerpt ?: Str::limit(strip_tags($article->cleaned_content), 160);
        $seoImage = $article->article_image ? Storage::disk('public')->url($article->article_image) : null;
        $fullTitle = $article->article_title . ' | ' . config('app.name');
        $canonicalUrl = route('article-detail', $article->article_slug);

        return view('front-end.article-detail', [
            'title' => $fullTitle,
            'infos' => (new Info())->getInfo(),
            'agencies_footer' => (new Partner())->getAgencies(),
            'article' => $article,

            // ==== SEO ====
            'seo_title' => $fullTitle,
            'seo_description' => $seoDescription,
            'seo_image' => $seoImage,
            'seo_type' => 'article',
            'canonical_url' => $canonicalUrl,
            'published_time' => $article->created_at,
            'modified_time' => $article->updated_at,
            'seo_section' => $article->articleCategory->article_category_name ?? 'Berita',
            'feed_url' => route('feeds.article'),
            'feed_title' => 'Artikel Terbaru - ' . config('app.name'),
            'jsonld' => array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $article->article_title,
                'description' => $seoDescription,
                'image' => $seoImage ? [$seoImage] : null,
                'datePublished' => $article->created_at->toIso8601String(),
                'dateModified' => $article->updated_at->toIso8601String(),
                'author' => [
                    '@type' => 'Organization',
                    'name' => 'Tim Redaksi ' . config('app.name'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('images/logo.png'), // sesuaikan path logo asli
                    ],
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl,
                ],
            ]),
        ]);
    }

    /**
     * Increment view counter dengan IP-based caching
     */
    private function incrementViewCount(Article $article)
    {
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();
        $cacheKey = "article_view:{$article->uuid}:{$ipAddress}:{$userAgent}";

        // Cek apakah IP ini sudah menghitung view dalam 24 jam terakhir
        if (!Cache::has($cacheKey)) {
            $article->increment('views');
            Cache::put($cacheKey, true, now()->addHours(24));
        }
    }

    /**
     * Membersihkan konten HTML dari WYSIWYG editor
     */
    private function cleanArticleContent($html)
    {
        if (empty($html)) {
            return '';
        }

        // 1. Hapus semua inline styles
        $html = preg_replace('/\s+style="[^"]*"/i', '', $html);

        // 2. Hapus tag span dan em (tapi pertahankan kontennya)
        $html = preg_replace('/<(span|em)[^>]*>(.*?)<\/\1>/si', '$2', $html);

        // 3. Hapus tag font (legacy)
        $html = preg_replace('/<font[^>]*>(.*?)<\/font>/si', '$1', $html);

        // 4. Fix multiple line breaks menjadi pemisah paragraf
        $html = preg_replace('/(<br\s*\/?>\s*){2,}/i', '</p><p>', $html);

        // 5. Hapus paragraf kosong
        $html = preg_replace('/<p[^>]*>\s*(&nbsp;|\s)*<\/p>/i', '', $html);

        // 6. Deteksi dan perbaiki struktur khusus
        if ($this->isFaqContent($html)) {
            $html = $this->fixFaqStructure($html);
        } else {
            $html = $this->fixArticleStructure($html);
        }

        // 7. Tambahkan Tailwind classes ke elemen umum
        $html = $this->addTailwindClasses($html);

        return $html;
    }

    /**
     * Cek apakah konten berisi FAQ
     */
    private function isFaqContent($html)
    {
        return str_contains($html, 'Pertanyaan yang sering diajukan') ||
            preg_match('/<ul>\s*<li><strong>.*\?<\/strong>/i', $html) ||
            str_contains($html, 'INATRADE');
    }

    /**
     * Perbaiki struktur untuk konten FAQ
     */
    private function fixFaqStructure($html)
    {
        // Ganti <pre> dengan heading untuk FAQ
        $html = preg_replace(
            '/<pre>\s*Pertanyaan yang sering diajukan:\s*<\/pre>/i',
            '<h2 class="text-2xl font-bold mt-8 mb-6">Pertanyaan yang Sering Diajukan</h2>',
            $html
        );

        // Pattern untuk deteksi pertanyaan FAQ
        $patterns = [
            // Pattern: <ul><li><strong>Pertanyaan?</strong></li></ul>
            '/<ul>\s*<li>\s*<strong>(.*?\??)\s*<\/strong>\s*<\/li>\s*<\/ul>/si' =>
            '<div class="faq-item bg-slate-50 rounded-xl p-6 mb-6"><h3 class="text-lg font-semibold text-slate-800 mb-4">$1</h3>',

            // Pattern: <p><strong>Pertanyaan?</strong></p>
            '/<p>\s*<strong>(.*?\??)\s*<\/strong>\s*<\/p>/si' =>
            '<div class="faq-item mb-6"><h3 class="text-lg font-semibold mb-4">$1</h3>',

            // Pattern: <strong>Pertanyaan?</strong> (tanpa wrapper)
            '/<strong>(.*?\??)<\/strong>/' => '<strong class="text-slate-800">$1</strong>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html);
        }

        return $html;
    }

    /**
     * Perbaiki struktur untuk artikel biasa
     */
    private function fixArticleStructure($html)
    {
        // Normalize heading levels
        $html = preg_replace_callback(
            '/<h([1-6])([^>]*)>/i',
            function ($matches) {
                $level = $matches[1];
                $attrs = $matches[2];

                // Remove any existing classes
                $attrs = preg_replace('/\s+class="[^"]*"/i', '', $attrs);

                // Add Tailwind classes berdasarkan level
                $classes = [
                    '1' => 'text-3xl font-bold mt-8 mb-6',
                    '2' => 'text-2xl font-bold mt-6 mb-4',
                    '3' => 'text-xl font-semibold mt-4 mb-3',
                    '4' => 'text-lg font-semibold mt-3 mb-2',
                    '5' => 'text-base font-semibold mt-2 mb-2',
                    '6' => 'text-sm font-semibold mt-2 mb-1 uppercase tracking-wide',
                ];

                $class = $classes[$level] ?? 'font-semibold mt-2 mb-1';

                return "<h{$level} class=\"{$class}\"{$attrs}>";
            },
            $html
        );

        return $html;
    }

    /**
     * Tambahkan Tailwind classes ke elemen HTML
     */
    private function addTailwindClasses($html)
    {
        // Tambahkan class ke list
        $html = preg_replace_callback(
            '/<(ul|ol)([^>]*)>/i',
            function ($matches) {
                $tag = $matches[1];
                $attrs = $matches[2];

                // Remove existing class jika ada
                $attrs = preg_replace('/\s+class="[^"]*"/i', '', $attrs);

                if ($tag === 'ul') {
                    $class = 'list-disc pl-5 space-y-1 mb-4';
                } else {
                    $class = 'list-decimal pl-5 space-y-2 mb-4';
                }

                return "<{$tag} class=\"{$class}\"{$attrs}>";
            },
            $html
        );

        // Tambahkan class ke link
        $html = preg_replace_callback(
            '/<a\s+([^>]+href="[^"]+"[^>]*)>/i',
            function ($matches) {
                $attrs = $matches[1];

                // Cek apakah sudah ada class
                if (!preg_match('/class="/i', $attrs)) {
                    return '<a class="text-primary hover:underline" ' . $attrs . '>';
                }

                return '<a ' . $attrs . '>';
            },
            $html
        );

        // Tambahkan class ke gambar
        $html = preg_replace_callback(
            '/<img([^>]+)>/i',
            function ($matches) {
                $attrs = $matches[1];

                // Cek apakah sudah ada class
                if (!preg_match('/class="/i', $attrs)) {
                    // Tambahkan loading lazy untuk performance
                    if (!preg_match('/loading=/i', $attrs)) {
                        $attrs .= ' loading="lazy"';
                    }
                    return '<img class="rounded-lg w-full h-auto my-4" ' . $attrs . '>';
                }

                return '<img' . $attrs . '>';
            },
            $html
        );

        // Tambahkan class ke blockquote
        $html = str_replace(
            '<blockquote>',
            '<blockquote class="border-l-4 border-primary pl-4 py-2 my-6 italic bg-slate-50 rounded-r">',
            $html
        );

        return $html;
    }

    /**
     * Increment share counter dengan rate limiting
     */
    public function incrementShare(Request $request, $article_slug)
    {
        $article = Article::where('article_slug', $article_slug)->first();

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        // Rate limiting: 1 share per IP per 5 menit
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $cacheKey = "article_share:{$article->uuid}:{$ipAddress}:{$userAgent}";

        if (Cache::has($cacheKey)) {
            $ttl = Cache::get($cacheKey . '_ttl', now()->addMinutes(5));
            $minutesLeft = now()->diffInMinutes($ttl, false);

            return response()->json([
                'success' => false,
                'message' => 'Anda sudah membagikan artikel ini. Coba lagi dalam ' . max(1, $minutesLeft) . ' menit.',
                'shares_count' => $article->shares_count
            ], 429);
        }

        // Increment share count
        $article->increment('shares_count');

        // Set cache untuk 5 menit
        Cache::put($cacheKey, true, now()->addMinutes(5));
        Cache::put($cacheKey . '_ttl', now()->addMinutes(5), now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'shares_count' => $article->shares_count,
            'message' => 'Terima kasih telah membagikan artikel ini!'
        ]);
    }
}
