<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

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
}
