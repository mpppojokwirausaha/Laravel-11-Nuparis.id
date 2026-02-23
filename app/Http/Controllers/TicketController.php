<?php

namespace App\Http\Controllers;

use App\Models\ConsultantSpecialization;
use App\Models\Info;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'ticket_title' => 'required|string|max:255',
            'ticket_whatsapp' => 'required|string|max:20',
            'ticket_email' => 'required|email|max:255',
            'ticket_content' => 'required|string',
            'ticket_document_support' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'consultant_specialization_uuid' => 'required|exists:consultant_specializations,uuid', // ← Tambahkan exists
        ]);
    
        $ticketCode = 'nuparis-' . Carbon::now()->format('ymdHi');
        
        // Handle file upload
        if ($request->hasFile('ticket_document_support')) {
            $file = $request->file('ticket_document_support');
            $filename = $ticketCode . '_initial_report_client.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tickets/' . $ticketCode, $filename, 'public'); // ← Tambahkan disk
            $validated['ticket_document_support'] = $path;
        } else {
            $validated['ticket_document_support'] = null;
        }
        
        $validated['ticket_code'] = $ticketCode;
        
        \Log::info('Validated data before creating ticket:', $validated);
        
        // Panggil method createTicket
        $result = Ticket::createTicket($validated);
        
        // Handle response
        if (!$result['success']) {
            \Log::error('Failed to create ticket', ['error' => $result['message']]);
            return back()
                ->withErrors(['error' => $result['message']])
                ->withInput();
        }
        
        \Log::info('Ticket created successfully', ['ticket_code' => $ticketCode]);
        
        // Jika API request
        if ($request->expectsJson()) {
            return response()->json($result, 200);
        }
        
        // Jika web request
        return redirect()
            ->route('tickets.show', $result['data']->uuid) // atau route lain
            ->with('success', 'Ticket berhasil dibuat dengan kode: ' . $ticketCode);
    }
    
     public function detail(Request $request)
    {
        $ticket = Ticket::where('ticket_code', $request->ticket_code)->first();

        return view('front-end.ticket.ticket-detail', [
            'title' => 'Ticket | ' . config('app.name'),
            'ticket' => $ticket,
            'ticketProgress' => $ticket->ticket_progress ?? [],
            'bidang' => (new ConsultantSpecialization())->getConsultantSpecialization(),
            'infos' => (new Info)->getInfo(),
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
