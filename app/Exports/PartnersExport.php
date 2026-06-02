<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
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
use Illuminate\Support\Facades\Storage;

class PartnersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithProperties
{
    protected $partners;
    protected $startNumber;
    protected $totalRecords;
    protected $exportDate;
    protected $displayFilterText;

    public function __construct($partners, $startNumber = 1, $displayFilterText = 'Semua Data')
    {
        $this->partners = $partners;
        $this->startNumber = $startNumber;
        $this->totalRecords = $partners->count();
        $this->exportDate = now();
        $this->displayFilterText = $displayFilterText;
    }

    public function properties(): array
    {
        return [
            'creator'        => 'nuparis.id',
            'title'          => 'Laporan Data Mitra',
            'description'    => 'Export data mitra/partner',
            'subject'        => 'Laporan Mitra',
            'keywords'       => 'mitra,partner,laporan,export',
            'category'       => 'Laporan',
            'manager'        => 'Admin',
            'company'        => 'nuparis.id',
        ];
    }

    public function collection()
    {
        return $this->partners;
    }

    public function title(): string
    {
        return 'Laporan Mitra';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA MITRA',
            'NOMOR TELEPON',
            'EMAIL',
            'TIPE MITRA',
            'URL WEBSITE',
            'FILE NPWP',
            'FILE NIB',
            'TANGGAL DIBUAT',
        ];
    }

    /**
     * Format nomor telepon menjadi number (hapus + dan karakter non-digit)
     */
    protected function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '-';
        }

        // Hanya ambil angka (hapus +, spasi, strip, dll)
        $number = preg_replace('/[^0-9]/', '', (string) $phone);

        if (empty($number)) {
            return '-';
        }

        // Jika dimulai dengan '0', ganti dengan '62'
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        // Jika dimulai dengan '8' (tanpa 62), tambahkan 62
        if (substr($number, 0, 1) === '8' && strlen($number) <= 12) {
            $number = '62' . $number;
        }

        // Jika kurang dari 10 digit atau lebih dari 15 digit, anggap tidak valid
        if (strlen($number) < 10 || strlen($number) > 15) {
            return $phone; // return asli jika format tidak dikenal
        }

        return $number;
    }

    /**
     * Format link file
     */
    protected function formatFileLink($filePath, $type = 'file')
    {
        if (empty($filePath)) {
            return '-';
        }

        try {
            $fullUrl = url(Storage::url($filePath));

            return [
                'url' => $fullUrl,
                'display' => ($type === 'npwp' ? 'Lihat NPWP' : 'Lihat NIB')
            ];
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function map($partner): array
    {
        static $rowNumber = 0;
        if ($rowNumber === 0) {
            $rowNumber = $this->startNumber;
        }

        $currentNumber = $rowNumber;
        $rowNumber++;

        // Format nomor telepon (number, tanpa +)
        $phoneNumber = $this->formatPhoneNumber($partner->partner_phone ?? '');

        // Format tipe mitra
        $partnerType = $partner->partner_type;
        if (is_array($partnerType)) {
            $partnerType = implode(', ', $partnerType);
        } elseif (is_string($partnerType)) {
            $decoded = json_decode($partnerType, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $partnerType = implode(', ', $decoded);
            }
        }
        $partnerType = $partnerType ?: '-';

        // URL Website - tampilkan lengkap
        $websiteUrl = $partner->partner_url ?? '-';
        if (empty($websiteUrl) || $websiteUrl === '') {
            $websiteUrl = '-';
        }

        // Format NPWP
        $npwpData = $this->formatFileLink($partner->partner_NPWP ?? '', 'npwp');
        $npwpDisplay = is_array($npwpData) ? $npwpData['display'] : $npwpData;
        $npwpUrl = is_array($npwpData) ? $npwpData['url'] : null;

        // Format NIB
        $nibData = $this->formatFileLink($partner->partner_NIB ?? '', 'nib');
        $nibDisplay = is_array($nibData) ? $nibData['display'] : $nibData;
        $nibUrl = is_array($nibData) ? $nibData['url'] : null;

        // Simpan URL untuk hyperlink di styles()
        $this->npwpUrls[$currentNumber] = $npwpUrl;
        $this->nibUrls[$currentNumber] = $nibUrl;

        return [
            $currentNumber,
            $partner->partner_name ?? '-',
            $phoneNumber,
            $partner->partner_email ?? '-',
            $partnerType,
            $websiteUrl,
            $npwpDisplay,
            $nibDisplay,
            $partner->created_at ? $partner->created_at->format('d/m/Y H:i:s') : '-',
        ];
    }

    protected $npwpUrls = [];
    protected $nibUrls = [];

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 35,  // NAMA MITRA
            'C' => 20,  // NOMOR TELEPON (diperlebar sedikit)
            'D' => 45,  // EMAIL
            'E' => 18,  // TIPE MITRA
            'F' => 55,  // URL WEBSITE
            'G' => 18,  // FILE NPWP
            'H' => 18,  // FILE NIB
            'I' => 22,  // TANGGAL DIBUAT
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->totalRecords + 1;
        $lastColumn = 'I';

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
        // STYLE NOMOR TELEPON - NUMBER (bukan TEXT)
        // ==========================================
        $sheet->getStyle('C2:C' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('0');

        $sheet->getStyle('C2:C' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // ==========================================
        // WRAP TEXT UNTUK EMAIL
        // ==========================================
        $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setWrapText(true);

        // ==========================================
        // WRAP TEXT UNTUK URL WEBSITE
        // ==========================================
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setWrapText(true);

        // ==========================================
        // STYLE EMAIL (left alignment)
        // ==========================================
        $sheet->getStyle('D2:D' . $lastRow)->applyFromArray([
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
        // HYPERLINK UNTUK NPWP (Column G) DAN NIB (Column H)
        // ==========================================
        for ($i = 2; $i <= $lastRow; $i++) {
            $rowNum = $i;

            // NPWP Link
            if (isset($this->npwpUrls[$i - 1]) && $this->npwpUrls[$i - 1]) {
                $sheet->getCell('G' . $rowNum)->getHyperlink()->setUrl($this->npwpUrls[$i - 1]);
                $sheet->getStyle('G' . $rowNum)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0563C1'],
                        'underline' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            } else {
                $sheet->getStyle('G' . $rowNum)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            }

            // NIB Link
            if (isset($this->nibUrls[$i - 1]) && $this->nibUrls[$i - 1]) {
                $sheet->getCell('H' . $rowNum)->getHyperlink()->setUrl($this->nibUrls[$i - 1]);
                $sheet->getStyle('H' . $rowNum)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0563C1'],
                        'underline' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            } else {
                $sheet->getStyle('H' . $rowNum)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            }
        }

        // ==========================================
        // FREEZE PANE
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
