<?php

namespace App\Models;

use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Traits\HasCleanExcerpt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Event extends Model implements Feedable
{
    use HasFactory, HasCleanExcerpt;

    public $incrementing = false;
    protected $table = 'events';
    protected $primaryKey = 'uuid';
    protected $casts = [
        'uuid' => 'string',
        'event_type' => 'array',
        'event_date_start' => 'datetime',
        'event_date_end' => 'datetime',
    ];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'event_title',
        'event_slug',
        'event_description',
        'event_image',
        'event_link',
        'event_type',
        'event_is_active',
        'event_date_start',
        'event_date_end',
        'event_location',
        'event_price',
        'event_quota',
        'event_category_uuid',
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
            if ($model->isDirty('event_image')) {
                $oldImage = $model->getOriginal('event_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        // Sebelum delete: hapus image image
        static::deleting(function ($model) {
            if ($model->event_image && Storage::disk('public')->exists($model->event_image)) {
                Storage::disk('public')->delete($model->event_image);
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
        return static::cleanExcerpt($this->event_description, 85);
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

    // ==== Feed (RSS/Atom) ====
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->uuid)
            ->title($this->event_title)
            ->summary(static::cleanExcerpt($this->event_description, 200))
            ->updated($this->updated_at)
            ->link(route('event-detail', $this->event_slug))
            ->authorName(config('app.name'))
            ->image($this->event_image ? Storage::disk('public')->url($this->event_image) : null);
    }

    public static function getFeedItems()
    {
        // Hanya event yang aktif & belum lewat, supaya feed tidak penuh event lama
        return static::where('event_is_active', true)
            ->where(function ($query) {
                $query->whereNull('event_date_end')
                    ->orWhereDate('event_date_end', '>=', today());
            })
            ->orderBy('event_date_start')
            ->limit(50)
            ->get();
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
