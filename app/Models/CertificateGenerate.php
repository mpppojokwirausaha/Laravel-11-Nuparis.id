<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CertificateGenerate extends Model
{
    use HasUuids;

    protected $table = 'certificate_generates';

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'certificate_template_id',
        'mode',
        'total_requested',
        'total_success',
        'total_failed',
        'failed_detail',
        'file_path',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id', 'uuid');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CertificateItem::class, 'certificate_generate_id', 'uuid');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }
}
