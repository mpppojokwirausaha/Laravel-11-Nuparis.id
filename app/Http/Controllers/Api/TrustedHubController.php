<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrustedHub;
use Exception;

class TrustedHubController extends Controller
{
    public function index()
    {
        try {
            $TrustedHubs = (new TrustedHub())->getTrustedHubs();

            if ($TrustedHubs->isEmpty()) {
                return response()->json([
                    'status' => 204,
                    'message' => 'Tidak ada data surat ditemukan.',
                    'body' => []
                ], 200);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Data TrustedHubs retrieved successfully.',
                'body' => $TrustedHubs
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
}
