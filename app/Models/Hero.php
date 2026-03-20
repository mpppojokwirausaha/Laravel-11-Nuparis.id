<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Hero extends Model
{
    public $incrementing = false;
    protected $table = 'heroes';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    protected $casts = ['uuid' => 'string'];

    protected $fillable = [
        'uuid',
        'hero_name',
        'hero_status',
        'hero_slug',
        'hero_assets',
    ];

    protected static function booted()
    {
        // Sebelum create: generate UUID
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Sebelum update: hapus image lama kalau diganti
        static::updating(function ($model) {
            if ($model->isDirty('news_assets')) {
                $oldAssets = $model->getOriginal('news_asset');
                if ($oldAssets && Storage::disk('public')->exists($oldAssets)) {
                    Storage::disk('public')->delete($oldAssets);
                }
            }
        });

        // Sebelum delete: hapus image image
        static::deleting(function ($model) {
            if ($model->news_assets && Storage::disk('public')->exists($model->news_assets)) {
                Storage::disk('public')->delete($model->news_assets);
            }
        });
    }

    // Accessor untuk ambil URL video jika file-nya .mp4
    public static function getAssets()
    {
        // get 1 video status true
        $hero = self::where('hero_status', true)
            ->where('hero_assets', 'like', '%.mp4')
            ->first();

        if ($hero && $hero->hero_assets) {
            return [
                'url' => asset('storage/' . ltrim($hero->hero_assets, '/'))
            ];
        }

        // if not found, get image status true
        $imageExtensions = ['jpg', 'jpeg', 'png', 'bmp'];

        return self::where('hero_status', true)
            ->where(function ($query) use ($imageExtensions) {
                foreach ($imageExtensions as $ext) {
                    $query->orWhere('hero_assets', 'like', "%.$ext");
                }
            })
            ->get()
            ->map(function ($hero) {
                return asset('storage/' . ltrim($hero->hero_assets, '/'));
            });
    }

    public function getRouteKeyName()
    {
        return 'hero_name';
    }
}
