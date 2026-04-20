<?php

namespace App\Http\Controllers;

use App\Models\ConsultantSpecialization;
use App\Models\Info;
use App\Models\Ticket;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class TicketController extends Controller
{
    public function index()
    {
        return view('front-end.ticket.index', [
            'title' => 'Ticket | ' . config('app.name'),
            'tickets' => null,
            'bidang' => (new ConsultantSpecialization())->getConsultantSpecialization(),
            'infos' => ((new Info)->getInfo()),
        ]);
    }

    public function getBidang(Request $request)
    {
        try {
            $bidang = (new ConsultantSpecialization())->getConsultantSpecialization();
            return response()->json([
                'success' => true,
                'bidang' => $bidang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data bidang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_name_client'             => 'required|string|max:255',
            'ticket_title'                   => 'required|string|max:255',
            'ticket_whatsapp'                => 'required|string|max:20',
            'ticket_email'                   => 'required|email|max:255',
            'ticket_content'                 => 'required|string',
            'ticket_document_support'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'consultant_specialization_uuid' => 'required|exists:consultant_specializations,uuid',
        ]);

        Log::info('TicketController@store - Data validated successfully');

        $ticketCode = 'nuparis-' . Carbon::now()->format('ymdHi');

        // Handle file upload
        if ($request->hasFile('ticket_document_support')) {
            $file = $request->file('ticket_document_support');
            $filename = $ticketCode . '_initial_report_client.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tickets/' . $ticketCode, $filename, 'public');
            $validated['ticket_document_support'] = $path;
        } else {
            $validated['ticket_document_support'] = null;
            Log::info('TicketController@store - No file uploaded');
        }

        $validated['ticket_code'] = $ticketCode;

        $result = Ticket::createTicket($validated);

        // Gagal buat tiket
        if (!$result['success']) {
            Log::error('TicketController@store - Failed to create ticket', [
                'error_message' => $result['message'],
                'ticket_code'   => $ticketCode,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal membuat tiket.',
                ], 422);
            }

            return back()
                ->withErrors(['error' => $result['message']])
                ->withInput();
        }

        // Send email notification to client
        try {
            Notification::route('mail', $result['data']->ticket_email)
                ->notify(new TicketCreatedNotification($result['data']));

            Log::info('TicketCreatedNotification sent successfully', [
                'ticket_code' => $result['data']->ticket_code,
                'email'       => $result['data']->ticket_email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send TicketCreatedNotification', [
                'ticket_code' => $result['data']->ticket_code,
                'email'       => $result['data']->ticket_email,
                'error'       => $e->getMessage(),
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil dibuat.',
                'ticket'  => [
                    'code' => $result['data']->ticket_code ?? $ticketCode,
                    'uuid' => $result['data']->uuid ?? null,
                ],
                'data' => $result['data'],
            ], 200);
        }

        return redirect()
            ->route('tickets.show', $result['data']->uuid)
            ->with('success', 'Ticket berhasil dibuat dengan kode: ' . $ticketCode);
    }

    public function detail(Request $request)
    {
        $ticket = Ticket::where('ticket_code', $request->ticket_code)->first();

        return view('front-end.ticket.ticket-detail', [
            'title'          => 'Ticket | ' . config('app.name'),
            'ticket'         => $ticket,
            'ticketProgress' => $ticket->ticket_progress ?? [],
            'bidang'         => (new ConsultantSpecialization())->getConsultantSpecialization(),
            'infos'          => (new Info)->getInfo(),
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required'
        ]);

        $tickets = Ticket::searchTicket($request->ticket_code);

        return response()->json([
            'success' => true,
            'tickets' => $tickets,
        ]);
    }
}
