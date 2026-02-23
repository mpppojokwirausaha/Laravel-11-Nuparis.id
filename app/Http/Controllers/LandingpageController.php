<?php

namespace App\Http\Controllers;

use \App\Models\Activity;
use \App\Models\Article;
use App\Models\DocumentToss;
use \App\Models\News;
use \App\Models\Partner;
use \App\Models\Review;
use App\Models\Event;
use App\Models\Hero;
use App\Models\Info;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LandingpageController extends Controller
{
    public function index()
    {
        return view('front-end.landingpage', [
            'title' => env('APP_NAME') . ' | Support Your Company Goal',
            'events' => (new Event())->getEvent(),
            'activities' => (new Activity())->getActivity(),
            'partnerLayers' => (new Partner())->getPartner(),
            'articles' => (new Article())->getArticle(),
            'news' => (new News())->getNews(),
            'infos' => ((new Info))->getInfo(),
            'reviews' => (new Review())->getReview(),
            'members' => (new User())->getMembers(),
            'consultants' => (new User())->getconsultants(),
            'heroes' => (new Hero())->getAssets(),
            'properties' => (new Property())->getProperties(),
        ]);
    }

    public function news()
    {
        return view('front-end.news-more', [
            'title' => 'News | ' . config('app.name'),
            'infos' => (new Info())->getInfo(),
        ]);
    }

    public function newsData()
    {
        try {
            $news = News::all();
            return response()->json([
                'success' => true,
                'newsData' => $news->toArray(),
                'total' => $news->count()
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            \Log::error('news API error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage(),
                'eventData' => []
            ], 500);
        }
    }

    public function toss($slug)
    {
        // Ambil data dokumen berdasarkan slug
        $toss = DocumentToss::where('document_slug', $slug)->first();
        
        // Jika data tidak ditemukan, tampilkan halaman 404
        if (!$toss) {
            abort(404);
        }
    
        $start = $toss->document_start;
        $end = $toss->document_end;
        
        // Nilai default jika kedua tanggal null
        $expired = '-';
        $statusDoc = 'Valid';
    
        // CEK KONDISI TANGGAL:
        // 1. Kondisi: kedua tanggal terisi
        if ($start && $end) {
            $startCarbon = Carbon::parse($start);
            $endCarbon = Carbon::parse($end);
            
            // Format tanggal berdasarkan kesamaan bulan & tahun
            $expired = $startCarbon->month === $endCarbon->month && $startCarbon->year === $endCarbon->year
                ? $startCarbon->format('j') . ' - ' . $endCarbon->format('j F Y')  // "25 - 31 Juli 2025"
                : $startCarbon->format('j M Y') . ' - ' . $endCarbon->format('j M Y'); // "25 Jul 2025 - 31 Agu 2025"
            
            // Tentukan status: Berlaku / Tidak Berlaku
            $statusDoc = now()->gt($endCarbon) ? 'Tidak Berlaku' : 'Berlaku';
        } 
        // 2. Kondisi: start terisi, end null
        elseif ($start && !$end) {
            $expired = 'Mulai ' . Carbon::parse($start)->format('j F Y'); // "Mulai 25 Juli 2025"
            // Status tetap Valid (default)
        } 
        // 3. Kondisi: start null, end terisi
        elseif (!$start && $end) {
            $endCarbon = Carbon::parse($end);
            $expired = 'Berakhir ' . $endCarbon->format('j F Y'); // "Berakhir 31 Agustus 2025"
            // Tentukan status berdasarkan tanggal akhir
            $statusDoc = now()->gt($endCarbon) ? 'Tidak Berlaku' : 'Berlaku';
        }
        // 4. Kondisi: kedua null - menggunakan nilai default
    
        // Ambil file dokumen
        $urlDocument = $toss->document_final_path;
        
        // Kirim data ke view
        return view('front-end.toss', [
            'title' => 'Toss | ' . config('app.name'),
            'documentData' => collect([
                'id' => $toss->document_no,
                'name' => $toss->document_name,
                'description' => $toss->document_description,
                'notes' => $toss->document_notes,
                'expired' => $expired,
                'status' => $statusDoc,
                'signedBy' => $toss->document_bySign,
                'receivedBy' => $toss->document_toReceive,
                'action' => $toss->document_action,
            ]),
            'url_document' => $urlDocument,
            'size_document' => Storage::size($urlDocument),
            'size_mime_type' => Storage::mimeType($urlDocument),
            'qr_code' => $toss->qr_path,
            'statusDoc' => $statusDoc,
            'infos' => ((new Info)->getInfo()),
        ]);
    }
}
