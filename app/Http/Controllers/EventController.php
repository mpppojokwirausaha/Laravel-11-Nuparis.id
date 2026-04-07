<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Info;
use App\Models\Order;
use App\Models\Partner;
use Illuminate\Support\Facades\Log;

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
        // Load event dengan count participants menggunakan withCount
        $event = Event::withCount('participants')->where('event_slug', $slug)->first();

        if (!$event) {
            abort(404, 'Event tidak ditemukan');
        }

        // Data dari withCount akan tersedia di $event->participants_count
        $registeredCount = $event->participants_count ?? 0;

        // Hitung sisa kuota
        $remainingQuota = $event->event_quota ? max($event->event_quota - $registeredCount, 0) : null;

        // Cek apakah pendaftaran aktif (event_is_active = true)
        $isRegistrationActive = (bool) $event->event_is_active;

        // Cek apakah kuota masih tersedia
        $isQuotaAvailable = $remainingQuota === null || $remainingQuota > 0;

        // Tombol pendaftaran aktif jika event aktif DAN kuota tersedia
        $canRegister = $isRegistrationActive && $isQuotaAvailable;

        return view('front-end.event-detail', [
            'title' => 'Event | ' . config('app.name'),
            'infos' => (new Info)->getInfo(),
            'agencies_footer' => (new Partner())->getAgencies(),
            'event' => $event,
            'registeredCount' => $registeredCount,
            'remainingQuota' => $remainingQuota,
            'canRegister' => $canRegister,
            'isRegistrationActive' => $isRegistrationActive,
        ]);
    }
}
