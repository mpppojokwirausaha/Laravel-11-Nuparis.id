<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Info;
use App\Models\Order;
use App\Models\Partner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function event()
    {
        return view('front-end.event-more', [
            'title' => 'Event | ' . config('app.name'),
            'infos' => (new Info())->getInfo(),
            'agencies_footer' => (new Partner())->getAgencies(),
        ]);
    }

    public function eventData()
    {
        try {
            $events = Event::withCount('participants')->orderBy('event_date_start', 'desc')->get();
            $paidStatuses = ['paid', 'success'];

            $orderCounts = Order::query()
                ->selectRaw('order_reference_uuid, COUNT(*) as total')
                ->where('order_reference_type', 'event')
                ->whereIn('order_transaction_status', $paidStatuses)
                ->groupBy('order_reference_uuid')
                ->pluck('total', 'order_reference_uuid');

            $eventData = $events->map(function ($event) use ($orderCounts) {
                $orderRegistered = (int) ($orderCounts[$event->uuid] ?? 0);
                $participantRegistered = (int) ($event->participants_count ?? 0);
                $registeredCount = max($orderRegistered, $participantRegistered);

                $item = $event->toArray();
                $item['registeredCount'] = $registeredCount;
                $item['event_registered'] = $registeredCount;

                return $item;
            });

            return response()->json([
                'success' => true,
                'eventData' => $eventData,
                'total' => $eventData->count()
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            Log::error('Event API error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage(),
                'eventData' => []
            ], 500);
        }
    }

    public function eventDetail($slug)
    {
        $event = Event::with('eventCategory')
            ->withCount('participants')
            ->where('event_slug', $slug)
            ->first();

        if (!$event) {
            abort(404, 'Event tidak ditemukan');
        }

        if ($event->event_description) {
            $event->event_description = $this->cleanTrixContent($event->event_description);
        }

        $registeredCount = $event->participants_count ?? 0;
        $remainingQuota = $event->event_quota ? max($event->event_quota - $registeredCount, 0) : null;

        $isRegistrationActive = (bool) $event->event_is_active;
        $isQuotaAvailable = $remainingQuota === null || $remainingQuota > 0;
        $isEventEnded = $event->event_date_end ? now()->gt($event->event_date_end) : false;

        $canRegister = $isRegistrationActive && $isQuotaAvailable && !$isEventEnded;

        // ==== SEO ====
        $seoDescription = Str::limit(strip_tags($event->event_description), 160);
        $seoImage = $event->event_image ? Storage::disk('public')->url($event->event_image) : null;
        $fullTitle = $event->event_title . ' | ' . config('app.name');
        $canonicalUrl = route('event-detail', $event->event_slug);

        // event_price adalah varchar, bisa berisi "Gratis", "Rp 50.000", dll — bersihkan jadi angka
        $numericPrice = preg_replace('/[^0-9]/', '', (string) $event->event_price);
        $isFreePrice = $numericPrice === '' || (int) $numericPrice === 0
            || stripos($event->event_price, 'gratis') !== false
            || stripos($event->event_price, 'free') !== false;

        $eventStatus = 'https://schema.org/EventScheduled';

        // Tentukan mode kehadiran berdasarkan kombinasi link online & lokasi fisik
        $hasOnlineLink = !empty($event->event_link);
        $hasPhysicalLocation = !empty($event->event_location);

        if ($hasOnlineLink && $hasPhysicalLocation) {
            $attendanceMode = 'https://schema.org/MixedEventAttendanceMode';
        } elseif ($hasOnlineLink) {
            $attendanceMode = 'https://schema.org/OnlineEventAttendanceMode';
        } else {
            $attendanceMode = 'https://schema.org/OfflineEventAttendanceMode';
        }

        $jsonld = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->event_title,
            'description' => $seoDescription,
            'startDate' => $event->event_date_start?->toIso8601String(),
            'eventStatus' => $eventStatus,
            'eventAttendanceMode' => $attendanceMode,
            'organizer' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => url('/'),
            ],
        ];

        if ($hasPhysicalLocation) {
            $jsonld['location'] = [
                '@type' => 'Place',
                'name' => $event->event_location,
                'address' => $event->event_location,
            ];
        }

        if ($hasOnlineLink) {
            $virtualLocation = [
                '@type' => 'VirtualLocation',
                'url' => $event->event_link,
            ];

            $jsonld['location'] = $hasPhysicalLocation
                ? [$jsonld['location'], $virtualLocation]
                : $virtualLocation;
        }

        // FIX: hapus 'return' — sebelumnya bikin function berhenti di sini
        if ($event->event_date_end) {
            $jsonld['endDate'] = $event->event_date_end->toIso8601String();
        }

        if ($seoImage) {
            $jsonld['image'] = [$seoImage];
        }

        if ($isFreePrice || $numericPrice !== '') {
            $jsonld['offers'] = [
                '@type' => 'Offer',
                'price' => $isFreePrice ? 0 : (int) $numericPrice,
                'priceCurrency' => 'IDR',
                'availability' => $isQuotaAvailable
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/SoldOut',
                'url' => $canonicalUrl,
                'priceValidUntil' => ($event->event_date_end ?? $event->event_date_start)?->toDateString(),
            ];
        }

        return view('front-end.event-detail', [
            'title' => $fullTitle,
            'infos' => (new Info)->getInfo(),
            'agencies_footer' => (new Partner())->getAgencies(),
            'event' => $event,
            'registeredCount' => $registeredCount,
            'remainingQuota' => $remainingQuota,
            'canRegister' => $canRegister,
            'isRegistrationActive' => $isRegistrationActive,
            'isEventEnded' => $isEventEnded,

            // ==== SEO ====
            'seo_title' => $fullTitle,
            'seo_description' => $seoDescription,
            'seo_image' => $seoImage,
            'seo_type' => 'website',
            'canonical_url' => $canonicalUrl,
            'jsonld' => $jsonld,
            'feed_url' => route('feeds.event'),
            'feed_title' => 'Event Terbaru - ' . config('app.name'),
        ]);
    }

    private function cleanTrixContent($content)
    {
        if (empty($content)) return $content;

        // Pattern 1: Hapus <figure> dan <a> wrapper, sisakan <img> saja
        $pattern1 = '/<figure[^>]*data-trix-attachment[^>]*>.*?<a[^>]*>(<img[^>]*>).*?<\/a>.*?<\/figure>/is';
        $content = preg_replace($pattern1, '$1', $content);

        // Pattern 2: Hapus semua <figcaption> dan isinya
        $pattern2 = '/<figcaption[^>]*>.*?<\/figcaption>/is';
        $content = preg_replace($pattern2, '', $content);

        // Pattern 3: Hapus semua atribut data-trix-*
        $pattern3 = '/\sdata-trix-[\w-]+="[^"]*"/i';
        $content = preg_replace($pattern3, '', $content);

        // Pattern 4: Hapus <figure> kosong yang mungkin tersisa (tanpa data-trix-attachment)
        $pattern4 = '/<figure[^>]*>\s*<\/figure>/is';
        $content = preg_replace($pattern4, '', $content);

        // Pattern 5: Hapus class attachment__caption dan sejenisnya (opsional)
        $pattern5 = '/\sclass="attachment__[^"]*"/i';
        $content = preg_replace($pattern5, '', $content);

        return $content;
    }
}
