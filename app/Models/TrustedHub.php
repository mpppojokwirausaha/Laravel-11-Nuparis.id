<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrustedHub extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $table = 'trusted_hubs';
    protected $casts = ['uuid' => 'string'];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'trustedhub_url',
        'trustedhub_description',
    ];

    protected static function booted()
    {
        // Sebelum create: generate UUID
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getTrustedHubs()
    {
        return TrustedHub::OrderBy('trustedhub_url', 'asc')->get();
    }
}
