<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateTemplateField extends Model
{
    use HasUuids;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'certificate_template_id',
        'field_key',
        'label',
        'x',
        'y',
        'font_size',
        'font_color',
        'font_family',
        'font_bold',
        'font_underline',
        'text_align',
        'is_placed',
        'usage_count',
        'is_archived',
    ];

    protected $casts = [
        'is_placed' => 'boolean',
        'font_bold' => 'boolean',
        'font_underline' => 'boolean',
        'usage_count' => 'integer',
        'is_archived' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    // Field cuma boleh dihapus permanen kalau belum pernah kepakai generate sama sekali.
    public function canBeDeleted(): bool
    {
        return $this->usage_count === 0;
    }
}
