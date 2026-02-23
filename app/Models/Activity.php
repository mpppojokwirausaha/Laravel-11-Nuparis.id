<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'activities';
    protected $primaryKey = 'uuid';
    protected $casts = ['uuid' => 'string'];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'activity_title',
        'activity_slug',
        'activity_description',
        'activity_image',
        'activity_category_uuid',
        'activity_location',
        'activity_date'
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
        return 'activity_slug';
    }

    public function getActivity()
    {
        return $this->orderBy('activity_date', 'desc')->get();
    }

    public function getActivityMore()
    {
        return $this->orderBy('activity_date', 'desc')->paginate(12);
    }

    public function getShortDescriptionAttribute()
    {
        return Str::limit($this->activity_description, 85);
    }

    public function getActivityDetail($slug)
    {
        return $this->where('activity_slug', $slug)->first();
    }

    public static function getStat()
    {
        $today = now()->day;
        $chartData = collect(range(1, $today))
            ->map(function ($day) {
                $date = now()->startOfMonth()->addDays($day - 1)->toDateString();
                return Article::whereDate('created_at', $date)->count();
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
            $description = 'New activities this month';
            $direction = 'heroicon-m-arrow-trending-up';
        }

        return [
            'currentCount' => $currentCount,
            'description' => $description,
            'icon' => $direction,
            'color' => $percentChange < 0 ? 'danger' : 'success',
            'chart' => $chartData
        ];
    }

    // relationship
    public function activityCategory()
    {
        return $this->belongsTo(ActivityCategory::class, 'activity_category_uuid');
    }
}
