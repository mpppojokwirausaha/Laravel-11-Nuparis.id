<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Info extends Model
{
    use HasFactory;

    public $incrementing =  false;
    protected $table = 'infos';
    protected $primaryKey = 'uuid';
    protected $casts =
    [
        'id' => 'string',
        'offices_location' => 'array',
    ];
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'no_whatsapp',
        'address',
        'email',
        'facebook',
        'instagram',
        'youtube',
        'logo',
        'meta_domain',
        'meta_title',
        'meta_desc',
        'meta_keywords',
        'meta_image',
        'proposal',
        'partner_guide',
        'privacy_policy',
        'terms_conditions',
        'offices_location'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getInfo()
    {
        return $this->first();
    }
}
