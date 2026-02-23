<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ConsultantSpecialization extends Model
{
    public $incrementvng = false;
    protected $table = 'consultant_specializations';
    protected $primaryKey = 'uuid';
    protected $casts = ['uuid' => 'string'];
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'consultant_specialization_slug',
        'consultant_specialization_name',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'consultant_specialization_slug';
    }

    public function getConsultantSpecialization()
    {
        return $this->all();
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class, 'consultant_specialization_uuid', 'uuid');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'consultant_specialization_uuid', 'uuid');
    }
}
