<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction;

class LetterController extends Controller
{

    public function index()
    {
        try {
            $letters = Letter::with('letterCategory')->get();

            if ($letters->isEmpty()) {
                return response()->json([
                    'status' => 204,
                    'message' => 'Tidak ada data surat ditemukan.',
                    'body' => []
                ], 200);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Data letters retrieved successfully.',
                'body' => $letters
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mengambil data letters.',
                // 'error' => $e->getMessage(),
                'body' => []
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            $letter = Letter::with('letterCategory')->where('letter_slug', $slug)->first();

            if (!$letter) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Surat tidak ditemukan.',
                    'body' => null
                ], 404);
            }

            return response()->json([
                'status' => 200,

                'message' => 'Data surat retrieved successfully.',
                'body' => [
                    'client_key' => Config::$clientKey ?? config('midtrans.client_key'),
                    'letters' => $letter
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mengambil data surat.',
                // 'error' => $e->getMessage(),
                'body' => null
            ], 500);
        }
    }

    public function downloadLetter($orderId, $slug)
    {
        Log::info("🔽 [DownloadLetter] Request download file", [
            'order_id' => $orderId,
            'letter_slug' => $slug,
        ]);

        // Set Midtrans config
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');

        // Cek status transaksi Midtrans
        try {
            $status = Transaction::status($orderId);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Transaksi tidak valid atau tidak ditemukan.'
            ], 403);
        }

        if (!in_array($status->transaction_status, ['capture', 'settlement'])) {
            return response()->json([
                'error' => 'Transaksi Anda belum berhasil diselesaikan.'
            ], 403);
        }

        // Ambil record surat
        $letter = Letter::where('letter_slug', $slug)->first();

        if (!$letter || !$letter->letter_file_path) {
            return response()->json([
                'error' => 'File surat tidak tersedia di database.'
            ], 404);
        }

        // Full path file di storage
        $filePath = storage_path("app/public/{$letter->letter_file_path}");

        if (!file_exists($filePath)) {
            return response()->json([
                'error' => 'File surat tidak ditemukan di storage.',
                'checked_path' => $filePath,
            ], 404);
        }

        // Download file ke browser
        return response()->download($filePath, basename($letter->letter_file_path));
    }
}
