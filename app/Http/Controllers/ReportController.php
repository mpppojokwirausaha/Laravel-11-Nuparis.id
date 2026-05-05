<?php

namespace App\Http\Controllers;

use App\Filament\Pages\Report as ReportPage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    /**
     * Download PDF laporan.
     *
     * Dipanggil dari route: GET /report/download?key={cacheKey}
     */
    public function download(Request $request): Response
    {
        // Autentikasi: hanya user yang login
        if (! auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $key = $request->query('key');

        if (empty($key)) {
            abort(400, 'Cache key tidak ditemukan.');
        }

        // Pastikan key milik user yang sedang login (format: report_data_{userId}_...)
        $expectedPrefix = 'report_data_' . auth()->id() . '_';
        if (! str_starts_with($key, $expectedPrefix)) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil data dari cache
        $summary = Cache::get($key . '_summary', []);
        $data    = Cache::get($key . '_data', []);
        $notes   = Cache::get($key . '_notes', '');

        // Fallback ke format lama jika tidak ditemukan
        if (empty($summary) && empty($data)) {
            $reportData = Cache::get($key, []);
            $summary = $reportData['summary'] ?? [];
            $data    = $reportData['data'] ?? [];
        }

        if (empty($summary) && empty($data)) {
            abort(404, 'Sesi laporan telah habis atau tidak ditemukan. Silakan generate ulang laporan.');
        }

        // Render PDF menggunakan static method dari Report Page
        try {
            $pdfContent = ReportPage::renderPdfStatic($summary, $data, $notes);
        } catch (\Exception $e) {
            Log::error('Error rendering PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Gagal membuat PDF: ' . $e->getMessage());
        }

        // Hapus cache notes setelah digunakan
        Cache::forget($key . '_notes');
        Cache::forget($key . '_summary');
        Cache::forget($key . '_data');

        // Buat nama file
        $clientName = $summary['client_name'] ?? 'laporan';
        $clientName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $clientName);
        $date       = now()->format('Ymd_His');
        $filename   = "laporan_progress_{$clientName}_{$date}.pdf";

        // Return download response
        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($pdfContent),
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ]);
    }
}
