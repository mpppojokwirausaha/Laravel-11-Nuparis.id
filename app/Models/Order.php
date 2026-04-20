<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = ['uuid'];

    public $incrementing = false;
    protected $table = 'orders';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    // relasi tabel
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_uuid', 'uuid');
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class, 'order_uuid', 'uuid');
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail($notification = null): array
    {
        return [
            $this->order_customer_email ?? 'customer@example.com' => $this->order_customer_name ?? 'Customer',
        ];
    }

    /**
     * Generate order ID for letter
     */
    public static function generateLetterOrderId(): string
    {
        return 'SCRIDB.NUPARIS.ID-LETTER-ORD-' . strtoupper(Str::random(8)) . '-' . time();
    }

    /**
     * Generate order ID for event
     */
    public static function generateEventOrderId(): string
    {
        return 'NUPARIS.ID-EVENT-ORD-' . strtoupper(Str::random(8)) . '-' . time();
    }

    /**
     * Create a paid order (for Midtrans transaction)
     */
    public static function createPaidOrder(array $data): self
    {
        return self::create([
            'uuid' => Str::uuid(),
            'order_id' => $data['order_id'],
            'order_gross_amount' => $data['gross_amount'],
            'order_transaction_status' => 'pending',
            'order_customer_name' => $data['customer_name'],
            'order_customer_email' => $data['customer_email'],
            'order_customer_phone' => $data['customer_phone'] ?? null,
            'order_reference_type' => $data['reference_type'],
            'order_reference_uuid' => $data['reference_uuid'],
            'order_product_name' => $data['product_name'],
            'order_product_slug' => $data['product_slug'],
        ]);
    }

    /**
     * Create a free event order (success immediately)
     */
    public static function createFreeEventOrder(array $data): self
    {
        return self::create([
            'uuid' => Str::uuid(),
            'order_id' => $data['order_id'] ?? self::generateEventOrderId(),
            'order_gross_amount' => 0,
            'order_transaction_status' => 'success',
            'order_customer_name' => $data['customer_name'],
            'order_customer_email' => $data['customer_email'],
            'order_customer_phone' => $data['customer_phone'] ?? null,
            'order_reference_type' => 'event',
            'order_reference_uuid' => $data['event_uuid'],
            'order_product_name' => $data['product_name'],
            'order_product_slug' => $data['product_slug'],
            'order_paid_at' => now(),
        ]);
    }

    /**
     * Create a free letter order (success immediately)
     */
    public static function createFreeLetterOrder(array $data): self
    {
        return self::create([
            'uuid' => Str::uuid(),
            'order_id' => $data['order_id'] ?? self::generateLetterOrderId(),
            'order_gross_amount' => 0,
            'order_transaction_status' => 'success',
            'order_customer_name' => $data['customer_name'] ?? 'Customer',
            'order_customer_email' => $data['customer_email'],
            'order_customer_phone' => $data['customer_phone'] ?? null,
            'order_reference_type' => 'letter',
            'order_reference_uuid' => $data['letter_uuid'],
            'order_product_name' => $data['product_name'],
            'order_product_slug' => $data['product_slug'],
            'order_paid_at' => now(),
        ]);
    }
}
