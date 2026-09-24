<?php

namespace App\Models;

use App\Traits\HasCleanExcerpt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory, HasCleanExcerpt;

    public $incrementing = false;
    protected $table = 'reviews';
    protected $primaryKey = 'uuid';
    protected $casts = ['uuid' => 'string'];
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'review_fullname',
        'review_slug',
        'review_link',
        'review_rating',
        'review_content',
        'review_avatar',
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
            if ($model->isDirty('review_avatar')) {
                $oldImage = $model->getOriginal('review_avatar');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        // Sebelum delete: hapus image image
        static::deleting(function ($model) {
            if ($model->review_avatar && Storage::disk('public')->exists($model->review_avatar)) {
                Storage::disk('public')->delete($model->review_avatar);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'review_slug';
    }

    public static function getReview()
    {
        return self::latest()->get();
    }

    public static function getReviewMore()
    {
        return self::latest()->paginate(12);
    }

    public function getShortContentAttribute()
    {
        return static::cleanExcerpt($this->review_content, 85);
    }

    public static function getStat()
    {
        $today = now()->day;
        $startOfMonth = now()->startOfMonth();

        $counts = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartData = collect(range(1, $today))
            ->map(function ($day) use ($startOfMonth, $counts) {
                $date = $startOfMonth->copy()->addDays($day - 1)->toDateString();
                return $counts->get($date, 0);
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
            $description = 'New reviews this month';
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
