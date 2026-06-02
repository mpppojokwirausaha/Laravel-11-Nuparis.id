<?php

namespace App\Exports;

use App\Models\EventParticipant;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ParticipantsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithProperties
{
    protected $eventUuid;
    protected $eventName;
    protected $totalRecords;
    protected $exportDate;

    public function __construct($eventUuid, $eventName = 'Event')
    {
        $this->eventUuid = $eventUuid;
        $this->eventName = $eventName;
        $this->exportDate = now();
    }

    public function query()
    {
        return EventParticipant::where('event_uuid', $this->eventUuid)
            ->orderBy('created_at', 'desc');
    }

    public function properties(): array
    {
        return [
            'creator'        => 'nuparis.id',
            'title'          => 'Laporan Peserta ' . $this->eventName,
            'description'    => 'Export data peserta event',
            'subject'        => 'Laporan Peserta Event',
            'keywords'       => 'peserta,event,laporan,export',
            'category'       => 'Laporan',
            'manager'        => 'Admin',
            'company'        => 'nuparis.id',
        ];
    }

    public function title(): string
    {
        return 'Peserta ' . $this->eventName;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'EMAIL',
            'NOMOR WHATSAPP',
            'PERUSAHAAN',
            'SUMBER INFORMASI',
            'KODE TIKET',
            'STATUS CHECK-IN',
            'WAKTU CHECK-IN',
            'TANGGAL REGISTRASI',
        ];
    }

    public function map($participant): array
    {
        static $row = 0;
        $row++;

        // Format nomor WhatsApp - HAPUS '+' dan hanya ambil angka
        $whatsapp = $participant->participant_no_wa ?? '';
        // Hapus karakter '+' dan karakter non-digit lainnya
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsapp);
        // Jika kosong tampilkan '-', jika ada tapi 0 tampilkan '0'
        $whatsappDisplay = !empty($whatsappNumber) ? $whatsappNumber : '-';

        // Status Check-in
        $checkinStatus = $participant->checked_in_at ? 'SUDAH CHECK-IN' : 'BELUM CHECK-IN';

        // Waktu Check-in
        $checkinTime = '-';
        if ($participant->checked_in_at) {
            $checkinTime = date('d/m/Y H:i:s', strtotime($participant->checked_in_at));
        }

        return [
            $row,
            $participant->participant_name ?? '-',
            $participant->participant_email ?? '-',
            $whatsappDisplay,
            $participant->company ?? '-',
            $participant->source ?? '-',
            $participant->ticket_code ?? '-',
            $checkinStatus,
            $checkinTime,
            $participant->created_at ? date('d/m/Y H:i:s', strtotime($participant->created_at)) : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 30,  // NAMA LENGKAP
            'C' => 35,  // EMAIL
            'D' => 20,  // NOMOR WHATSAPP
            'E' => 30,  // PERUSAHAAN
            'F' => 25,  // SUMBER INFORMASI
            'G' => 20,  // KODE TIKET
            'H' => 18,  // STATUS CHECK-IN
            'I' => 22,  // WAKTU CHECK-IN
            'J' => 22,  // TANGGAL REGISTRASI
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Dapatkan total records
        $totalRecords = EventParticipant::where('event_uuid', $this->eventUuid)->count();
        $lastRow = $totalRecords + 1;
        $lastColumn = 'J';

        // ==========================================
        // HEADER STYLE - WARNA NAVY
        // ==========================================
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Arial',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1B2A4A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        // ==========================================
        // DATA ROWS BORDER
        // ==========================================
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ==========================================
        // STYLE NO (Center, Bold)
        // ==========================================
        $sheet->getStyle('A2:A' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => ['bold' => true],
        ]);

        // ==========================================
        // STYLE NOMOR WHATSAPP SEBAGAI NUMBER
        // ==========================================
        $sheet->getStyle('D2:D' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('0');

        $sheet->getStyle('D2:D' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // ==========================================
        // WRAP TEXT UNTUK BEBERAPA KOLOM
        // ==========================================
        $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setWrapText(true);  // Nama
        $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setWrapText(true);  // Email
        $sheet->getStyle('E2:E' . $lastRow)->getAlignment()->setWrapText(true);  // Perusahaan
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setWrapText(true);  // Sumber Informasi

        // ==========================================
        // STYLE EMAIL (left alignment)
        // ==========================================
        $sheet->getStyle('C2:C' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // ==========================================
        // ZEBRA STRIPING
        // ==========================================
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':' . $lastColumn . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9F9F9'],
                    ],
                ]);
            }
        }

        // ==========================================
        // STATUS CHECK-IN COLOR
        // SUDAH CHECK-IN = GREEN (28A745) - text putih
        // BELUM CHECK-IN = ORANGE (FD7E14) - text putih
        // ==========================================
        for ($i = 2; $i <= $lastRow; $i++) {
            $status = (string) $sheet->getCell('H' . $i)->getValue();

            if ($status === 'SUDAH CHECK-IN') {
                $sheet->getStyle('H' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '28A745'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            } elseif ($status === 'BELUM CHECK-IN') {
                $sheet->getStyle('H' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FD7E14'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
        }

        // ==========================================
        // STYLE KODE TIKET (Center)
        // ==========================================
        $sheet->getStyle('G2:G' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // ==========================================
        // STYLE WAKTU CHECK-IN & TANGGAL REGISTRASI (Center)
        // ==========================================
        $sheet->getStyle('I2:I' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('J2:J' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // ==========================================
        // FREEZE PANE (Freeze header row)
        // ==========================================
        $sheet->freezePane('A2');

        // ==========================================
        // PAGE SETUP (Landscape)
        // ==========================================
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setPrintArea('A1:' . $lastColumn . $lastRow);

        return [];
    }
}
