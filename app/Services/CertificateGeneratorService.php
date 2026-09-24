<?php

namespace App\Services;

use App\Imports\CertificateCsvImport;
use App\Models\CertificateGenerate;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;

class CertificateGeneratorService
{
    /**
     * Batas jumlah baris yang diproses dari file CSV/Excel yang diupload.
     */
    public const MAX_ROWS = 5;

    /**
     * @return array{generate: CertificateGenerate, url: string|null, failedRows: array}
     */
    public function generate(CertificateTemplate $template, string $mode, array $manualRow = [], ?string $csvPath = null): array
    {
        $rows = $mode === 'manual'
            ? [$this->normalizeRow($manualRow)]
            : $this->readCsv($csvPath);

        $folder = 'certificates/generate_' . now()->format('Ymd_His');
        $files = [];
        $failedRows = [];

        foreach ($rows as $index => $row) {
            try {
                $files[] = $this->generateOnePdf($template, $row, $folder, $index);
            } catch (\Throwable $e) {
                $failedRows[] = ($row['nama'] ?? 'Baris ' . ($index + 1)) . ': ' . $e->getMessage();
            }
        }

        $filePath = null;
        if (! empty($files)) {
            $filePath = count($files) === 1 ? $files[0] : $this->zipFiles($files, $folder);
        }

        $generate = CertificateGenerate::create([
            'certificate_template_id' => $template->uuid,
            'mode' => $mode,
            'total_requested' => count($rows),
            'total_success' => count($files),
            'total_failed' => count($failedRows),
            'failed_detail' => $failedRows ? implode('; ', $failedRows) : null,
            'file_path' => $filePath,
        ]);

        return [
            'generate' => $generate,
            'url' => $filePath ? Storage::disk('public')->url($filePath) : null,
            'failedRows' => $failedRows,
        ];
    }

    protected function normalizeRow(array $row): array
    {
        return [
            'nama' => $row['nama'] ?? '',
            'keterangan' => $row['keterangan'] ?? '',
            'tempat' => $row['tempat'] ?? '',
            'tanggal' => $row['tanggal'] ?? '',
            'tahun' => $row['tahun'] ?? '',
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function readCsv(string $path): array
    {
        $import = new CertificateCsvImport();
        Excel::import($import, $path);

        if (empty($import->rows)) {
            throw new \RuntimeException('File kosong atau format tidak dikenali. Pastikan baris pertama adalah header kolom.');
        }

        return array_slice($import->rows, 0, self::MAX_ROWS);
    }

    /**
     * Import halaman pertama PDF template pakai FPDI, lalu gambar teks & QR code
     * langsung di atasnya sesuai posisi yang sudah ditandai. Tidak ada rasterisasi
     * (tidak butuh Imagick/Ghostscript) — hasilnya tetap PDF vector asli.
     */
    protected function generateOnePdf(CertificateTemplate $template, array $row, string $folder, int $index): string
    {
        $templatePath = Storage::disk('public')->path($template->background_image);

        $pdf = new Fpdi('P', 'pt');
        $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);

        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);

        foreach ($template->fields as $field) {
            // posisi disimpan dalam % (0-100), konversi ke koordinat pt sesuai ukuran halaman asli
            $x = ($field->x / 100) * $size['width'];
            $y = ($field->y / 100) * $size['height'];

            if ($field->field_key === 'barcode') {
                $code = $row['nama'] ?? null;
                $code = $code ? Str::slug($code) . '-' . Str::random(6) : Str::random(10);

                $qrBinary = QrCode::format('png')->size((int) $field->font_size)->generate($code);

                $tmpQrPath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
                file_put_contents($tmpQrPath, $qrBinary);

                $qrSize = (int) $field->font_size;
                $pdf->Image($tmpQrPath, $x - ($qrSize / 2), $y - ($qrSize / 2), $qrSize, $qrSize);

                @unlink($tmpQrPath);
                continue;
            }

            $value = (string) ($row[$field->field_key] ?? '');
            [$r, $g, $b] = $this->hexToRgb($field->font_color);

            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('Helvetica', '', (int) $field->font_size);

            $textWidth = $pdf->GetStringWidth($value);
            $pdf->SetXY($x - ($textWidth / 2), $y - ($field->font_size / 2));
            $pdf->Cell($textWidth, (int) $field->font_size, $value);
        }

        $name = $row['nama'] ?? ('sertifikat_' . ($index + 1));
        $filename = $folder . '/' . Str::slug($name) . '.pdf';

        Storage::disk('public')->makeDirectory($folder);
        $pdf->Output(Storage::disk('public')->path($filename), 'F');

        return $filename;
    }

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    protected function zipFiles(array $files, string $folder): string
    {
        $zipRelative = "{$folder}/sertifikat.zip";
        $zipFull = Storage::disk('public')->path($zipRelative);

        $zip = new ZipArchive();
        $zip->open($zipFull, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($files as $file) {
            $zip->addFile(Storage::disk('public')->path($file), basename($file));
        }

        $zip->close();

        return $zipRelative;
    }
}
