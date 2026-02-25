<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Letter;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class OrderController extends Controller
{
    public $midtrans;

    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);

        // Log untuk debugging (opsional, bisa dihapus setelah production)
        Log::info('Midtrans Config Loaded', [
            'is_production' => Config::$isProduction,
            'server_key_prefix' => substr(Config::$serverKey, 0, 10) . '...',
            'client_key_prefix' => substr(Config::$clientKey, 0, 10) . '...'
        ]);

        $this->midtrans = [
            'server_key' => Config::$serverKey,
            'client_key' => Config::$clientKey,
            'is_production' => Config::$isProduction,
        ];
    }

    /**
     * Handle paid event registration with Midtrans
     */
    public function createMidtransTransaction(Request $request)
    {
        // Log input
        Log::info('Memulai transaksi Midtrans', ['input' => $request->all()]);

        // Validasi input
        $request->validate([
            'type' => 'required|string',
            'uuid' => 'required|string',
            'participant' => 'required|array',
            'participant.name' => 'required|string|max:255',
            'participant.email' => 'required|email|max:255',
            'participant.phone' => 'required|string|max:15',
            'participant.company' => 'nullable|string|max:255',
            'participant.source' => 'nullable|string|max:255'
        ]);

        $type = $request->type;
        $uuid = $request->uuid;
        $participant = $request->participant;

        // Ambil data produk berdasarkan type
        switch ($type) {
            case 'letter':
                $product = Letter::where('uuid', $uuid)->firstOrFail();
                $productName = $product->letter_name;
                $productSlug = $product->letter_slug;
                $grossAmount = $product->letter_price ?? 0;
                break;

            case 'event':
                $product = Event::where('uuid', $uuid)->firstOrFail();
                $productName = $product->event_title;
                $productSlug = $product->event_slug;
                $grossAmount = $product->event_price ?? 0;

                // Validasi event berbayar
                if ($grossAmount <= 0) {
                    return response()->json([
                        'error' => 'Event ini gratis. Silakan gunakan pendaftaran gratis.'
                    ], 400);
                }

                // Cek kuota event
                $registered = EventParticipant::where('event_uuid', $uuid)->count();
                $remainingQuota = $product->event_quota - $registered;

                if ($remainingQuota <= 0) {
                    return response()->json([
                        'error' => 'Kuota event sudah penuh'
                    ], 400);
                }
                break;

            default:
                return response()->json(['error' => 'Tipe produk tidak didukung'], 400);
        }

        // Buat order ID unik
        if ($type === 'letter') {
            $orderId = 'SCRIDB.NUPARIS.ID-LETTER-ORD-' . strtoupper(Str::random(8)) . '-' . time();
        } elseif ($type === 'event') {
            $orderId = 'NUPARIS.ID-EVENT-ORD-' . strtoupper(Str::random(8)) . '-' . time();
        }

        // Parameter Snap Midtrans dengan data customer real
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => [
                [
                    'id' => $productSlug,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => $productName,
                ],
            ],
            'customer_details' => [
                'first_name' => $participant['name'],
                'email' => $participant['email'],
                'phone' => $participant['phone'],
            ],
            'callbacks' => [
                'finish' => route('landingpage'),
            ]
        ];

        // Log parameter transaksi untuk debugging
        Log::info('Midtrans Transaction Parameters', [
            'order_id' => $orderId,
            'params' => $params
        ]);

        try {
            // Ambil Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Simpan order ke database
            Order::create([
                'uuid' => Str::uuid(),
                'order_id' => $orderId,
                'order_gross_amount' => $grossAmount,
                'order_transaction_status' => 'pending',
                'order_customer_name' => $participant['name'],
                'order_customer_email' => $participant['email'],
                'order_customer_phone' => $participant['phone'],
                'order_customer_company' => $participant['company'] ?? null,
                'order_customer_source' => $participant['source'] ?? null,
                'order_reference_type' => $type,
                'order_reference_uuid' => $uuid,
                'order_product_name' => $productName,
                'order_product_slug' => $productSlug,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Transaksi Midtrans berhasil dibuat', [
                'order_id' => $orderId,
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal membuat transaksi Midtrans', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal membuat transaksi',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle free event registration
     */
    public function registerFree(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'event_uuid' => 'required|string|exists:events,uuid',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:15',
                'company' => 'nullable|string|max:255',
                'source' => 'nullable|string|max:255'
            ]);

            DB::beginTransaction();

            // Ambil data event
            $event = Event::where('uuid', $request->event_uuid)->firstOrFail();

            // Validasi event gratis
            if ($event->event_price > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event ini berbayar. Silakan gunakan metode pembayaran.'
                ], 400);
            }

            // Cek kuota
            $registered = EventParticipant::where('event_uuid', $request->event_uuid)->count();
            $remainingQuota = $event->event_quota - $registered;

            if ($remainingQuota <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, kuota event sudah penuh'
                ], 400);
            }

            // Generate order ID unik untuk free event
            $orderId = 'NUPARIS.ID-ORD-' . strtoupper(Str::random(8)) . '-' . time();

            // Simpan order free
            $order = Order::create([
                'uuid' => Str::uuid(),
                'order_id' => $orderId,
                'order_gross_amount' => 0,
                'order_transaction_status' => 'success',
                'order_customer_name' => $request->name,
                'order_customer_email' => $request->email,
                'order_customer_phone' => $request->phone,
                'order_customer_company' => $request->company,
                'order_customer_source' => $request->source,
                'order_reference_type' => 'event',
                'order_reference_uuid' => $request->event_uuid,
                'order_product_name' => $event->event_title,
                'order_product_slug' => $event->event_slug,
                'order_paid_at' => now(),
            ]);

            // Generate unique ticket code
            $ticketCode = 'NUPARIS.ID-TCK-' . strtoupper(Str::random(8)) . '-' . date('Ymd');

            // Buat participant
            EventParticipant::create([
                'uuid' => Str::uuid(),
                'event_uuid' => $order->order_reference_uuid,
                'order_uuid' => $order->uuid,
                'participant_name' => $order->order_customer_name,
                'participant_email' => $order->order_customer_email,
                'participant_no_wa' => $order->order_customer_phone,
                'participant_nib' => null,
                'participant_address' => null,
                'ticket_code' => $ticketCode,
                'checked_in_at' => null,
            ]);

            DB::commit();

            Log::info('Free event registration success', [
                'event_uuid' => $request->event_uuid,
                'event_title' => $event->event_title,
                'email' => $request->email,
                'order_id' => $orderId,
                'ticket_code' => $ticketCode
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Tiket telah dikirim ke email Anda.',
                'data' => [
                    'order_id' => $orderId,
                    'ticket_code' => $ticketCode,
                    'event_title' => $event->event_title,
                    'event_date' => Carbon::parse($event->event_date_start)->format('d F Y H:i'),
                    'event_location' => $event->event_location
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Free registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification (webhook)
     */
    public function handleNotification(Request $request)
    {
        try {
            Log::info('📩 [Midtrans] Payload notifikasi diterima:', [
                'payload' => $request->all(),
            ]);

            $notif = new Notification();

            $orderId = $notif->order_id;
            $status = $notif->transaction_status;
            $fraud = $notif->fraud_status ?? null;

            Log::info('📦 [Midtrans] Data notifikasi terurai:', [
                'order_id' => $orderId,
                'transaction_status' => $status,
                'fraud_status' => $fraud,
            ]);

            // Cari order berdasarkan order_id
            $order = Order::where('order_id', $orderId)->first();

            if (!$order) {
                Log::warning("⚠️ [Midtrans] Order dengan ID {$orderId} tidak ditemukan di database.");
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }

            $oldStatus = $order->order_transaction_status;
            $newStatus = $status;

            // Update status berdasarkan notifikasi Midtrans
            switch ($status) {
                case 'capture':
                    if ($fraud === 'challenge') {
                        $newStatus = 'challenge';
                    } else {
                        $newStatus = 'success';
                    }
                    break;

                case 'settlement':
                    $newStatus = 'success';
                    $order->order_paid_at = now();
                    break;

                case 'pending':
                    $newStatus = 'pending';
                    break;

                case 'deny':
                case 'expire':
                case 'cancel':
                    $newStatus = 'failed';
                    break;

                default:
                    $newStatus = $status;
                    break;
            }

            $order->order_transaction_status = $newStatus;
            $order->save();

            // Jika status berubah menjadi success dan ini adalah event, buat participant
            if ($oldStatus !== 'success' && $newStatus === 'success' && $order->order_reference_type === 'event') {
                DB::beginTransaction();
                try {
                    // Cek apakah participant sudah ada
                    $existingParticipant = EventParticipant::where('order_uuid', $order->uuid)->first();

                    if (!$existingParticipant) {
                        // Generate unique ticket code
                        $ticketCode = 'EVT-' . strtoupper(Str::random(8)) . '-' . date('Ymd');

                        // Buat participant
                        EventParticipant::create([
                            'uuid' => Str::uuid(),
                            'event_uuid' => $order->order_reference_uuid,
                            'order_uuid' => $order->uuid,
                            'participant_name' => $order->order_customer_name,
                            'participant_email' => $order->order_customer_email,
                            'participant_no_wa' => $order->order_customer_phone,
                            'participant_company' => $order->order_customer_company,
                            'participant_source' => $order->order_customer_source,
                            'ticket_code' => $ticketCode,
                            'check_in_status' => false,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        Log::info('✅ Participant created after successful payment', [
                            'order_id' => $orderId,
                            'ticket_code' => $ticketCode
                        ]);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('❌ Gagal membuat participant setelah payment success', [
                        'order_id' => $orderId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            Log::info('✅ [Midtrans] Status order berhasil diperbarui:', [
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            return response()->json([
                'message' => 'Notifikasi berhasil diproses',
                'status' => $newStatus,
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
