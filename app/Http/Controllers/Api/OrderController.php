<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class OrderController extends Controller
{

    public $midtrans;

    public function __construct()
    {
        $this->midtrans = [
            Config::$serverKey      = config('midtrans.server_key'),
            Config::$clientKey      = config('midtrans.client_key'),
            Config::$isProduction   = config('midtrans.is_production'),
            Config::$isSanitized    = config('midtrans.is_sanitized'),
            Config::$is3ds          = config('midtrans.is_3ds'),
        ];
    }

    public function createMidtransTransaction(Request $request)
    {
        Log::info('=== [createMidtransTransaction] Memulai proses transaksi Midtrans ===', [
            'input' => $request->all(),
        ]);
    
        $request->validate([
            'slug' => 'required|string',
        ]);
    
        Log::info('Validasi slug berhasil', ['slug' => $request->slug]);
    
        $letter = Letter::where('letter_slug', $request->slug)->first();
    
        if (!$letter) {
            Log::warning('Surat tidak ditemukan di database', ['slug' => $request->slug]);
            return response()->json(['error' => 'Surat tidak ditemukan.'], 404);
        }
    
        Log::info('Surat ditemukan', [
            'letter_name' => $letter->letter_name,
            'letter_price' => $letter->letter_price,
        ]);
    
        $orderId = 'scribd.nuparis.id-ORDER-' . Str::upper(Str::random(8));
    
        Log::info('Membuat order ID baru', ['order_id' => $orderId]);
    
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $letter->letter_price ?? 0,
            ],
            'item_details' => [
                [
                    'id' => $letter->letter_slug,
                    'price' => $letter->letter_price ?? 0,
                    'quantity' => 1,
                    'name' => $letter->letter_name,
                ],
            ],
            'customer_details' => [
                'first_name' => 'Pembeli',
                'email' => 'user@example.com',
            ],
        ];
    
        try {
            Log::info('Mengirim permintaan Snap Token ke Midtrans', [
                'order_id' => $orderId,
                'params' => $params,
            ]);
    
            $snapToken = Snap::getSnapToken($params);
    
            Log::info('Berhasil mendapatkan Snap Token dari Midtrans', [
                'order_id' => $orderId,
                'snap_token' => $snapToken,
            ]);
    
            Order::create([
                'order_id' => $orderId,
                'order_gross_amount' => $letter->letter_price ?? 0,
                'order_letter_name' => $letter->letter_name,
                'order_letter_slug' => $letter->letter_slug,
                'order_transaction_status' => 'pending',
                'order_customer_name' => 'Pembeli',
                'order_customer_email' => 'user@example.com',
                'order_customer_phone' => '1234567890',
            ]);
    
            Log::info('Order berhasil disimpan ke database', ['order_id' => $orderId]);
    
            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat membuat transaksi Midtrans', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return response()->json([
                'error' => 'Gagal membuat transaksi',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function handleNotification(Request $request)
    {
        try {
            // 🔹 Log payload mentah dari Midtrans
            Log::info('📩 [Midtrans] Payload notifikasi diterima:', [
                'payload' => $request->all(),
            ]);
    
            // 🔹 Ambil data notifikasi dari Midtrans SDK
            $notif = new Notification();
    
            $orderId = $notif->order_id;
            $status = $notif->transaction_status;
            $fraud = $notif->fraud_status ?? null;
            $paymentType = $notif->payment_type ?? null;
            $grossAmount = $notif->gross_amount ?? null;
    
            Log::info('📦 [Midtrans] Data notifikasi terurai:', [
                'order_id' => $orderId,
                'transaction_status' => $status,
                'fraud_status' => $fraud,
                'payment_type' => $paymentType,
                'gross_amount' => $grossAmount,
            ]);
    
            // 🔍 Cari order berdasarkan order_id
            $order = Order::where('order_id', $orderId)->first();
    
            if (!$order) {
                Log::warning("⚠️ [Midtrans] Order dengan ID {$orderId} tidak ditemukan di database.");
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }
    
            // 🔄 Update status order berdasarkan status transaksi
            switch ($status) {
                case 'capture':
                    if ($fraud === 'challenge') {
                        $order->order_transaction_status = 'challenge';
                    } else {
                        $order->order_transaction_status = 'success';
                    }
                    break;
    
                case 'settlement':
                    $order->order_transaction_status = 'success';
                    $order->order_paid_at = now();
                    break;
    
                case 'pending':
                    $order->order_transaction_status = 'pending';
                    break;
    
                case 'deny':
                    $order->order_transaction_status = 'failed';
                    break;
    
                case 'expire':
                    $order->order_transaction_status = 'expired';
                    break;
    
                case 'cancel':
                    $order->order_transaction_status = 'cancelled';
                    break;
    
                default:
                    $order->order_transaction_status = $status;
                    break;
            }
    
            $order->save();
    
            Log::info('✅ [Midtrans] Status order berhasil diperbarui:', [
                'order_id' => $orderId,
                'new_status' => $order->order_transaction_status,
            ]);
    
            return response()->json([
                'message' => 'Notifikasi berhasil diproses',
                'status' => $order->order_transaction_status,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ [Midtrans] Gagal memproses notifikasi:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return response()->json(['error' => 'Gagal memproses notifikasi'], 500);
        }
    }
}
