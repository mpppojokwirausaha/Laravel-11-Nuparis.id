<?php

namespace App\Http\Controllers\Dashboard\Management;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('dashboard.management.event.index', [
            'title' => 'Event | ' . config('app.name'),
        ]);
    }

    public function create()
    {
        return view('dashboard.management.event.create', [
            'title' => 'Create Event | ' . config('app.name'),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Event $event)
    {
        //
    }

    public function edit(Event $event)
    {
        return view('dashboard.management.event.edit', [
            'title' => 'Edit Event | ' . config('app.name'),
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        //
    }

    public function destroy(Event $event)
    {
        //
    }
}
