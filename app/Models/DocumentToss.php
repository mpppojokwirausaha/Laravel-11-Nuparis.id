<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentToss extends Model
{
    public $incrementing = false;
    protected $table = 'tosses';
    protected $primaryKey = 'uuid';
    protected $casts = [
        'uuid' => 'string',
    ];

    protected $keyType = 'string';

    protected $guarded = [
        'uuid',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
            $model->uuid = (string) Str::uuid();
            }
        });
    }
}
