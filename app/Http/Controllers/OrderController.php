<?php

namespace App\Http\Controllers;

use \Illuminate\Validation\ValidationException;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Letter;
use App\Models\Order;
use App\Notifications\EventRegistrationSuccessNotification;
use App\Notifications\LetterOrderSuccessNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Exception;

class OrderController extends Controller
{
    private const TYPE_EVENT        = 'event';
    private const TYPE_LETTER       = 'letter';

    private const STATUS_SUCCESS    = 'success';
    private const STATUS_PENDING    = 'pending';
    private const STATUS_FAILED     = 'failed';
    private const STATUS_CHALLENGE  = 'challenge';

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    private function normalizePhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-numeric characters except '+'
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Remove leading '+' if exists (will add later if needed)
        $phone = ltrim($phone, '+');

        // Check if starts with 0 (e.g., 083xxxxx)
        if (preg_match('/^0/', $phone)) {
            $phone = '62' . substr($phone, 1);
        }
        // Check if starts with 62 (already international)
        elseif (preg_match('/^62/', $phone)) {
            $phone = $phone;
        }
        // Check if starts with 8 (local without 0, e.g., 8xxxx)
        elseif (preg_match('/^8/', $phone)) {
            $phone = '62' . $phone;
        }

        // Remove any non-digit characters again just in case
        $phone = preg_replace('/[^0-9]/', '', $phone);

