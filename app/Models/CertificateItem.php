<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CertificateItem extends Model
{
    use HasUuids;

    protected $table = 'certificate_items';

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'certificate_generate_id',
        'certificate_template_id',
        'slug',
        'nama',
        'keterangan',
        'deskripsi',
        'catatan',
        'fields',
        'tempat',
        'tanggal',
        'tahun',
        'valid_from',
        'valid_until',
        'file_path',
        'qr_path',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'fields' => 'array',
    ];

    public function generate(): BelongsTo
    {
        return $this->belongsTo(CertificateGenerate::class, 'certificate_generate_id', 'uuid');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id', 'uuid');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    public function getDisplayLabelAttribute(): string
    {
        if (! empty($this->fields)) {
            foreach ($this->fields as $f) {
                if (! empty($f['value'])) {
                    return $f['value'];
                }
            }
        }

        return $this->nama ?: $this->slug;
    }
}
