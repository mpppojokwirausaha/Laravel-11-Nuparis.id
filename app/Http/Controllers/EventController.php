<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Info;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function event()
    {
        return view('front-end.event-more', [
            'title' => 'Event | ' . config('app.name'),
            'infos' => (new Info())->getInfo(),
        ]);
    }

    public function eventData()
    {
        try {
            $events = Event::all();

            return response()->json([
                'success' => true,
                'eventData' => $events->toArray(),
                'total' => $events->count()
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            \Log::error('Event API error: ' . $e->getMessage(), [
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
        return view('front-end.event-detail', [
            'title' => 'Event | ' . config('app.name'),
            'infos' => ((new Info)->getInfo()),
            'event' => (new Event())->getEventDetail($slug),
        ]);
    }

}
