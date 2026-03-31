<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Partner extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'partners';
    protected $primaryKey = 'uuid';
    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'partner_id',
        'partner_name',
        'partner_slug',
        'partner_phone',
        'partner_NIB',
        'partner_NPWP',
        'partner_email',
        'partner_description',
        'partner_image',
        'partner_address',
        'partner_url',
        'partner_status',
        'partner_footer_status',
    ];

    protected static function booted()
    {
        // Sebelum create: generate UUID otomatis
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Sebelum update: hapus file lama kalau diganti
        static::updating(function ($model) {
            // Hapus partner_image lama
            if ($model->isDirty('partner_image')) {
                $oldImage = $model->getOriginal('partner_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            // Hapus partner_NPWP lama
            if ($model->isDirty('partner_NPWP')) {
                $oldNPWP = $model->getOriginal('partner_NPWP');
                if ($oldNPWP && Storage::disk('public')->exists($oldNPWP)) {
                    Storage::disk('public')->delete($oldNPWP);
                }
            }

            // Hapus partner_NIB lama
            if ($model->isDirty('partner_NIB')) {
                $oldNIB = $model->getOriginal('partner_NIB');
                if ($oldNIB && Storage::disk('public')->exists($oldNIB)) {
                    Storage::disk('public')->delete($oldNIB);
                }
            }
        });

        // Sebelum delete: hapus semua file
        static::deleting(function ($model) {
            foreach (['partner_image', 'partner_NPWP', 'partner_NIB'] as $field) {
                $file = $model->{$field};
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'partner_slug';
    }

    public function getPartner()
    {
        $partners = Partner::all();

        $layers = 2;
        $partnerLayers = array_fill(0, $layers, collect());

        foreach ($partners as $index => $partner) {
            $partnerLayers[$index % $layers]->push($partner);
        }

        return collect($partnerLayers);
    }

    public function getAgencies()
    {
        return Partner::where('partner_footer_status', 'Active')->get();
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
            $description = 'New mitras this month';
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
