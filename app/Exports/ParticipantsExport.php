<?php

namespace App\Exports;

use App\Models\EventParticipant;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParticipantsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $eventUuid;

    public function __construct($eventUuid)
    {
        $this->eventUuid = $eventUuid;
    }

    public function query()
    {
        return EventParticipant::where('event_uuid', $this->eventUuid)
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Email',
            'Nomor WhatsApp',
            'Perusahaan',
            'Sumber Informasi',
            'Kode Tiket',
            'Status Check-in',
            'Waktu Check-in',
            'Tanggal Registrasi',
        ];
    }

    public function map($participant): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $participant->participant_name,
            $participant->participant_email,
            $participant->participant_no_wa,
            $participant->company ?? '-',
            $participant->source ?? '-',
            $participant->ticket_code,
            $participant->checked_in_at ? 'Sudah Check-in' : 'Belum Check-in',
            $participant->checked_in_at ? date('d/m/Y H:i', strtotime($participant->checked_in_at)) : '-',
            date('d/m/Y H:i', strtotime($participant->created_at)),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
