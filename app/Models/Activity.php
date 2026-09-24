<?php

namespace App\Models;

use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Activity extends Model implements Feedable
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
        // Sebelum create: generate UUID
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Sebelum update: hapus image lama kalau diganti
        static::updating(function ($model) {
            if ($model->isDirty('activity_image')) {
                $oldImage = $model->getOriginal('activity_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        // Sebelum delete: hapus image image
        static::deleting(function ($model) {
            if ($model->activity_image && Storage::disk('public')->exists($model->activity_image)) {
                Storage::disk('public')->delete($model->activity_image);
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

    // ==== Feed (RSS/Atom) ====
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->uuid)
            ->title($this->activity_title)
            ->summary(Str::limit($this->activity_description, 200))
            ->updated($this->updated_at)
            ->link(route('activity-detail', $this->activity_slug))
            ->authorName('Tim Redaksi ' . config('app.name'))
            ->image($this->activity_image ? Storage::disk('public')->url($this->activity_image) : null);
    }

    public static function getFeedItems()
    {
        return static::latest('created_at')->limit(50)->get();
    }

    // relationship
    public function activityCategory()
    {
        return $this->belongsTo(ActivityCategory::class, 'activity_category_uuid');
    }
}
