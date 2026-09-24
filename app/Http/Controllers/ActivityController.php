<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Info;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    /**
     * Menampilkan halaman aktivitas (list)
     */
    public function activity()
    {
        return view('front-end.activity-more', [
            'title' => 'Event | ' . config('app.name'),
            'infos' => (new Info())->getInfo(),
            'agencies_footer' => (new Partner())->getAgencies(),
        ]);
    }

    public function activityData()
    {
        try {
            $activities = Activity::all();
            return response()->json([
                'success' => true,
                'activitiesData' => $activities->toArray(),
                'total' => $activities->count()
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            \Log::error('activities API error: ' . $e->getMessage(), [
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
    public function activityDetail($activity_slug)
    {
        $activity = Activity::with('activityCategory')
            ->where('activity_slug', $activity_slug)
            ->first();

        if (!$activity) {
            abort(404);
        }

        $this->incrementViewCount($activity);

        $activity->cleaned_content = $this->cleanActivityContent($activity->activity_description);

        // ==== SEO ====
        $seoDescription = Str::limit(strip_tags($activity->cleaned_content), 160);
        $seoImage = $activity->activity_image ? Storage::disk('public')->url($activity->activity_image) : null;
        $fullTitle = $activity->activity_title . ' | ' . config('app.name');
        $canonicalUrl = route('activity-detail', $activity->activity_slug);

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $activity->activity_title,
            'description' => $seoDescription,
            'datePublished' => $activity->created_at->toIso8601String(),
            'dateModified' => $activity->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonicalUrl,
            ],
        ];

        if ($seoImage) {
            $jsonld['image'] = [$seoImage];
        }

        if (!empty($activity->activity_location)) {
            $jsonld['contentLocation'] = [
                '@type' => 'Place',
                'name' => $activity->activity_location,
                'address' => $activity->activity_location,
            ];
        }

        // Kapan kegiatan ini benar-benar terjadi (beda dari kapan artikelnya dipublikasi)
        if (!empty($activity->activity_date)) {
            $jsonld['temporalCoverage'] = $activity->activity_date instanceof \Carbon\Carbon
                ? $activity->activity_date->toDateString()
                : (string) $activity->activity_date;
        }

        return view('front-end.activity-detail', [
            'title' => $fullTitle,
            'infos' => (new Info())->getInfo(),
            'activity' => $activity,
            'agencies_footer' => (new Partner())->getAgencies(),

            // ==== SEO ====
            'seo_title' => $fullTitle,
            'seo_description' => $seoDescription,
            'seo_image' => $seoImage,
            'seo_type' => 'article',
            'canonical_url' => $canonicalUrl,
            'published_time' => $activity->created_at,
            'modified_time' => $activity->updated_at,
            'seo_section' => $activity->activityCategory->activity_category_name ?? 'Portofolio',
            'jsonld' => $jsonld,
            'feed_url' => route('feeds.activity'),
            'feed_title' => 'Portofolio Kegiatan - ' . config('app.name'),
        ]);
    }

    /**
     * Increment view counter dengan IP-based caching
     */
    private function incrementViewCount(Activity $activity)
    {
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();
        $cacheKey = "activity_view:{$activity->uuid}:{$ipAddress}:{$userAgent}";

        // Cek apakah IP ini sudah menghitung view dalam 24 jam terakhir
        if (!Cache::has($cacheKey)) {
            $activity->increment('views');
            Cache::put($cacheKey, true, now()->addHours(24));
        }
    }

    /**
     * Membersihkan konten HTML dari WYSIWYG editor untuk aktivitas
     */
    private function cleanActivityContent($html)
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

        // 6. Deteksi dan perbaiki struktur khusus untuk aktivitas
        if ($this->isEventScheduleContent($html)) {
            $html = $this->fixEventScheduleStructure($html);
        } else {
            $html = $this->fixActivityStructure($html);
        }

        // 7. Tambahkan Tailwind classes ke elemen umum
        $html = $this->addTailwindClasses($html);

        return $html;
    }

    /**
     * Cek apakah konten berisi jadwal event
     */
    private function isEventScheduleContent($html)
    {
        return str_contains($html, 'Jadwal Kegiatan') ||
            str_contains($html, 'Agenda Kegiatan') ||
            preg_match('/<table.*?>.*?<tr>.*?<td>.*?Waktu.*?<\/td>.*?<\/tr>.*?<\/table>/si', $html);
    }

    /**
     * Perbaiki struktur untuk konten jadwal event
     */
    private function fixEventScheduleStructure($html)
    {
        // Ganti heading untuk jadwal kegiatan
        $html = preg_replace(
            '/<h[1-6][^>]*>\s*(Jadwal Kegiatan|Agenda Kegiatan)\s*<\/h[1-6]>/i',
            '<h2 class="text-2xl font-bold mt-8 mb-6">$1</h2>',
            $html
        );

        // Perbaiki tabel jadwal
        $html = preg_replace_callback(
            '/<table([^>]*)>(.*?)<\/table>/si',
            function ($matches) {
                $tableAttrs = $matches[1];
                $tableContent = $matches[2];

                // Remove existing classes
                $tableAttrs = preg_replace('/\s+class="[^"]*"/i', '', $tableAttrs);

                // Style untuk tabel jadwal
                $tableAttrs .= ' class="min-w-full divide-y divide-gray-200 shadow-sm rounded-lg overflow-hidden my-6"';

                // Style untuk thead
                $tableContent = preg_replace(
                    '/<thead>([\s\S]*?)<\/thead>/i',
                    '<thead><tr class="bg-primary text-white">$1</tr></thead>',
                    $tableContent
                );

                // Style untuk th
                $tableContent = preg_replace(
                    '/<th([^>]*)>(.*?)<\/th>/si',
                    '<th$1 class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">$2</th>',
                    $tableContent
                );

                // Style untuk tbody rows
                $tableContent = preg_replace(
                    '/<tr>([\s\S]*?)<\/tr>/si',
                    '<tr class="hover:bg-gray-50 even:bg-gray-50">$1</tr>',
                    $tableContent
                );

                // Style untuk td
                $tableContent = preg_replace(
                    '/<td([^>]*)>(.*?)<\/td>/si',
                    '<td$1 class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">$2</td>',
                    $tableContent
                );

                return "<table{$tableAttrs}>{$tableContent}</table>";
            },
            $html
        );

        return $html;
    }

    /**
     * Perbaiki struktur untuk aktivitas biasa
     */
    private function fixActivityStructure($html)
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
                    return '<a class="text-primary hover:underline font-medium" ' . $attrs . '>';
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
                    return '<img class="rounded-lg w-full h-auto my-4 shadow-md" ' . $attrs . '>';
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

        // Tambahkan class untuk highlight info penting
        $html = str_replace(
            '<strong>',
            '<strong class="text-slate-800 font-semibold">',
            $html
        );

        return $html;
    }

    /**
     * Increment share counter dengan rate limiting
     */
    public function incrementShare(Request $request, $activity_slug)
    {
        $activity = Activity::where('activity_slug', $activity_slug)->first();

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        // Rate limiting: 1 share per IP per 5 menit
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $cacheKey = "activity_share:{$activity->uuid}:{$ipAddress}:{$userAgent}";

        if (Cache::has($cacheKey)) {
            $ttl = Cache::get($cacheKey . '_ttl', now()->addMinutes(5));
            $minutesLeft = now()->diffInMinutes($ttl, false);

            return response()->json([
                'success' => false,
                'message' => 'Anda sudah membagikan aktivitas ini. Coba lagi dalam ' . max(1, $minutesLeft) . ' menit.',
                'shares_count' => $activity->shares_count
            ], 429);
        }

        // Increment share count
        $activity->increment('shares_count');

        // Set cache untuk 5 menit
        Cache::put($cacheKey, true, now()->addMinutes(5));
        Cache::put($cacheKey . '_ttl', now()->addMinutes(5), now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'shares_count' => $activity->shares_count,
            'message' => 'Terima kasih telah membagikan aktivitas ini!'
        ]);
    }

    /**
     * Menampilkan halaman galeri aktivitas
     */
    public function activityGallery()
    {
        $activities = Activity::whereNotNull('gallery_images')
            ->where('gallery_images', '!=', '')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('front-end.activity-gallery', [
            'title' => 'Galeri Aktivitas | ' . config('app.name'),
            'infos' => (new Info())->getInfo(),
            'activities' => $activities,
        ]);
    }
}
