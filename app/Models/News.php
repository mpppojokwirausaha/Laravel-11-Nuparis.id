<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'news';
    protected $primaryKey = 'uuid';
    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'news_source',
        'news_title',
        'news_slug',
        'news_url',
        'news_image',
        'news_content',
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
            if ($model->isDirty('news_image')) {
                $oldImage = $model->getOriginal('news_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        // Sebelum delete: hapus image image
        static::deleting(function ($model) {
            if ($model->news_image && Storage::disk('public')->exists($model->news_image)) {
                Storage::disk('public')->delete($model->news_image);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'news_slug';
    }

    public function getNews()
    {
        return $this->latest()->get();
    }

    public function getNewsMore()
    {
        return $this->latest()->paginate(12);
    }

    public function getShortContentAttribute()
    {
        return Str::limit($this->news_content, 85);
    }

    public static function getStat()
    {
        $today = now()->day;
        $chartData = collect(range(1, $today))
            ->map(function ($day) {
                $date = now()->startOfMonth()->addDays($day - 1)->toDateString();
                return self::whereDate('created_at', $date)->count();
            })
            ->toArray();

        $currentCount = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastCount = self::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $percentChange = 0;
        $direction = 'heroicon-m-arrow-trending-up';
        $description = 'No change';

        if ($lastCount > 0) {
            $percentChange = (($currentCount - $lastCount) / $lastCount) * 100;
            $description = number_format(abs($percentChange), 2) . '% ' . ($percentChange >= 0 ? 'increase' : 'decrease');
            $direction = $percentChange >= 0
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down';
        } elseif ($currentCount > 0) {
            $description = 'New news this month';
            $direction = 'heroicon-m-arrow-trending-up';
        }

        return [
            'currentCount' => $currentCount,
            'description' => $description,
            'icon' => $direction,
            'color' => $percentChange < 0 ? 'danger' : 'success',
            'chart' => $chartData,
        ];
    }
}
