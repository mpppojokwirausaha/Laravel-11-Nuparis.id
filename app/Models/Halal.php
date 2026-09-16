<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Halal extends Model
{
    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_pelaku',
        'nama_brand',
        'nik_ktp',
        'nib',
        'npwp',
        'modal_awal',
        'tahun_berdiri',
        'luas_usaha',
        'pendapatan_minggu',
        'no_whatsapp',
        'email',
        'alamat',
        'bahan',
        'cara_pembuatan',
    ];

    protected $casts = [
        'modal_awal'        => 'integer',
        'pendapatan_minggu' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
