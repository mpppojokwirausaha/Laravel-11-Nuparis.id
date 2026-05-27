<?php

namespace App\Exports;

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

class TicketsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithProperties
{
    protected $query;
    protected $displayFilterText;
    protected $totalRecords;
    protected $exportDate;
    protected $showContent;

    public function __construct($query, $displayFilterText = 'Semua Data', $showContent = false)
    {
        $this->query = $query;
        $this->displayFilterText = $displayFilterText;
        $this->totalRecords = $query->count();
        $this->exportDate = now();
        $this->showContent = $showContent;
    }

    public function properties(): array
    {
        return [
            'creator'        => 'nuparis.id',
            'title'          => 'Laporan Data Tiket',
            'description'    => 'Export data tiket',
            'subject'        => 'Laporan Tiket',
            'keywords'       => 'tiket,laporan,export',
            'category'       => 'Laporan',
            'manager'        => 'Admin',
            'company'        => 'nuparis.id',
        ];
    }

    public function query()
    {
        return $this->query->with(['ticketStatus', 'consultantSpecialization']);
    }

    public function title(): string
    {
        return 'Laporan Tiket';
    }

    public function headings(): array
    {
        $headings = [
            'NO',
            'KODE TIKET',
            'JUDUL TIKET',
            'NAMA CLIENT',
        ];

        if ($this->showContent) {
            $headings[] = 'KONTEN TIKET';
        }

        $headings = array_merge($headings, [
            'WHATSAPP',
            'EMAIL',
            'SPESIALISASI',
            'STATUS',
            'TANGGAL DIBUAT',
            'TANGGAL DIUPDATE',
        ]);

        return $headings;
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $whatsapp = $row->ticket_whatsapp;
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsapp);
        $whatsappNumber = !empty($whatsappNumber) ? (int)$whatsappNumber : 0;

        $data = [
            $no,
            $row->ticket_code,
            $row->ticket_title,
            $row->ticket_name_client,
        ];

        if ($this->showContent) {
            $data[] = strip_tags($row->ticket_content);
        }

        $data = array_merge($data, [
            $whatsappNumber,
            $row->ticket_email,
            $row->consultantSpecialization?->consultant_specialization_name ?? '-',
            $row->ticketStatus?->ticket_status_name ?? '-',
            $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '-',
            $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '-',
        ]);

        return $data;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 6,
            'B' => 22,
            'C' => 40,
            'D' => 30,
        ];

        if ($this->showContent) {
            $widths['E'] = 55;
            $widths['F'] = 18;
            $widths['G'] = 30;
            $widths['H'] = 25;
            $widths['I'] = 15;
            $widths['J'] = 22;
            $widths['K'] = 22;
        } else {
            $widths['E'] = 18;
            $widths['F'] = 30;
            $widths['G'] = 25;
            $widths['H'] = 15;
            $widths['I'] = 22;
            $widths['J'] = 22;
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->totalRecords + 1;
        $columnCount = $this->showContent ? 11 : 10;
        $lastColumn = chr(64 + $columnCount);

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
                'vertical' => Alignment::VERTICAL_TOP,
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
        // STYLE KODE TIKET (Center)
        // ==========================================
        $sheet->getStyle('B2:B' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // ==========================================
        // STYLE WHATSAPP SEBAGAI NUMBER
        // ==========================================
        $whatsappColumn = $this->showContent ? 'F' : 'E';
        $sheet->getStyle($whatsappColumn . '2:' . $whatsappColumn . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('0');

        $sheet->getStyle($whatsappColumn . '2:' . $whatsappColumn . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // ==========================================
        // ZEBRA STRIPING DULU sebelum status coloring
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
        // STATUS COLOR (setelah zebra, agar tidak tertimpa)
        // OPEN    = GREEN  (28A745) - text putih
        // PENDING = YELLOW (FFC107) - text hitam
        // CLOSE   = GRAY   (6C757D) - text putih
        // ==========================================
        $statusColumn = $this->showContent ? 'I' : 'H';

        for ($i = 2; $i <= $lastRow; $i++) {
            $status = strtolower((string) $sheet->getCell($statusColumn . $i)->getValue());

            $color = match ($status) {
                'open'    => '28A745',
                'pending' => 'FFC107',
                'close'   => '6C757D',
                default   => 'FFFFFF',
            };

            $textColor = match ($status) {
                'open', 'close' => 'FFFFFF',
                'pending'       => '333333',
                default         => '333333',
            };

            $sheet->getStyle($statusColumn . $i)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => $textColor],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // ==========================================
        // WRAP TEXT UNTUK KONTEN (jika ditampilkan)
        // ==========================================
        if ($this->showContent) {
            $sheet->getStyle('E2:E' . $lastRow)->getAlignment()->setWrapText(true);
        }

        // ==========================================
        // HIDE KOLOM TANGGAL DIUPDATE
        // ==========================================
        $updatedColumn = $this->showContent ? 'K' : 'J';
        $sheet->getColumnDimension($updatedColumn)->setVisible(false);

        // ==========================================
        // FREEZE PANE
        // ==========================================
        $sheet->freezePane('A2');

        // ==========================================
        // PAGE SETUP (Landscape)
        // ==========================================
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);

        return [];
    }
}
