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
    ];

    protected $casts = [
        'is_placed' => 'boolean',
        'font_bold' => 'boolean',
        'font_underline' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }
}
