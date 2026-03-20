<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'articles';
    protected $primaryKey = 'uuid';
    protected $casts = [
        'uuid' => 'string',
        'shares_count' => 'integer',
        'views' => 'integer',
        'comments_count' => 'integer',
    ];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'article_title',
        'article_slug',
        'article_description',
        'excerpt',
        'article_image',
        'shares_count',
        'views',
        'comments_count',
        'article_category_uuid',
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
            if ($model->isDirty('article_image')) {
                $oldImage = $model->getOriginal('article_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        // Sebelum delete: hapus image image
        static::deleting(function ($model) {
            if ($model->article_image && Storage::disk('public')->exists($model->article_image)) {
                Storage::disk('public')->delete($model->article_image);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'article_slug';
    }

    // TAMBAHKAN ACCESSOR UNTUK KOMPATIBILITAS DENGAN VIEW
    public function getContentAttribute()
    {
        return $this->article_description;
    }

    public function getFeaturedImageAttribute()
    {
        return $this->article_image;
    }

    public function getArticle()
    {
        return $this->latest()->get();
    }

    public function getArticleMore()
    {
        return $this->latest()->paginate(12);
    }

    public function getShortTitleAttribute()
    {
        return Str::limit($this->article_title, 65);
    }

    public function getShortDescriptionAttribute()
    {
        return Str::limit(strip_tags($this->article_description), 85);
    }

    // add badge
    public function getArticleBadgeAttribute()
    {
        $name = strtolower($this->articleCategory->article_category_name ?? '');

        $badges = [
            'perizinan' => [
                'label' => 'Perizinan',
            ],
            'non perizinan' => [
                'label' => 'Non Perizinan',
            ],
        ];

        return $badges[$name] ?? [
            'label' => ucfirst($name)
        ];
    }

    public function getArticleDetail($slug)
    {
        return $this->where('article_slug', $slug)->first();
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
            $description = 'New articles this month';
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

    // relationship
    public function articleCategory()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_uuid');
    }
}
