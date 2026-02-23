<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    public $incrementing = false;
    protected $table = 'properties';
    protected $primaryKey = 'uuid';
    protected $casts = [
        'uuid' => 'string',
        'property_image' => 'array',
        'property_fasilities' => 'array',
        'property_certificate' => 'array',
    ];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'property_name',
        'property_slug',
        'property_type',
        'property_transaction_type',
        'property_price',
        'property_address',
        'property_latitude',
        'property_longitude',
        'property_description',
        'property_image',   
        'property_building_area',
        'property_land_area',
        'property_no_whatsapp',
        'property_fasilities',
        'property_certificate',
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
        return 'property_slug';
    }

    public function getProperties()
    {
        return $this->latest()->get();
    }

    public function getPropertiryMore()
    {
        return $this->latest()->paginate(12);
    }

    public function getPropertyDetail($property_slug)
    {
        return $this->where('property_slug', $property_slug)->first();
    }

    public function getShortTitleAttribute()
    {
        return Str::limit($this->property_name, 25);
    }

    public function getShortDescriptionAttribute()
    {
        return Str::limit($this->property_description, 85);
    }
}