        return $phone;
    }

    /**
     * Main entry point for transaction creation
     */
    public function createMidtransTransaction(Request $request)
    {
        try {
            $request->validate([
                'type' => ['required', Rule::in([self::TYPE_EVENT, self::TYPE_LETTER])],
                'slug' => 'required|string',
            ]);

            $product = $this->findProduct($request->type, $request->slug);

            switch ($request->type) {
                case 'event':
                    return $product->event_price > 0
                        ? $this->createPaidEventOrder($product, $request)
                        : $this->registerFreeEvent($product, $request);

                case 'letter':
                    return $product->letter_price > 0
                        ? $this->createPaidLetterOrder($product, $request)
                        : $this->orderFreeLetter($product, $request);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Transaction creation failed'], 500);
        }
    }

    private function findProduct(string $type, string $slug)
    {
        if ($type === self::TYPE_EVENT) return Event::where('event_slug', $slug)->firstOrFail();
        if ($type === self::TYPE_LETTER) return Letter::where('letter_slug', $slug)->firstOrFail();
    }

    /**
     * Free Event Registration
     */
    private function registerFreeEvent(Event $event, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'company' => 'nullable|string',
            'source' => 'nullable|string',
        ]);
        $data['phone'] = $this->normalizePhoneNumber($data['phone']);

        DB::beginTransaction();

        try {
            $this->checkEventQuota($event);

            $order = Order::createFreeEventOrder([
                'customer_name' => $data['name'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'],
                'event_uuid' => $event->uuid,
                'product_name' => $event->event_title,
                'product_slug' => $event->event_slug,
            ]);

            $participant = EventParticipant::createWithTicket([
                'event_uuid' => $event->uuid,
                'order_uuid' => $order->uuid,
                'participant_name' => $data['name'],
                'participant_email' => $data['email'],
                'participant_no_wa' => $data['phone'],
                'company' => $data['company'] ?? null,
                'source' => $data['source'] ?? null,
            ]);

            DB::commit();

            $this->sendEmail($participant, new EventRegistrationSuccessNotification($participant));

            return response()->json([
                'success' => true,
                'message' => 'Event registration successful',
                'data' => [
                    'order_id' => $order->order_id,
                    'ticket_code' => $participant->ticket_code,
                    'event_title' => $event->event_title,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Free Letter Order
     */
    private function orderFreeLetter(Letter $letter, Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::createFreeLetterOrder([
                'customer_email' => $data['email'],
                'letter_uuid' => $letter->uuid,
                'product_name' => $letter->letter_name,
                'product_slug' => $letter->letter_slug,
            ]);

            DB::commit();

            $this->sendEmail($order, new LetterOrderSuccessNotification($order));

            return response()->json([
                'success' => true,
                'message' => 'Letter order successful',
                'data' => [
                    'order_id' => $order->order_id,
                    'product_name' => $letter->letter_name,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Paid Event Order with Midtrans
     */
    private function createPaidEventOrder(Event $event, Request $request)
    {
        $participant = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|number|max:13',
            'company' => 'nullable|string',
            'source' => 'nullable|string',
        ]);
        $participant['phone'] = $this->normalizePhoneNumber($participant['phone']);

        $this->checkEventQuota($event);

        $orderId = Order::generateEventOrderId();

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $event->event_price,
            ],
            'item_details' => [[
                'id' => $event->event_slug,
                'price' => $event->event_price,
                'quantity' => 1,
                'name' => $event->event_title,
            ]],
            'customer_details' => [
                'first_name' => $participant['name'],
                'email' => $participant['email'],
                'phone' => $participant['phone'],
            ],
        ]);

        Order::createPaidOrder([
            'order_id' => $orderId,
            'gross_amount' => $event->event_price,
            'customer_name' => $participant['name'],
            'customer_email' => $participant['email'],
            'customer_phone' => $participant['phone'],
            'reference_type' => self::TYPE_EVENT,
            'reference_uuid' => $event->uuid,
            'product_name' => $event->event_title,
            'product_slug' => $event->event_slug,
            'order_transaction_status' => 'pending',
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Paid Letter Order with Midtrans
     */
    private function createPaidLetterOrder(Letter $letter, Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $orderId = Order::generateLetterOrderId();

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $letter->letter_price,
            ],
            'item_details' => [[
                'id' => $letter->letter_slug,
                'price' => $letter->letter_price,
                'quantity' => 1,
                'name' => $letter->letter_name,
            ]],
            'customer_details' => [
                'first_name' => 'Customer',
                'email' => $data['email'],
            ],
        ]);

        Order::createPaidOrder([
            'order_id' => $orderId,
            'gross_amount' => $letter->letter_price,
            'customer_name' => 'Customer',
            'customer_email' => $data['email'],
            'reference_type' => self::TYPE_LETTER,
            'reference_uuid' => $letter->uuid,
            'product_name' => $letter->letter_name,
            'product_slug' => $letter->letter_slug,
            'order_transaction_status' => 'pending',
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $orderId,
        ]);
    }

    /**
     * Midtrans Webhook Handler
     */
    public function handleNotification(Request $request)
    {
        try {
            $notification = new Notification();
            $order = Order::where('order_id', $notification->order_id)->first();
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            DB::beginTransaction();

            $oldStatus = $order->order_transaction_status;
            $newStatus = $this->mapStatus($notification->transaction_status, $notification->fraud_status);

            $order->order_transaction_status = $newStatus;

            if ($newStatus === self::STATUS_SUCCESS && $oldStatus !== self::STATUS_SUCCESS) {
                $order->order_paid_at = now();
                $order->save();

                $this->afterPaymentSuccess($order);
            } else {
                $order->save();
            }

            DB::commit();

            return response()->json(['message' => 'Webhook processed']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Webhook failed'], 500);
        }
    }

    private function afterPaymentSuccess(Order $order): void
    {
        if ($order->order_reference_type === self::TYPE_LETTER) {
            $this->sendEmail($order, new LetterOrderSuccessNotification($order));
            return;
        }

        if ($order->order_reference_type === self::TYPE_EVENT) {
            $normalizedPhone = $this->normalizePhoneNumber($order->order_customer_phone);

            $participant = EventParticipant::createWithTicket([
                'event_uuid' => $order->order_reference_uuid,
                'order_uuid' => $order->uuid,
                'participant_name' => $order->order_customer_name,
                'participant_email' => $order->order_customer_email,
                'participant_no_wa' => $normalizedPhone,
            ]);

            $this->sendEmail($participant, new EventRegistrationSuccessNotification($participant));
        }
    }

    private function mapStatus(string $status, ?string $fraudStatus): string
    {
        return match ($status) {
            'capture' => $fraudStatus === 'challenge' ? self::STATUS_CHALLENGE : self::STATUS_SUCCESS,
            'settlement' => self::STATUS_SUCCESS,
            'pending' => self::STATUS_PENDING,
            'deny', 'expire', 'cancel' => self::STATUS_FAILED,
            default => $status,
        };
    }

    private function checkEventQuota(Event $event): void
    {
        $registered = EventParticipant::where('event_uuid', $event->uuid)->lockForUpdate()->count();

        if ($registered >= $event->event_quota) {
            abort(409, 'Event quota is full');
        }
    }

    private function sendEmail($notifiable, $notification): void
    {
        try {
            $notifiable->notify($notification);
        } catch (\Exception $e) {
            Log::warning('Email sending failed', ['error' => $e->getMessage()]);
        }
    }

    public function registerFree(Request $request)
    {
        try {
            $request->validate([
                'event_slug' => 'required|string|exists:events,event_slug',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:15',
                'company' => 'nullable|string|max:255',
                'source' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $event = Event::where('event_slug', $request->event_slug)->firstOrFail();

        return $this->registerFreeEvent($event, $request);
    }
}
