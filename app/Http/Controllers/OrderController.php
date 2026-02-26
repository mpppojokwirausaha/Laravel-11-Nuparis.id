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
     * Handle paid event/letter registration with Midtrans
     */
    public function createMidtransTransaction(Request $request)
    {
        Log::info('Memulai transaksi Midtrans', ['input' => $request->all()]);

        // Validasi dasar
        $rules = [
            'type' => 'required|string|in:event,letter',
            'slug' => 'required|string',
        ];

        // Validasi khusus untuk event (butuh participant)
        if ($request->type === 'event') {
            $rules['participant'] = 'required|array';
            $rules['participant.name'] = 'required|string|max:255';
            $rules['participant.email'] = 'required|email|max:255';
            $rules['participant.phone'] = 'required|string|max:15';
            $rules['participant.company'] = 'nullable|string|max:255';
            $rules['participant.source'] = 'nullable|string|max:255';
        }
        // Letter: tanpa validasi tambahan

        $request->validate($rules);

        $type = $request->type;
        $slug = $request->slug;

        switch ($type) {
            case 'letter':
                $product = Letter::where('letter_slug', $slug)->firstOrFail();
                $productUuid = $product->uuid;
                $productName = $product->letter_name;
                $productSlug = $product->letter_slug;
                $grossAmount = $product->letter_price ?? 0;

                if ($grossAmount <= 0) {
                    return response()->json([
                        'error' => 'Letter ini gratis. Silakan gunakan halaman download.'
                    ], 400);
                }

                // Letter: data customer default
                $customerName = 'Customer';
                $customerEmail = null;
                $customerPhone = null;

                // FORMAT ASLI UNTUK LETTER
                $orderId = 'SCRIDB.NUPARIS.ID-LETTER-ORD-' . strtoupper(Str::random(8)) . '-' . time();
                break;

            case 'event':
                $product = Event::where('event_slug', $slug)->firstOrFail();
                $productUuid = $product->uuid;
                $productName = $product->event_title;
                $productSlug = $product->event_slug;
                $grossAmount = $product->event_price ?? 0;

                if ($grossAmount <= 0) {
                    return response()->json([
                        'error' => 'Event ini gratis. Silakan gunakan pendaftaran gratis.'
                    ], 400);
                }

                $registered = EventParticipant::where('event_uuid', $productUuid)->count();
                $remainingQuota = $product->event_quota - $registered;

                if ($remainingQuota <= 0) {
                    return response()->json([
                        'error' => 'Kuota event sudah penuh'
                    ], 400);
                }

                $participant = $request->participant;
                $customerName = $participant['name'];
                $customerEmail = $participant['email'];
                $customerPhone = $participant['phone'];

                // FORMAT ASLI UNTUK EVENT
                $orderId = 'NUPARIS.ID-EVENT-ORD-' . strtoupper(Str::random(8)) . '-' . time();
                break;

            default:
                return response()->json(['error' => 'Tipe produk tidak didukung'], 400);
        }

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
                'first_name' => $customerName ?? 'Customer',
                'email' => $customerEmail ?? 'customer@example.com',
                'phone' => $customerPhone ?? '08123456789',
            ],
            'callbacks' => [
                'finish' => route('landingpage'),
            ]
        ];

        Log::info('Midtrans Transaction Parameters', [
            'order_id' => $orderId,
            'params' => $params
        ]);

        try {
            $snapToken = Snap::getSnapToken($params);

            Order::create([
                'uuid' => Str::uuid(),
                'order_id' => $orderId,
                'order_gross_amount' => $grossAmount,
                'order_transaction_status' => 'pending',
                'order_customer_name' => $customerName,
                'order_customer_email' => $customerEmail,
                'order_customer_phone' => $customerPhone,
                'order_reference_type' => $type,
                'order_reference_uuid' => $productUuid,
                'order_product_name' => $productName,
                'order_product_slug' => $productSlug,
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal membuat transaksi Midtrans', [
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
            $request->validate([
                'event_slug' => 'required|string|exists:events,event_slug',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:15',
                'company' => 'nullable|string|max:255',
                'source' => 'nullable|string|max:255'
            ]);

            DB::beginTransaction();

            $event = Event::where('event_slug', $request->event_slug)->firstOrFail();

            if ($event->event_price > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event ini berbayar. Silakan gunakan metode pembayaran.'
                ], 400);
            }

            $registered = EventParticipant::where('event_uuid', $event->uuid)->count();
            $remainingQuota = $event->event_quota - $registered;

            if ($remainingQuota <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, kuota event sudah penuh'
                ], 400);
            }

            // FORMAT ASLI UNTUK FREE EVENT
            $orderId = 'NUPARIS.ID-ORD-' . strtoupper(Str::random(8)) . '-' . time();

            $order = Order::create([
                'uuid' => Str::uuid(),
                'order_id' => $orderId,
                'order_gross_amount' => 0,
                'order_transaction_status' => 'success',
                'order_customer_name' => $request->name,
                'order_customer_email' => $request->email,
                'order_customer_phone' => $request->phone,
                'order_reference_type' => 'event',
                'order_reference_uuid' => $event->uuid,
                'order_product_name' => $event->event_title,
                'order_product_slug' => $event->event_slug,
                'order_paid_at' => now(),
            ]);

            $ticketCode = 'NUPARIS.ID-TCK-' . strtoupper(Str::random(8)) . '-' . date('Ymd');

            // Participant tetap punya company & source
            EventParticipant::create([
                'uuid' => Str::uuid(),
                'event_uuid' => $event->uuid,
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
                'event_uuid' => $event->uuid,
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

            $order = Order::where('order_id', $orderId)->first();

            if (!$order) {
                Log::warning("⚠️ [Midtrans] Order dengan ID {$orderId} tidak ditemukan di database.");
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }

            $oldStatus = $order->order_transaction_status;

            switch ($status) {
                case 'capture':
                    $newStatus = ($fraud === 'challenge') ? 'challenge' : 'success';
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
            }

            $order->order_transaction_status = $newStatus;
            $order->save();

            // handle event participant
            if ($oldStatus !== 'success' && $newStatus === 'success' && $order->order_reference_type === 'event') {
                $existingParticipant = EventParticipant::where('order_uuid', $order->uuid)->first();
                if (!$existingParticipant) {
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

                    Log::info('✅ Participant created after successful payment', [
                        'order_id' => $orderId,
                        'ticket_code' => $ticketCode
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
