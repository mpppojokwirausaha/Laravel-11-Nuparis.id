<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'events';
    protected $primaryKey = 'uuid';
    protected $casts = ['uuid' => 'string'];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'event_title',
        'event_slug',
        'event_description',
        'event_image',
        'event_date_start',
        'event_date_end',
        'event_location',
        'event_price',
        'event_quota',
        'event_category_uuid',
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
        return 'event_slug';
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_uuid', 'uuid');
    }

    public function getEvent()
    {
        return $this->latest()->get();
    }

    public function getEventMore()
    {
        return $this->orderBy('event_date_start', 'desc')->paginate(12);
    }

    public function getShortTitleAttribute()
    {
        return Str::limit($this->event_title, 65);
    }

    public function getShortDescriptionAttribute()
    {
        return Str::limit($this->event_description, 85);
    }

    // add badge
    public function getEventBadgeAttribute()
    {
        $now = now();
        $status = '';
        $badgeColor = '';

        if ($this->event_date_start && $this->event_date_end) {
            if ($now->lt($this->event_date_start)) {
                $status = 'Upcoming';
                $badgeColor = 'yellow';
            } elseif ($now->between($this->event_date_start, $this->event_date_end)) {
                $status = 'Ongoing';
                $badgeColor = 'green';
            } else {
                $status = 'Ended';
                $badgeColor = 'red';
            }
        } else {
            $status = 'Unknown';
            $badgeColor = 'gray';
        }

        return [
            'label' => $status,
            'color' => $badgeColor,
        ];
    }

    public function getEventStatusAttribute(): string
    {
        $now = Carbon::now();

        if ($now->lt($this->event_date_start)) {
            return 'Upcoming';
        }

        if ($now->between($this->event_date_start, $this->event_date_end)) {
            return 'Active';
        }

        return 'Inactive';
    }

    public function getEventDetail($slug)
    {
        return $this->where('event_slug', $slug)->first();
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
            $description = 'New events this month';
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
    public function eventCategory()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_uuid', 'uuid');
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class, 'event_uuid', 'uuid');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'event_uuid', 'uuid');
    }
}
