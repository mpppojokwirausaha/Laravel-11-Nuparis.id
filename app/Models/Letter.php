<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Letter extends Model
{
    use HasFactory;
    protected $guarded = [
        'uuid',
    ];

    public $incrementing =  false;
    protected $table = 'letters';
    protected $primaryKey = 'uuid';
    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($letter) {
            if (empty($letter->uuid)) {
                $letter->uuid = (string) Str::uuid();
            }

            if (empty($letter->letter_slug) && !empty($letter->letter_name)) {
                $letter->letter_slug = Str::slug($letter->letter_name);
            }
        });

        static::created(function ($letter) {
            if ($letter->letter_file_path && $letter->letter_slug) {
                $extension = pathinfo($letter->letter_file_path, PATHINFO_EXTENSION);
                $newPath = 'letters/' . $letter->letter_slug . '-' . time() . '.' . $extension;

                if (Storage::disk('public')->exists($letter->letter_file_path)) {
                    Storage::disk('public')->move($letter->letter_file_path, $newPath);
                    $letter->update(['letter_file_path' => $newPath]);
                }
            }
        });

        static::updating(function ($letter) {
            if ($letter->isDirty('letter_file_path')) {
                $oldPath = $letter->getOriginal('letter_file_path');
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });


        static::deleting(function ($letter) {
            if ($letter->letter_file_path && Storage::disk('public')->exists($letter->letter_file_path)) {
                Storage::disk('public')->delete($letter->letter_file_path);
            }
        });
    }


    public function getRouteKeyName()
    {
        return 'letter_slug';
    }

    public function letterCategory()
    {
        return $this->belongsTo(LetterCategory::class, 'letter_category_uuid');
    }
}
