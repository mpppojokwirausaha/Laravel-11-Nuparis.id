<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    public $incrementing = false;
    protected $table = 'event_participants';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    protected $casts = [
        'uuid' => 'string',
        'checked_in_at' => 'datetime',
    ];

    protected $fillable = [
        'uuid',
        'event_uuid',
        'order_uuid',
        'participant_name',
        'participant_nib',
        'participant_address',
        'participant_email',
        'participant_no_wa',
        'ticket_code',
        'checked_in_at',
    ];

    // Relasi
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_uuid', 'uuid');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_uuid', 'uuid');
    }
}
