<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $table = 'ticket_statuses';
    protected $casts = ['uuid' => 'string'];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'ticket_status_name',
    ];

    //relationship
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'uuid');
    }
}
