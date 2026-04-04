<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class EventParticipant extends Model
{
    use Notifiable;

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

    /**
     * Route notifications for the Mail channel.
     */
    public function routeNotificationForMail($notification = null): string
    {
        return $this->participant_email;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_uuid', 'uuid');
    }

    /**
     * Generate ticket code for event participant
     */
    public static function generateTicketCode(): string
    {
        return 'NUPARIS.ID-EVENT-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
    }

    /**
     * Create an event participant with ticket code
     */
    public static function createWithTicket(array $data): self
    {
        $ticketCode = $data['ticket_code'] ?? self::generateTicketCode();

        return self::create([
            'uuid' => Str::uuid(),
            'event_uuid' => $data['event_uuid'],
            'order_uuid' => $data['order_uuid'],
            'participant_name' => $data['participant_name'],
            'participant_email' => $data['participant_email'],
            'participant_no_wa' => $data['participant_no_wa'] ?? null,
            'participant_nib' => $data['participant_nib'] ?? null,
            'participant_address' => $data['participant_address'] ?? null,
            'ticket_code' => $ticketCode,
            'checked_in_at' => null,
        ]);
    }
}
