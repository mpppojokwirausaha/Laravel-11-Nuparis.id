<?php

namespace App\Models;

use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Traits\HasCleanExcerpt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Property extends Model implements Feedable
{
    use HasCleanExcerpt;

    public $incrementing = false;
    protected $table = 'properties';
    protected $primaryKey = 'uuid';
    protected $casts = [
        'uuid' => 'string',
        'property_image' => 'array',
        'property_fasilities' => 'array',
        'property_certificate' => 'array',
    ];
    protected $keyType = 'string';
    protected $fillable = [
        'uuid',
        'property_name',
        'property_slug',
        'property_type',
        'property_status',
        'property_date_start',
        'property_date_end',
        'property_transaction_type',
        'property_price',
        'property_address',
        'property_latitude',
        'property_longitude',
        'property_description',
        'property_image',
        'property_building_area',
        'property_land_area',
        'property_no_whatsapp',
        'property_fasilities',
        'property_certificate',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // UUID
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            // Slug
            if (empty($model->property_slug) && $model->property_name) {
                $model->property_slug = Str::slug($model->property_name);
            }
        });

        static::updating(function ($model) {
            // Update slug jika nama berubah
            if ($model->isDirty('property_name')) {
                $model->property_slug = Str::slug($model->property_name);
            }

            // ===== HANDLE ARRAY FILE: IMAGE =====
            if ($model->isDirty('property_image')) {
                $oldFiles = (array) $model->getOriginal('property_image');
                $newFiles = (array) $model->property_image;

                $deletedFiles = array_diff($oldFiles, $newFiles);

                foreach ($deletedFiles as $file) {
                    if ($file && Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }

            // ===== HANDLE ARRAY FILE: CERTIFICATE =====
            if ($model->isDirty('property_certificate')) {
                $oldFiles = (array) $model->getOriginal('property_certificate');
                $newFiles = (array) $model->property_certificate;

                $deletedFiles = array_diff($oldFiles, $newFiles);

                foreach ($deletedFiles as $file) {
                    if ($file && Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        });

        static::deleting(function ($model) {
            // Hapus semua image
            foreach ((array) $model->property_image as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }

            // Hapus semua certificate
            foreach ((array) $model->property_certificate as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS (WAJIB untuk array stabil)
    |--------------------------------------------------------------------------
    */

    public function setPropertyImageAttribute($value)
    {
        $this->attributes['property_image'] = json_encode(array_values((array) $value));
    }

    public function setPropertyCertificateAttribute($value)
    {
        $this->attributes['property_certificate'] = json_encode(array_values((array) $value));
    }

    public function getRouteKeyName()
    {
        return 'property_slug';
    }

    public function getProperties()
    {
        return Property::where('property_status', 'Active')
            ->where(function ($query) {
                $query->whereDate('property_date_end', '>=', today())
                    ->orWhereNull('property_date_end');
            })
            ->latest()->get();
    }

    public function getPropertiryMore()
    {
        return $this->latest()->paginate(12);
    }

    public function getPropertyDetail($property_slug)
    {
        return $this->where('property_slug', $property_slug)->first();
    }

    public function getShortTitleAttribute()
    {
        return Str::limit($this->property_name, 25);
    }

    public function getShortDescriptionAttribute()
    {
        return static::cleanExcerpt($this->property_description, 85);
    }

    // ==== Feed (RSS/Atom) ====
    public function toFeedItem(): FeedItem
    {
        $images = (array) $this->property_image;

        return FeedItem::create()
            ->id($this->uuid)
            ->title($this->property_name)
            ->summary(static::cleanExcerpt($this->property_description, 200))
            ->updated($this->updated_at)
            ->link(route('property-detail', $this->property_slug))
            ->authorName(config('app.name'))
            ->image(!empty($images[0]) ? Storage::disk('public')->url($images[0]) : null);
    }

    public static function getFeedItems()
    {
        // Sama seperti scope di getProperties(): hanya listing aktif
        return static::where('property_status', 'Active')
            ->where(function ($query) {
                $query->whereDate('property_date_end', '>=', today())
                    ->orWhereNull('property_date_end');
            })
            ->latest('created_at')
            ->limit(50)
            ->get();
    }
}
