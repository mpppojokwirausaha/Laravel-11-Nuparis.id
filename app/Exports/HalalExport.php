<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HalalExport extends DefaultValueBinder implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    WithColumnWidths,
    WithCustomValueBinder
{
    // Kolom yang dipaksa jadi teks murni (bukan angka), biar 0 di depan tidak
    // hilang dan tidak berubah jadi notasi ilmiah.
    // A = No (angka biasa, tidak masuk sini)
    protected array $textColumns = ['B', 'C', 'D', 'E', 'F', 'H', 'I', 'K', 'L', 'M', 'N', 'O'];

    protected int $number = 0;

    public function __construct(protected Collection $halals) {}

    public function collection(): Collection
    {
        return $this->halals;
    }

    public function headings(): array
    {
        return [
            'No',                  // A
            'Nama Pelaku',         // B - text
            'Nama Brand',          // C - text
            'NIK KTP',             // D - text
            'NIB',                 // E - text
            'NPWP',                // F - text
            'Modal Awal',          // G - nominal (Rp)
            'Tahun Berdiri',       // H - text
            'Luas Usaha',          // I - text
            'Pendapatan/Minggu',   // J - nominal (Rp)
            'No. WhatsApp',        // K - text
            'Email',               // L - text
            'Alamat',              // M - text
            'Bahan',               // N - text
            'Cara Pembuatan',      // O - text
            'Tanggal Pengajuan',   // P - date
        ];
    }

    public function map($halal): array
    {
        $this->number++;

        return [
            $this->number,
            $halal->nama_pelaku,
            $halal->nama_brand,
            $halal->nik_ktp,
            $halal->nib,
            $halal->npwp,
            $halal->modal_awal,
            $halal->tahun_berdiri,
            $halal->luas_usaha,
            $halal->pendapatan_minggu,
            $halal->no_whatsapp,
            $halal->email,
            $halal->alamat,
            strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", (string) $halal->bahan)),
            strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", (string) $halal->cara_pembuatan)),
            $halal->created_at ? Date::PHPToExcel($halal->created_at) : null,
        ];
    }

    // Memaksa kolom di $textColumns ditulis sebagai string murni di cell Excel.
    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), $this->textColumns, true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    // Format tampilan cell: nominal Rp untuk Modal Awal & Pendapatan/Minggu,
    // format tanggal untuk Tanggal Pengajuan.
    public function columnFormats(): array
    {
        return [
            'G' => '"Rp" #,##0',
            'J' => '"Rp" #,##0',
            'P' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    // Lebar kolom manual (bukan autosize) biar kolom teks panjang tidak melebar berlebihan
    // dan cocok dipakai bareng wrap text.
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 20,
            'D' => 18,
            'E' => 16,
            'F' => 18,
            'G' => 15,
            'H' => 14,
            'I' => 12,
            'J' => 16,
            'K' => 16,
            'L' => 22,
            'M' => 30,
            'N' => 35,
            'O' => 40,
            'P' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        // Semua cell: rata kiri, wrap text, vertical top
        $sheet->getStyle("A1:P{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        // Kolom "No" biar rapi di tengah
        $sheet->getStyle("A1:A{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Freeze header row biar tetap kelihatan saat scroll
        $sheet->freezePane('A2');

        return [
            1 => [
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }
}
