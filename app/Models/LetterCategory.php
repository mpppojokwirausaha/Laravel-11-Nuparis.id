<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LetterCategory extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'letter_categories';
    protected $primaryKey = 'uuid';
    protected $casts = ['uuid' => 'string'];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'letter_category_name',
        'letter_category_slug'
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
        return 'letter_category_slug';
    }

    // relationship
    public function letters()
    {
        return $this->hasMany(Letter::class, 'letter_category_uuid', 'uuid');
    }
}
