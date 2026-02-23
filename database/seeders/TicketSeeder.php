<?php

namespace Database\Seeders;

use App\Models\ConsultantSpecialization;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::create([
            'ticket_code' => 'nuparis-' . '2508040719',
            'ticket_title' => 'Test Ticket',
            'ticket_whatsapp' => '6281234567890',
            'ticket_email' => 'BqgHg@example.com',
            'ticket_content' => 'This is a test ticket.',
            'consultant_specialization_uuid' => ConsultantSpecialization::first()->uuid,
            'ticket_progress' => json_encode([
                [
                    "file" => [
                        "tickets/nuparis-2508040719"
                    ],
                    "progress" => "<p>PO dikirim kepada:<br>\"aamboro@tuv-nord.com\" &lt;aamboro@tuv-nord.com&gt;; \"dchen@tuv-nord.com\" &lt;dchen@tuv-nord.com&gt;; \"Riksaciptasinergi@gmail.com\" &lt;Riksaciptasinergi@gmail.com&gt;<br><br><br>catatan:<br>yusup monitor email kantor nya</p>",
                    "timestamp" => "2025-08-05 11:27:32"
                ]
            ])
        ]);
    }
}
